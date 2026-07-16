import { Loader2 } from "lucide-react";
import SlipRow from "./SlipRow";
import { formatPeriode } from "../../utils/formatPeriode";

function SlipTable({ data = [], loading }) {
  const rows = Array.isArray(data)
    ? data.filter((item) => item && (item.nip || item.nama || item.pegawai?.nip || item.pegawai?.nama || item.bulan || item.periode))
    : [];

  if (loading) {
    return (
      <div className="bg-white rounded-2xl shadow-md overflow-hidden flex items-center justify-center py-20">
        <Loader2 size={32} className="animate-spin text-green-700" />
        <span className="ml-3 text-gray-500">Memuat data...</span>
      </div>
    );
  }

  if (!rows.length) {
    return (
      <div className="bg-white rounded-2xl shadow-md overflow-hidden flex items-center justify-center py-20">
        <p className="text-gray-500">Tidak ada data slip gaji.</p>
      </div>
    );
  }

  return (
    <div className="bg-white rounded-2xl shadow-md overflow-x-auto">

      <table className="w-full min-w-[900px] text-sm">

        <thead className="bg-green-700 text-white">

          <tr>

            <th className="px-5 py-3 text-left font-semibold">NIP</th>

            <th className="px-5 py-3 text-left font-semibold">Nama</th>

            <th className="px-5 py-3 text-left font-semibold">Bulan</th>

            <th className="px-5 py-3 text-right font-semibold">Total Gaji</th>

            <th className="px-5 py-3 text-center font-semibold">Status</th>

            <th className="px-5 py-3 text-center font-semibold">Aksi</th>

          </tr>

        </thead>

        <tbody>

          {rows.map((item, index) => (
            <SlipRow
              key={item.id || `slip-${index}`}
              id={item.id}
              nip={item.nip || item.pegawai?.nip || "-"}
              nama={item.nama || item.pegawai?.nama || "-"}
              bulan={item.bulan ? formatPeriode(item.bulan, item.tahun) : item.periode || "-"}
              gaji={item.gaji_bersih_hitung ?? item.total_gaji ?? item.gaji_bersih ?? item.total ?? item.gaji_pokok}
              status={(item.status || (item.dibagikan === 1 ? "Dibagikan" : "Belum"))}
            />
          ))}

        </tbody>

      </table>

    </div>
  );
}

export default SlipTable;
