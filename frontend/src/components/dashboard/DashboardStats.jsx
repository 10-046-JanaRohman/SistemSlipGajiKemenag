import StatCard from "./StatCard";

function DashboardStats() {
  return (
    <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

      <StatCard
        title="Total Pegawai"
        value="1.245"
        to="/admin/pegawai"
      />

      <StatCard
        title="Slip Bulan Ini"
        value="956"
        to="/admin/slip-gaji"
      />

      <StatCard
        title="Sudah Dibagikan"
        value="910"
        to="/admin/slip-gaji"
      />

      <StatCard
        title="Belum Dibagikan"
        value="46"
        to="/admin/slip-gaji"
      />

    </div>
  );
}

export default DashboardStats;