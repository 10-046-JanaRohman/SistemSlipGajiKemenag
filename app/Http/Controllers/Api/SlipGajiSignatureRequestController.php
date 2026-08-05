<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\SlipGaji;
use App\Models\SlipGajiSignatureRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SlipGajiSignatureRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = SlipGajiSignatureRequest::with(['slipGaji.pegawai', 'user', 'reviewer'])
            ->latest();

        if (!$this->isReviewer($user)) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json([
            'success' => true,
            'data' => $query->paginate(10),
        ]);
    }

    public function store(Request $request, SlipGaji $slipGaji)
    {
        $user = $request->user();
        $this->authorizePegawaiSlip($user, $slipGaji);

        $validated = $request->validate([
            'request_message' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'request_message.required' => 'Alasan penggunaan TTD wajib diisi.',
            'request_message.min' => 'Alasan penggunaan TTD minimal 10 karakter.',
        ]);

        $existing = SlipGajiSignatureRequest::where('slip_gaji_id', $slipGaji->id)
            ->where('user_id', $user->id)
            ->whereIn('status', [
                SlipGajiSignatureRequest::STATUS_PENDING,
                SlipGajiSignatureRequest::STATUS_APPROVED,
            ])
            ->latest()
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => $existing->status === SlipGajiSignatureRequest::STATUS_APPROVED
                    ? 'Pengajuan TTD slip ini sudah disetujui.'
                    : 'Pengajuan TTD slip ini masih menunggu persetujuan.',
                'data' => $existing,
            ], 409);
        }

        $signatureRequest = SlipGajiSignatureRequest::create([
            'slip_gaji_id' => $slipGaji->id,
            'user_id' => $user->id,
            'status' => SlipGajiSignatureRequest::STATUS_PENDING,
            'request_message' => $validated['request_message'],
        ]);

        $slipGaji->load('pegawai');
        $pegawaiName = $slipGaji->pegawai->nama ?? $user->name;
        $periode = trim($slipGaji->bulan.' '.$slipGaji->tahun);

        User::whereIn('role', ['admin', 'super_admin'])->get()->each(function (User $admin) use ($pegawaiName, $periode) {
            Notifikasi::create([
                'user_id' => $admin->id,
                'judul' => 'Pengajuan TTD Slip Gaji',
                'isi' => "{$pegawaiName} mengajukan TTD untuk slip gaji periode {$periode}.",
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan TTD berhasil dikirim.',
            'data' => $signatureRequest->load(['slipGaji.pegawai', 'user']),
        ], 201);
    }

    public function review(Request $request, SlipGajiSignatureRequest $signatureRequest)
    {
        abort_unless($this->isReviewer($request->user()), 403, 'Anda tidak memiliki akses untuk meninjau pengajuan TTD.');
        abort_unless($signatureRequest->status === SlipGajiSignatureRequest::STATUS_PENDING, 422, 'Pengajuan TTD ini sudah ditinjau.');

        $validated = $request->validate([
            'status' => ['required', Rule::in([
                SlipGajiSignatureRequest::STATUS_APPROVED,
                SlipGajiSignatureRequest::STATUS_REJECTED,
            ])],
            'admin_response' => ['nullable', 'string', 'max:1000', 'required_if:status,'.SlipGajiSignatureRequest::STATUS_REJECTED],
        ], [
            'admin_response.required_if' => 'Balasan wajib diisi saat menolak pengajuan.',
        ]);

        $signatureRequest->update([
            'status' => $validated['status'],
            'admin_response' => $validated['admin_response'] ?? null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $signatureRequest->load(['slipGaji.pegawai', 'user']);
        $statusText = $signatureRequest->status === SlipGajiSignatureRequest::STATUS_APPROVED
            ? 'disetujui'
            : 'ditolak';
        $periode = trim($signatureRequest->slipGaji->bulan.' '.$signatureRequest->slipGaji->tahun);

        Notifikasi::create([
            'user_id' => $signatureRequest->user_id,
            'judul' => 'Pengajuan TTD Slip Gaji '.$statusText,
            'isi' => trim("Pengajuan TTD slip gaji periode {$periode} {$statusText}. ".($signatureRequest->admin_response ?? '')),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan TTD berhasil diperbarui.',
            'data' => $signatureRequest->fresh(['slipGaji.pegawai', 'user', 'reviewer']),
        ]);
    }

    private function authorizePegawaiSlip(User $user, SlipGaji $slipGaji): void
    {
        $pegawai = $user->pegawai;

        abort_unless($pegawai && $slipGaji->pegawai_id === $pegawai->id, 403, 'Anda tidak memiliki akses ke slip ini.');
    }

    private function isReviewer(User $user): bool
    {
        return in_array($user->role, ['admin', 'super_admin'], true);
    }
}
