import RiwayatRow from "./RiwayatRow";

const data = [
  {
    nip: "19871231",
    nama: "Ahmad Fauzi",
    periode: "Juli 2026",
    tanggal: "08 Juli 2026",
    status: "Sudah Dibagikan",
  },
  {
    nip: "19880121",
    nama: "Budi Santoso",
    periode: "Juli 2026",
    tanggal: "08 Juli 2026",
    status: "Sudah Dibagikan",
  },
  {
    nip: "19901110",
    nama: "Rina Amelia",
    periode: "Juli 2026",
    tanggal: "08 Juli 2026",
    status: "Belum Dibagikan",
  },
  {
    nip: "19921212",
    nama: "Siti Rahma",
    periode: "Juli 2026",
    tanggal: "08 Juli 2026",
    status: "Sudah Dibagikan",
  },
];

function RiwayatTable() {
  return (
    <div className="bg-white rounded-2xl shadow overflow-hidden">

      <table className="w-full">

        <thead className="bg-green-700 text-white">

          <tr>

            <th className="py-4">NIP</th>

            <th>Nama Pegawai</th>

            <th>Periode</th>

            <th>Tanggal Dibagikan</th>

            <th>Status</th>

            <th>Aksi</th>

          </tr>

        </thead>

        <tbody>

          {data.map((item) => (

            <RiwayatRow
              key={item.nip}
              nip={item.nip}
              nama={item.nama}
              periode={item.periode}
              tanggal={item.tanggal}
              status={item.status}
            />

          ))}

        </tbody>

      </table>

    </div>
  );
}

export default RiwayatTable;