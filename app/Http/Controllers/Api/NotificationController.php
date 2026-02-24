<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\FcmService;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\Workshop;
use App\Models\User;

class NotificationController extends Controller
{

  public function __construct
  (
    private FcmService $fcmService
  ) {
  }


  public function getUnreadNotifications()
  {
    $notifications = Notification::where('user_id', auth()->id())
      ->whereNull('read_at')
      ->latest()
      ->get();

    return response()->json([
      'data' => $notifications
    ]);
  }
  public function markAsRead($id)
  {
    $notification = Notification::where('user_id', auth()->id())->findOrFail($id);

    $notification->update(['read_at' => now()]);

    return response()->json([
      'message' => 'Notification marked as read'
    ]);
  }

  public function markAllAsRead()
  {
    Notification::where('user_id', auth()->id())
      ->whereNull('read_at')
      ->update(['read_at' => now()]);

    return response()->json([
      'message' => 'All notifications marked as read'
    ]);
  }


  // fort admin
  public function send(Request $request)
  {
    $request->validate([
      'title' => 'required|string',
      'body' => 'required|string',
      'user_id' => 'nullable|exists:users,id',
      'workshop_id' => 'nullable|exists:workshops,id',

      'route' => 'nullable|string',
    ]);

    $tokens = [];

    $userIds = [];

    if ($request->filled('user_id')) {
      $user = User::find($request->user_id);
      if ($user && $user->fcm_token) {
        $tokens = [$user->fcm_token];

        $userIds = [$user->id];
      }
    } elseif ($request->filled('workshop_id')) {
      $users = User::whereHasMorph('userable', [Employee::class], function ($q) use ($request) {
        $q->whereHas('workshops', function ($w) use ($request) {
          $w->where('workshops.id', $request->workshop_id);
        });
      })->whereNotNull('fcm_token')
        ->get(['id', 'fcm_token']);

      $tokens = $users->pluck('fcm_token')->toArray();
      $userIds = $users->pluck('id')->toArray();
    } else {
      $users = User::whereNotNull('fcm_token')->get(['id', 'fcm_token']);
      $tokens = $users->pluck('fcm_token')->toArray();
      $userIds = $users->pluck('id')->toArray();
    }

    if (empty($tokens)) {
      return response()->json(['message' => 'No active devices found for the target'], 404);
    }

    try {
      return $this->executePush(
        $tokens,
        $request->title,
        $request->body,
        ['route' => $request->route ?? '/notifications', 'type' => 'admin_broadcast'],
        $userIds
      );
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

    $target = $this->getTargetData($user);

    return $this->executePush(
      $target['tokens'],
      'تم تسجيل الحضور',
      "تم تسجيل دخولك بنجاح إلى الورشة: {$workshop->name}",
      ['route' => '/', 'workshopId' => $request->workshop_id],
      $target['ids']
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

    $target = $this->getTargetData($user);

    return $this->executePush(
      $target['tokens'],
      'تم تسجيل الخروج',
      "تم تسجيل خروجك بنجاح إلى الورشة: {$workshop->name}",
      ['route' => '/', 'workshopId' => $request->workshop_id],
      $target['ids']
    );

  }



  private function getTargetData($user)
  {
    $ids = [$user->id];
    $tokens = $user->fcm_token ? [$user->fcm_token] : [];

    $admins = User::where('userable_type', 'Admin')->whereNotNull('fcm_token')->get(['id', 'fcm_token']);

    $ids = array_unique(array_merge($ids, $admins->pluck('id')->toArray()));
    $tokens = array_unique(array_merge($tokens, $admins->pluck('fcm_token')->toArray()));

    return ['ids' => $ids, 'tokens' => $tokens];
  }

  private function executePush($tokens, $title, $body, $data = [], $userIds = [])
  {
    if (empty($tokens)) {
      return response()->json(['message' => 'No tokens found'], 404);
    }

    try {
      $this->fcmService->sendPush(
        $tokens,
        $title,
        $body,
        $data
      );

      foreach ($userIds as $id) {
        Notification::create([
          'user_id' => $id,
          'title' => $title,
          'body' => $body,
          'data' => $data,
        ]);
      }

      return response()->json(['message' => 'Notification sent and archived']);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 500);
    }
  }

}