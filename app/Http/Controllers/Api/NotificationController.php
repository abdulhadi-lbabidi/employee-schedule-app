<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\FcmService;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

  public function __construct
  (
    private FcmService $fcmService
  ) {
  }

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
      if ($user->fcm_token) {
        $tokens = [$user->fcm_token];
      }
    } elseif ($request->filled('workshop_id')) {
      $tokens = User::whereHasMorph('userable', [Employee::class], function ($q) use ($request) {
        $q->whereHas('workshops', function ($w) use ($request) {
          $w->where('workshops.id', $request->workshop_id);
        });
      })->whereNotNull('fcm_token')->pluck('fcm_token')->toArray();
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
}