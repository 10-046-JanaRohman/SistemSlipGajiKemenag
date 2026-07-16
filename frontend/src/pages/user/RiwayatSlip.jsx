import { useState, useEffect, useCallback } from "react";
import { Link } from "react-router-dom";
import UserLayout from "../../layouts/UserLayout";
import PageTransition from "../../components/common/PageTransition";
import api from "../../services/api";
import { formatPeriode } from "../../utils/formatPeriode";

function RiwayatSlip() {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);
  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const result = await api.getRiwayatSlip();
      const payload = result?.data || result;
      setData(Array.isArray(payload?.data) ? payload.data : Array.isArray(payload) ? payload : []);
    } catch {
      setData([]);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => { fetchData(); }, [fetchData]);
  return (
    <UserLayout>
      <PageTransition>
        <div className="space-y-8">
          <div>
            <h1 className="text-5xl font-bold text-slate-800">Riwayat Slip</h1>
            <p className="text-gray-500 mt-2">Riwayat slip gaji Anda.</p>
          </div>

          {loading ? (
            <p className="text-gray-500">Memuat...</p>
          ) : data.length === 0 ? (
            <div className="bg-white rounded-2xl shadow p-8 text-center">
              <p className="text-gray-500">Belum ada riwayat slip gaji.</p>
            </div>
          ) : (
            <div className="bg-white rounded-2xl shadow overflow-hidden">
              <table className="w-full">
                <thead className="bg-green-700 text-white">
                  <tr>
                    <th className="p-4">Periode</th>
                    <th>Total Gaji</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  {data.map((item) => (
                    <tr key={item.id} className="border-b hover:bg-gray-50 text-center">
                      <td className="py-4">{item.bulan ? formatPeriode(item.bulan, item.tahun) : item.periode || "-"}</td>
                      <td className="font-semibold">
                        {new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0 }).format(item.total_gaji ?? item.total ?? 0)}
                      </td>
                      <td>{item.tanggal || item.created_at ? new Date(item.tanggal || item.created_at).toLocaleDateString("id-ID") : "-"}</td>
                      <td>
                        <div className="flex items-center justify-center gap-2">
                          <Link
                            to={`/user/slip/${item.id}`}
                            className="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg transition inline-block"
                          >
                            Detail
                          </Link>
                          <button
                            onClick={() => api.downloadPdf(`/slip-gaji/${item.id}/pdf`, `slip-gaji-${item.id}.pdf`)}
                            className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition"
                          >
                            Download
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      </PageTransition>
    </UserLayout>
  );
}

export default RiwayatSlip;
