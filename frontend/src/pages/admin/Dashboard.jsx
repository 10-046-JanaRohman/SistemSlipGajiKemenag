import { useState, useEffect } from "react";
import AdminLayout from "../../layouts/AdminLayout";
import PageTransition from "../../components/common/PageTransition";

import DashboardHeader from "../../components/dashboard/DashboardHeader";
import DashboardStats from "../../components/dashboard/DashboardStats";
import RecentActivity from "../../components/dashboard/RecentActivity";
import DashboardImportTable from "../../components/dashboard/DashboardImportTable";
import DashboardLatestSlip from "../../components/dashboard/DashboardLatestSlip";
import api from "../../services/api";

function Dashboard() {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const result = await api.getDashboard();
        setData(result?.data || result);
      } catch (err) {
        console.error("Dashboard fetch error:", err);
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, []);

  return (
    <AdminLayout>
      <PageTransition>

        <div className="space-y-8">

          <DashboardHeader />

          <DashboardStats data={data} loading={loading} />

          <RecentActivity data={data} />

          <DashboardImportTable data={data} />

          <DashboardLatestSlip data={data} />

        </div>

      </PageTransition>
    </AdminLayout>
  );
}

export default Dashboard;
