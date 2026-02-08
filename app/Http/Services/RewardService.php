<?php


namespace App\Http\Services;
use App\Models\Reward;

class RewardService
{
    public function getAll()
    {
        return Reward::whereNull('deleted_at')
            ->get();
    }

    public function getArchived()
    {
        return Reward::onlyTrashed()
            ->get();
    }


    public function create(array $data)
    {
        return Reward::create($data);
    }

    public function update(Reward $reward, array $data)
    {
        $reward->update($data);
        return $reward;
    }

    public function delete(Reward $reward)
    {
        return $reward->delete();
    }

    public function forceDelete(Reward $reward)
    {
        return $reward->forceDelete();
    }

    public function restore(Reward $reward)
    {
        return $reward->restore();
    }
}
