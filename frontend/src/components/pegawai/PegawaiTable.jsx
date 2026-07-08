import { Pencil, Trash2 } from "lucide-react";

const pegawai = [
  {
    nip: "19871231",
    nama: "Ahmad Fauzi",
    jabatan: "Analis Keuangan",
    golongan: "III/b",
    status: "Aktif",
  },
  {
    nip: "19880121",
    nama: "Budi Santoso",
    jabatan: "Staff Keuangan",
    golongan: "III/a",
    status: "Aktif",
  },
  {
    nip: "19901110",
    nama: "Rina Amelia",
    jabatan: "Kasubbag TU",
    golongan: "IV/a",
    status: "Aktif",
  },
  {
    nip: "19921212",
    nama: "Siti Rahma",
    jabatan: "Bendahara",
    golongan: "III/c",
    status: "Nonaktif",
  },
];

function PegawaiTable() {
  return (
    <div className="bg-white rounded-2xl shadow overflow-hidden">

      <table className="w-full">

        <thead className="bg-green-700 text-white">

          <tr>

            <th className="p-4">NIP</th>

            <th>Nama</th>

            <th>Jabatan</th>

            <th>Golongan</th>

            <th>Status</th>

            <th>Aksi</th>

          </tr>

        </thead>

        <tbody>

          {pegawai.map((item) => (

            <tr
              key={item.nip}
              className="border-b hover:bg-gray-50 transition"
            >

              <td className="p-4 text-center">
                {item.nip}
              </td>

              <td className="text-center">
                {item.nama}
              </td>

              <td className="text-center">
                {item.jabatan}
              </td>

              <td className="text-center">
                {item.golongan}
              </td>

              <td className="text-center">

                <span
                  className={`px-4 py-1 rounded-full text-sm font-semibold ${
                    item.status === "Aktif"
                      ? "bg-green-100 text-green-700"
                      : "bg-red-100 text-red-700"
                  }`}
                >
                  {item.status}
                </span>

              </td>

              <td>

                <div className="flex justify-center gap-3">

                  <button className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">

                    <Pencil size={16} />

                    Edit

                  </button>

                  <button className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">

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