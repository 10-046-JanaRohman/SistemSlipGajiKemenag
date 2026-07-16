import { useState, useEffect, useCallback } from "react";
import AdminLayout from "../../layouts/AdminLayout";
import PageTransition from "../../components/common/PageTransition";

import SlipHeader from "../../components/slip/SlipHeader";
import SlipToolbar from "../../components/slip/SlipToolbar";
import SlipTable from "../../components/slip/SlipTable";
import SlipPagination from "../../components/slip/SlipPagination";
import api from "../../services/api";

function SlipGaji() {
  const [data, setData] = useState([]);
  const [meta, setMeta] = useState({ current_page: 1, last_page: 1, total: 0 });
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState("");
  const [bulan, setBulan] = useState("");
  const [tahun, setTahun] = useState("");
  const [page, setPage] = useState(1);

  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const params = { page };
      if (search) params.search = search;
      if (bulan) params.bulan = bulan;
      if (tahun) params.tahun = tahun;
      const result = await api.getSlipGaji(params);
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

  useEffect(() => {
    fetchData();
  }, [fetchData]);

  const handleSearch = (value) => {
    setSearch(value);
    setPage(1);
  };

  const handleFilter = (b, t) => {
    setBulan(b);
    setTahun(t);
    setPage(1);
  };

  return (
    <AdminLayout>
      <PageTransition>

        <div className="space-y-8">

          <SlipHeader />

          <SlipToolbar
            search={search}
            onSearch={handleSearch}
            bulan={bulan}
            tahun={tahun}
            onFilter={handleFilter}
          />

          <SlipTable data={data} loading={loading} />

          <SlipPagination
            meta={meta}
            page={page}
            onPageChange={setPage}
          />

        </div>

      </PageTransition>
    </AdminLayout>
  );
}

export default SlipGaji;
