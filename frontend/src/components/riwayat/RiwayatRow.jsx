import { Eye } from "lucide-react";
import { Link } from "react-router-dom";

function RiwayatRow({
  id,
  nip,
  nama,
  periode,
  tanggal,
  status,
}) {
  const hasValidId = id !== undefined && id !== null && id !== "";

  return (
    <tr className="border-b hover:bg-gray-50 transition">

      <td className="px-5 py-3 whitespace-nowrap">
        {nip}
      </td>

      <td className="px-5 py-3 max-w-[260px] truncate">
        {nama}
      </td>

      <td className="px-5 py-3 whitespace-nowrap">
        {periode}
      </td>

      <td className="px-5 py-3 whitespace-nowrap">
        {tanggal}
      </td>

      <td className="px-5 py-3 text-center">

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

      <td className="px-5 py-3 text-center">

        {hasValidId ? (
          <Link
            to={`/admin/detail-slip?id=${id}`}
            className="inline-flex items-center justify-center gap-2 mx-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition"
          >
            <Eye size={16} />
            Detail
          </Link>
        ) : (
          <button disabled className="bg-gray-300 text-gray-500 px-4 py-2 rounded-lg flex items-center gap-2 mx-auto cursor-not-allowed">
            <Eye size={16} />
            Detail
          </button>
        )}

      </td>

    </tr>
  );
}

export default RiwayatRow;
