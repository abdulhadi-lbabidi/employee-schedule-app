<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Workshop\CreateWorkshopRequest;
use App\Http\Requests\Workshop\UpdateWorkshopRequest;
use App\Http\Resources\WorkshopResource;
use App\Http\Services\WorkshopService;
use App\Models\Workshop;

class WorkshopController extends Controller
{
    public function __construct
    (
        private WorkshopService $workshopService
    ) {
    }
    public function index()
    {
        return WorkshopResource::collection(
            $this->workshopService->getAll()
        );
    }

    public function store(CreateWorkshopRequest $request)
    {
        $workshop = $this->workshopService->create($request->validated());
        return new WorkshopResource($workshop);
    }

    public function show(Workshop $workshop)
    {
        return new WorkshopResource($workshop);
    }

    public function update(UpdateWorkshopRequest $request, Workshop $workshop)
    {
        $workshop = $this->workshopService->update($workshop, $request->validated());
        return new WorkshopResource($workshop);
    }

    public function destroy(Workshop $workshop)
    {
        $this->workshopService->delete($workshop);
        return response()->json(['message' => 'Workshop deleted successfully']);
    }
}