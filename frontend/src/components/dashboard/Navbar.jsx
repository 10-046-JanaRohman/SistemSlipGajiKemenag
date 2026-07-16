import { useState, useEffect } from "react";
import { Bell, Search, ChevronDown } from "lucide-react";

function Navbar() {
  const [user, setUser] = useState({ name: "", role: "" });

  useEffect(() => {
    try {
      const stored = localStorage.getItem("user");
      if (stored) {
        const parsed = JSON.parse(stored);
        setUser({
          name: parsed.name || parsed.nama || "User",
          role: parsed.role || "",
        });
      }
    } catch {
      // abaikan
    }
  }, []);

  const initial = user.name.charAt(0).toUpperCase();

  return (
    <header className="h-20 bg-white shadow-sm px-8 flex items-center justify-between">

      {/* Kiri */}
      <div>

        <h2 className="text-3xl font-bold text-slate-800">
          Dashboard
        </h2>

        <p className="text-gray-500 mt-1">
          Selamat datang di Sistem Dashboard Slip Gaji
        </p>

      </div>

      {/* Kanan */}
      <div className="flex items-center gap-6">

        {/* Search */}
        <div className="relative">

          <Search
            size={18}
            className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"
          />

          <input
            type="text"
            placeholder="Cari..."
            className="w-72 h-11 pl-11 pr-4 rounded-xl bg-gray-100 outline-none focus:ring-2 focus:ring-green-700"
          />

        </div>

        {/* Notifikasi */}
        <button
          className="relative w-11 h-11 rounded-xl bg-gray-100 hover:bg-gray-200 transition flex items-center justify-center"
        >

          <Bell size={20} />

          <span className="absolute top-2 right-2 w-2 h-2 rounded-full bg-red-500"></span>

        </button>

        {/* Profil */}
        <button
          className="flex items-center gap-3 hover:bg-gray-100 rounded-xl px-3 py-2 transition"
        >

          <div className="w-11 h-11 rounded-full bg-green-700 flex items-center justify-center text-white font-bold">
            {initial}
          </div>

          <div className="text-left">

            <p className="font-semibold">
              {user.name}
            </p>

            <p className="text-sm text-gray-500 capitalize">
              {user.role}
            </p>

          </div>

          <ChevronDown
            size={18}
            className="text-gray-500"
          />

        </button>

      </div>

    </header>
  );
}

export default Navbar;
