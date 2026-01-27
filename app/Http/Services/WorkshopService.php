<?php


namespace App\Http\Services;

use App\Models\Workshop;

class WorkshopService
{
    public function getAll()
    {
        return Workshop::whereNull('deleted_at')
            ->get();
    }

    public function getArchived()
    {
        return Workshop::onlyTrashed()
            ->get();
    }


    public function create(array $data)
    {
        return Workshop::create($data);
    }

    public function update(Workshop $workshop, array $data)
    {
        $workshop->update($data);
        return $workshop;
    }

    public function delete(Workshop $workshop)
    {
        return $workshop->delete();
    }

    public function forceDelete(Workshop $workshop)
    {
        return $workshop->forceDelete();
    }

    public function restore(Workshop $workshop)
    {
        return $workshop->restore();
    }
}