import { useCallback, useEffect, useState } from "react";
import { Check, ClipboardCheck, Loader2, X } from "lucide-react";
import AdminLayout from "../../layouts/AdminLayout";
import PageTransition from "../../components/common/PageTransition";
import api from "../../services/api";
import { formatPeriode } from "../../utils/formatPeriode";

const statusStyles = {
  pending: "bg-yellow-100 text-yellow-700",
  approved: "bg-green-100 text-green-700",
  rejected: "bg-red-100 text-red-700",
};

const statusLabels = {
  pending: "Menunggu",
  approved: "Disetujui",
  rejected: "Ditolak",
};

function PengajuanTtd() {
  const [items, setItems] = useState([]);
  const [meta, setMeta] = useState({ current_page: 1, last_page: 1, total: 0 });
  const [status, setStatus] = useState("pending");
  const [page, setPage] = useState(1);
  const [loading, setLoading] = useState(true);
  const [selected, setSelected] = useState(null);
  const [reviewStatus, setReviewStatus] = useState("approved");
  const [adminResponse, setAdminResponse] = useState("");
  const [reviewError, setReviewError] = useState("");
  const [submitting, setSubmitting] = useState(false);

  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const params = { page };
      if (status) params.status = status;
      const result = await api.getSlipSignatureRequests(params);
      const payload = result?.data || result;
      setItems(Array.isArray(payload?.data) ? payload.data : []);
      setMeta({
        current_page: payload?.current_page || 1,
        last_page: payload?.last_page || 1,
        total: payload?.total || 0,
      });
    } catch {
      setItems([]);
    } finally {
      setLoading(false);
    }
  }, [page, status]);

  useEffect(() => {
    const timeoutId = window.setTimeout(() => {
      fetchData();
    }, 0);

    return () => window.clearTimeout(timeoutId);
  }, [fetchData]);

  const openReview = (item, nextStatus) => {
    setSelected(item);
    setReviewStatus(nextStatus);
    setAdminResponse("");
    setReviewError("");
  };

  const submitReview = async (event) => {
    event.preventDefault();
    if (!selected) return;

    setSubmitting(true);
    setReviewError("");
    try {
      await api.reviewSlipSignatureRequest(selected.id, {
        status: reviewStatus,
        admin_response: adminResponse.trim(),
      });
      setSelected(null);
      await fetchData();
      window.dispatchEvent(new Event("notifikasi:perbarui"));
    } catch (err) {
      setReviewError(err.message || "Gagal memperbarui pengajuan.");
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <AdminLayout>
      <PageTransition>
        <div className="space-y-6">
          <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h1 className="text-3xl font-bold text-slate-800 sm:text-5xl">Pengajuan TTD</h1>
              <p className="mt-2 text-gray-500">Persetujuan tanda tangan bendahara pada PDF slip gaji.</p>
            </div>
            <div className="flex flex-wrap gap-2">
              {["pending", "approved", "rejected", ""].map((value) => (
                <button
                  key={value || "all"}
                  type="button"
                  onClick={() => { setStatus(value); setPage(1); }}
                  className={`rounded-xl px-4 py-2 text-sm font-semibold transition ${status === value ? "bg-green-700 text-white" : "bg-white text-slate-700 hover:bg-green-50"}`}
                >
                  {value ? statusLabels[value] : "Semua"}
                </button>
              ))}
            </div>
          </div>

          <div className="overflow-x-auto rounded-2xl bg-white shadow-md">
            <table className="w-full min-w-[1080px] text-sm">
              <thead className="bg-green-700 text-white">
                <tr>
                  <th className="px-5 py-3 text-left font-semibold">Pegawai</th>
                  <th className="px-5 py-3 text-left font-semibold">Periode</th>
                  <th className="px-5 py-3 text-left font-semibold">Alasan</th>
                  <th className="px-5 py-3 text-left font-semibold">Balasan</th>
                  <th className="px-5 py-3 text-center font-semibold">Status</th>
                  <th className="px-5 py-3 text-center font-semibold">Aksi</th>
                </tr>
              </thead>
              <tbody>
                {loading ? (
                  <tr>
                    <td colSpan={6} className="px-5 py-16 text-center text-gray-500">
                      <Loader2 size={28} className="mx-auto mb-3 animate-spin text-green-700" />
                      Memuat pengajuan...
                    </td>
                  </tr>
                ) : items.length ? items.map((item) => {
                  const slip = item.slip_gaji || {};
                  const pegawai = slip.pegawai || {};

                  return (
                    <tr key={item.id} className="border-b align-top last:border-0 hover:bg-gray-50">
                      <td className="px-5 py-4">
                        <p className="font-semibold text-slate-800">{pegawai.nama || item.user?.name || "-"}</p>
                        <p className="text-xs text-gray-500">{pegawai.nip || item.user?.nip || "-"}</p>
                      </td>
                      <td className="whitespace-nowrap px-5 py-4">{formatPeriode(slip.bulan, slip.tahun)}</td>
                      <td className="max-w-[320px] px-5 py-4 text-gray-700">{item.request_message}</td>
                      <td className="max-w-[260px] px-5 py-4 text-gray-600">{item.admin_response || "-"}</td>
                      <td className="px-5 py-4 text-center">
                        <span className={`inline-flex rounded-full px-3 py-1 font-semibold ${statusStyles[item.status] || "bg-gray-100 text-gray-600"}`}>
                          {statusLabels[item.status] || item.status}
                        </span>
                      </td>
                      <td className="px-5 py-4">
                        {item.status === "pending" ? (
                          <div className="flex justify-center gap-2">
                            <button type="button" onClick={() => openReview(item, "approved")} className="inline-flex items-center gap-2 rounded-lg bg-green-700 px-3 py-2 font-semibold text-white transition hover:bg-green-800">
                              <Check size={16} /> Approve
                            </button>
                            <button type="button" onClick={() => openReview(item, "rejected")} className="inline-flex items-center gap-2 rounded-lg bg-red-600 px-3 py-2 font-semibold text-white transition hover:bg-red-700">
                              <X size={16} /> Tolak
                            </button>
                          </div>
                        ) : (
                          <p className="text-center text-gray-500">Sudah ditinjau</p>
                        )}
                      </td>
                    </tr>
                  );
                }) : (
                  <tr>
                    <td colSpan={6} className="px-5 py-16 text-center text-gray-500">Belum ada pengajuan TTD.</td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>

          {meta.last_page > 1 && (
            <div className="flex items-center justify-between rounded-2xl bg-white px-5 py-4 shadow">
              <span className="text-sm text-gray-500">Total {meta.total} pengajuan</span>
              <div className="flex gap-2">
                <button type="button" disabled={page <= 1} onClick={() => setPage((current) => Math.max(1, current - 1))} className="rounded-lg bg-gray-100 px-4 py-2 font-semibold text-slate-700 disabled:opacity-50">Sebelumnya</button>
                <button type="button" disabled={page >= meta.last_page} onClick={() => setPage((current) => Math.min(meta.last_page, current + 1))} className="rounded-lg bg-gray-100 px-4 py-2 font-semibold text-slate-700 disabled:opacity-50">Berikutnya</button>
              </div>
            </div>
          )}

          {selected && (
            <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4">
              <form onSubmit={submitReview} className="w-full max-w-lg rounded-2xl bg-white p-5 shadow-xl sm:p-6">
                <div className="flex items-start justify-between gap-4">
                  <div>
                    <h2 className="text-xl font-bold text-slate-800">
                      {reviewStatus === "approved" ? "Approve Pengajuan" : "Tolak Pengajuan"}
                    </h2>
                    <p className="mt-1 text-sm text-gray-500">Balasan akan dikirim ke pegawai.</p>
                  </div>
                  <button type="button" onClick={() => setSelected(null)} className="rounded-lg p-2 text-gray-500 transition hover:bg-gray-100" aria-label="Tutup dialog">
                    <X size={20} />
                  </button>
                </div>

                <div className="mt-5 rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700">
                  {selected.request_message}
                </div>

                <label className="mt-5 block text-sm font-semibold text-slate-700" htmlFor="admin-response">
                  Balasan
                </label>
                <textarea
                  id="admin-response"
                  value={adminResponse}
                  onChange={(event) => setAdminResponse(event.target.value)}
                  rows={4}
                  className="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-100"
                  placeholder={reviewStatus === "approved" ? "Contoh: Disetujui untuk keperluan sesuai pengajuan." : "Contoh: Alasan penggunaan belum jelas, silakan ajukan ulang dengan keterangan lengkap."}
                  required={reviewStatus === "rejected"}
                />

                {reviewError && <p className="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{reviewError}</p>}

                <button
                  type="submit"
                  disabled={submitting}
                  className={`mt-5 inline-flex items-center justify-center gap-2 rounded-xl px-5 py-3 font-semibold text-white transition disabled:cursor-not-allowed disabled:opacity-70 ${reviewStatus === "approved" ? "bg-green-700 hover:bg-green-800" : "bg-red-600 hover:bg-red-700"}`}
                >
                  <ClipboardCheck size={18} />
                  {submitting ? "Menyimpan..." : "Simpan Review"}
                </button>
              </form>
            </div>
          )}
        </div>
      </PageTransition>
    </AdminLayout>
  );
}

export default PengajuanTtd;
