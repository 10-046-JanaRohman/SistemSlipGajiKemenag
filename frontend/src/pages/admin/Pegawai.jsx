import AdminLayout from "../../layouts/AdminLayout";
import PageTransition from "../../components/common/PageTransition";

import PegawaiHeader from "../../components/pegawai/PegawaiHeader";
import PegawaiToolbar from "../../components/pegawai/PegawaiToolbar";
import PegawaiTable from "../../components/pegawai/PegawaiTable";
import PegawaiPagination from "../../components/pegawai/PegawaiPagination";
import PegawaiModal from "../../components/pegawai/PegawaiModal";

function Pegawai() {
  return (
    <AdminLayout>
      <PageTransition>

        <div className="space-y-8">

          <PegawaiHeader />

          <PegawaiToolbar />

          <PegawaiTable />

          <PegawaiPagination />

          <PegawaiModal />

        </div>

      </PageTransition>
    </AdminLayout>
  );
}

export default Pegawai;