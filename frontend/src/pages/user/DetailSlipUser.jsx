import { useState, useEffect } from "react";
import { useParams, Link } from "react-router-dom";
import { Loader2, ArrowLeft } from "lucide-react";
import UserLayout from "../../layouts/UserLayout";
import PageTransition from "../../components/common/PageTransition";
import SignatureDownloadButton from "../../components/user/SignatureDownloadButton";
import api from "../../services/api";
import { formatPeriode } from "../../utils/formatPeriode";

function formatRupiah(val) {
  if (val === undefined || val === null) return "-";
  const num = typeof val === "string" ? parseFloat(val.replace(/[^0-9,-]/g, "").replace(",", ".")) : val;
  if (!Number.isFinite(num)) return "-";
  return new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0 }).format(num);
}

function DetailSlipUser() {
  const { id } = useParams();
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    if (!id || id === "undefined" || id === "null") {
      const timeoutId = window.setTimeout(() => {
        setError("ID slip tidak ditemukan.");
        setLoading(false);
      }, 0);

      return () => window.clearTimeout(timeoutId);
    }

    let active = true;
    const fetchDetail = async () => {
      try {
        const result = await api.getSlipDetail(id);
        const payload = result?.data || result;
        const slip = payload?.slip || payload;
        if (!active) return;
        setData({
          ...slip,
          rincian: payload?.rincian || slip?.rincian || slip?.detail_gaji || {},
          signature_request: payload?.signature_request || slip?.signature_request || null,
        });
      } catch (err) {
        if (!active) return;
        setError(err.message || "Gagal memuat data slip.");
      } finally {
        if (active) setLoading(false);
      }
    };

    const timeoutId = window.setTimeout(() => {
      fetchDetail();
    }, 0);

    return () => {
      active = false;
      window.clearTimeout(timeoutId);
    };
  }, [id]);

  if (loading) {
    return (
      <UserLayout>
        <PageTransition>
          <div className="flex items-center justify-center py-20">
            <Loader2 size={32} className="animate-spin text-green-700" />
            <span className="ml-3 text-gray-500">Memuat data slip...</span>
          </div>
        </PageTransition>
      </UserLayout>
    );
  }

  if (error) {
    return (
      <UserLayout>
        <PageTransition>
          <div className="bg-white rounded-2xl shadow p-8 text-center">
            <p className="text-red-600 font-semibold">{error}</p>
            <Link to="/user/slip" className="inline-block mt-4 bg-gray-200 hover:bg-gray-300 px-6 py-2 rounded-xl">
              Kembali
            </Link>
          </div>
        </PageTransition>
      </UserLayout>
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
    <UserLayout>
      <PageTransition>
        <div className="space-y-8">

          {/* Judul */}
          <div>
            <h1 className="text-3xl font-bold text-slate-800 sm:text-5xl">Detail Slip Gaji</h1>
            <p className="text-gray-500 mt-2">Informasi lengkap slip gaji Anda.</p>
          </div>

          {/* Card Data Pegawai */}
          <div className="bg-white rounded-2xl p-5 shadow sm:p-8">
            <h2 className="text-2xl font-bold mb-6">Data Pegawai</h2>
            <div className="grid grid-cols-1 gap-5 sm:grid-cols-2">
              <Item label="Nama" value={pegawai.nama || data?.nama || rincianPegawai.nama || "-"} />
              <Item label="NIP" value={pegawai.nip || data?.nip || rincianPegawai.nip || "-"} />
              <Item label="Jabatan" value={pegawai.jabatan || data?.jabatan || rincianPegawai.jabatan || "-"} />
              <Item label="Golongan" value={pegawai.golongan || data?.golongan || rincianPegawai.golongan || "-"} />
              <Item label="Unit Kerja" value={pegawai.unit_kerja || pegawai.keterangan_satuan_kerja || "-"} />
              <Item label="Periode" value={data?.bulan ? formatPeriode(data.bulan, data.tahun) : data?.periode || "-"} />
            </div>
          </div>

          {/* Card Rincian Gaji */}
          <div className="bg-white rounded-2xl p-5 shadow sm:p-8">
            <h2 className="text-2xl font-bold mb-6">Rincian Gaji</h2>
            <div className="space-y-4">
              {pendapatan ? (
                Object.entries(pendapatan).map(([title, value]) => (
                  <Row key={title} title={title} value={formatRupiah(value)} />
                ))
              ) : (
                <>
                  <Row title="Gaji Pokok" value={formatRupiah(rincian.gaji_pok ?? rincian.gajiPokok ?? data?.gaji_pok)} />
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
          <div className="flex flex-col gap-3 sm:flex-row sm:gap-4">
            <SignatureDownloadButton
              slipId={id}
              signatureRequest={data?.signature_request}
              onUpdated={(signatureRequest) => setData((current) => ({ ...current, signature_request: signatureRequest }))}
              className="justify-center bg-green-700 hover:bg-green-800 text-white px-8 py-3 rounded-xl font-semibold flex items-center gap-2"
              label="Download PDF"
            />
            <Link
              to="/user/slip"
              className="justify-center bg-gray-200 hover:bg-gray-300 px-8 py-3 rounded-xl font-semibold flex items-center gap-2"
            >
              <ArrowLeft size={20} />
              Kembali
            </Link>
          </div>

        </div>
      </PageTransition>
    </UserLayout>
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

export default DetailSlipUser;
