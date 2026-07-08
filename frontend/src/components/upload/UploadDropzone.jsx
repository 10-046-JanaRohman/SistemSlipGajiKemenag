import { UploadCloud, FileSpreadsheet } from "lucide-react";

function UploadDropzone() {
  return (
    <div className="bg-white rounded-2xl shadow-md p-10">

      <div className="border-2 border-dashed border-green-600 rounded-2xl p-14 flex flex-col items-center">

        <UploadCloud
          size={70}
          className="text-green-700"
        />

        <h2 className="text-3xl font-bold mt-6">
          Drag & Drop File Excel
        </h2>

        <p className="text-gray-500 mt-3">
          atau
        </p>

        <button className="mt-6 bg-green-700 hover:bg-green-800 text-white px-8 py-4 rounded-xl flex items-center gap-3 transition">

          <FileSpreadsheet size={22} />

          Pilih File Excel

        </button>

        <p className="mt-6 text-gray-400">

          Belum ada file dipilih

        </p>

      </div>

    </div>
  );
}

export default UploadDropzone;