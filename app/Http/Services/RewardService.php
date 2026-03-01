<?php


namespace App\Http\Services;

use App\Models\Reward;
use App\Models\User;

class RewardService
{

  public function __construct(private NotificationService $notificationService)
  {
  }

  public function getAll()
  {
    return Reward::with('employee', 'admin')
      ->whereNull('deleted_at')
      ->get();
  }

  public function getArchived()
  {
    return Reward::onlyTrashed()
      ->get();
  }


  public function create(array $data)
  {
    $reward = Reward::create($data);
    $user = User::where('userable_id', $data['employee_id'])
      ->where('userable_type', 'Employee')
      ->first();
    if ($user) {
      $this->notificationService->sendToUser(
        $user,
        'مكافأة جديدة 🎁',
        "تهانينا {$user->full_name}، تم صرف مكافأة جديدة لك بقيمة {$reward->amount}",
        [
          'type' => 'reward_received',
          'reward_id' => (string) $reward->id,
          'route' => '/rewards'
        ]
      );
    }

    return $reward;
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

  public function getByEmployeeId($employeeId)
  {
    return Reward::with('admin')
      ->where('employee_id', $employeeId)
      ->get();
  }
}
