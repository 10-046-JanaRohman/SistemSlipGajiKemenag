import { Search } from "lucide-react";

function RiwayatToolbar() {
  return (
    <div className="bg-white rounded-2xl shadow p-6">

      <div className="grid grid-cols-4 gap-4">

        {/* Search */}
        <div className="relative col-span-2">

          <Search
            size={20}
            className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"
          />

          <input
            type="text"
            placeholder="Cari Nama atau NIP..."
            className="w-full h-14 border rounded-xl pl-12 pr-4 outline-none focus:ring-2 focus:ring-green-700"
          />

        </div>

        {/* Bulan */}
        <select className="h-14 border rounded-xl px-4 outline-none">

          <option>Semua Bulan</option>

          <option>Januari</option>

          <option>Februari</option>

          <option>Maret</option>

          <option>April</option>

          <option>Mei</option>

          <option>Juni</option>

          <option>Juli</option>

          <option>Agustus</option>

          <option>September</option>

          <option>Oktober</option>

          <option>November</option>

          <option>Desember</option>

        </select>

        {/* Tahun */}
        <select className="h-14 border rounded-xl px-4 outline-none">

          <option>2026</option>

          <option>2025</option>

          <option>2024</option>

        </select>

      </div>

    </div>
  );
}

export default RiwayatToolbar;