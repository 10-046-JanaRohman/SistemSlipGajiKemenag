import { useState, useEffect } from "react";
import { Menu } from "lucide-react";
import GlobalSearch from "../common/GlobalSearch";
import NotificationBell from "../common/NotificationBell";
import ProfileMenu from "../common/ProfileMenu";

function Navbar({ onMenuClick }) {
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

  return (
    <header className="sticky top-0 z-20 bg-white px-4 py-3 shadow-sm sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8">

      {/* Kiri */}
      <div className="flex min-w-0 items-center gap-3">
        <button type="button" onClick={onMenuClick} className="rounded-lg p-2 text-slate-700 hover:bg-gray-100 lg:hidden" aria-label="Buka menu"><Menu size={24} /></button>
        <div className="min-w-0">

        <h2 className="truncate text-xl font-bold text-slate-800 sm:text-2xl lg:text-3xl">
          Dashboard
        </h2>

        <p className="mt-1 hidden text-sm text-gray-500 sm:block">
          Selamat datang di Sistem Dashboard Slip Gaji
        </p>

        </div></div>

      {/* Kanan */}
      <div className="mt-3 flex items-center gap-2 sm:mt-0 sm:gap-4 lg:gap-6">

        {/* Search */}
        <GlobalSearch />

        {/* Notifikasi */}
        <NotificationBell />

        {/* Profil */}
        <ProfileMenu user={user} profilePath="/admin/profil" />

      </div>

    </header>
  );
}

export default Navbar;
