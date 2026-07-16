import UserSidebar from "../components/user/UserSidebar";
import UserNavbar from "../components/user/UserNavbar";

function UserLayout({ children }) {
  return (
    <div className="min-h-screen bg-gray-100">

      {/* Sidebar */}
      <UserSidebar />

      {/* Content */}
      <main className="min-h-screen ml-64">

        {/* Navbar */}
        <UserNavbar />

        {/* Halaman */}
        <div className="p-8">
          {children}
        </div>

      </main>

    </div>
  );
}

export default UserLayout;
