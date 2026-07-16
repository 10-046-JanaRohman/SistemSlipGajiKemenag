import { useNavigate, NavLink } from "react-router-dom";
import logoKemenag from "../../assets/images/logo-kemenag.png";

import {
  LayoutDashboard,
  FileText,
  History,
  User,
  LogOut,
} from "lucide-react";
import api from "../../services/api";

function UserSidebar() {
  const navigate = useNavigate();

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
          to="/user/dashboard"
          icon={<LayoutDashboard size={20} />}
          text="Dashboard"
        />

        <Menu
          to="/user/slip"
          icon={<FileText size={20} />}
          text="Slip Gaji Saya"
        />

        <Menu
          to="/user/riwayat"
          icon={<History size={20} />}
          text="Riwayat Slip"
        />

        <Menu
          to="/user/profil"
          icon={<User size={20} />}
          text="Profil"
        />

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

export default UserSidebar;
