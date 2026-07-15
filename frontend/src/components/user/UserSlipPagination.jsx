function UserSlipPagination() {
  return (
    <div className="flex justify-end gap-3">

      <button className="border px-4 py-2 rounded-lg hover:bg-gray-100">
        Sebelumnya
      </button>

      <button className="bg-green-700 text-white px-4 py-2 rounded-lg">
        1
      </button>

      <button className="border px-4 py-2 rounded-lg hover:bg-gray-100">
        Berikutnya
      </button>

    </div>
  );
}

export default UserSlipPagination;