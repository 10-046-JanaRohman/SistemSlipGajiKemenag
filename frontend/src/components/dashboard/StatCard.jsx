function formatRupiah(num) {
  if (num === null || num === undefined) return "0";
  const n = typeof num === "string" ? parseFloat(num) : num;
  if (isNaN(n)) return "0";
  return new Intl.NumberFormat("id-ID").format(n);
}

function StatCard({ title, value, color, isCurrency }) {
  const display = isCurrency ? `Rp ${formatRupiah(value)}` : formatRupiah(value);

  return (
    <div className="bg-white rounded-2xl shadow-sm p-6 border min-w-0">
      <p className="text-gray-500 leading-snug">
        {title}
      </p>
      <h2
        className={`mt-3 font-bold leading-tight break-words ${
          isCurrency ? "text-2xl xl:text-3xl" : "text-4xl"
        } ${color || "text-black"}`}
      >
        {display}
      </h2>

    </div>
  );
}

export default StatCard;
