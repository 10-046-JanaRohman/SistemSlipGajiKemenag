<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kemenag Lampung - Pegawai</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-100">

<div class="flex min-h-screen">

    <aside class="w-64 bg-green-800 text-white flex flex-col">
        <div class="p-6 border-b border-green-700">
            <h1 class="text-2xl font-bold leading-tight">KEMENAG</h1>
            <p class="text-sm text-green-100 mt-1">Provinsi Lampung</p>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="{{ route('pegawai.dashboard') }}"
               class="block px-4 py-3 rounded-xl hover:bg-green-700 transition {{ request()->routeIs('pegawai.dashboard') ? 'bg-green-700' : '' }}">
                Dashboard
            </a>

            <a href="{{ route('pegawai.slip') }}"
                class="block px-4 py-3 rounded-xl hover:bg-green-700 transition
                {{ request()->routeIs('pegawai.slip') ? 'bg-green-700' : '' }}">
                Slip Saya
            </a>

            <a href="{{ route('pegawai.riwayat') }}"
            class="block px-4 py-3 rounded-xl hover:bg-green-700 transition
            {{ request()->routeIs('pegawai.riwayat') ? 'bg-green-700' : '' }}">
                Riwayat Slip
            </a>

            <a href="{{ route('pegawai.profil') }}"
            class="block px-4 py-3 rounded-xl hover:bg-green-700 transition {{ request()->routeIs('pegawai.profil') ? 'bg-green-700' : '' }}">
                Profil
            </a>

            <a href="{{ route('pegawai.ganti-password') }}"
               class="block px-4 py-3 rounded-xl hover:bg-green-700 transition
               {{ request()->routeIs('pegawai.ganti-password') ? 'bg-green-700' : '' }}">
                Ganti Password
            </a>
        </nav>

        <div class="p-4 border-t border-green-700">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-xl transition">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm border-b">
            <div class="px-8 py-5 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">@yield('title', 'Dashboard Pegawai')</h2>
                </div>

                <div class="text-right">
                    <p class="font-semibold text-gray-800">
                        {{ auth()->user()->pegawai->nama ?? auth()->user()->name }}
                    </p>
                    <p class="text-sm text-gray-500">Pegawai</p>
                </div>
            </div>
        </header>

        <main class="p-8">
            @yield('content')
        </main>
    </div>

</div>

@stack('scripts')
</body>
</html>