function DashboardLatestSlip() {
  const slips = [
    {
      nama: "Ahmad Fauzi",
      periode: "Juli 2026",
      status: "Sudah Dibagikan",
    },
    {
      nama: "Budi Santoso",
      periode: "Juli 2026",
      status: "Sudah Dibagikan",
    },
    {
      nama: "Rina Amelia",
      periode: "Juli 2026",
      status: "Belum Dibagikan",
    },
  ];

  return (
    <div className="bg-white rounded-2xl shadow-md p-6">

      <h2 className="text-2xl font-bold mb-6">
        Slip Gaji Terbaru
      </h2>

      <table className="w-full">

        <thead className="border-b">

          <tr className="text-left text-gray-500">

            <th className="pb-3">Pegawai</th>
            <th>Periode</th>
            <th>Status</th>

          </tr>

        </thead>

        <tbody>

          {slips.map((item, index) => (

            <tr
              key={index}
              className="border-b last:border-none"
            >

              <td className="py-4 font-medium">
                {item.nama}
              </td>

              <td>{item.periode}</td>

              <td>

                <span
                  className={`px-3 py-1 rounded-full text-sm ${
                    item.status === "Sudah Dibagikan"
                      ? "bg-green-100 text-green-700"
                      : "bg-red-100 text-red-700"
                  }`}
                >
                  {item.status}
                </span>

              </td>

            </tr>

          ))}

        </tbody>

      </table>

    </div>
  );
}

export default DashboardLatestSlip;