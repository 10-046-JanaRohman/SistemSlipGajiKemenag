function PegawaiPagination() {
  return (
    <div className="flex justify-end items-center gap-4 mt-6">

      <button className="border px-5 py-2 rounded-lg hover:bg-gray-100">
        Sebelumnya
      </button>

      <button className="w-10 h-10 rounded-lg bg-green-700 text-white">
        1
      </button>

      <button className="w-10 h-10 rounded-lg border">
        2
      </button>

      <button className="border px-5 py-2 rounded-lg hover:bg-gray-100">
        Berikutnya
      </button>

    </div>
  );
}

export default PegawaiPagination;