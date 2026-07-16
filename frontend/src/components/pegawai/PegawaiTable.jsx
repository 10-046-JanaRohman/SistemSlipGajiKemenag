import { Pencil, Trash2, Loader2 } from "lucide-react";

function PegawaiTable({ data = [], loading, onEdit, onDelete }) {
  const rows = Array.isArray(data)
    ? data.filter((item) => item && (item.nip || item.nama || item.jabatan || item.golongan))
    : [];

  if (loading) {
    return (
      <div className="bg-white rounded-2xl shadow overflow-hidden flex items-center justify-center py-20">
        <Loader2 size={32} className="animate-spin text-green-700" />
        <span className="ml-3 text-gray-500">Memuat data...</span>
      </div>
    );
  }

  if (!rows.length) {
    return (
      <div className="bg-white rounded-2xl shadow overflow-hidden flex items-center justify-center py-20">
        <p className="text-gray-500">Tidak ada data pegawai.</p>
      </div>
    );
  }

  return (
    <div className="bg-white rounded-2xl shadow overflow-x-auto">

      <table className="w-full min-w-[900px] text-sm">

        <thead className="bg-green-700 text-white">

          <tr>

            <th className="px-5 py-3 text-left font-semibold">NIP</th>

            <th className="px-5 py-3 text-left font-semibold">Nama</th>

            <th className="px-5 py-3 text-left font-semibold">Jabatan</th>

            <th className="px-5 py-3 text-left font-semibold">Golongan</th>

            <th className="px-5 py-3 text-center font-semibold">Status</th>

            <th className="px-5 py-3 text-center font-semibold">Aksi</th>

          </tr>

        </thead>

        <tbody>

          {rows.map((item) => (
            <tr
              key={item.id || item.nip}
              className="border-b hover:bg-gray-50 transition"
            >
              <td className="px-5 py-3 whitespace-nowrap">
                {item.nip || "-"}
              </td>

              <td className="px-5 py-3 max-w-[260px] truncate">
                {item.nama || "-"}
              </td>

              <td className="px-5 py-3 max-w-[240px] truncate">
                {item.jabatan || "-"}
              </td>

              <td className="px-5 py-3 whitespace-nowrap">
                {item.golongan || "-"}
              </td>

              <td className="px-5 py-3 text-center">

                <span
                  className={`px-4 py-1 rounded-full text-sm font-semibold ${
                    (item.status || "Aktif") === "Aktif"
                      ? "bg-green-100 text-green-700"
                      : "bg-red-100 text-red-700"
                  }`}
                >
                  {item.status || "Aktif"}
                </span>

              </td>

              <td className="px-5 py-3">

                <div className="flex justify-center gap-3">

                  <button
                    onClick={() => onEdit(item)}
                    className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition"
                  >
                    <Pencil size={16} />
                    Edit
                  </button>

                  <button
                    onClick={() => onDelete(item)}
                    className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition"
                  >
                    <Trash2 size={16} />
                    Hapus
                  </button>

                </div>

              </td>

            </tr>
          ))}

        </tbody>

      </table>

    </div>
  );
}

export default PegawaiTable;
