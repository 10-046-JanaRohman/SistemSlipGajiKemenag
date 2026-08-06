import { useState } from "react";
import { ClipboardCheck, Download, Send, X } from "lucide-react";
import api from "../../services/api";

const statusMap = {
  pending: {
    label: "Menunggu persetujuan",
    className: "bg-yellow-100 text-yellow-700",
  },
  approved: {
    label: "TTD disetujui",
    className: "bg-green-100 text-green-700",
  },
  rejected: {
    label: "TTD ditolak",
    className: "bg-red-100 text-red-700",
  },
};

function SignatureDownloadButton({
  slipId,
  signatureRequest = null,
  className = "",
  label = "Download PDF",
  onUpdated,
}) {
  const [open, setOpen] = useState(false);
  const [localRequest, setLocalRequest] = useState(null);
  const [message, setMessage] = useState("");
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");
  const [submitting, setSubmitting] = useState(false);
  const [downloading, setDownloading] = useState(false);

  const request = localRequest || signatureRequest;

  const updateRequest = (nextRequest) => {
    setLocalRequest(nextRequest);
    onUpdated?.(nextRequest);
  };

  const downloadWithoutSignature = async () => {
    if (!slipId) return;

    setError("");
    setDownloading(true);
    try {
      await api.getSlipPdf(slipId);
    } catch (err) {
      setError(err.message || "Gagal mengunduh PDF.");
    } finally {
      setDownloading(false);
    }
  };

  const downloadWithSignature = async () => {
    if (!slipId || request?.status !== "approved") return;

    setError("");
    setDownloading(true);
    try {
      await api.getSlipPdf(slipId, { signature_request_id: request.id });
    } catch (err) {
      setError(err.message || "Gagal mengunduh PDF dengan TTD.");
    } finally {
      setDownloading(false);
    }
  };

  const submitRequest = async (event) => {
    event.preventDefault();
    if (!slipId) return;

    setError("");
    setSuccess("");
    setSubmitting(true);
    try {
      const result = await api.requestSlipSignature(slipId, message.trim());
      const nextRequest = result?.data || null;
      updateRequest(nextRequest);
      setMessage("");
      setSuccess(result?.message || "Pengajuan TTD berhasil dikirim.");
      window.dispatchEvent(new Event("notifikasi:perbarui"));
    } catch (err) {
      const existingRequest = err?.payload?.data || null;
      if (existingRequest) updateRequest(existingRequest);
      setError(err.message || "Gagal mengirim pengajuan TTD.");
    } finally {
      setSubmitting(false);
    }
  };

  const statusInfo = request?.status ? statusMap[request.status] : null;
  const canSubmit = !request || request.status === "rejected";

  return (
    <>
      <button type="button" onClick={() => setOpen(true)} className={className}>
        <Download size={20} />
        {label}
      </button>

      {open && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4">
          <section className="w-full max-w-xl rounded-2xl bg-white p-5 shadow-xl sm:p-6">
            <div className="flex items-start justify-between gap-4">
              <div>
                <h2 className="text-xl font-bold text-slate-800">Download Slip Gaji</h2>
                <p className="mt-1 text-sm text-gray-500">Pilih versi PDF yang dibutuhkan.</p>
              </div>
              <button type="button" onClick={() => setOpen(false)} className="rounded-lg p-2 text-gray-500 transition hover:bg-gray-100" aria-label="Tutup dialog">
                <X size={20} />
              </button>
            </div>

            <div className="mt-5 grid gap-3 sm:grid-cols-2">
              <button
                type="button"
                onClick={downloadWithoutSignature}
                disabled={downloading}
                className="flex min-h-24 flex-col items-start justify-center rounded-xl border border-gray-200 px-4 py-3 text-left transition hover:border-green-300 hover:bg-green-50 disabled:cursor-not-allowed disabled:opacity-70"
              >
                <span className="flex items-center gap-2 font-bold text-slate-800"><Download size={18} /> Tanpa TTD</span>
                <span className="mt-1 text-sm text-gray-500">Download langsung tanpa tanda tangan bendahara.</span>
              </button>

              <button
                type="button"
                onClick={downloadWithSignature}
                disabled={downloading || request?.status !== "approved"}
                className="flex min-h-24 flex-col items-start justify-center rounded-xl border border-gray-200 px-4 py-3 text-left transition hover:border-green-300 hover:bg-green-50 disabled:cursor-not-allowed disabled:opacity-60"
              >
                <span className="flex items-center gap-2 font-bold text-slate-800"><ClipboardCheck size={18} /> Dengan TTD</span>
                <span className="mt-1 text-sm text-gray-500">Tersedia setelah disetujui admin.</span>
              </button>
            </div>

            {statusInfo && (
              <div className="mt-4 rounded-xl border border-gray-200 px-4 py-3 text-sm">
                <span className={`inline-flex rounded-full px-3 py-1 font-semibold ${statusInfo.className}`}>
                  {statusInfo.label}
                </span>
                <p className="mt-3 text-gray-700">{request.request_message}</p>
                {request.admin_response && <p className="mt-2 font-semibold text-slate-700">Balasan: {request.admin_response}</p>}
              </div>
            )}

            {canSubmit && (
              <form onSubmit={submitRequest} className="mt-5 space-y-3">
                <label className="block text-sm font-semibold text-slate-700" htmlFor={`signature-request-${slipId}`}>
                  Alasan penggunaan TTD
                </label>
                <textarea
                  id={`signature-request-${slipId}`}
                  value={message}
                  onChange={(event) => setMessage(event.target.value)}
                  rows={4}
                  className="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-100"
                  placeholder="Contoh: Untuk melengkapi dokumen pengajuan kredit bank."
                  required
                  minLength={10}
                />
                <button
                  type="submit"
                  disabled={submitting}
                  className="inline-flex items-center justify-center gap-2 rounded-xl bg-green-700 px-5 py-3 font-semibold text-white transition hover:bg-green-800 disabled:cursor-not-allowed disabled:opacity-70"
                >
                  <Send size={18} />
                  {submitting ? "Mengirim..." : "Ajukan TTD"}
                </button>
              </form>
            )}

            {success && <p className="mt-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">{success}</p>}
            {error && <p className="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{error}</p>}
          </section>
        </div>
      )}
    </>
  );
}

export default SignatureDownloadButton;
