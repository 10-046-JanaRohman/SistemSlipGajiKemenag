<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function show(Request $request)
    {
        if (! in_array($request->user()?->role, ['admin', 'super_admin'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke pengaturan.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan berhasil diambil.',
            'data' => $this->settingsData(),
        ]);
    }

    public function update(Request $request)
    {
        if ($request->user()?->role !== 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya super admin yang dapat mengubah pengaturan bendahara.',
            ], 403);
        }

        $validated = $request->validate([
            'pdf_bendahara_nama' => ['nullable', 'string', 'max:255'],
            'pdf_bendahara_nip' => ['nullable', 'string', 'max:100'],
        ]);

        AppSetting::setValue('pdf_bendahara_nama', $validated['pdf_bendahara_nama'] ?? null);
        AppSetting::setValue('pdf_bendahara_nip', $validated['pdf_bendahara_nip'] ?? null);

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan bendahara berhasil disimpan.',
            'data' => $this->settingsData(),
        ]);
    }

    public function updateSignature(Request $request)
    {
        if ($request->user()?->role !== 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya super admin yang dapat mengubah tanda tangan bendahara.',
            ], 403);
        }

        $validated = $request->validate([
            'pdf_bendahara_tanda_tangan' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ], [
            'pdf_bendahara_tanda_tangan.required' => 'Silakan pilih gambar tanda tangan.',
            'pdf_bendahara_tanda_tangan.image' => 'Berkas tanda tangan harus berupa gambar.',
            'pdf_bendahara_tanda_tangan.mimes' => 'Gunakan gambar PNG, JPG, atau JPEG.',
            'pdf_bendahara_tanda_tangan.max' => 'Ukuran gambar tanda tangan maksimal 2 MB.',
        ]);

        $oldPath = AppSetting::getValue('pdf_bendahara_tanda_tangan');
        $path = $validated['pdf_bendahara_tanda_tangan']->store('signatures/bendahara', 'public');

        AppSetting::setValue('pdf_bendahara_tanda_tangan', $path);

        if ($oldPath && str_starts_with($oldPath, 'signatures/bendahara/')) {
            Storage::disk('public')->delete($oldPath);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tanda tangan bendahara berhasil diunggah.',
            'data' => $this->settingsData(),
        ]);
    }

    private function settingsData(): array
    {
        return [
            'pdf_bendahara_nama' => AppSetting::getValue('pdf_bendahara_nama', 'Nama Bendahara'),
            'pdf_bendahara_nip' => AppSetting::getValue('pdf_bendahara_nip', 'NIP Bendahara'),
            'pdf_bendahara_tanda_tangan_tersedia' => (bool) AppSetting::getValue('pdf_bendahara_tanda_tangan'),
        ];
    }
}
