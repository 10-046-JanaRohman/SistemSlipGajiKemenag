function DashboardImportTable() {
  const imports = [
    {
      file: "Gaji_Juli_2026.xlsx",
      bulan: "Juli",
      tahun: "2026",
      status: "Berhasil",
      waktu: "08 Jul 2026 09:10",
    },
    {
      file: "Gaji_Juni_2026.xlsx",
      bulan: "Juni",
      tahun: "2026",
      status: "Berhasil",
      waktu: "01 Jul 2026 08:55",
    },
  ];

  return (
    <div className="bg-white rounded-2xl shadow-md p-6">

      <h2 className="text-2xl font-bold mb-6">
        Import Excel Terakhir
      </h2>

      <table className="w-full">

        <thead className="border-b">

          <tr className="text-left text-gray-500">

            <th className="pb-3">File</th>
            <th>Bulan</th>
            <th>Tahun</th>
            <th>Status</th>
            <th>Waktu</th>

          </tr>

        </thead>

        <tbody>

          {imports.map((item, index) => (

            <tr
              key={index}
              className="border-b last:border-none"
            >

              <td className="py-4 font-medium">
                {item.file}
              </td>

              <td>{item.bulan}</td>

              <td>{item.tahun}</td>

              <td>

                <span className="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
                  {item.status}
                </span>

              </td>

              <td>{item.waktu}</td>

            </tr>

          ))}

        </tbody>

      </table>

    </div>
  );
}

export default DashboardImportTable;