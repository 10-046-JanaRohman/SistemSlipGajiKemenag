import UserSlipRow from "./UserSlipRow";

const slip = [
  {
    periode: "Juli 2026",
    total: "Rp5.700.000",
    status: "Tersedia",
  },
  {
    periode: "Juni 2026",
    total: "Rp5.600.000",
    status: "Tersedia",
  },
  {
    periode: "Mei 2026",
    total: "Rp5.500.000",
    status: "Tersedia",
  },
];

function UserSlipTable() {
  return (
    <div className="bg-white rounded-2xl shadow-md overflow-hidden">

      <table className="w-full">

        <thead className="bg-green-700 text-white">

          <tr>

            <th className="py-4">Periode</th>

            <th>Total Gaji</th>

            <th>Status</th>

            <th>Aksi</th>

          </tr>

        </thead>

        <tbody>

          {slip.map((item) => (

            <UserSlipRow
              key={item.periode}
              periode={item.periode}
              total={item.total}
              status={item.status}
            />

          ))}

        </tbody>

      </table>

    </div>
  );
}

export default UserSlipTable;