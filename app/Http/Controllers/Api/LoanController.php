<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Loan\CreateLoanRequest;
use App\Http\Requests\Loan\UpdateLoanRequest;
use App\Http\Resources\LoanResource;
use App\Http\Services\LoanService;
use App\Models\Loan;

class LoanController extends Controller
{
  public function __construct(
    private LoanService $loanService
  ) {
  }
  public function index()
  {
    $loans = $this->loanService->getAll();
    return LoanResource::collection($loans);
  }

  public function archived()
  {
    $loans = $this->loanService->getArchived();
    return LoanResource::collection($loans);
  }
  public function store(CreateLoanRequest $request)
  {
    $employee = $this->loanService->create($request->validated());
    return new LoanResource($employee);
  }

  public function show(Loan $loan)
  {
    return new LoanResource($loan->load('employee'));
  }


  public function update(UpdateLoanRequest $request, Loan $loan)
  {
    $employee = $this->loanService->update($loan, $request->validated());
    return new LoanResource($employee);
  }

  public function destroy(Loan $loan)
  {
    $this->loanService->delete($loan);
    return response()->json([
      'message' => 'Loans archived successfully'
    ]);
  }

  public function restore($id)
  {
    $employee = Loan::onlyTrashed()->findOrFail($id);
    $this->loanService->restore($employee);

    return response()->json(['message' => 'Employee restored successfully']);
  }

  public function forceDelete($id)
  {
    $employee = Loan::onlyTrashed()->findOrFail($id);
    $this->loanService->forceDelete($employee);

    return response()->json(['message' => 'Employee permanently deleted']);
  }


  public function approve(Loan $loan)
  {
    $this->loanService->approve($loan);
    return response()->json(['message' => 'Loan approved']);
  }

  public function reject(Loan $loan)
  {
    $this->loanService->reject($loan);
    return response()->json(['message' => 'Loan rejected']);
  }

  public function pay(Loan $loan)
  {
    $this->loanService->pay($loan, request('amount'));
    return response()->json(['message' => 'Payment recorded']);
  }
}