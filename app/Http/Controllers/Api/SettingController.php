<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

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
            'data' => [
                'pdf_bendahara_nama' => AppSetting::getValue('pdf_bendahara_nama', 'Nama Bendahara'),
                'pdf_bendahara_nip' => AppSetting::getValue('pdf_bendahara_nip', 'NIP Bendahara'),
            ],
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
            'data' => [
                'pdf_bendahara_nama' => AppSetting::getValue('pdf_bendahara_nama', 'Nama Bendahara'),
                'pdf_bendahara_nip' => AppSetting::getValue('pdf_bendahara_nip', 'NIP Bendahara'),
            ],
        ]);
    }
}
