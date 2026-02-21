<?php

namespace App\Http\Services;

use App\Models\Loan;

class LoanService
{
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

    return Loan::create([
      'employee_id' => auth()->user()->userable_id,
      'amount' => $data['amount'],
      'paid_amount' => 0,
      'status' => 'waiting',
      'date' => $data['date'],
    ]);
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
  }

  public function reject(Loan $loan)
  {
    if ($loan->status === 'completed') {
      throw new \Exception("Cannot reject a completed loan.");
    }

    return $loan->forceDelete();
  }

  public function pay(Loan $loan, $amount)
  {
    if ($loan->status === 'rejected') {
      throw new \Exception("Cannot pay a rejected loan.");
    }
    if ($loan->status === 'completed') {
      throw new \Exception("Cannot pay a completed loan.");
    }

    $loan->paid_amount += $amount;

    if ($loan->paid_amount >= $loan->amount) {
      $loan->status = 'completed';
    } else {
      $loan->status = 'partially';
    }

    $loan->save();
  }

}