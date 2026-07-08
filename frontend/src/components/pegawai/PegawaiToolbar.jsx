import { Search } from "lucide-react";

function PegawaiToolbar() {
  return (
    <div className="bg-white rounded-2xl shadow p-6">

      <div className="flex justify-between items-center">

        <div className="relative w-[420px]">

          <Search
            className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"
            size={20}
          />

          <input
            type="text"
            placeholder="Cari Pegawai..."
            className="w-full border rounded-xl h-14 pl-12 pr-4 outline-none focus:ring-2 focus:ring-green-700"
          />

        </div>

        <select className="border rounded-xl h-14 px-5 outline-none">

          <option>Semua Jabatan</option>

          <option>Analis Keuangan</option>

          <option>Staff Keuangan</option>

          <option>Kasubbag TU</option>

          <option>Bendahara</option>

        </select>

      </div>

    </div>
  );
}

export default PegawaiToolbar;