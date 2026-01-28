<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reward\CreateRewardRequest;
use App\Http\Requests\Reward\UpdateRewardRequest;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\RewardResource;
use App\Http\Services\RewardService;
use App\Models\Reward;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    public function __construct(
        private RewardService $rewardService
    ) {
    }

    public function index()
    {
        $reward = $this->rewardService->getAll();
        return RewardResource::collection($reward);
    }

    public function archived()
    {
        $reward = $this->rewardService->getArchived();
        return PaymentResource::collection($reward);
    }
    public function store(CreateRewardRequest $request)
    {
        $reward = $this->rewardService->create($request->validated());
        return new PaymentResource($reward);
    }

    public function show(Reward $reward)
    {
        return new PaymentResource($reward->load('employee'));
    }


    public function update(UpdateRewardRequest $request, Reward $reward)
    {
        $reward = $this->rewardService->update($reward, $request->validated());
        return new PaymentResource($reward);
    }

    public function destroy(Reward $reward)
    {
        $this->rewardService->delete($reward);
        return response()->json([
            'message' => 'Reward archived successfully'
        ]);
    }

    public function restore($id)
    {
        $reward = Reward::onlyTrashed()->findOrFail($id);
        $this->rewardService->restore($reward);

        return response()->json(['message' => 'Reward restored successfully']);
    }

    public function forceDelete($id)
    {
        $reward = Reward::onlyTrashed()->findOrFail($id);
        $this->rewardService->forceDelete($reward);

        return response()->json(['message' => 'Reward permanently deleted']);
    }
}