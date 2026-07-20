import UserSidebar from "../components/user/UserSidebar";
import UserNavbar from "../components/user/UserNavbar";
import { useState } from "react";

function UserLayout({ children }) {
  const [menuOpen, setMenuOpen] = useState(false);
  return (
    <div className="min-h-screen bg-gray-100">

      {/* Sidebar */}
      <UserSidebar />
      {menuOpen && <button type="button" aria-label="Tutup menu" onClick={() => setMenuOpen(false)} className="fixed inset-0 z-40 bg-slate-950/45 lg:hidden" />}
      {menuOpen && <UserSidebar mobile onClose={() => setMenuOpen(false)} />}

      {/* Content */}
      <main className="min-h-screen lg:ml-64">

        {/* Navbar */}
        <UserNavbar onMenuClick={() => setMenuOpen(true)} />

        {/* Halaman */}
        <div className="p-4 sm:p-6 lg:p-8">
          {children}
        </div>

      </main>

    </div>
  );
}

export default UserLayout;
