<?php


namespace App\Http\Services;

use App\Models\Attendance;
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

    return $records->groupBy(function ($item) {
      $date = Carbon::parse($item->date);
      $start = $date->startOfWeek(Carbon::SATURDAY)->format('Y-m-d');
      $end = $date->endOfWeek(Carbon::FRIDAY)->format('Y-m-d');
      return $start . " إلى " . $end;
    })->map(function ($weekRecords, $range) {
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
      ];
    })->values();
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


      $actualTotalRequired = $attendances->sum(function ($item) {
        return $item->estimated_amount - $item->paid_amount;
      });

      if (round($request->amount_paid, 2) > round($actualTotalRequired, 2)) {
        return response()->json([
          'message' => "The paid amount ({$request->amount_paid}) exceeds the total required amount ({$actualTotalRequired}) for the selected records."
        ], 422);
      }


      $payment = Payment::create([
        'employee_id' => $request->employee_id,
        'admin_id' => auth()->id(),
        'total_amount' => $actualTotalRequired,
        'amount_paid' => $request->amount_paid,
        'payment_date' => $request->payment_date,
        'is_paid' => round($request->amount_paid, 2) >= round($actualTotalRequired, 2),
      ]);

      $amountToDistribute = $request->amount_paid;

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

      return response()->json(['message' => 'Payment paid successfully'], 200);
    });
  }



  public function update(Payment $payment, array $data)
  {
    $finalTotal = $payment->total_amount;

    if (isset($data['amount_paid'])) {
      $newAmountPaid = $payment->amount_paid + $data['amount_paid'];

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
