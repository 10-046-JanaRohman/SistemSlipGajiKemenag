import { BrowserRouter, Routes, Route } from "react-router-dom";

// Login
import Login from "../pages/Login";

// Admin
import Dashboard from "../pages/admin/Dashboard";
import Pegawai from "../pages/admin/Pegawai";
import SlipGaji from "../pages/admin/SlipGaji";
import DetailSlip from "../pages/admin/DetailSlip";
import UploadSlip from "../pages/admin/UploadSlip";
import RiwayatSlip from "../pages/admin/RiwayatSlip";
import Notifikasi from "../pages/admin/Notifikasi";
import Profil from "../pages/admin/Profil";
import Pengaturan from "../pages/admin/Pengaturan";

function AppRoutes() {
  return (
    <BrowserRouter>
      <Routes>

        {/* Login */}
        <Route path="/" element={<Login />} />

        {/* Dashboard */}
        <Route path="/dashboard" element={<Dashboard />} />

        {/* Pegawai */}
        <Route path="/pegawai" element={<Pegawai />} />

        {/* Import Excel */}
        <Route path="/import-excel" element={<UploadSlip />} />

        {/* Slip Gaji */}
        <Route path="/slip-gaji" element={<SlipGaji />} />

        {/* Detail Slip */}
        <Route path="/detail-slip" element={<DetailSlip />} />

        {/* Riwayat */}
        <Route path="/riwayat-slip" element={<RiwayatSlip />} />

        {/* Notifikasi */}
        <Route path="/notifikasi" element={<Notifikasi />} />

        {/* Profil */}
        <Route path="/profil" element={<Profil />} />

        {/* Pengaturan */}
        <Route path="/pengaturan" element={<Pengaturan />} />

      </Routes>
    </BrowserRouter>
  );
}

export default AppRoutes;