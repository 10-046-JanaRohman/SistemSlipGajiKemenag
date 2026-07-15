import { Plus } from "lucide-react";

function PegawaiHeader() {
  return (
    <div className="flex justify-between items-center">

      <div>

        <h1 className="text-5xl font-bold text-slate-800">
          Pegawai
        </h1>

        <p className="text-gray-500 mt-2">
          Kelola seluruh data pegawai.
        </p>

      </div>

      <button
        className="bg-green-700 hover:bg-green-800 text-white px-6 py-3 rounded-xl flex items-center gap-2 transition"
      >

        <Plus size={20} />

        Tambah Pegawai

      </button>

    </div>
  );
}

export default PegawaiHeader;