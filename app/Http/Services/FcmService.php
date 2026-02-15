<?php

namespace App\Http\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmService
{
  protected $messaging;

  public function __construct()
  {
    $credentialsPath = config('services.firebase.credentials');

    $factory = (new Factory)->withServiceAccount($credentialsPath);
    $this->messaging = $factory->createMessaging();
  }

  public function sendPush($tokens, $title, $body, $data = [])
  {
    $notification = Notification::create($title, $body);

    $message = CloudMessage::new()
      ->withNotification($notification)
      ->withData($data);

    $report = $this->messaging->sendMulticast($message, $tokens);

    return [
      'success_count' => $report->successes()->count(),
      'failure_count' => $report->failures()->count(),
      'errors' => array_map(fn($f) => $f->error()->getMessage(), $report->failures()->getItems()),
    ];
  }
}