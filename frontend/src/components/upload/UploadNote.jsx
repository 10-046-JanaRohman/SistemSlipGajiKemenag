import {
  FileSpreadsheet,
  Download,
  CircleCheck,
} from "lucide-react";

function UploadNote() {
  return (
    <div className="bg-white rounded-2xl shadow-md p-8">

      <div className="flex items-center gap-3 mb-6">

        <FileSpreadsheet
          className="text-green-700"
          size={26}
        />

        <h2 className="text-2xl font-bold">
          Panduan Import
        </h2>

      </div>

      <button className="mb-8 flex items-center gap-3 bg-green-50 text-green-700 hover:bg-green-100 px-5 py-3 rounded-xl transition">

        <Download size={20} />

        Download Template Excel

      </button>

      <div className="space-y-4">

        <Item text="Format file harus .xlsx" />

        <Item text="Ukuran maksimum file 20 MB." />

        <Item text="Gunakan template resmi Kanwil Kementerian Agama Provinsi Lampung." />

        <Item text="Jangan mengubah nama maupun urutan kolom pada template." />

        <Item text="Pastikan data pegawai telah lengkap sebelum proses import." />

      </div>

    </div>
  );
}

function Item({ text }) {
  return (
    <div className="flex gap-3">

      <CircleCheck
        size={20}
        className="text-green-700 mt-1"
      />

      <p className="text-gray-600">
        {text}
      </p>

    </div>
  );
}

export default UploadNote;