import UserLayout from "../../layouts/UserLayout";
import PageTransition from "../../components/common/PageTransition";

import UserHistoryHeader from "../../components/user/UserHistoryHeader";
import UserHistoryTable from "../../components/user/UserHistoryTable";
import UserHistoryPagination from "../../components/user/UserHistoryPagination";

function Riwayat() {
  return (
    <UserLayout>
      <PageTransition>

        <div className="space-y-8">

          <UserHistoryHeader />

          <UserHistoryTable />

          <UserHistoryPagination />

        </div>

      </PageTransition>
    </UserLayout>
  );
}

export default Riwayat;