import { useState, useEffect } from "react";
import { useSearchParams } from "react-router-dom";
import { Loader2, Download, ArrowLeft } from "lucide-react";
import AdminLayout from "../../layouts/AdminLayout";
import PageTransition from "../../components/common/PageTransition";
import api from "../../services/api";
import { formatPeriode } from "../../utils/formatPeriode";

function formatRupiah(val) {
  if (val === undefined || val === null) return "-";
  const num = typeof val === "string" ? parseFloat(val.replace(/[^0-9,-]/g, "").replace(",", ".")) : val;
  if (!Number.isFinite(num)) return "-";
  return new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0 }).format(num);
}

function DetailSlip() {
  const [searchParams] = useSearchParams();
  const id = searchParams.get("id");

  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [downloadError, setDownloadError] = useState("");

  useEffect(() => {
    if (!id || id === "undefined" || id === "null") {
      setError("ID slip tidak ditemukan.");
      setLoading(false);
      return;
    }
    const fetchDetail = async () => {
      try {
        const result = await api.getSlipDetail(id);
        const payload = result?.data || result;
        const slip = payload?.slip || payload;
        setData({
          ...slip,
          rincian: payload?.rincian || slip?.rincian || slip?.detail_gaji || {},
        });
      } catch (err) {
        setError(err.message || "Gagal memuat data slip.");
      } finally {
        setLoading(false);
      }
    };
    fetchDetail();
  }, [id]);

  const handleDownload = async () => {
    setDownloadError("");
    try {
      await api.getSlipPdf(id);
    } catch (err) {
      setDownloadError(err.message || "Gagal mengunduh PDF.");
    }
  };

  if (loading) {
    return (
      <AdminLayout>
        <PageTransition>
          <div className="flex items-center justify-center py-20">
            <Loader2 size={32} className="animate-spin text-green-700" />
            <span className="ml-3 text-gray-500">Memuat data slip...</span>
          </div>
        </PageTransition>
      </AdminLayout>
    );
  }

  if (error) {
    return (
      <AdminLayout>
        <PageTransition>
          <div className="bg-white rounded-2xl shadow p-8 text-center">
            <p className="text-red-600 font-semibold">{error}</p>
            <button onClick={() => window.history.back()} className="mt-4 bg-gray-200 hover:bg-gray-300 px-6 py-2 rounded-xl">
              Kembali
            </button>
          </div>
        </PageTransition>
      </AdminLayout>
    );
  }

  const pegawai = data?.pegawai || {};
  const rincian = data?.rincian || data?.detail_gaji || {};
  const rincianPegawai = rincian?.pegawai || {};
  const pendapatan = rincian?.pendapatan || null;
  const potongan = rincian?.potongan || null;
  const totalGaji =
    rincian?.gaji_bersih ??
    (rincian?.total_pendapatan !== undefined && rincian?.total_potongan !== undefined
      ? rincian.total_pendapatan - rincian.total_potongan
      : undefined) ??
    data?.gaji_bersih ??
    data?.total_gaji ??
    data?.total ??
    rincian?.total_diterima;

  return (
    <AdminLayout>
      <PageTransition>
        <div className="space-y-8">

          {/* Judul */}
          <div>
            <h1 className="text-5xl font-bold text-slate-800">Detail Slip Gaji</h1>
            <p className="text-gray-500 mt-2">Informasi lengkap slip gaji pegawai.</p>
          </div>

          {/* Card Data Pegawai */}
          <div className="bg-white rounded-2xl shadow p-8">
            <h2 className="text-2xl font-bold mb-6">Data Pegawai</h2>
            <div className="grid grid-cols-2 gap-5">
              <Item label="Nama" value={pegawai.nama || data?.nama || rincianPegawai.nama || "-"} />
              <Item label="NIP" value={pegawai.nip || data?.nip || rincianPegawai.nip || "-"} />
              <Item label="Jabatan" value={pegawai.jabatan || data?.jabatan || rincianPegawai.jabatan || "-"} />
              <Item label="Golongan" value={pegawai.golongan || data?.golongan || rincianPegawai.golongan || "-"} />
              <Item label="Unit Kerja" value={pegawai.unit_kerja || pegawai.keterangan_satuan_kerja || pegawai.satker_1 || data?.unit_kerja || "-"} />
              <Item label="Periode" value={data?.bulan ? formatPeriode(data.bulan, data.tahun) : data?.periode || "-"} />
            </div>
          </div>

          {/* Card Rincian Gaji */}
          <div className="bg-white rounded-2xl shadow p-8">
            <h2 className="text-2xl font-bold mb-6">Rincian Gaji</h2>
            <div className="space-y-4">
              {pendapatan ? (
                Object.entries(pendapatan).map(([title, value]) => (
                  <Row key={title} title={title} value={formatRupiah(value)} />
                ))
              ) : (
                <>
                  <Row title="Gaji Pokok" value={formatRupiah(rincian.gaji_pokok ?? rincian.gajiPokok ?? data?.gaji_pokok)} />
                  <Row title="Tunjangan" value={formatRupiah(rincian.tunjangan ?? rincian.total_tunjangan ?? data?.tunjangan)} />
                  <Row title="Transport" value={formatRupiah(rincian.transport ?? rincian.tunjangan_transport)} />
                  <Row title="Lembur" value={formatRupiah(rincian.lembur ?? rincian.tunjangan_lembur)} />
                </>
              )}
              <hr />
              {potongan ? (
                Object.entries(potongan).map(([title, value]) => (
                  <Row key={title} title={title} value={`- ${formatRupiah(value)}`} />
                ))
              ) : (
                <>
                  <Row title="BPJS" value={`- ${formatRupiah(rincian.bpjs ?? rincian.potongan_bpjs)}`} />
                  <Row title="Pajak" value={`- ${formatRupiah(rincian.pajak ?? rincian.potongan_pajak ?? data?.potongan)}`} />
                </>
              )}
              <hr />
              <Row title="TOTAL DITERIMA" value={formatRupiah(totalGaji)} bold />
            </div>
          </div>

          {/* Tombol */}
          {downloadError && (
            <div className="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
              {downloadError}
            </div>
          )}

          <div className="flex gap-4">
            <button
              onClick={handleDownload}
              className="bg-green-700 hover:bg-green-800 text-white px-8 py-3 rounded-xl font-semibold flex items-center gap-2"
            >
              <Download size={20} />
              Download PDF
            </button>
            <button
              onClick={() => window.history.back()}
              className="bg-gray-200 hover:bg-gray-300 px-8 py-3 rounded-xl font-semibold flex items-center gap-2"
            >
              <ArrowLeft size={20} />
              Kembali
            </button>
          </div>

        </div>
      </PageTransition>
    </AdminLayout>
  );
}

function Item({ label, value }) {
  return (
    <div>
      <p className="text-gray-500 text-sm">{label}</p>
      <p className="font-semibold text-lg">{value}</p>
    </div>
  );
}

function Row({ title, value, bold }) {
  return (
    <div className="flex justify-between">
      <span className={bold ? "font-bold text-xl" : ""}>{title}</span>
      <span className={bold ? "font-bold text-xl text-green-700" : ""}>{value}</span>
    </div>
  );
}

export default DetailSlip;
