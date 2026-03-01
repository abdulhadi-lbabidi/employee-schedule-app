<?php

namespace App\Http\Services;

use App\Models\Employee;
use App\Models\Loan;
use App\Models\User;

class LoanService
{

  public function __construct(private NotificationService $notificationService)
  {
  }

  public function getAll()
  {
    $user = auth()->user();

    if ($user->userable_type === 'Employee') {
      return Loan::where('employee_id', $user->userable_id)
        ->where('status', '!=', 'rejected')
        ->with(['employee.user'])
        ->get();
    }

    return Loan::with(['employee.user', 'admin'])->get();
  }

  public function getArchived()
  {
    return Loan::onlyTrashed()->get();
  }

  public function create(array $data)
  {
    $loan = Loan::create([
      'employee_id' => auth()->user()->userable_id,
      'amount' => $data['amount'],
      'paid_amount' => 0,
      'status' => 'waiting',
      'date' => $data['date'],
    ]);

    $admins = User::where('userable_type', 'Admin')->get();
    $employeeName = auth()->user()->full_name;

    foreach ($admins as $admin) {
      $this->notificationService->sendToUser(
        $admin,
        'طلب سلفة جديد 💳',
        "قام الموظف {$employeeName} بطلب سلفة بقيمة {$loan->amount}",
        ['type' => 'loan_request', 'loan_id' => (string) $loan->id, 'route' => '/admin/loans']
      );
    }

    return $loan;
  }

  public function update(Loan $loan, array $data)
  {
    $loan->update($data);
    return $loan;
  }

  public function delete(Loan $loan)
  {
    return $loan->delete();
  }

  public function forceDelete(Loan $loan)
  {
    return $loan->forceDelete();
  }

  public function restore(Loan $loan)
  {
    return $loan->restore();
  }

  public function approve(Loan $loan)
  {
    if ($loan->status === 'completed') {
      throw new \Exception("Cannot approve a completed loan.");
    }

    $loan->update([
      'status' => 'approved',
      'admin_id' => auth()->user()->userable_id,
    ]);
    $this->notifyEmployee($loan, 'تمت الموافقة على السلفة ✅', "تمت الموافقة على طلب السلفة الخاص بك بقيمة {$loan->amount}");
  }

  public function reject(Loan $loan)
  {
    if ($loan->status === 'completed') {
      throw new \Exception("Cannot reject a completed loan.");
    }
    $this->notifyEmployee($loan, 'تم رفض طلب السلفة ❌', "نعتذر، تم رفض طلب السلفة الخاص بك بقيمة {$loan->amount}");
    return $loan->forceDelete();
  }

  public function pay(Loan $loan, $amount)
  {
    if ($loan->status === 'waiting') {
      throw new \Exception("Cannot pay a waiting loan.");
    }
    if ($loan->status === 'rejected') {
      throw new \Exception("Cannot pay a rejected loan.");
    }
    if ($loan->status === 'completed') {
      throw new \Exception("Cannot pay a completed loan.");
    }


    $remainingToPay = $loan->amount - $loan->paid_amount;

    if ($amount > $remainingToPay) {
      throw new \Exception("The paid amount ($amount) exceeds the remaining loan balance ($remainingToPay).");
    }

    $loan->paid_amount += $amount;

    if ($loan->paid_amount >= $loan->amount) {
      $loan->status = 'completed';
    } else {
      $loan->status = 'partially';
    }

    $loan->save();

    $message = "تم تسجيل دفع مبلغ {$amount}. المتبقي عليك: " . ($loan->amount - $loan->paid_amount);
    $this->notifyEmployee($loan, 'تحديث دفع السلفة 💰', $message);
  }


  private function notifyEmployee(Loan $loan, string $title, string $body)
  {
    $user = User::where('userable_id', $loan->employee_id)
      ->where('userable_type', 'Employee')
      ->first();

    if ($user) {
      $this->notificationService->sendToUser($user, $title, $body, [
        'type' => 'loan_update',
        'loan_id' => (string) $loan->id,
        'status' => $loan->status,
        'route' => '/loans'
      ]);
    }

  }
}
