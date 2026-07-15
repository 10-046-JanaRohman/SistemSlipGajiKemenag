import Sidebar from "../components/dashboard/Sidebar";
import Navbar from "../components/dashboard/Navbar";

function AdminLayout({ children }) {
  return (
    <div className="min-h-screen flex bg-gray-100">

      {/* Sidebar Admin */}
      <Sidebar />

      {/* Content */}
      <main className="flex-1">

        {/* Navbar */}
        <Navbar />

        {/* Page Content */}
        <div className="p-8">
          {children}
        </div>

      </main>

    </div>
  );
}

export default AdminLayout;