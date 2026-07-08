import { Search, Upload } from "lucide-react";

function SlipToolbar() {
  return (
    <div className="bg-white rounded-2xl shadow-md p-6">

      <div className="flex justify-between items-center">

        {/* Search */}
        <div className="relative w-96">

          <Search
            size={20}
            className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"
          />

          <input
            type="text"
            placeholder="Cari Pegawai..."
            className="w-full border rounded-xl pl-12 pr-4 py-3 outline-none focus:ring-2 focus:ring-green-600"
          />

        </div>

        {/* Filter */}
        <div className="flex gap-4">

          <select className="border rounded-xl px-4 py-3">

            <option>Januari</option>
            <option>Februari</option>
            <option>Maret</option>
            <option>April</option>
            <option>Mei</option>
            <option>Juni</option>
            <option>Juli</option>

          </select>

          <select className="border rounded-xl px-4 py-3">

            <option>2026</option>
            <option>2025</option>

          </select>

          <button className="bg-green-700 hover:bg-green-800 text-white px-6 rounded-xl flex items-center gap-2">

            <Upload size={18} />

            Upload Slip

          </button>

        </div>

      </div>

    </div>
  );
}

export default SlipToolbar;