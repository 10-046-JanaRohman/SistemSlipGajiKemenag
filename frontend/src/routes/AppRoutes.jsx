import { BrowserRouter, Routes, Route } from "react-router-dom";

// ====================
// Login
// ====================
import Login from "../pages/Login";

// ====================
// Admin
// ====================
import Dashboard from "../pages/admin/Dashboard";
import Pegawai from "../pages/admin/Pegawai";
import SlipGaji from "../pages/admin/SlipGaji";
import DetailSlip from "../pages/admin/DetailSlip";
import UploadSlip from "../pages/admin/UploadSlip";
import RiwayatSlip from "../pages/admin/RiwayatSlip";
import Notifikasi from "../pages/admin/Notifikasi";
import Profil from "../pages/admin/Profil";
import Pengaturan from "../pages/admin/Pengaturan";
import SlipSaya from "../pages/user/SlipSaya";
import Riwayat from "../pages/user/Riwayat";
import ProfilUser from "../pages/user/Profil";

// ====================
// User
// ====================
import DashboardUser from "../pages/user/Dashboard";

function AppRoutes() {
  return (
    <BrowserRouter>
      <Routes>

        {/* ==================== */}
        {/* Login */}
        {/* ==================== */}
        <Route
          path="/"
          element={<Login />}
        />

        {/* ==================== */}
        {/* Admin */}
        {/* ==================== */}

        <Route
          path="/admin/dashboard"
          element={<Dashboard />}
        />

        <Route
          path="/admin/pegawai"
          element={<Pegawai />}
        />

        <Route
          path="/admin/slip-gaji"
          element={<SlipGaji />}
        />

        <Route
          path="/admin/detail-slip"
          element={<DetailSlip />}
        />

        <Route
          path="/admin/import-excel"
          element={<UploadSlip />}
        />

        <Route
          path="/admin/riwayat"
          element={<RiwayatSlip />}
        />

        <Route
          path="/admin/notifikasi"
          element={<Notifikasi />}
        />

        <Route
          path="/admin/profil"
          element={<Profil />}
        />

        <Route
          path="/admin/pengaturan"
          element={<Pengaturan />}
        />

        {/* ==================== */}
        {/* User */}
        {/* ==================== */}

        <Route
          path="/user/dashboard"
          element={<DashboardUser />}
        />

         <Route
          path="/user/slip"
          element={<SlipSaya />}
        />

        <Route
          path="/user/riwayat"
          element={<Riwayat />}
          />

        <Route
          path="/user/profil"
          element={<ProfilUser />}
          />
          
      </Routes>
    </BrowserRouter>
  );
}

export default AppRoutes;