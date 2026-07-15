import { Search } from "lucide-react";

function UserSlipToolbar() {
  return (
    <div className="bg-white rounded-2xl shadow-md p-6">

      <div className="flex justify-between items-center">

        <div className="relative w-96">

          <Search
            size={20}
            className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"
          />

          <input
            type="text"
            placeholder="Cari Periode..."
            className="w-full border rounded-xl pl-12 pr-4 py-3 outline-none focus:ring-2 focus:ring-green-700"
          />

        </div>

        <select className="border rounded-xl px-4 py-3">

          <option>Semua Tahun</option>

          <option>2026</option>

          <option>2025</option>

        </select>

      </div>

    </div>
  );
}

export default UserSlipToolbar;