<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Controller untuk manajemen data pegawai
 * 
 * @package App\Http\Controllers\Admin
 * @author Jana Rohman
 * @version 1.0.0
 * 
 * Features:
 * - CRUD Pegawai
 * - Import dari Excel
 * - Manajemen User terkait
 */
class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $query = \App\Models\Pegawai::query();
        
        if ($search) {
            $query->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
        }
        
        $pegawais = $query->latest()->get();

        return view('admin.pegawai.index', compact('pegawais', 'search'));
    }

    public function create()
    {
        return view('admin.pegawai.create');
    }

    public function store(Request $request)
    {
    $request->validate([
        'nama' => 'required',
        'nip' => 'required|unique:pegawais,nip',
        'email' => 'required|email|unique:users,email',
        'jabatan' => 'required',
        'golongan' => 'required',
        'unit_kerja' => 'required',
        'no_hp' => 'nullable',
    ]);

    $user = \App\Models\User::create([
        'name' => $request->nama,
        'email' => $request->email,
        'password' => bcrypt('password123'),
        'role' => 'pegawai',
    ]);

    \App\Models\Pegawai::create([
        'user_id' => $user->id,
        'nama' => $request->nama,
        'nip' => $request->nip,
        'jabatan' => $request->jabatan,
        'golongan' => $request->golongan,
        'unit_kerja' => $request->unit_kerja,
        'no_hp' => $request->no_hp,
    ]);

    return redirect()
        ->route('pegawai.index')
        ->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function show(Pegawai $pegawai)
    {
        return view('admin.pegawai.show', compact('pegawai'));
    }

    public function edit(Pegawai $pegawai)
    {
        return view('admin.pegawai.edit', compact('pegawai'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $request->validate([
            'nama'        => 'required',
            'jabatan'     => 'required',
            'golongan'    => 'required',
            'unit_kerja'  => 'required',
            'no_hp'       => 'nullable',
        ]);

        $pegawai->update([
            'nama'         => $request->nama,
            'jabatan'      => $request->jabatan,
            'golongan'     => $request->golongan,
            'unit_kerja'   => $request->unit_kerja,
            'no_hp'        => $request->no_hp,
        ]);

        $pegawai->user->update([
            'name' => $request->nama,
        ]);

        return redirect()
            ->route('pegawai.index')
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(Pegawai $pegawai)
    {
        $pegawai->user()->delete();

        return redirect()
            ->route('pegawai.index')
            ->with('success', 'Data pegawai berhasil dihapus.');
    }
}