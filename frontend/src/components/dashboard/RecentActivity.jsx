function RecentActivity() {
  const data = [
    {
      title: "Upload Slip Gaji Juli",
      time: "5 menit yang lalu",
      color: "bg-green-500",
    },
    {
      title: "Import Data Excel",
      time: "20 menit yang lalu",
      color: "bg-blue-500",
    },
    {
      title: "Menambah Pegawai Baru",
      time: "1 jam yang lalu",
      color: "bg-yellow-500",
    },
    {
      title: "Menghapus Slip Lama",
      time: "Kemarin",
      color: "bg-red-500",
    },
  ];

  return (
    <div className="bg-white rounded-2xl shadow p-6 h-[380px]">

      <h2 className="text-2xl font-bold mb-6">
        Aktivitas Terbaru
      </h2>

      <div className="space-y-6">

        {data.map((item,index)=>(
          <div
            key={index}
            className="flex gap-4"
          >

            <div className={`w-1 rounded ${item.color}`} />

            <div>

              <p className="font-semibold">
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