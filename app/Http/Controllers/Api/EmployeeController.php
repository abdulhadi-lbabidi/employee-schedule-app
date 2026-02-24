<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Employee\CreateEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Services\EmployeeService;
use App\Http\Controllers\Controller;
use App\Models\Employee;

class EmployeeController extends Controller
{
  public function __construct(
    private EmployeeService $employeeService
  ) {
  }
  public function index()
  {
    $employees = $this->employeeService->getAll();
    return EmployeeResource::collection($employees);
  }

  public function archived()
  {
    $employee = $this->employeeService->getArchived();
    return EmployeeResource::collection($employee);
  }

  public function duesReport()
  {
    $reportData = $this->employeeService->getEmployeesDues();

    return response()->json([
      'data' => [
        'employees' => $reportData['employees']
      ],
      'summary' => $reportData['summary'],

    ]);
  }

  public function store(CreateEmployeeRequest $request)
  {
    $employee = $this->employeeService->create($request->validated());
    return new EmployeeResource($employee);
  }

  public function show(Employee $employee)
  {
    return new EmployeeResource($employee->load('user'));
  }


  public function update(UpdateEmployeeRequest $request, Employee $employee)
  {
    $employee = $this->employeeService->update($employee, $request->validated());
    return new EmployeeResource($employee);
  }

  public function destroy(Employee $employee)
  {
    $this->employeeService->delete($employee);
    return response()->json([
      'message' => 'Admin archived successfully'
    ]);
  }

  public function restore($id)
  {
    $employee = Employee::onlyTrashed()->findOrFail($id);
    $this->employeeService->restore($employee);

    return response()->json(['message' => 'Employee restored successfully']);
  }

  public function forceDelete($id)
  {
    $employee = Employee::onlyTrashed()->findOrFail($id);
    $this->employeeService->forceDelete($employee);

    return response()->json(['message' => 'Employee permanently deleted']);
  }

}