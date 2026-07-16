import { Search } from "lucide-react";

const months = [
  { value: "1", label: "Januari" },
  { value: "2", label: "Februari" },
  { value: "3", label: "Maret" },
  { value: "4", label: "April" },
  { value: "5", label: "Mei" },
  { value: "6", label: "Juni" },
  { value: "7", label: "Juli" },
  { value: "8", label: "Agustus" },
  { value: "9", label: "September" },
  { value: "10", label: "Oktober" },
  { value: "11", label: "November" },
  { value: "12", label: "Desember" },
];

function RiwayatToolbar({ search, bulan, tahun, onSearch, onBulanChange, onTahunChange }) {
  return (
    <div className="bg-white rounded-2xl shadow p-6">

      <div className="grid grid-cols-1 lg:grid-cols-4 gap-4">

        {/* Search */}
        <div className="relative lg:col-span-2">

          <Search
            size={20}
            className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"
          />

          <input
            type="text"
            value={search}
            onChange={(event) => onSearch(event.target.value)}
            placeholder="Cari Nama atau NIP..."
            className="w-full h-14 border rounded-xl pl-12 pr-4 outline-none focus:ring-2 focus:ring-green-700"
          />

        </div>

        {/* Bulan */}
        <select
          value={bulan}
          onChange={(event) => onBulanChange(event.target.value)}
          className="h-14 border rounded-xl px-4 outline-none"
        >
          <option value="">Semua Bulan</option>
          {months.map((item) => (
            <option key={item.value} value={item.value}>{item.label}</option>
          ))}

        </select>

        {/* Tahun */}
        <select
          value={tahun}
          onChange={(event) => onTahunChange(event.target.value)}
          className="h-14 border rounded-xl px-4 outline-none"
        >
          <option value="">Semua Tahun</option>
          <option value="2026">2026</option>
          <option value="2025">2025</option>
          <option value="2024">2024</option>

        </select>

      </div>

    </div>
  );
}

export default RiwayatToolbar;
