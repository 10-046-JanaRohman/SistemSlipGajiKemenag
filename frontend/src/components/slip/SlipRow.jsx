import { Link } from "react-router-dom";

const formatRupiah = (val) => {
  if (val === undefined || val === null) return "-";
  const num = typeof val === "string" ? parseFloat(val.replace(/[^0-9,-]/g, "").replace(",", ".")) : val;
  if (!Number.isFinite(num)) return "-";
  return new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0 }).format(num);
};

function SlipRow({
  nip,
  nama,
  bulan,
  gaji,
  status,
  id,
}) {
  const hasValidId = id !== undefined && id !== null && id !== "";
  
  return (
    <tr className="border-b hover:bg-gray-50">

      <td className="px-5 py-3 whitespace-nowrap">{nip}</td>

      <td className="px-5 py-3 max-w-[260px] truncate">{nama}</td>

      <td className="px-5 py-3 whitespace-nowrap">{bulan}</td>

      <td className="px-5 py-3 text-right whitespace-nowrap">{formatRupiah(gaji)}</td>

      <td className="px-5 py-3 text-center">

        <span
          className={`px-3 py-1 rounded-full text-sm font-semibold ${
            status === "Sudah" || status === "Dibagikan" || status === "Sudah Dibagikan"
              ? "bg-green-100 text-green-700"
              : "bg-red-100 text-red-700"
          }`}
        >
          {status}
        </span>

      </td>

      <td className="px-5 py-3 text-center">

        {hasValidId ? (
          <Link
            to={`/admin/detail-slip?id=${id}`}
            className="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition"
          >
            Detail
          </Link>
        ) : (
          <button disabled className="bg-gray-300 text-gray-500 px-4 py-2 rounded-lg cursor-not-allowed">
            Detail
          </button>
        )}

      </td>

    </tr>
  );
}

export default SlipRow;
