function RecentActivity({ data = {} }) {
  const slipList = data?.slip_terbaru ?? data?.slipTerbaru ?? [];
  const items = Array.isArray(slipList)
    ? slipList.map((item) => ({
        title: `Slip gaji ${item.nama || item.pegawai?.nama || "pegawai"}`,
        time: item.tanggal_terbit || item.created_at
          ? new Date(item.tanggal_terbit || item.created_at).toLocaleDateString("id-ID")
          : "Baru saja",
        color: "bg-green-500",
      }))
    : [];

  if (!items.length) {
    return (
      <div className="bg-white rounded-2xl shadow p-6 h-[380px] min-w-0">
        <h2 className="text-2xl font-bold mb-6">Aktivitas Terbaru</h2>
        <p className="text-gray-500 text-center py-8">Belum ada aktivitas.</p>
      </div>
    );
  }

  return (
    <div className="bg-white rounded-2xl shadow p-6 h-[380px] min-w-0 overflow-hidden">

      <h2 className="text-2xl font-bold mb-6">
        Aktivitas Terbaru
      </h2>

      <div className="space-y-6 max-h-[300px] overflow-y-auto pr-2">

        {items.map((item, index) => (
          <div key={index} className="flex gap-4">

            <div className={`w-1 rounded ${item.color}`} />

            <div className="min-w-0">

              <p className="font-semibold leading-snug break-words">
                {item.title}
              </p>

              <p className="text-gray-500 text-sm">
                {item.time}
              </p>

            </div>

          </div>
        ))}

      </div>

    </div>
  );
}

export default RecentActivity;
