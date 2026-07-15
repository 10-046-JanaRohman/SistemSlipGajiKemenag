import SlipRow from "./SlipRow";

const slipData = [
  {
    nip: "19871231",
    nama: "Ahmad Fauzi",
    bulan: "Juli 2026",
    gaji: "Rp5.500.000",
    status: "Sudah",
  },
  {
    nip: "19880121",
    nama: "Budi Santoso",
    bulan: "Juli 2026",
    gaji: "Rp4.800.000",
    status: "Belum",
  },
  {
    nip: "19901110",
    nama: "Rina Amelia",
    bulan: "Juli 2026",
    gaji: "Rp6.200.000",
    status: "Sudah",
  },
];

function SlipTable() {
  return (
    <div className="bg-white rounded-2xl shadow-md overflow-hidden">

      <table className="w-full">

        <thead className="bg-green-700 text-white">

          <tr>

            <th className="py-4">NIP</th>

            <th>Nama</th>

            <th>Bulan</th>

            <th>Total Gaji</th>

            <th>Status</th>

            <th>Aksi</th>

          </tr>

        </thead>

        <tbody>

          {slipData.map((item) => (

            <SlipRow
              key={item.nip}
              nip={item.nip}
              nama={item.nama}
              bulan={item.bulan}
              gaji={item.gaji}
              status={item.status}
            />

          ))}

        </tbody>

      </table>

    </div>
  );
}

export default SlipTable;