import AdminLayout from "../../layouts/AdminLayout";
import PageTransition from "../../components/common/PageTransition";

import SlipHeader from "../../components/slip/SlipHeader";
import SlipToolbar from "../../components/slip/SlipToolbar";
import SlipTable from "../../components/slip/SlipTable";
import SlipPagination from "../../components/slip/SlipPagination";

function SlipGaji() {
  return (
    <AdminLayout>
      <PageTransition>

        <div className="space-y-8">

          <SlipHeader />

          <SlipToolbar />

          <SlipTable />

          <SlipPagination />

        </div>

      </PageTransition>
    </AdminLayout>
  );
}

export default SlipGaji;