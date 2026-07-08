function RiwayatPagination() {
  return (
    <div className="flex justify-end items-center gap-3">

      <button className="border rounded-lg px-4 py-2 hover:bg-gray-100 transition">

        Sebelumnya

      </button>

      <button className="bg-green-700 text-white rounded-lg px-4 py-2">

        1

      </button>

      <button className="border rounded-lg px-4 py-2 hover:bg-gray-100 transition">

        2

      </button>

      <button className="border rounded-lg px-4 py-2 hover:bg-gray-100 transition">

        Berikutnya

      </button>

    </div>
  );
}

export default RiwayatPagination;