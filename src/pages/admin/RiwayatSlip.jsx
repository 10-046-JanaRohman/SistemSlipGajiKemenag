import { useState, useEffect, useCallback } from "react";
import AdminLayout from "../../layouts/AdminLayout";
import PageTransition from "../../components/common/PageTransition";
import api from "../../services/api";

import RiwayatHeader from "../../components/riwayat/RiwayatHeader";
import RiwayatToolbar from "../../components/riwayat/RiwayatToolbar";
import RiwayatTable from "../../components/riwayat/RiwayatTable";
import RiwayatPagination from "../../components/riwayat/RiwayatPagination";

function RiwayatSlip() {
  const [data, setData] = useState([]);
  const [meta, setMeta] = useState({ current_page: 1, last_page: 1, total: 0 });
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [search, setSearch] = useState("");
  const [bulan, setBulan] = useState("");
  const [tahun, setTahun] = useState(new Date().getFullYear().toString());

  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const result = await api.getAdminRiwayatSlip({ page, search, bulan, tahun });
      const payload = result?.data || result;
      setData(Array.isArray(payload?.data) ? payload.data : Array.isArray(payload) ? payload : []);
      setMeta({
        current_page: payload?.current_page || 1,
        last_page: payload?.last_page || 1,
        total: payload?.total || 0,
      });
    } catch {
      setData([]);
    } finally {
      setLoading(false);
    }
  }, [page, search, bulan, tahun]);

  useEffect(() => { fetchData(); }, [fetchData]);

  return (
    <AdminLayout>
      <PageTransition>
        <div className="space-y-8">
          <RiwayatHeader />
          <RiwayatToolbar
            search={search}
            bulan={bulan}
            tahun={tahun}
            onSearch={(value) => {
              setSearch(value);
              setPage(1);
            }}
            onBulanChange={(value) => {
              setBulan(value);
              setPage(1);
            }}
            onTahunChange={(value) => {
              setTahun(value);
              setPage(1);
            }}
          />
          <RiwayatTable data={data} loading={loading} />
          <RiwayatPagination meta={meta} page={page} onPageChange={setPage} />
        </div>
      </PageTransition>
    </AdminLayout>
  );
}

export default RiwayatSlip;
