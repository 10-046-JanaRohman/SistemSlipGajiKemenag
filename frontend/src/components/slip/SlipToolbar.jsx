import { useNavigate } from "react-router-dom";
import { Search, Upload } from "lucide-react";

const bulanList = [
  "Januari", "Februari", "Maret", "April", "Mei", "Juni",
  "Juli", "Agustus", "September", "Oktober", "November", "Desember"
];

const tahunList = [2026, 2025, 2024];

function SlipToolbar({ search, onSearch, bulan, tahun, onFilter }) {
  const navigate = useNavigate();

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
            value={search}
            onChange={(e) => onSearch(e.target.value)}
            placeholder="Cari Pegawai..."
            className="w-full border rounded-xl pl-12 pr-4 py-3 outline-none focus:ring-2 focus:ring-green-600"
          />

        </div>

        {/* Filter */}
        <div className="flex gap-4">

          <select
            value={bulan}
            onChange={(e) => onFilter(e.target.value, tahun)}
            className="border rounded-xl px-4 py-3"
          >
            <option value="">Semua Bulan</option>
            {bulanList.map((b, i) => (
              <option key={i} value={i + 1}>{b}</option>
            ))}
          </select>

          <select
            value={tahun}
            onChange={(e) => onFilter(bulan, e.target.value)}
            className="border rounded-xl px-4 py-3"
          >
            <option value="">Semua Tahun</option>
            {tahunList.map((t) => (
              <option key={t} value={t}>{t}</option>
            ))}
          </select>

          <button
            onClick={() => navigate("/admin/import-excel")}
            className="bg-green-700 hover:bg-green-800 text-white px-6 rounded-xl flex items-center gap-2"
          >
            <Upload size={18} />
            Upload Slip
          </button>

        </div>

      </div>

    </div>
  );
}

export default SlipToolbar;
