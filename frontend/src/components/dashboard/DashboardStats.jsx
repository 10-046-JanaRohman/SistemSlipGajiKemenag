import StatCard from "./StatCard";

function DashboardStats() {
  return (
    <div className="grid grid-cols-4 gap-6">

      <StatCard
        title="Total Pegawai"
        value="1.245"
      />

      <StatCard
        title="Slip Bulan Ini"
        value="956"
        color="text-blue-600"
      />

      <StatCard
        title="Sudah Dibagikan"
        value="910"
      />

      <StatCard
        title="Belum Dibagikan"
        value="46"
        color="text-red-600"
      />

    </div>
  );
}

export default DashboardStats;