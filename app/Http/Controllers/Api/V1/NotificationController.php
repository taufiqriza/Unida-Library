<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\MemberNotification;
use Illuminate\Http\Request;

class NotificationController extends BaseController
{
    public function index(Request $request)
    {
        $query = MemberNotification::where('member_id', $request->user()->id);

        if ($request->boolean('unread_only')) {
            $query->whereNull('read_at');
        }

        $notifications = $query->orderByDesc('created_at')->paginate($request->per_page ?? 20);

        return $this->paginated($notifications->through(fn($n) => [
            'id' => $n->id,
            'type' => $n->type,
            'title' => $n->title,
            'body' => $n->body,
            'data' => $n->data,
            'read_at' => $n->read_at?->toIso8601String(),
            'created_at' => $n->created_at?->toIso8601String(),
        ]));
    }

    public function unreadCount(Request $request)
    {
        $count = MemberNotification::where('member_id', $request->user()->id)
            ->whereNull('read_at')
            ->count();

        return $this->success(['count' => $count]);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = MemberNotification::where('member_id', $request->user()->id)->find($id);

        if (!$notification) {
            return $this->error('Notifikasi tidak ditemukan', 404);
        }

        $notification->update(['read_at' => now()]);

        return $this->success(null, 'Notifikasi ditandai sudah dibaca');
    }

    public function markAllAsRead(Request $request)
    {
        MemberNotification::where('member_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $this->success(null, 'Semua notifikasi ditandai sudah dibaca');
    }
}
