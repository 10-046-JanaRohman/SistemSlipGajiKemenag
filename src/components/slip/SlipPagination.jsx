function SlipPagination({ meta, page, onPageChange }) {
  const { current_page = 1, last_page = 1, total = 0 } = meta || {};

  if (!total) return null;

  const currentPage = current_page || page;
  const pages = Array.from(
    new Set([
      1,
      currentPage - 2,
      currentPage - 1,
      currentPage,
      currentPage + 1,
      currentPage + 2,
      last_page,
    ].filter((p) => p >= 1 && p <= last_page))
  ).sort((a, b) => a - b);

  return (
    <div className="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3">

      <p className="text-gray-500 text-sm">
        Total: {total} slip
      </p>

      <div className="flex flex-wrap items-center gap-2">

        <button
          disabled={page <= 1}
          onClick={() => onPageChange(page - 1)}
          className="border rounded-lg px-4 py-2 hover:bg-gray-100 transition disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Sebelumnya
        </button>

        {pages.map((p, index) => (
          <span key={p} className="flex items-center gap-2">
            {index > 0 && p - pages[index - 1] > 1 && (
              <span className="text-gray-400">...</span>
            )}
            <button
              onClick={() => onPageChange(p)}
              className={`rounded-lg px-4 py-2 ${
                p === page
                  ? "bg-green-700 text-white"
                  : "border hover:bg-gray-100 transition"
              }`}
            >
              {p}
            </button>
          </span>
        ))}

        <button
          disabled={page >= last_page}
          onClick={() => onPageChange(page + 1)}
          className="border rounded-lg px-4 py-2 hover:bg-gray-100 transition disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Berikutnya
        </button>

      </div>

    </div>
  );
}

export default SlipPagination;
