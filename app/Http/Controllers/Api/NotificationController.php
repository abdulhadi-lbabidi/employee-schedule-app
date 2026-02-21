<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\FcmService;
use App\Models\Employee;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

  public function __construct
  (
    private FcmService $fcmService
  ) {
  }


  // fort admin
  public function send(Request $request)
  {
    $request->validate([
      'title' => 'required|string',
      'body' => 'required|string',
      'user_id' => 'nullable|exists:users,id',
      'workshop_id' => 'nullable|exists:workshops,id',
    ]);

    $tokens = [];

    if ($request->filled('user_id')) {
      $user = User::find($request->user_id);
      if ($user && $user->fcm_token) {
        $tokens = [$user->fcm_token];
      }
    } elseif ($request->filled('workshop_id')) {
      $tokens = User::whereHasMorph('userable', [Employee::class], function ($q) use ($request) {
        $q->whereHas('workshops', function ($w) use ($request) {
          $w->where('workshops.id', $request->workshop_id);
        });
      })->whereNotNull('fcm_token')->pluck('fcm_token')->toArray();
    } else {
      $tokens = User::whereNotNull('fcm_token')->pluck('fcm_token')->toArray();
    }

    if (empty($tokens)) {
      return response()->json(['message' => 'No active devices found for the target'], 404);
    }

    try {
      $this->fcmService->sendPush(
        $tokens,
        $request->title,
        $request->body,
        ['click_action' => 'FLUTTER_NOTIFICATION_CLICK']
      );

      return response()->json(['message' => 'Notification sent successfully']);
    } catch (\Exception $e) {
      return response()->json(['error' => 'Failed to send: ' . $e->getMessage()], 500);
    }
  }



  // for users
  public function userCheckIn(Request $request)
  {
    $request->validate([
      'user_id' => 'required|exists:users,id',
      'workshop_id' => 'required|exists:workshops,id',
    ]);

    $user = User::find($request->user_id);
    $workshop = Workshop::find($request->workshop_id);

    $tokens = $this->getTokensForUserAndAdmins($user);

    return $this->executePush(
      $tokens,
      'تم تسجيل الحضور',
      "تم تسجيل دخولك بنجاح إلى الورشة: {$workshop->name}",
      ['type' => 'check_in', 'workshop_id' => $request->workshop_id]
    );
  }

  public function userCheckOut(Request $request)
  {
    $request->validate([
      'user_id' => 'required|exists:users,id',
      'workshop_id' => 'required|exists:workshops,id',
    ]);

    $user = User::find($request->user_id);
    $workshop = Workshop::find($request->workshop_id);

    $tokens = $this->getTokensForUserAndAdmins($user);

    return $this->executePush(
      $tokens,
      'تم تسجيل الخروج',
      "تم تسجيل خروجك بنجاح من الورشة: {$workshop->name}",
      ['type' => 'check_out', 'workshop_id' => $request->workshop_id]
    );
  }

  private function getTokensForUserAndAdmins($user)
  {
    $tokens = [];

    if ($user->fcm_token) {
      $tokens[] = $user->fcm_token;
    }

    $adminTokens = User::where('userable_type', 'Admin')
      ->whereNotNull('fcm_token')
      ->pluck('fcm_token')
      ->toArray();

    return array_unique(array_merge($tokens, $adminTokens));
  }

  private function executePush($tokens, $title, $body, $data = [])
  {
    if (empty($tokens)) {
      return response()->json(['message' => 'No tokens found'], 404);
    }

    try {
      $this->fcmService->sendPush(
        $tokens,
        $title,
        $body,
        array_merge($data, ['click_action' => 'FLUTTER_NOTIFICATION_CLICK'])
      );
      return response()->json(['message' => 'Notification sent to user and admin']);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 500);
    }
  }

}