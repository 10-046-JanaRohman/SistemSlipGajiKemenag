import { Link } from "react-router-dom";

function SlipRow({
  nip,
  nama,
  bulan,
  gaji,
  status,
}) {
  return (
    <tr className="border-b hover:bg-gray-50 text-center">

      <td className="py-4">{nip}</td>

      <td>{nama}</td>

      <td>{bulan}</td>

      <td>{gaji}</td>

      <td>

        <span
          className={`px-3 py-1 rounded-full text-sm font-semibold ${
            status === "Sudah"
              ? "bg-green-100 text-green-700"
              : "bg-red-100 text-red-700"
          }`}
        >
          {status}
        </span>

      </td>

      <td>

        <Link to="/detail-slip">

          <button className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">

            Detail

          </button>

        </Link>

      </td>

    </tr>
  );
}

export default SlipRow;