<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        $items = $request->user()->notifikasis()->latest()->limit(20)->get();

        return response()->json([
            'success' => true,
            'data' => $items,
            'unread_count' => $items->where('dibaca', false)->count(),
        ]);
    }

    public function markAsRead(Request $request, Notifikasi $notifikasi)
    {
        abort_unless($notifikasi->user_id === $request->user()->id, 404);
        $notifikasi->update(['dibaca' => true]);

        return response()->json(['success' => true, 'data' => $notifikasi->fresh()]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->notifikasis()->where('dibaca', false)->update(['dibaca' => true]);

        return response()->json(['success' => true]);
    }
}
