<?php


namespace App\Http\Services;

use App\Models\Attendance;
use App\Models\Discount;
use App\Models\Employee;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentService
{
  public function getAll()
  {
    return Payment::with(['employee.user', 'admin'])
      ->whereNull('deleted_at')
      ->get();
  }



  public function getArchived()
  {
    return Payment::onlyTrashed()
      ->get();
  }


  public function getUnpaidWeeks(Employee $employee)
  {
    $records = Attendance::where('employee_id', $employee->id)
      ->whereRaw('paid_amount < estimated_amount')
      ->orderBy('date', 'asc')
      ->get();

    $totalUnusedDiscounts = Discount::where('employee_id', $employee->id)
      ->where('is_used', false)
      ->sum('amount');



    $weeks = $records->groupBy(function ($item) {
      $date = Carbon::parse($item->date);
      $start = $date->startOfWeek(Carbon::SATURDAY)->format('Y-m-d');
      $end = $date->endOfWeek(Carbon::FRIDAY)->format('Y-m-d');
      return $start . " إلى " . $end;
    })->map(function ($weekRecords, $range) {

      $totalAlreadyPaid = $weekRecords->sum('paid_amount');
      $remainingAmount = $weekRecords->sum(function ($record) {
        return $record->estimated_amount - $record->paid_amount;
      });

      return [
        'week_range' => $range,
        'total_regular_hours' => round($weekRecords->sum('regular_hours'), 2),
        'total_overtime_hours' => round($weekRecords->sum('overtime_hours'), 2),
        'estimated_amount' => round($remainingAmount, 2),
        'days_count' => $weekRecords->count(),
        'ids' => $weekRecords->pluck('id'),
        'payment_status' => $totalAlreadyPaid > 0 ? 'partially_paid' : 'unpaid',
      ];
    })->values();

    $grandTotalRemaining = $weeks->sum('estimated_amount');

    return [
      'weeks' => $weeks,
      'summary' => [
        'gross_total' => round($grandTotalRemaining, 2),
        'discounts' => round($totalUnusedDiscounts, 2),
        'net_total' => round($grandTotalRemaining - $totalUnusedDiscounts, 2),
      ]
    ];
  }


  public function paySelectedRecords(Request $request)
  {
    return DB::transaction(function () use ($request) {

      $attendances = Attendance::whereIn('id', $request->attendance_ids)
        ->where('employee_id', $request->employee_id)
        ->whereRaw('paid_amount < estimated_amount')
        ->orderBy('date', 'asc')
        ->get();

      if ($attendances->isEmpty()) {
        return response()->json(['message' => 'There are no records to pay'], 400);
      }


      $totalDiscounts = Discount::where('employee_id', $request->employee_id)
        ->where('is_used', false)
        ->sum('amount');

      $grossAmountRequired = $attendances->sum(function ($item) {
        return $item->estimated_amount - $item->paid_amount;
      });

      $netTotalRequired = $grossAmountRequired - $totalDiscounts;


      if (round($request->amount_paid, 2) > round($netTotalRequired, 2)) {
        return response()->json([
          'message' => "The paid amount ({$request->amount_paid}) exceeds the total required amount ({$netTotalRequired}) for the selected records."
        ], 422);
      }


      $payment = Payment::create([
        'employee_id' => $request->employee_id,
        'admin_id' => auth()->id(),
        'total_amount' => $grossAmountRequired,
        'amount_paid' => $request->amount_paid,
        'payment_date' => $request->payment_date,
        'is_paid' => round($request->amount_paid, 2) >= round($grossAmountRequired, 2),
      ]);

      $amountToDistribute = $request->amount_paid + $totalDiscounts;

      foreach ($attendances as $attendance) {
        if ($amountToDistribute <= 0)
          break;

        $remainingOnRecord = $attendance->estimated_amount - $attendance->paid_amount;

        if ($amountToDistribute >= $remainingOnRecord) {
          $amountToDistribute -= $remainingOnRecord;
          $attendance->paid_amount = $attendance->estimated_amount;
        } else {
          $attendance->paid_amount += $amountToDistribute;
          $amountToDistribute = 0;
        }
        $attendance->save();
      }

      Discount::where('employee_id', $request->employee_id)
        ->where('is_used', false)
        ->update(['is_used' => true]);

      return response()->json(['message' => 'Payment paid successfully'], 200);
    });
  }



  public function update(Payment $payment, array $data)
  {
    return DB::transaction(function () use ($payment, $data) {
      $finalTotal = $payment->total_amount;

      if (isset($data['amount_paid'])) {
        $additionalAmount = $data['amount_paid'];
        $newTotalPaid = $payment->amount_paid + $additionalAmount;

        if ($newTotalPaid > $finalTotal) {
          throw new \Exception("Error: The total paid amount cannot exceed the required total.");
        }

        $payment->amount_paid = $newTotalPaid;
        $payment->is_paid = $payment->amount_paid >= $finalTotal;

        if (isset($data['payment_date'])) {
          $payment->payment_date = $data['payment_date'];
        }
        $payment->save();

        $attendances = Attendance::where('employee_id', $payment->employee_id)
          ->whereRaw('paid_amount < estimated_amount')
          ->orderBy('date', 'asc')
          ->get();

        $amountToDistribute = $additionalAmount;

        foreach ($attendances as $attendance) {
          if ($amountToDistribute <= 0)
            break;

          $remainingOnRecord = $attendance->estimated_amount - $attendance->paid_amount;

          if ($amountToDistribute >= $remainingOnRecord) {
            $amountToDistribute -= $remainingOnRecord;
            $attendance->paid_amount = $attendance->estimated_amount;
          } else {
            $attendance->paid_amount += $amountToDistribute;
            $amountToDistribute = 0;
          }
          $attendance->save();
        }
      } else {
        $payment->update($data);
      }

      return $payment;
    });
  }



  public function updateValue(Payment $payment, array $data)
  {
    $finalTotal = $payment->total_amount;

    if (isset($data['amount_paid'])) {
      $newAmountPaid = $data['amount_paid'];

      if ($newAmountPaid > $finalTotal) {
        throw new \Exception("Error: The total paid amount ({$newAmountPaid}) cannot exceed the required total ({$finalTotal}).");
      }

      $data['amount_paid'] = $newAmountPaid;
    }

    $payment->fill($data);

    if (isset($data['amount_paid'])) {
      $payment->is_paid = $payment->amount_paid >= $finalTotal;
    }

    $payment->save();
    return $payment;
  }

  public function delete(Payment $payment)
  {
    return $payment->delete();
  }

  public function forceDelete(Payment $payment)
  {
    return $payment->forceDelete();
  }

  public function restore(Payment $payment)
  {
    return $payment->restore();
  }
}