<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    public function index()
    {
        $pegawai = Auth::user()->pegawai;

        return view(
            'pegawai.profil.index',
            compact('pegawai')
        );
    }
}