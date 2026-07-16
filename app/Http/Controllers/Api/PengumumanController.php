<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isManager = in_array($user?->role, ['admin', 'super_admin'], true);

        $query = Pengumuman::query()
            ->with('creator:id,name')
            ->latest('published_at')
            ->latest('id');

        if (! $isManager) {
            $query->whereNotNull('published_at')
                ->where('published_at', '<=', now());
        }

        return response()->json([
            'success' => true,
            'data' => $query->paginate(min($request->integer('per_page', 10), 100)),
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureManager($request);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string', 'max:2000'],
            'published_at' => ['nullable', 'date'],
        ]);

        $pengumuman = Pengumuman::create([
            ...$validated,
            'published_at' => $validated['published_at'] ?? now(),
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil diterbitkan.',
            'data' => $pengumuman->load('creator:id,name'),
        ], 201);
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $this->ensureManager($request);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string', 'max:2000'],
            'published_at' => ['nullable', 'date'],
        ]);

        $pengumuman->update([
            ...$validated,
            'published_at' => $validated['published_at'] ?? now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil diperbarui.',
            'data' => $pengumuman->fresh()->load('creator:id,name'),
        ]);
    }

    public function destroy(Request $request, Pengumuman $pengumuman)
    {
        $this->ensureManager($request);
        $pengumuman->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil dihapus.',
        ]);
    }

    private function ensureManager(Request $request): void
    {
        abort_unless(
            in_array($request->user()?->role, ['admin', 'super_admin'], true),
            403,
            'Anda tidak memiliki akses untuk mengelola pengumuman.'
        );
    }
}
