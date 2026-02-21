<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
  public function __construct(
    private DashboardService $dashboardService
  ) {
  }


  public function index()
  {
    $stats = $this->dashboardService->statistics();
    return response()->json($stats);
  }
}
