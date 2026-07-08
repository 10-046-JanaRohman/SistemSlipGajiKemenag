import AdminLayout from "../../layouts/AdminLayout";
import PageTransition from "../../components/common/PageTransition";

import UploadHeader from "../../components/upload/UploadHeader";
import UploadDropzone from "../../components/upload/UploadDropzone";
import UploadNote from "../../components/upload/UploadNote";
import UploadButton from "../../components/upload/UploadButton";
import UploadHistory from "../../components/upload/UploadHistory";

function UploadSlip() {
  return (
    <AdminLayout>
      <PageTransition>

        <div className="space-y-8">

          <UploadHeader />

          <UploadDropzone />

          <UploadNote />

          <UploadButton />

          <UploadHistory />

        </div>

      </PageTransition>
    </AdminLayout>
  );
}

export default UploadSlip;