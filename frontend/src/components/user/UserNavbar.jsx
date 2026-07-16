import { useState, useEffect } from "react";
import { Bell, Search } from "lucide-react";

function UserNavbar() {
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

        <h2 className="text-2xl font-bold text-slate-800">
          Dashboard Pegawai
        </h2>

        <p className="text-gray-500 text-sm">
          Sistem Dashboard Slip Gaji Pegawai
        </p>

      </div>

      {/* Kanan */}
      <div className="flex items-center gap-6">

        {/* Search */}
        <div className="flex items-center bg-gray-100 rounded-xl px-4 h-11 w-72">

          <Search
            size={18}
            className="text-gray-400"
          />

          <input
            type="text"
            placeholder="Cari..."
            className="bg-transparent outline-none ml-3 w-full"
          />

        </div>

        {/* Notifikasi */}
        <button className="relative">

          <Bell className="text-gray-600" />

          <span className="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-red-500"></span>

        </button>

        {/* Profil */}
        <div className="flex items-center gap-3">

          <div className="w-11 h-11 rounded-full bg-green-700 flex items-center justify-center text-white font-bold">
            {initial}
          </div>

          <div>

            <p className="font-semibold">
              {user.name}
            </p>

            <p className="text-sm text-gray-500 capitalize">
              {user.role}
            </p>

          </div>

        </div>

      </div>

    </header>
  );
}

export default UserNavbar;
