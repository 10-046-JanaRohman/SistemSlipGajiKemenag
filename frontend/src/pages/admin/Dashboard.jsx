import AdminLayout from "../../layouts/AdminLayout";
import PageTransition from "../../components/common/PageTransition";

import DashboardHeader from "../../components/dashboard/DashboardHeader";
import DashboardStats from "../../components/dashboard/DashboardStats";
import DashboardChart from "../../components/dashboard/DashboardChart";
import RecentActivity from "../../components/dashboard/RecentActivity";
import DashboardImportTable from "../../components/dashboard/DashboardImportTable";
import DashboardLatestSlip from "../../components/dashboard/DashboardLatestSlip";

function Dashboard() {
  return (
    <AdminLayout>
      <PageTransition>

        <div className="space-y-8">

          <DashboardHeader />

          <DashboardStats />

          <div className="grid grid-cols-3 gap-6">

            <div className="col-span-2">
              <DashboardChart />
            </div>

            <RecentActivity />

          </div>

          <DashboardImportTable />

          <DashboardLatestSlip />

        </div>

      </PageTransition>
    </AdminLayout>
  );
}

export default Dashboard;