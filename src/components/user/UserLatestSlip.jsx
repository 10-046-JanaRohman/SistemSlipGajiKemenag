import {
  Calendar,
  Wallet,
  Download,
  Eye,
} from "lucide-react";
import { Link } from "react-router-dom";
import { useState } from "react";
import api from "../../services/api";
import { formatPeriode } from "../../utils/formatPeriode";

function formatRupiah(num) {
  if (num === null || num === undefined) return "Rp0";
  const n = typeof num === "string" ? parseFloat(num) : num;
  if (isNaN(n)) return "Rp0";
  return `Rp${new Intl.NumberFormat("id-ID").format(n)}`;
}

function UserLatestSlip({ data, loading }) {
  const [downloadError, setDownloadError] = useState("");
  const slip = data?.slip_terakhir ?? null;

  if (loading) {
    return (
      <div className="bg-white rounded-2xl shadow-md p-8 animate-pulse">
        <div className="h-6 bg-gray-200 rounded w-1/3 mb-4"></div>
        <div className="h-4 bg-gray-200 rounded w-1/2 mb-8"></div>
        <div className="h-10 bg-gray-200 rounded w-1/4 mb-4"></div>
        <div className="h-10 bg-gray-200 rounded w-1/3"></div>
      </div>
    );
  }

  const periode = slip ? formatPeriode(slip.bulan, slip.tahun) : "-";
  const gaji = slip?.gaji_bersih ?? 0;
  const status = data?.status_slip || "Belum Ada Slip";
  const slipId = slip?.id ?? null;

  const handleDownload = async () => {
    if (!slipId) return;

    setDownloadError("");
    try {
      await api.getSlipPdf(slipId);
    } catch (error) {
      setDownloadError(error.message || "Gagal mengunduh PDF.");
    }
  };

  return (
    <div className="bg-white rounded-2xl shadow-md p-8">
      {/* Judul */}
      <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

        <div>

          <h2 className="text-2xl font-bold text-slate-800">
            Slip Gaji Terbaru
          </h2>

          <p className="text-gray-500 mt-1">
            Informasi slip gaji periode terbaru.
          </p>

        </div>

        <span className={`px-4 py-2 rounded-full font-semibold ${
          slip
            ? "bg-green-100 text-green-700"
            : "bg-yellow-100 text-yellow-700"
        }`}>
          {status}
        </span>

      </div>

      {/* Isi */}
      <div className="mt-8 grid grid-cols-1 gap-8 sm:grid-cols-2">
        <div>

          <div className="flex items-center gap-3 mb-6">

            <Calendar
              size={22}
              className="text-green-700"
            />

            <div>

              <p className="text-gray-500 text-sm">
                Periode
              </p>

              <h3 className="text-xl font-bold">
                {periode}
              </h3>

            </div>

          </div>

          <div className="flex items-center gap-3">
            <Wallet
              size={22}
              className="text-green-700"
            />
            <div>

              <p className="text-gray-500 text-sm">
                Total Diterima
              </p>
              <h2 className="text-3xl font-bold text-green-700">
                {formatRupiah(gaji)}
              </h2>

            </div>

          </div>

        </div>

        {/* Tombol */}
        <div className="flex flex-col justify-center gap-4">
          {slipId ? (
            <>
              <Link
                to={`/user/slip/${slipId}`}
                className="w-full bg-green-700 hover:bg-green-800 text-white py-3 rounded-xl flex justify-center items-center gap-2 transition"
              >
                <Eye size={20} />
                Lihat Slip
              </Link>

              <button
                type="button"
                onClick={handleDownload}
                className="w-full border border-green-700 text-green-700 hover:bg-green-50 py-3 rounded-xl flex justify-center items-center gap-2 transition"
              >
                <Download size={20} />
                Download PDF
              </button>
            </>
          ) : (
            <p className="text-gray-500 text-center py-6">
              Belum ada slip gaji.
            </p>
          )}

        </div>

      </div>

      {downloadError && (
        <div className="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
          {downloadError}
        </div>
      )}

    </div>
  );
}

export default UserLatestSlip;
