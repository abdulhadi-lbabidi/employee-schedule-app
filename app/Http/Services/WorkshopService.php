<?php


namespace App\Http\Services;

use App\Models\Workshop;

class WorkshopService
{
    public function getAll()
    {
        return Workshop::all();
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
}