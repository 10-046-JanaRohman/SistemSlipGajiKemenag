import { useEffect, useState } from "react";
import { Eye, Loader2, X } from "lucide-react";
import api from "../../services/api";
import { formatPeriode } from "../../utils/formatPeriode";

function formatDate(value) {
  if (!value) return "-";
  return new Date(value).toLocaleString("id-ID", {
    day: "2-digit",
    month: "long",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

function statusLabel(status) {
  const labels = {
    queued: "Dalam antrean",
    processing: "Sedang diproses",
    completed: "Selesai diproses",
    failed: "Gagal diproses",
  };

  return labels[status] || "Sedang diproses";
}

function StatusBadge({ className, children }) {
  return (
    <span className={`inline-flex min-w-40 justify-center rounded-full px-3 py-1 text-sm ${className}`}>
      {children}
    </span>
  );
}

function UploadHistory() {
  const [rows, setRows] = useState([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [pagination, setPagination] = useState({ currentPage: 1, lastPage: 1, total: 0 });
  const [detailItem, setDetailItem] = useState(null);

  useEffect(() => {
    let active = true;

    const fetchHistory = async () => {
      setLoading(true);
      try {
        const result = await api.getImportHistory({ page });
        const payload = result?.data || result;
        const list = Array.isArray(payload?.data) ? payload.data : Array.isArray(payload) ? payload : [];
        if (active) {
          setRows(list);
          setPagination({
            currentPage: payload?.current_page || page,
            lastPage: payload?.last_page || 1,
            total: payload?.total || list.length,
          });
        }
      } catch {
        if (active) {
          setRows([]);
          setPagination({ currentPage: 1, lastPage: 1, total: 0 });
        }
      } finally {
        if (active) setLoading(false);
      }
    };

    fetchHistory();

    return () => {
      active = false;
    };
  }, [page]);

  return (
    <div className="bg-white rounded-2xl shadow-md overflow-hidden">
      <div className="px-8 py-6 border-b">
        <h2 className="text-2xl font-bold">Riwayat Import</h2>
        {!loading && pagination.total > 0 && (
          <p className="mt-1 text-sm text-gray-500">Menampilkan riwayat proses import dan operator yang menjalankannya.</p>
        )}
      </div>

      {loading ? (
        <div className="flex items-center justify-center py-12 text-gray-500">
          <Loader2 size={24} className="animate-spin text-green-700" />
          <span className="ml-3">Memuat riwayat import...</span>
        </div>
      ) : !rows.length ? (
        <p className="py-12 text-center text-gray-500">Belum ada riwayat import.</p>
      ) : (
        <>
          <div className="overflow-x-auto">
            <table className="w-full min-w-[1100px]">
              <thead className="bg-green-700 text-white">
                <tr>
                  <th className="py-4">Nama File</th>
                  <th>Periode</th>
                  <th>Waktu Upload</th>
                  <th>Jumlah Data</th>
                  <th>Status</th>
                  <th>Keterangan</th>
                  <th>Operator</th>
                </tr>
              </thead>
              <tbody>
                {rows.map((item) => {
                  const failureCount = item.log_gagal?.length || Number(item.gagal || 0);

                  return (
                    <tr key={item.id} className="border-b text-center transition hover:bg-gray-50">
                      <td className="max-w-[230px] truncate px-3 py-4 font-medium" title={item.nama_file || item.file || "-"}>{item.nama_file || item.file || "-"}</td>
                      <td className="px-3">{item.bulan ? formatPeriode(item.bulan, item.tahun) : "-"}</td>
                      <td className="px-3">{formatDate(item.created_at)}</td>
                      <td className="px-3">{item.jumlah_data ?? ((item.berhasil || 0) + (item.gagal || 0))}</td>
                      <td className="px-3 py-4">
                        <div className="flex min-w-[10.5rem] flex-col items-center gap-2">
                          {item.ditambahkan > 0 && <StatusBadge className="bg-green-100 text-green-700">{item.ditambahkan} data baru</StatusBadge>}
                          {item.diperbarui > 0 && <StatusBadge className="bg-blue-100 text-blue-700">{item.diperbarui} diperbarui</StatusBadge>}
                          {item.berhasil > 0 && !item.ditambahkan && !item.diperbarui && <StatusBadge className="bg-green-100 text-green-700">{item.berhasil} baris berhasil</StatusBadge>}
                          {item.gagal > 0 && <StatusBadge className="bg-red-100 text-red-700">{item.gagal} baris gagal</StatusBadge>}
                          {!item.berhasil && !item.gagal && !item.ditambahkan && !item.diperbarui && <StatusBadge className={item.status === "failed" ? "bg-red-100 text-red-700" : "bg-yellow-100 text-yellow-700"}>{statusLabel(item.status)}</StatusBadge>}
                        </div>
                      </td>
                      <td className="min-w-[190px] px-3 py-4">
                        {failureCount > 0 ? (
                          <div className="flex flex-col items-center gap-2">
                            <span className="text-sm text-red-700">{failureCount} baris perlu diperbaiki</span>
                            <button type="button" onClick={() => setDetailItem(item)} className="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-700">
                              <Eye size={16} /> Lihat Detail
                            </button>
                          </div>
                        ) : (
                          <span className="text-sm text-gray-500">Tidak ada kesalahan</span>
                        )}
                      </td>
                      <td className="px-3 font-medium">{item.uploader?.name || "-"}</td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>

          {pagination.lastPage > 1 && (
            <div className="flex flex-wrap items-center justify-between gap-4 border-t px-6 py-4">
              <p className="text-sm text-gray-500">Total {pagination.total} riwayat</p>
              <div className="flex items-center gap-2">
                <button type="button" onClick={() => setPage((current) => Math.max(1, current - 1))} disabled={page <= 1} className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50">Sebelumnya</button>
                <span className="rounded-lg bg-green-700 px-4 py-2 text-sm font-semibold text-white">{pagination.currentPage}</span>
                <button type="button" onClick={() => setPage((current) => Math.min(pagination.lastPage, current + 1))} disabled={page >= pagination.lastPage} className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50">Berikutnya</button>
              </div>
            </div>
          )}
        </>
      )}

      {detailItem && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/45 p-4" role="dialog" aria-modal="true" aria-labelledby="detail-import-title">
          <div className="w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div className="flex items-start justify-between border-b px-6 py-5">
              <div>
                <h3 id="detail-import-title" className="text-xl font-bold text-slate-800">Detail Kesalahan Import</h3>
                <p className="mt-1 text-sm text-gray-500">{detailItem.nama_file || "File import"} - {formatPeriode(detailItem.bulan, detailItem.tahun)}</p>
              </div>
              <button type="button" onClick={() => setDetailItem(null)} className="rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-700" aria-label="Tutup detail"><X size={20} /></button>
            </div>
            <div className="max-h-[60vh] overflow-y-auto px-6 py-5">
              {detailItem.log_gagal?.length ? (
                <div className="space-y-3">
                  {detailItem.log_gagal.map((log, index) => (
                    <div key={`${log.baris || index}-${log.keterangan || index}`} className="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-800">
                      <span className="font-bold">Baris {log.baris || "-"}:</span> {log.keterangan || "Data tidak valid"}
                    </div>
                  ))}
                </div>
              ) : (
                <p className="text-sm text-gray-500">Tidak ada rincian kesalahan yang tersimpan untuk import ini.</p>
              )}
            </div>
            <div className="flex justify-end border-t px-6 py-4">
              <button type="button" onClick={() => setDetailItem(null)} className="rounded-lg bg-green-700 px-5 py-2 font-semibold text-white transition hover:bg-green-800">Tutup</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

export default UploadHistory;
