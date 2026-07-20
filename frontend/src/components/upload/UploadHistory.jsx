import { useEffect, useState } from "react";
import { Loader2 } from "lucide-react";
import api from "../../services/api";
import { formatPeriode } from "../../utils/formatPeriode";

function formatDate(value) {
  if (!value) return "-";
  return new Date(value).toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "long",
    year: "numeric",
  });
}

function UploadHistory() {
  const [rows, setRows] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    let active = true;

    const fetchHistory = async () => {
      setLoading(true);
      try {
        const result = await api.getImportHistory();
        const payload = result?.data || result;
        const list = Array.isArray(payload?.data) ? payload.data : Array.isArray(payload) ? payload : [];
        if (active) setRows(list);
      } catch {
        if (active) setRows([]);
      } finally {
        if (active) setLoading(false);
      }
    };

    fetchHistory();

    return () => {
      active = false;
    };
  }, []);

  return (
    <div className="bg-white rounded-2xl shadow-md overflow-hidden">
      <div className="px-8 py-6 border-b">
        <h2 className="text-2xl font-bold">Riwayat Import Terakhir</h2>
      </div>

      {loading ? (
        <div className="flex items-center justify-center py-12 text-gray-500">
          <Loader2 size={24} className="animate-spin text-green-700" />
          <span className="ml-3">Memuat riwayat import...</span>
        </div>
      ) : !rows.length ? (
        <p className="py-12 text-center text-gray-500">Belum ada riwayat import.</p>
      ) : (
        <table className="w-full">
          <thead className="bg-green-700 text-white">
            <tr>
              <th className="py-4">Nama File</th>
              <th>Periode</th>
              <th>Tanggal Import</th>
              <th>Status</th>
              <th>Keterangan</th>
              <th>Operator</th>
            </tr>
          </thead>
          <tbody>
            {rows.map((item) => (
              <tr key={item.id} className="border-b hover:bg-gray-50 text-center">
                <td className="py-4 font-medium">{item.nama_file || item.file || "-"}</td>
                <td>{item.bulan ? formatPeriode(item.bulan, item.tahun) : "-"}</td>
                <td>{formatDate(item.created_at)}</td>
                <td>
                  <div className="flex flex-wrap justify-center gap-2">
                    {item.berhasil > 0 && (
                      <span className="bg-green-100 text-green-700 px-4 py-1 rounded-full text-sm">
                        {item.berhasil} baris berhasil
                      </span>
                    )}
                    {item.gagal > 0 && (
                      <span className="bg-red-100 text-red-700 px-4 py-1 rounded-full text-sm">
                        {item.gagal} baris gagal
                      </span>
                    )}
                    {!item.berhasil && !item.gagal && (
                      <span className="bg-yellow-100 text-yellow-700 px-4 py-1 rounded-full text-sm">
                        {item.status || "Diproses"}
                      </span>
                    )}
                  </div>
                </td>
                <td className="max-w-xs px-3 py-3 text-left text-sm text-red-700">
                  {item.log_gagal?.length
                    ? item.log_gagal.slice(0, 2).map((log) => (
                      <div key={`${log.baris}-${log.keterangan}`}>
                        Baris {log.baris || "-"}: {log.keterangan}
                      </div>
                    ))
                    : "-"}
                  {item.log_gagal?.length > 2 && <div>dan {item.log_gagal.length - 2} lainnya.</div>}
                </td>
                <td>{item.uploader?.name || "-"}</td>
              </tr>
            ))}
          </tbody>
        </table>
      )}
    </div>
  );
}

export default UploadHistory;
