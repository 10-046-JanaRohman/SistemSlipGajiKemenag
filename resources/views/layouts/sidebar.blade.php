<aside class="w-72 bg-green-900 text-white flex flex-col">

    <div class="p-6 border-b border-green-800">
        <h2 class="font-bold text-xl">
            KEMENAG
        </h2>
        <p class="text-sm text-green-200">
            Provinsi Lampung
        </p>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-1">

        <a href="{{ route('admin.dashboard') }}"
           class="block py-3 px-4 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('admin.dashboard') ? 'bg-green-800' : '' }}">
            Dashboard
        </a>

        <a href="{{ route('pegawai.index') }}"
           class="block py-3 px-4 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('pegawai.*') ? 'bg-green-800' : '' }}">
            Pegawai
        </a>

        <a href="{{ route('gaji-imports.create') }}"
           class="block py-3 px-4 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('gaji-imports.*') ? 'bg-green-800' : '' }}">
            Import Excel
        </a>

        <a href="{{ route('slip-gaji.index') }}"
           class="block py-3 px-4 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('slip-gaji.*') ? 'bg-green-800' : '' }}">
            Slip Gaji
        </a>

        <a href="{{ route('admin.riwayat-slip') }}"
           class="block py-3 px-4 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('admin.riwayat-slip') ? 'bg-green-800' : '' }}">
            Riwayat Slip
        </a>

    </nav>

    <div class="p-4 border-t border-green-800">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full py-2 rounded-lg bg-red-600 hover:bg-red-700">
                Logout
            </button>
        </form>
    </div>

</aside>