import { Navigate } from "react-router-dom";

function getStoredUser() {
  try {
    return JSON.parse(localStorage.getItem("user") || "null");
  } catch {
    return null;
  }
}

function ProtectedRoute({ children, roles = [] }) {
  const token = localStorage.getItem("token");
  const user = getStoredUser();
  const role = (user?.role || localStorage.getItem("role") || "").toLowerCase();
  const allowedRoles = roles.map((item) => item.toLowerCase());
  const isAllowed = allowedRoles.includes(role)
    || (role === "super_admin" && allowedRoles.includes("admin"));

  if (!token) {
    return <Navigate to="/" replace />;
  }

  if (allowedRoles.length && !isAllowed) {
    return <Navigate to={role === "pegawai" || role === "user" ? "/user/dashboard" : "/admin/dashboard"} replace />;
  }

  return children;
}

export default ProtectedRoute;
