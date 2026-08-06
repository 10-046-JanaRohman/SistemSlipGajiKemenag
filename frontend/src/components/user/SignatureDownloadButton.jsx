import { useState } from "react";
import { Check, ClipboardCheck, Clock3, Download, Send, X } from "lucide-react";
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

function formatDateTime(value) {
  if (!value) return "";

  const normalizedValue = typeof value === "string" && !/[zZ]|[+-]\d{2}:?\d{2}$/.test(value)
    ? `${value.replace(" ", "T")}Z`
    : value;
  const date = new Date(normalizedValue);
  if (Number.isNaN(date.getTime())) return "";

  const formatted = new Intl.DateTimeFormat("id-ID", {
    day: "2-digit",
    month: "long",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
    timeZone: "Asia/Jakarta",
  }).format(date);

  return `${formatted} WIB`;
}

function getReviewerName(request) {
  return request?.reviewer?.name
    || request?.reviewer?.nama
    || request?.reviewed_by_user?.name
    || request?.reviewed_by_user?.nama
    || request?.reviewer_name
    || request?.reviewed_by_name
    || (request?.reviewed_by ? "Admin" : "");
}

function buildSteps(request, showRequestForm) {
  if (!request) {
    return [
      {
        title: "Ajukan TTD",
        description: "Pilih Dengan TTD lalu isi alasan penggunaan tanda tangan.",
        state: showRequestForm ? "active" : "upcoming",
      },
      {
        title: "Menunggu review admin",
        description: "Pengajuan akan masuk ke daftar review admin.",
        state: "upcoming",
      },
      {
        title: "Keputusan admin",
        description: "Status disetujui atau ditolak akan tampil di sini.",
        state: "upcoming",
      },
    ];
  }

  const isPending = request.status === "pending";
  const isApproved = request.status === "approved";
  const isRejected = request.status === "rejected";
  const reviewerName = getReviewerName(request);
  const submittedAt = formatDateTime(request.created_at);
  const reviewedAt = formatDateTime(request.reviewed_at);
  const decisionLabel = isApproved ? "Disetujui" : "Ditolak";
  const reviewerText = reviewerName ? `${decisionLabel} oleh ${reviewerName}` : decisionLabel;

  return [
    {
      title: "Pengajuan dikirim",
      description: submittedAt || "Alasan penggunaan TTD sudah dikirim.",
      state: "complete",
    },
    {
      title: "Menunggu review admin",
      description: isPending ? "Admin sedang meninjau pengajuan Anda." : "Pengajuan telah ditinjau admin.",
      state: isPending ? "active" : "complete",
    },
    {
      title: isPending ? "Keputusan admin" : reviewerText,
      description: isPending
        ? "Hasil review akan tampil setelah admin memberi keputusan."
        : reviewedAt || "Keputusan sudah tercatat.",
      state: isPending ? "upcoming" : isRejected ? "rejected" : "complete",
    },
  ];
}

function SignatureRequestStepper({ request, showRequestForm }) {
  const steps = buildSteps(request, showRequestForm);

  return (
    <div className="mt-5 rounded-xl border border-gray-200 px-4 py-4">
      <p className="text-sm font-bold text-slate-800">Status Pengajuan TTD</p>
      <div className="mt-4">
        {steps.map((step, index) => {
          const isLast = index === steps.length - 1;
          const isComplete = step.state === "complete";
          const isActive = step.state === "active";
          const isRejected = step.state === "rejected";
          const markerClass = isRejected
            ? "border-red-600 bg-red-600 text-white"
            : isComplete
              ? "border-green-700 bg-green-700 text-white"
              : isActive
                ? "border-yellow-400 bg-yellow-100 text-yellow-700"
                : "border-gray-300 bg-white text-gray-400";
          const lineClass = isComplete ? "bg-green-200" : "bg-gray-200";

          return (
            <div key={`${step.title}-${index}`} className="grid grid-cols-[32px_1fr] gap-3">
              <div className="flex flex-col items-center">
                <span className={`flex h-8 w-8 items-center justify-center rounded-full border-2 ${markerClass}`}>
                  {isRejected ? <X size={16} /> : isComplete ? <Check size={16} /> : <Clock3 size={15} />}
                </span>
                {!isLast && <span className={`h-10 w-0.5 ${lineClass}`} />}
              </div>
              <div className={isLast ? "pb-0" : "pb-4"}>
                <p className="font-semibold text-slate-800">{step.title}</p>
                <p className="mt-1 text-sm text-gray-500">{step.description}</p>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}

function SignatureDownloadButton({
  slipId,
  signatureRequest = null,
  className = "",
  label = "Download PDF",
  onUpdated,
}) {
  const [open, setOpen] = useState(false);
  const [localRequest, setLocalRequest] = useState(null);
  const [showRequestForm, setShowRequestForm] = useState(false);
  const [message, setMessage] = useState("");
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");
  const [submitting, setSubmitting] = useState(false);
  const [downloading, setDownloading] = useState(false);
  const [syncing, setSyncing] = useState(false);

  const request = localRequest || signatureRequest;
  const statusInfo = request?.status ? statusMap[request.status] : null;
  const canSubmit = !request || request.status === "rejected";

  const updateRequest = (nextRequest) => {
    setLocalRequest(nextRequest);
    onUpdated?.(nextRequest);
  };

  const syncSignatureRequest = async () => {
    if (!slipId) return request || null;

    setSyncing(true);
    try {
      const detailResult = await api.getSlipDetail(slipId);
      const detailPayload = detailResult?.data || detailResult;
      const detailRequest = detailPayload?.signature_request
        || detailPayload?.slip?.signature_request
        || null;

      if (detailRequest) {
        setLocalRequest(detailRequest);
        onUpdated?.(detailRequest);
        return detailRequest;
      }

      const result = await api.getSlipSignatureRequests({ slip_gaji_id: slipId });
      const payload = result?.data || result;
      const items = Array.isArray(payload?.data) ? payload.data : [];
      const latestRequest = items[0] || null;
      setLocalRequest(latestRequest);
      onUpdated?.(latestRequest);
      return latestRequest;
    } catch {
      return request || null;
    } finally {
      setSyncing(false);
    }
  };

  const openModal = () => {
    setOpen(true);
    syncSignatureRequest();
  };

  const closeModal = () => {
    setOpen(false);
    setShowRequestForm(false);
    setError("");
    setSuccess("");
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
    if (!slipId) return;

    const latestRequest = await syncSignatureRequest();
    const latestCanSubmit = !latestRequest || latestRequest.status === "rejected";

    if (latestRequest?.status !== "approved") {
      if (latestCanSubmit) {
        setShowRequestForm(true);
        setError("");
        setSuccess("");
      }
      return;
    }

    setError("");
    setDownloading(true);
    try {
      await api.getSlipPdf(slipId, { signature_request_id: latestRequest.id });
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
      setShowRequestForm(false);
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

  return (
    <>
      <button type="button" onClick={openModal} className={className}>
        <Download size={20} />
        {label}
      </button>

      {open && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4">
          <section className="max-h-[calc(100vh-2rem)] w-full max-w-xl overflow-y-auto rounded-2xl bg-white p-5 shadow-xl sm:p-6">
            <div className="flex items-start justify-between gap-4">
              <div>
                <h2 className="text-xl font-bold text-slate-800">Download Slip Gaji</h2>
                <p className="mt-1 text-sm text-gray-500">Pilih versi PDF yang dibutuhkan.</p>
              </div>
              <button type="button" onClick={closeModal} className="rounded-lg p-2 text-gray-500 transition hover:bg-gray-100" aria-label="Tutup dialog">
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
                disabled={downloading || syncing || request?.status === "pending"}
                className={`flex min-h-24 flex-col items-start justify-center rounded-xl border px-4 py-3 text-left transition disabled:cursor-not-allowed disabled:opacity-60 ${
                  showRequestForm
                    ? "border-green-500 bg-green-50"
                    : "border-gray-200 hover:border-green-300 hover:bg-green-50"
                }`}
              >
                <span className="flex items-center gap-2 font-bold text-slate-800"><ClipboardCheck size={18} /> Dengan TTD</span>
                <span className="mt-1 text-sm text-gray-500">
                  {syncing
                    ? "Memeriksa status pengajuan..."
                    : request?.status === "approved"
                    ? "Download PDF dengan tanda tangan bendahara."
                    : request?.status === "pending"
                      ? "Menunggu persetujuan admin."
                      : "Ajukan penggunaan tanda tangan bendahara."}
                </span>
              </button>
            </div>

            {(request || showRequestForm) && (
              <SignatureRequestStepper request={request} showRequestForm={showRequestForm} />
            )}

            {statusInfo && (
              <div className="mt-4 rounded-xl border border-gray-200 px-4 py-3 text-sm">
                <span className={`inline-flex rounded-full px-3 py-1 font-semibold ${statusInfo.className}`}>
                  {statusInfo.label}
                </span>
                <p className="mt-3 text-gray-700">{request.request_message}</p>
                {request.admin_response && <p className="mt-2 font-semibold text-slate-700">Balasan: {request.admin_response}</p>}
              </div>
            )}

            {canSubmit && showRequestForm && (
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
