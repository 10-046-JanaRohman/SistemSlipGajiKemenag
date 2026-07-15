import { Eye } from "lucide-react";
import { Link } from "react-router-dom";

function RiwayatRow({
  nip,
  nama,
  periode,
  tanggal,
  status,
}) {
  return (
    <tr className="border-b hover:bg-gray-50 transition text-center">

      <td className="py-4">
        {nip}
      </td>

      <td>
        {nama}
      </td>

      <td>
        {periode}
      </td>

      <td>
        {tanggal}
      </td>

      <td>

        <span
          className={`px-3 py-1 rounded-full text-sm font-semibold ${
            status === "Sudah Dibagikan"
              ? "bg-green-100 text-green-700"
              : "bg-yellow-100 text-yellow-700"
          }`}
        >
          {status}
        </span>

      </td>

      <td>

        <Link to="/admin/detail-slip">

          <button className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 mx-auto transition">

            <Eye size={16} />

            Detail

          </button>

        </Link>

      </td>

    </tr>
  );
}

export default RiwayatRow;