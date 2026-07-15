import AdminLayout from "../../layouts/AdminLayout";
import PageTransition from "../../components/common/PageTransition";

import DashboardHeader from "../../components/dashboard/DashboardHeader";
import DashboardStats from "../../components/dashboard/DashboardStats";
import DashboardImportTable from "../../components/dashboard/DashboardImportTable";
import DashboardLatestSlip from "../../components/dashboard/DashboardLatestSlip";
import RecentActivity from "../../components/dashboard/RecentActivity";

function Dashboard() {
  return (
    <AdminLayout>
      <PageTransition>

        <div className="space-y-8">

          {/* Header */}
          <DashboardHeader />

          {/* Statistik */}
          <DashboardStats />

          {/* Import Excel & Aktivitas */}
          <div className="grid grid-cols-1 xl:grid-cols-3 gap-6">

            <div className="xl:col-span-2">

              <DashboardImportTable />

            </div>

            <RecentActivity />

          </div>

          {/* Slip Terbaru */}
          <DashboardLatestSlip />

        </div>

      </PageTransition>
    </AdminLayout>
  );
}

export default Dashboard;