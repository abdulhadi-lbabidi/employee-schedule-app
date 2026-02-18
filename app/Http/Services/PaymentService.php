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
        'total_regular_hours' => $weekRecords->sum('regular_hours'),
        'total_overtime_hours' => $weekRecords->sum('overtime_hours'),
        'estimated_amount' => $remainingAmount,
        'days_count' => $weekRecords->count(),
        'ids' => $weekRecords->pluck('id'),
      ];
    })->values();
  }


  public function paySelectedRecords(Request $request)
  {
    return DB::transaction(function () use ($request) {
      $attendances = Attendance::whereIn('id', $request->attendance_ids)
        ->whereRaw('paid_amount < estimated_amount')
        ->orderBy('date', 'asc')
        ->get();

      if ($attendances->isEmpty()) {
        return response()->json(['message' => 'There are no records to pay'], 400);
      }


      $payment = Payment::create([
        'employee_id' => $request->employee_id,
        'admin_id' => auth()->id(),
        'total_amount' => $request->total_amount,
        'amount_paid' => $request->amount_paid,
        'payment_date' => $request->payment_date,
        'is_paid' => true
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
    $finalTotal = $data['total_amount'] ?? $payment->total_amount;

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