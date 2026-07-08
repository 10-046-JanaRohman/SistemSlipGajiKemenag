import { NavLink } from "react-router-dom";
import logoKemenag from "../../assets/images/logo-kemenag.png";

import {
  LayoutDashboard,
  Users,
  Upload,
  FileText,
  History,
  Bell,
  User,
  Settings,
  LogOut,
} from "lucide-react";

function Sidebar() {
  return (
    <aside className="w-72 bg-green-900 text-white flex flex-col">

      {/* Logo */}
      <div className="border-b border-green-800 py-8 px-5">

        <div className="flex flex-col items-center">

          <img
            src={logoKemenag}
            alt="Logo Kemenag"
            className="w-20 h-20 object-contain"
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
          to="/dashboard"
          icon={<LayoutDashboard size={20} />}
          text="Dashboard"
        />

        <Menu
          to="/pegawai"
          icon={<Users size={20} />}
          text="Pegawai"
        />

        <Menu
          to="/import-excel"
          icon={<Upload size={20} />}
          text="Import Excel"
        />

        <Menu
          to="/slip-gaji"
          icon={<FileText size={20} />}
          text="Slip Gaji"
        />

        <Menu
          to="/riwayat-slip"
          icon={<History size={20} />}
          text="Riwayat Slip"
        />

        <Menu
          to="/notifikasi"
          icon={<Bell size={20} />}
          text="Notifikasi"
        />

        <Menu
          to="/profil"
          icon={<User size={20} />}
          text="Profil"
        />

        <Menu
          to="/pengaturan"
          icon={<Settings size={20} />}
          text="Pengaturan"
        />

      </nav>

      {/* Logout */}
      <div className="border-t border-green-800 p-5">

        <button
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
        `flex items-center gap-3 px-4 py-3 rounded-xl transition ${
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