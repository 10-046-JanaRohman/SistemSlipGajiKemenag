import { formatPeriode } from "../../utils/formatPeriode";

function DashboardLatestSlip({ data = {} }) {
  const slipList = data?.slip_terbaru ?? data?.slipTerbaru ?? [];
  const slips = Array.isArray(slipList) ? slipList : [];

  if (!slips.length) {
    return (
      <div className="bg-white rounded-2xl shadow-md p-6 min-w-0">
        <h2 className="text-2xl font-bold mb-6">Slip Gaji Terbaru</h2>
        <p className="text-gray-500 text-center py-8">Belum ada data slip terbaru.</p>
      </div>
    );
  }

  return (
    <div className="bg-white rounded-2xl shadow-md p-6 min-w-0">

      <h2 className="text-2xl font-bold mb-6">
        Slip Gaji Terbaru
      </h2>

      <div className="overflow-x-auto">
        <table className="w-full min-w-[560px]">

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

                <td className="py-4 font-medium max-w-[280px] truncate">
                  {item.pegawai?.nama || item.nama || "-"}
                </td>

                <td>{item.bulan ? formatPeriode(item.bulan, item.tahun) : item.periode || "-"}</td>

                <td>

                  <span
                    className={`px-3 py-1 rounded-full text-sm ${
                      item.status === "Sudah Dibagikan"
                        ? "bg-green-100 text-green-700"
                        : "bg-green-100 text-green-700"
                    }`}
                  >
                    {item.status || "Tersedia"}
                  </span>

                </td>

              </tr>

            ))}

          </tbody>

        </table>
      </div>

    </div>
  );
}

export default DashboardLatestSlip;
