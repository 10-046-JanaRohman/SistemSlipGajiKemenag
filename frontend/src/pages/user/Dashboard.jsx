import UserLayout from "../../layouts/UserLayout";
import PageTransition from "../../components/common/PageTransition";

import UserHeader from "../../components/user/UserHeader";
import UserProfileCard from "../../components/user/UserProfileCard";
import UserStats from "../../components/user/UserStats";
import UserLatestSlip from "../../components/user/UserLatestSlip";
import UserAnnouncement from "../../components/user/UserAnnouncement";

function Dashboard() {
  return (
    <UserLayout>
      <PageTransition>

        <div className="space-y-8">

          <UserHeader />

          <div className="grid grid-cols-3 gap-6">

            <div>
              <UserProfileCard />
            </div>

            <div className="col-span-2">
              <UserStats />
            </div>

          </div>

          <div className="grid grid-cols-3 gap-6">

            <div className="col-span-2">
              <UserLatestSlip />
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