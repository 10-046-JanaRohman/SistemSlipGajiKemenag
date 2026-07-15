import { Eye, Download } from "lucide-react";

function UserHistoryRow({
  periode,
  tanggal,
  status,
}) {
  return (
    <tr className="border-b hover:bg-gray-50 text-center">

      <td className="py-4">{periode}</td>

      <td>{tanggal}</td>

      <td>

        <span className="px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-semibold">
          {status}
        </span>

      </td>

      <td>

        <div className="flex justify-center gap-3">

          <button className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <Eye size={16} />
            Lihat
          </button>

          <button className="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <Download size={16} />
            PDF
          </button>

        </div>

      </td>

    </tr>
  );
}

export default UserHistoryRow;