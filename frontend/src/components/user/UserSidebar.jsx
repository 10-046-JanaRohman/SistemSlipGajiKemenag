import { useNavigate, NavLink } from "react-router-dom";
import logoKemenag from "../../assets/images/logo-kemenag.png";

import {
  LayoutDashboard,
  FileText,
  History,
  User,
  LogOut,
  X,
} from "lucide-react";
import api from "../../services/api";

function UserSidebar({ mobile = false, onClose }) {
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
    <aside className={`fixed inset-y-0 left-0 flex h-screen w-64 flex-col overflow-y-auto bg-green-900 text-white ${mobile ? "z-50 lg:hidden" : "z-30 hidden lg:flex"}`}>

      {/* Logo */}
      <div className={`border-b border-green-800 px-4 ${mobile ? "py-4" : "py-8"}`}>

        {mobile && <div className="mb-3 flex items-center justify-between text-sm font-semibold text-green-100"><span>MENU PEGAWAI</span><button type="button" onClick={onClose} aria-label="Tutup menu" className="rounded-lg p-2 text-white hover:bg-green-800"><X size={20} /></button></div>}

        <div className="flex flex-col items-center">

          <img
            src={logoKemenag}
            alt="Logo Kemenag"
            className={`${mobile ? "h-12 w-12" : "h-16 w-16"} object-contain`}
          />

          <h2 className={`${mobile ? "mt-2 text-base" : "mt-4 text-lg"} text-center font-bold leading-tight`}>
            KEMENTERIAN AGAMA
          </h2>

          <p className="text-sm text-green-100 mt-1 text-center">
            PROVINSI LAMPUNG
          </p>

        </div>

      </div>

      {/* Menu */}
      <nav onClick={mobile ? onClose : undefined} className="flex-1 p-5 space-y-2">

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
          onClick={() => { if (mobile) onClose?.(); handleLogout(); }}
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
