import { formatPeriode } from "../../utils/formatPeriode";

function formatRupiah(value) {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(Number(value || 0));
}

function formatTanggal(value) {
  if (!value) return "-";

  return new Date(value).toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
}

function DashboardPeriodSummary({ data = {} }) {
  const rows = Array.isArray(data?.ringkasan_gaji_periode) ? data.ringkasan_gaji_periode : [];

  return (
    <div className="min-w-0 overflow-hidden rounded-2xl bg-white p-6 shadow-md">
      <div className="mb-5">
        <h2 className="text-2xl font-bold">Ringkasan Gaji per Periode</h2>
        <p className="mt-1 text-sm text-gray-500">Ringkasan mengikuti data slip yang tersimpan saat ini.</p>
      </div>

      {!rows.length ? (
        <p className="py-6 text-center text-gray-500">Belum ada data periode gaji.</p>
      ) : (
        <div className="overflow-x-auto">
          <table className="w-full min-w-[720px] text-sm">
            <thead className="border-b text-left text-gray-500">
              <tr>
                <th className="pb-3 font-semibold">Periode</th>
                <th className="pb-3 text-right font-semibold">Total Gaji</th>
                <th className="pb-3 text-center font-semibold">Slip Dibagikan</th>
                <th className="pb-3 text-right font-semibold">Terakhir Diperbarui</th>
              </tr>
            </thead>
            <tbody>
              {rows.map((item) => (
                <tr key={`${item.bulan}-${item.tahun}`} className="border-b last:border-none">
                  <td className="py-4 font-semibold text-slate-800">{formatPeriode(item.bulan, item.tahun)}</td>
                  <td className="py-4 text-right font-semibold text-blue-600">{formatRupiah(item.total_gaji)}</td>
                  <td className="py-4 text-center">{new Intl.NumberFormat("id-ID").format(item.slip_dibagikan || 0)}</td>
                  <td className="py-4 text-right text-gray-600">{formatTanggal(item.terakhir_diperbarui)}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}

export default DashboardPeriodSummary;
