import { useNavigate, NavLink } from "react-router-dom";
import logoKemenag from "../../assets/images/logo-kemenag.png";

import {
  LayoutDashboard,
  FileText,
  Users,
  Upload,
  History,
  LogOut,
  Settings,
  Megaphone,
} from "lucide-react";
import api from "../../services/api";

function Sidebar() {
  const navigate = useNavigate();
  const user = (() => {
    try {
      return JSON.parse(localStorage.getItem("user") || "null");
    } catch {
      return null;
    }
  })();

  const handleLogout = async () => {
    try {
      await api.logout();
    } catch {
      // tetap logout meski gagal
    }
    navigate("/");
  };

  return (
    <aside className="fixed inset-y-0 left-0 z-30 flex h-screen w-64 flex-col overflow-y-auto bg-green-900 text-white">

      {/* Logo */}
      <div className="border-b border-green-800 py-8 px-4">

        <div className="flex flex-col items-center">

          <img
            src={logoKemenag}
            alt="Logo Kemenag"
            className="w-16 h-16 object-contain"
          />

          <h2 className="mt-4 text-center text-lg font-bold leading-tight">
            KEMENTERIAN AGAMA
          </h2>

          <p className="text-sm text-green-100 mt-1 text-center">
            PROVINSI LAMPUNG
          </p>

        </div>

      </div>

      {/* Menu */}
      <nav className="flex-1 p-5 space-y-2">

        <Menu
          to="/admin/dashboard"
          icon={<LayoutDashboard size={20} />}
          text="Dashboard"
        />

        <Menu
          to="/admin/slip-gaji"
          icon={<FileText size={20} />}
          text="Slip Gaji"
        />

        <Menu
          to="/admin/pegawai"
          icon={<Users size={20} />}
          text="Pegawai"
        />

        <Menu
          to="/admin/import-excel"
          icon={<Upload size={20} />}
          text="Import Excel"
        />

        <Menu
          to="/admin/riwayat"
          icon={<History size={20} />}
          text="Riwayat"
        />

        <Menu
          to="/admin/notifikasi"
          icon={<Megaphone size={20} />}
          text="Pengumuman"
        />

        {user?.role === "super_admin" && (
          <Menu
            to="/admin/pengaturan"
            icon={<Settings size={20} />}
            text="Pengaturan"
          />
        )}

      </nav>

      {/* Logout */}
      <div className="p-5 border-t border-green-800">

        <button
          onClick={handleLogout}
          className="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-green-800 transition"
        >
          <LogOut size={20} />
          <span>Logout</span>
        </button>

      </div>

    </aside>
  );
}

function Menu({ to, icon, text }) {
  return (
    <NavLink
      to={to}
      end
      className={({ isActive }) =>
        `w-full flex items-center gap-3 px-4 py-3 rounded-xl transition ${
          isActive
            ? "bg-green-700"
            : "hover:bg-green-800"
        }`
      }
    >
      {icon}

      <span>{text}</span>

    </NavLink>
  );
}

export default Sidebar;
