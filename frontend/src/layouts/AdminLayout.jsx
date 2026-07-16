import Sidebar from "../components/dashboard/Sidebar";
import Navbar from "../components/dashboard/Navbar";

function AdminLayout({ children }) {
  return (
    <div className="min-h-screen bg-gray-100">

      {/* Sidebar Admin */}
      <Sidebar />

      {/* Content */}
      <main className="min-h-screen ml-64">

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
