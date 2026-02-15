<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Http\Services\AdminService;
use App\Http\Resources\AdminResource;
use App\Models\Admin;

class AdminController extends Controller
{
  public function __construct(
    private AdminService $adminService
  ) {
  }
  public function index()
  {
    $admins = $this->adminService->getAll();
    return AdminResource::collection($admins);
  }

  public function archived()
  {
    $admin = $this->adminService->getArchived();
    return AdminResource::collection($admin);
  }
  public function store(CreateAdminRequest $request)
  {
    $admin = $this->adminService->create($request->validated());
    return new AdminResource($admin);
  }

  public function show(Admin $admin)
  {
    return new AdminResource($admin->load('users'));
  }

  public function update(UpdateAdminRequest $request, Admin $admin)
  {
    $admin = $this->adminService->update($admin, $request->validated());
    return new AdminResource($admin);
  }

  public function destroy(Admin $admin)
  {
    $admin = $this->adminService->delete($admin);
    return response()->json([
      'message' => 'Admin archived successfully'
    ]);

  }


  public function restore($id)
  {
    $admins = Admin::onlyTrashed()->findOrFail($id);
    $this->adminService->restore($admins);

    return response()->json(['message' => 'Admin restored successfully']);
  }

  public function forceDelete($id)
  {
    $admin = Admin::onlyTrashed()->findOrFail($id);
    $this->adminService->forceDelete($admin);

    return response()->json(['message' => 'Admin permanently deleted']);
  }

}
