import { useState, useEffect } from "react";
import UserLayout from "../../layouts/UserLayout";
import PageTransition from "../../components/common/PageTransition";

import UserHeader from "../../components/user/UserHeader";
import UserProfileCard from "../../components/user/UserProfileCard";
import UserStats from "../../components/user/UserStats";
import UserLatestSlip from "../../components/user/UserLatestSlip";
import UserAnnouncement from "../../components/user/UserAnnouncement";
import api from "../../services/api";

function Dashboard() {
  const [data, setData] = useState(null);
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const storedUser = JSON.parse(localStorage.getItem("user") || "null");
        setUser(storedUser);

        const result = await api.getDashboard();
        setData(result?.data || result);
      } catch (err) {
        console.error("Dashboard user fetch error:", err);
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, []);

  return (
    <UserLayout>
      <PageTransition>

        <div className="space-y-8">

          <UserHeader user={data?.pegawai || user} />

          <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">

            <div>
              <UserProfileCard user={user} data={data} />
            </div>

            <div className="lg:col-span-2">
              <UserStats data={data} loading={loading} />
            </div>

          </div>

          <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">

            <div className="lg:col-span-2">
              <UserLatestSlip data={data} loading={loading} />
            </div>

            <div>
              <UserAnnouncement />
            </div>

          </div>

        </div>

      </PageTransition>
    </UserLayout>
  );
}

export default Dashboard;
