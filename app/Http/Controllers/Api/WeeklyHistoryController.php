<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WeeklyHistory\CreateWeeklyHistoryRequest;
use App\Http\Resources\WeeklyHistoryResource;
use App\Http\Services\WeeklyHistoryService;
use App\Models\WeeklyHistory;

class WeeklyHistoryController extends Controller
{
  public function __construct(private WeeklyHistoryService $historyService)
  {
  }

  public function index()
  {
    $histories = $this->historyService->getAll();
    return WeeklyHistoryResource::collection($histories);
  }

  public function store(CreateWeeklyHistoryRequest $request)
  {
    $history = $this->historyService->create($request->validated());
    return new WeeklyHistoryResource($history);
  }

  public function togglePayment(WeeklyHistory $weeklyHistory)
  {
    $updated = $this->historyService->togglePaymentStatus($weeklyHistory);
    return new WeeklyHistoryResource($updated);
  }
}
