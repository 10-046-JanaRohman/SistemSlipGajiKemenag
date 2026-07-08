const history = [
  {
    file: "Gaji_Juli_2026.xlsx",
    periode: "Juli 2026",
    tanggal: "08 Juli 2026",
    status: "Berhasil",
    operator: "Admin",
  },
  {
    file: "Gaji_Juni_2026.xlsx",
    periode: "Juni 2026",
    tanggal: "08 Juni 2026",
    status: "Berhasil",
    operator: "Admin",
  },
];

function UploadHistory() {
  return (
    <div className="bg-white rounded-2xl shadow-md overflow-hidden">

      <div className="px-8 py-6 border-b">

        <h2 className="text-2xl font-bold">
          Riwayat Import Terakhir
        </h2>

      </div>

      <table className="w-full">

        <thead className="bg-green-700 text-white">

          <tr>

            <th className="py-4">Nama File</th>

            <th>Periode</th>

            <th>Tanggal Import</th>

            <th>Status</th>

            <th>Operator</th>

          </tr>

        </thead>

        <tbody>

          {history.map((item, index) => (

            <tr
              key={index}
              className="border-b hover:bg-gray-50 text-center"
            >

              <td className="py-4 font-medium">

                {item.file}

              </td>

              <td>

                {item.periode}

              </td>

              <td>

                {item.tanggal}

              </td>

              <td>

                <span className="bg-green-100 text-green-700 px-4 py-1 rounded-full text-sm">

                  {item.status}

                </span>

              </td>

              <td>

                {item.operator}

              </td>

            </tr>

          ))}

        </tbody>

      </table>

    </div>
  );
}

export default UploadHistory;