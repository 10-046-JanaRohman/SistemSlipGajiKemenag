import AdminLayout from "../../layouts/AdminLayout";
import PageTransition from "../../components/common/PageTransition";

import RiwayatHeader from "../../components/riwayat/RiwayatHeader";
import RiwayatToolbar from "../../components/riwayat/RiwayatToolbar";
import RiwayatTable from "../../components/riwayat/RiwayatTable";
import RiwayatPagination from "../../components/riwayat/RiwayatPagination";

function RiwayatSlip() {
  return (
    <AdminLayout>
      <PageTransition>

        <div className="space-y-8">

          <RiwayatHeader />

          <RiwayatToolbar />

          <RiwayatTable />

          <RiwayatPagination />

        </div>

      </PageTransition>
    </AdminLayout>
  );
}

export default RiwayatSlip;