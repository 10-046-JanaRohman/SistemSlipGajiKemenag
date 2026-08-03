import { Loader2 } from "lucide-react";
import RiwayatRow from "./RiwayatRow";

function RiwayatTable({ data = [], loading }) {
  const monthNames = {
    1: "Januari",
    2: "Februari",
    3: "Maret",
    4: "April",
    5: "Mei",
    6: "Juni",
    7: "Juli",
    8: "Agustus",
    9: "September",
    10: "Oktober",
    11: "November",
    12: "Desember",
  };

  const formatPeriode = (item) => {
    if (item.periode) return item.periode;
    if (!item.bulan || !item.tahun) return "-";

    return `${monthNames[item.bulan] || item.bulan} ${item.tahun}`;
  };

  const rows = Array.isArray(data)
    ? data.filter((item) => item && (item.nip || item.nama || item.pegawai?.nip || item.pegawai?.nama || item.bulan || item.periode))
    : [];

  if (loading) {
    return (
      <div className="bg-white rounded-2xl shadow overflow-hidden flex items-center justify-center py-20">
        <Loader2 size={32} className="animate-spin text-green-700" />
        <span className="ml-3 text-gray-500">Memuat data...</span>
      </div>
    );
  }

  if (!rows.length) {
    return (
      <div className="bg-white rounded-2xl shadow overflow-hidden flex items-center justify-center py-20">
        <p className="text-gray-500">Belum ada riwayat slip gaji.</p>
      </div>
    );
  }

  const formatRow = (item) => ({
    id: item.id,
    nip: item.nip || item.pegawai?.nip || "-",
    nama: item.nama || item.pegawai?.nama || "-",
    periode: formatPeriode(item),
    tanggal: item.tanggal_terbit || item.created_at
      ? new Date(item.tanggal_terbit || item.created_at).toLocaleDateString("id-ID")
      : "-",
    status: item.status || (item.dibagikan === 1 ? "Sudah Dibagikan" : "Belum Dibagikan"),
  });

  return (
    <div className="bg-white rounded-2xl shadow overflow-x-auto">

      <table className="w-full min-w-[900px] text-sm">

        <thead className="bg-green-700 text-white">

          <tr>

            <th className="px-5 py-3 text-left font-semibold">NIP</th>

            <th className="px-5 py-3 text-left font-semibold">Nama Pegawai</th>

            <th className="px-5 py-3 text-left font-semibold">Periode</th>

            <th className="px-5 py-3 text-left font-semibold">Tanggal Dibagikan</th>

            <th className="px-5 py-3 text-center font-semibold">Status</th>

            <th className="px-5 py-3 text-center font-semibold">Aksi</th>

          </tr>

        </thead>

        <tbody>

          {rows.map((item) => {
            const row = formatRow(item);
            return (
              <RiwayatRow
                id={row.id}
                key={row.id || row.nip}
                nip={row.nip}
                nama={row.nama}
                periode={row.periode}
                tanggal={row.tanggal}
                status={row.status}
              />
            );
          })}

        </tbody>

      </table>

    </div>
  );
}

export default RiwayatTable;
