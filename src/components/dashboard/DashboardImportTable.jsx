import { formatPeriode } from "../../utils/formatPeriode";

function DashboardImportTable({ data = {} }) {
  const importTerakhir = data?.import_terakhir ?? null;

  const imports = importTerakhir
    ? [
        {
          file: importTerakhir.nama_file || importTerakhir.file || "-",
          periode: importTerakhir.bulan ? formatPeriode(importTerakhir.bulan, importTerakhir.tahun) : "-",
          status: importTerakhir.status || "Berhasil",
          waktu: importTerakhir.created_at
            ? new Date(importTerakhir.created_at).toLocaleDateString("id-ID", {
                day: "2-digit",
                month: "short",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit",
              })
            : "-",
        },
      ]
    : [];

  return (
    <div className="bg-white rounded-2xl shadow-md p-6 min-w-0">

      <h2 className="text-2xl font-bold mb-6">
        Import Excel Terakhir
      </h2>

      {!imports.length ? (
        <p className="text-gray-500 text-center py-8">Belum ada riwayat import.</p>
      ) : (
        <div className="overflow-x-auto">
          <table className="w-full min-w-[640px]">

            <thead className="border-b">

              <tr className="text-left text-gray-500">

                <th className="pb-3">File</th>
                <th>Periode</th>
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

                  <td className="py-4 font-medium max-w-[280px] truncate">
                    {item.file}
                  </td>

                  <td>{item.periode}</td>

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
      )}

    </div>
  );
}

export default DashboardImportTable;
