import UserHistoryRow from "./UserHistoryRow";

const history = [
  {
    periode: "Juli 2026",
    tanggal: "31 Juli 2026",
    status: "Tersedia",
  },
  {
    periode: "Juni 2026",
    tanggal: "30 Juni 2026",
    status: "Tersedia",
  },
  {
    periode: "Mei 2026",
    tanggal: "31 Mei 2026",
    status: "Tersedia",
  },
];

function UserHistoryTable() {
  return (
    <div className="bg-white rounded-2xl shadow-md overflow-hidden">

      <table className="w-full">

        <thead className="bg-green-700 text-white">

          <tr>

            <th className="py-4">Periode</th>

            <th>Tanggal Terbit</th>

            <th>Status</th>

            <th>Aksi</th>

          </tr>

        </thead>

        <tbody>

          {history.map((item) => (

            <UserHistoryRow
              key={item.periode}
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

export default UserHistoryTable;