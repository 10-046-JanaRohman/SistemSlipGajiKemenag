import { BrowserRouter, Routes, Route } from "react-router-dom";
import ProtectedRoute from "./ProtectedRoute";

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

// ====================
// User
// ====================
import DashboardUser from "../pages/user/Dashboard";
import SlipSaya from "../pages/user/SlipSaya";
import RiwayatSlipUser from "../pages/user/RiwayatSlip";
import ProfilUser from "../pages/user/Profil";
import DetailSlipUser from "../pages/user/DetailSlipUser";

function AppRoutes() {
  return (
    <BrowserRouter>
      <Routes>

        {/* ==================== */}
        {/* Login */}
        {/* ==================== */}
        <Route path="/" element={<Login />} />

        {/* ==================== */}
        {/* Admin */}
        {/* ==================== */}

        <Route path="/admin/dashboard" element={<ProtectedRoute roles={["admin"]}><Dashboard /></ProtectedRoute>} />
        <Route path="/admin/pegawai" element={<ProtectedRoute roles={["admin"]}><Pegawai /></ProtectedRoute>} />
        <Route path="/admin/slip-gaji" element={<ProtectedRoute roles={["admin"]}><SlipGaji /></ProtectedRoute>} />
        <Route path="/admin/detail-slip" element={<ProtectedRoute roles={["admin"]}><DetailSlip /></ProtectedRoute>} />
        <Route path="/admin/import-excel" element={<ProtectedRoute roles={["admin"]}><UploadSlip /></ProtectedRoute>} />
        <Route path="/admin/riwayat" element={<ProtectedRoute roles={["admin"]}><RiwayatSlip /></ProtectedRoute>} />
        <Route path="/admin/notifikasi" element={<ProtectedRoute roles={["admin"]}><Notifikasi /></ProtectedRoute>} />
        <Route path="/admin/profil" element={<ProtectedRoute roles={["admin"]}><Profil /></ProtectedRoute>} />
        <Route path="/admin/pengaturan" element={<ProtectedRoute roles={["super_admin"]}><Pengaturan /></ProtectedRoute>} />

        {/* ==================== */}
        {/* User */}
        {/* ==================== */}

        <Route path="/user/dashboard" element={<ProtectedRoute roles={["pegawai", "user"]}><DashboardUser /></ProtectedRoute>} />
        <Route path="/user/slip" element={<ProtectedRoute roles={["pegawai", "user"]}><SlipSaya /></ProtectedRoute>} />
        <Route path="/user/slip/:id" element={<ProtectedRoute roles={["pegawai", "user"]}><DetailSlipUser /></ProtectedRoute>} />
        <Route path="/user/riwayat" element={<ProtectedRoute roles={["pegawai", "user"]}><RiwayatSlipUser /></ProtectedRoute>} />
        <Route path="/user/profil" element={<ProtectedRoute roles={["pegawai", "user"]}><ProfilUser /></ProtectedRoute>} />

      </Routes>
    </BrowserRouter>
  );
}

export default AppRoutes;
