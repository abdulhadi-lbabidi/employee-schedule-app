<?php

namespace App\Http\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
  public function __construct(
    private FcmService $fcmService
  ) {
  }

  public function sendToUser(User $user, string $title, string $body, array $data = [])
  {
    if (!$user->fcm_token) {
      return;
    }

    $this->fcmService->sendPush(
      [$user->fcm_token],
      $title,
      $body,
      $data
    );

    Notification::create([
      'user_id' => $user->id,
      'title' => $title,
      'body' => $body,
      'data' => $data,
    ]);
  }
}
