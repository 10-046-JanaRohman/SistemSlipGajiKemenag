import Sidebar from "../components/dashboard/Sidebar";
import Navbar from "../components/dashboard/Navbar";
import { useState } from "react";

function AdminLayout({ children }) {
  const [menuOpen, setMenuOpen] = useState(false);
  return (
    <div className="min-h-screen bg-gray-100">

      {/* Sidebar Admin */}
      <Sidebar />
      {menuOpen && <button type="button" aria-label="Tutup menu" onClick={() => setMenuOpen(false)} className="fixed inset-0 z-40 bg-slate-950/45 lg:hidden" />}
      {menuOpen && <Sidebar mobile onClose={() => setMenuOpen(false)} />}

      {/* Content */}
      <main className="min-h-screen lg:ml-64">

        {/* Navbar */}
        <Navbar onMenuClick={() => setMenuOpen(true)} />

        {/* Page Content */}
        <div className="p-4 sm:p-6 lg:p-8">
          {children}
        </div>

      </main>

    </div>
  );
}

export default AdminLayout;
