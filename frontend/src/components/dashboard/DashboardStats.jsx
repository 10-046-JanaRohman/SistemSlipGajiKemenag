import StatCard from "./StatCard";
import { formatPeriode } from "../../utils/formatPeriode";

function DashboardStats({ data, loading }) {
  if (loading) {
    return (
      <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
        {[1,2,3,4].map((i) => (
          <div key={i} className="bg-white rounded-2xl shadow p-6 animate-pulse">
            <div className="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
            <div className="h-8 bg-gray-200 rounded w-1/3"></div>
          </div>
        ))}
      </div>
    );
  }

  const d = data || {};
  const totalPegawai = d.total_pegawai ?? d.totalPegawai ?? 0;
  const totalGajiPeriode = d.total_gaji_periode ?? d.gaji_periode ?? 0;
  const belumDibagikanPeriode = d.belum_dibagikan_periode ?? d.belum_terbit ?? 0;
  const sudahDibagikanPeriode = d.sudah_dibagikan_periode ?? Math.max(0, totalPegawai - belumDibagikanPeriode);
  const periodeAktif = d.periode_aktif?.bulan
    ? formatPeriode(d.periode_aktif.bulan, d.periode_aktif.tahun)
    : "Belum ada periode";

  return (
    <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">

      <StatCard
        title="Total Pegawai"
        value={totalPegawai}
      />

      <StatCard
        title="Total Gaji Periode"
        subtitle={`Periode ${periodeAktif}`}
        value={totalGajiPeriode}
        color="text-blue-600"
        isCurrency
      />

      <StatCard
        title="Sudah Dibagikan Periode Ini"
        subtitle={`Periode ${periodeAktif}`}
        value={sudahDibagikanPeriode}
      />

      <StatCard
        title="Belum Dibagikan Periode Ini"
        subtitle={`Periode ${periodeAktif}`}
        value={belumDibagikanPeriode}
        color="text-red-600"
      />

    </div>
  );
}

export default DashboardStats;
