import UserLayout from "../../layouts/UserLayout";
import PageTransition from "../../components/common/PageTransition";

import UserSlipHeader from "../../components/user/UserSlipHeader";
import UserSlipToolbar from "../../components/user/UserSlipToolbar";
import UserSlipTable from "../../components/user/UserSlipTable";
import UserSlipPagination from "../../components/user/UserSlipPagination";

function SlipSaya() {
  return (
    <UserLayout>
      <PageTransition>

        <div className="space-y-8">

          <UserSlipHeader />

          <UserSlipToolbar />

          <UserSlipTable />

          <UserSlipPagination />

        </div>

      </PageTransition>
    </UserLayout>
  );
}

export default SlipSaya;