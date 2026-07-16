import { UploadCloud, FileSpreadsheet } from "lucide-react";

const months = [
  { value: "1", label: "Januari" },
  { value: "2", label: "Februari" },
  { value: "3", label: "Maret" },
  { value: "4", label: "April" },
  { value: "5", label: "Mei" },
  { value: "6", label: "Juni" },
  { value: "7", label: "Juli" },
  { value: "8", label: "Agustus" },
  { value: "9", label: "September" },
  { value: "10", label: "Oktober" },
  { value: "11", label: "November" },
  { value: "12", label: "Desember" },
];

function UploadDropzone({
  file,
  onFileSelect,
  onValidationError,
  bulan,
  onBulanChange,
  tahun,
  onTahunChange,
}) {
  const handleFileChange = (event) => {
    const selectedFile = event.target.files?.[0] || null;

    if (!selectedFile) {
      onFileSelect(null);
      return;
    }

    const extension = selectedFile.name.split(".").pop()?.toLowerCase();
    const supportedExtensions = ["xlsx", "xls", "csv"];

    if (!supportedExtensions.includes(extension)) {
      onFileSelect(null);
      onValidationError?.("Format file harus .xlsx, .xls, atau .csv.");
      event.target.value = "";
      return;
    }

    if (selectedFile.size > 20 * 1024 * 1024) {
      onFileSelect(null);
      onValidationError?.("Ukuran file maksimal 20 MB.");
      event.target.value = "";
      return;
    }

    onValidationError?.("");
    onFileSelect(selectedFile);
  };

  return (
    <div className="bg-white rounded-2xl shadow-md p-10">
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <label className="block">
          <span className="block text-sm font-semibold text-gray-700 mb-2">Bulan</span>
          <select
            value={bulan}
            onChange={(event) => onBulanChange(event.target.value)}
            className="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-600"
          >
            <option value="">Pilih bulan</option>
            {months.map((item) => (
              <option key={item.value} value={item.value}>
                {item.label}
              </option>
            ))}
          </select>
        </label>

        <label className="block">
          <span className="block text-sm font-semibold text-gray-700 mb-2">Tahun</span>
          <input
            type="number"
            value={tahun}
            onChange={(event) => onTahunChange(event.target.value)}
            className="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-600"
            min="2000"
            max="2100"
          />
        </label>
      </div>

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

        <label className="mt-6 bg-green-700 hover:bg-green-800 text-white px-8 py-4 rounded-xl flex items-center gap-3 transition cursor-pointer">

          <FileSpreadsheet size={22} />

          Pilih File Excel / CSV

          <input
            type="file"
            accept=".xlsx,.xls,.csv"
            onChange={handleFileChange}
            className="hidden"
          />
        </label>

        <p className="mt-6 text-gray-400">

          {file?.name || "Belum ada file dipilih"}

        </p>

      </div>

    </div>
  );
}

export default UploadDropzone;
