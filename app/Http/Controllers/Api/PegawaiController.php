<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pegawai::latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $pegawais = $query->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Data pegawai berhasil diambil.',
            'data' => $pegawais,
        ]);
    }

    public function show(Pegawai $pegawai)
    {
        $pegawai->load('slipGajis');

        return response()->json([
            'success' => true,
            'message' => 'Detail pegawai berhasil diambil.',
            'data' => $pegawai,
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'nip' => ['required', 'string', 'max:50', 'unique:pegawais,nip'],
            'nama' => ['required', 'string', 'max:255'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'golongan' => ['nullable', 'string', 'max:50'],
            'status_pegawai' => ['nullable', 'string', 'max:100'],
        ]);

        $pegawai = Pegawai::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Pegawai berhasil ditambahkan.',
            'data' => $pegawai,
        ], 201);
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'nip' => ['required', 'string', 'max:50', Rule::unique('pegawais', 'nip')->ignore($pegawai->id)],
            'nama' => ['required', 'string', 'max:255'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'golongan' => ['nullable', 'string', 'max:50'],
            'status_pegawai' => ['nullable', 'string', 'max:100'],
        ]);

        $pegawai->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Pegawai berhasil diperbarui.',
            'data' => $pegawai,
        ]);
    }

    public function destroy(Request $request, Pegawai $pegawai)
    {
        $this->ensureAdmin($request);

        $pegawai->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pegawai berhasil dihapus.',
        ]);
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless(
            in_array($request->user()?->role, ['admin', 'super_admin'], true),
            403,
            'Anda tidak memiliki akses.'
        );
    }
}
