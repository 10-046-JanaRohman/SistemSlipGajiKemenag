import { ChevronDown, LogOut, UserRound } from "lucide-react";
import { useState } from "react";
import { useNavigate } from "react-router-dom";
import api from "../../services/api";

function ProfileMenu({ user, profilePath }) {
  const [open, setOpen] = useState(false);
  const navigate = useNavigate();
  const initial = (user?.name || "U").charAt(0).toUpperCase();

  const handleLogout = async () => {
    try {
      await api.logout();
    } catch {
      // Hapus sesi lokal walaupun server tidak dapat dihubungi.
      api.clearAuth();
    }
    navigate("/");
  };

  return (
    <div className="relative shrink-0">
      <button
        type="button"
        onClick={() => setOpen((current) => !current)}
        aria-expanded={open}
        className="flex items-center gap-2 rounded-xl px-1 py-2 transition hover:bg-gray-100 sm:px-3"
      >
        <div className="flex h-11 w-11 items-center justify-center rounded-full bg-green-700 font-bold text-white">{initial}</div>
        <div className="hidden text-left lg:block">
          <p className="max-w-40 truncate font-semibold">{user?.name || "User"}</p>
          <p className="text-sm capitalize text-gray-500">{user?.role || ""}</p>
        </div>
        <ChevronDown size={18} className={`hidden text-gray-500 transition lg:block ${open ? "rotate-180" : ""}`} />
      </button>

      {open && (
        <div className="absolute right-0 z-30 mt-2 w-52 overflow-hidden rounded-xl border border-gray-200 bg-white p-2 shadow-xl">
          <button type="button" onClick={() => { setOpen(false); navigate(profilePath); }} className="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm font-medium text-slate-700 hover:bg-green-50">
            <UserRound size={18} className="text-green-700" /> Profil Saya
          </button>
          <button type="button" onClick={handleLogout} className="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm font-medium text-red-700 hover:bg-red-50">
            <LogOut size={18} /> Logout
          </button>
        </div>
      )}
    </div>
  );
}

export default ProfileMenu;
