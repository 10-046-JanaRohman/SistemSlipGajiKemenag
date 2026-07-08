<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawais = Pegawai::latest()->paginate(10);

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
}