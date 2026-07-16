import { useState } from "react";
import AdminLayout from "../../layouts/AdminLayout";
import PageTransition from "../../components/common/PageTransition";
import api from "../../services/api";

import UploadHeader from "../../components/upload/UploadHeader";
import UploadDropzone from "../../components/upload/UploadDropzone";
import UploadNote from "../../components/upload/UploadNote";
import UploadButton from "../../components/upload/UploadButton";
import UploadHistory from "../../components/upload/UploadHistory";

const numericReviewColumns = new Set([
  "bulan", "tahun", "nogaji", "kdjns", "kdgol", "gjpokok", "tjistri",
  "tjanak", "tjupns", "tjstruk", "tjfungs", "tjdaerah", "tjpencil",
  "tjlain", "tjkompen", "pembul", "tjberas", "tjpph", "potpfkbul",
  "potpfk2", "potpfk10", "potpph", "potswrum", "potkelbtj", "potlain",
  "pottabrum", "bersih", "kdkawin", "kdjab", "thngj",
  "bpjs", "bpjs2",
]);

const normalizeNumber = (value) => {
  if (value === null || value === undefined || value === "") return null;
  const raw = String(value).trim();
  if (!raw) return null;
  const normalized = raw
    .replace(/rp/gi, "")
    .replace(/\s/g, "")
    .replace(/\./g, "")
    .replace(",", ".");

  return Number.isFinite(Number(normalized)) ? Number(normalized) : null;
};

const getReviewErrors = (data) => {
  const errors = [];
  if (!String(data?.nip || "").trim()) errors.push("NIP kosong");
  if (!String(data?.nmpeg || data?.nama || "").trim()) errors.push("Nama pegawai kosong");

  if (data?.thngj !== undefined && String(data.thngj || "").trim()) {
    const year = normalizeNumber(data.thngj);
    if (year === null || year < 2000 || year > 2100) errors.push("THNGJ tidak valid");
  }

  numericReviewColumns.forEach((column) => {
    if (data?.[column] === undefined || !String(data[column] || "").trim()) return;
    if (normalizeNumber(data[column]) === null) errors.push(`${column.toUpperCase()} harus angka`);
  });

  return errors;
};

function UploadSlip() {
  const [file, setFile] = useState(null);
  const [bulan, setBulan] = useState("");
  const [tahun, setTahun] = useState(new Date().getFullYear().toString());
  const [uploading, setUploading] = useState(false);
  const [progress, setProgress] = useState(0);
  const [message, setMessage] = useState("");
  const [refreshKey, setRefreshKey] = useState(0);
  const [preview, setPreview] = useState(null);
  const [previewPage, setPreviewPage] = useState(1);
  const [reviewToken, setReviewToken] = useState("");
  const [reviewChanges, setReviewChanges] = useState({});

  const validateBaseInput = () => {
    if (!file) {
      setMessage("Silakan pilih file Excel terlebih dahulu.");
      return false;
    }
    if (!bulan) {
      setMessage("Silakan pilih bulan.");
      return false;
    }
    if (!tahun) {
      setMessage("Silakan pilih tahun.");
      return false;
    }

    return true;
  };

  const handlePreview = async () => {
    if (!validateBaseInput()) return;

    setUploading(true);
    setProgress(0);
    setMessage("");
    setPreview(null);
    setPreviewPage(1);
    setReviewToken("");
    setReviewChanges({});

    try {
      const result = await api.previewImportGaji({ file, page: 1 });
      const payload = result?.data || result;
      setPreview(payload);
      setReviewToken(payload?.review_token || "");
      setMessage(result?.message || "Preview Excel berhasil dibuat. Silakan cek dan edit data sebelum import.");
    } catch (err) {
      setMessage(err.message || "Preview Excel gagal.");
    } finally {
      setUploading(false);
    }
  };

  const handleCellChange = (rowIndex, field, value) => {
    const rowNumber = preview?.rows?.[rowIndex]?.row_number;
    if (!rowNumber) return;

    const currentData = {
      ...(reviewChanges[rowNumber] || preview?.rows?.[rowIndex]?.data || {}),
      [field]: value,
    };

    setReviewChanges((currentChanges) => {
      return {
        ...currentChanges,
        [rowNumber]: currentData,
      };
    });

    setPreview((current) => {
      if (!current) return current;

      const rows = current.rows.map((row, index) => {
        if (index !== rowIndex) return row;

        const data = currentData;
        const errors = getReviewErrors(data);
        const nextValid = errors.length === 0;
        const prevValid = row.valid;
        const validDelta = nextValid ? (prevValid ? 0 : 1) : (prevValid ? -1 : 0);
        const invalidDelta = nextValid ? (prevValid ? 0 : -1) : (prevValid ? 1 : 0);

        return {
          ...row,
          data,
          errors,
          valid: nextValid,
          validDelta,
          invalidDelta,
        };
      });

      const changedRow = rows[rowIndex];
      const nextValidCount = current.valid + (changedRow?.validDelta || 0);
      const nextInvalidCount = current.invalid + (changedRow?.invalidDelta || 0);

      return {
        ...current,
        rows: rows.map((row) => {
          if (row.row_number !== rowNumber) return row;
          const { validDelta, invalidDelta, ...cleanRow } = row;
          return cleanRow;
        }),
        valid: nextValidCount,
        invalid: nextInvalidCount,
      };
    });
  };

  const loadPreviewPage = async (nextPage) => {
    const token = reviewToken || preview?.review_token;
    if (!token || nextPage < 1) return;

    setUploading(true);
    setMessage("");

    try {
      const result = await api.previewImportGaji({
        reviewToken: token,
        page: nextPage,
      });
      const payload = result?.data || result;
      const rows = (payload?.rows || []).map((row) => {
        const edited = reviewChanges[row.row_number];
        if (!edited) return row;

        const data = { ...row.data, ...edited };
        const errors = getReviewErrors(data);

        return {
          ...row,
          data,
          errors,
          valid: errors.length === 0,
        };
      });

      setPreview({
        ...payload,
        rows,
        valid: typeof payload?.valid === "number"
          ? payload.valid
          : rows.filter((row) => row.valid).length,
        invalid: typeof payload?.invalid === "number"
          ? payload.invalid
          : rows.filter((row) => !row.valid).length,
      });
      setReviewToken(payload?.review_token || token);
      setPreviewPage(nextPage);
    } catch (err) {
      setMessage(err.message || "Gagal memuat halaman review.");
    } finally {
      setUploading(false);
    }
  };

  const handleImportReviewed = async () => {
    if (!preview?.rows?.length) {
      setMessage("Silakan review Excel terlebih dahulu.");
      return;
    }

    const invalidRows = preview.rows.filter((row) => !row.valid);
    if (invalidRows.length) {
      setMessage(`Masih ada ${invalidRows.length} baris yang belum valid. Perbaiki dulu sebelum import.`);
      return;
    }

    setUploading(true);
    setProgress(0);
    setMessage("");

    try {
      const rows = Object.entries(reviewChanges).map(([row_number, data]) => ({
        row_number: Number(row_number),
        data,
      }));
      const result = await api.importReviewedGaji({
        bulan,
        tahun,
        rows,
        reviewToken: reviewToken || preview.review_token,
      });
      setMessage(result?.message || "Import hasil review berhasil.");
      setFile(null);
      setPreview(null);
      setPreviewPage(1);
      setReviewToken("");
      setReviewChanges({});
      setRefreshKey((k) => k + 1);
    } catch (err) {
      setMessage(err.message || "Import hasil review gagal.");
    } finally {
      setUploading(false);
    }
  };

  // Header berasal langsung dari Excel agar setiap kolom dapat diperiksa dan diperbaiki.
  const previewColumns = preview?.headers || [];
  const totalPages = preview ? Math.max(1, Math.ceil(preview.total / preview.preview_limit)) : 1;

  return (
    <AdminLayout>
      <PageTransition>
        <div className="space-y-8">
          <UploadHeader />
          <UploadDropzone
            file={file}
            onFileSelect={setFile}
            bulan={bulan}
            onBulanChange={setBulan}
            tahun={tahun}
            onTahunChange={setTahun}
          />
          <UploadNote />

          {message && (
            <div className={`p-4 rounded-xl text-sm font-semibold ${
              message.toLowerCase().includes("berhasil") || message.toLowerCase().includes("sukses")
                ? "bg-green-50 text-green-700 border border-green-200"
                : "bg-red-50 text-red-700 border border-red-200"
            }`}>
              {message}
            </div>
          )}

          <UploadButton
            onClick={handlePreview}
            disabled={uploading || !file}
            loading={uploading}
            progress={progress}
            label={preview ? "Review Ulang Excel" : "Review Excel"}
          />

          {preview && (
            <div className="space-y-4 rounded-2xl bg-white p-6 shadow-md">
              <div className="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                  <h2 className="text-2xl font-bold text-slate-800">Review Data Excel</h2>
                  <p className="text-sm text-gray-500">
                    Total {preview.total} baris, valid {preview.valid}, perlu diperbaiki {preview.invalid}.
                    {preview.total > preview.rows.length
                      ? ` Halaman ${previewPage} dari ${totalPages}, menampilkan ${preview.rows.length} baris.`
                      : ""}
                  </p>
                </div>
                <div className="flex flex-wrap gap-3">
                  <button
                    type="button"
                    onClick={() => loadPreviewPage(previewPage - 1)}
                    disabled={uploading || previewPage <= 1}
                    className="rounded-xl border border-gray-200 px-5 py-3 font-semibold text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-60"
                  >
                    Sebelumnya
                  </button>
                  <button
                    type="button"
                    onClick={() => loadPreviewPage(previewPage + 1)}
                    disabled={uploading || !preview.has_more}
                    className="rounded-xl border border-gray-200 px-5 py-3 font-semibold text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-60"
                  >
                    Berikutnya
                  </button>
                  <button
                    type="button"
                    onClick={handleImportReviewed}
                    disabled={uploading || preview.invalid > 0 || !reviewToken}
                    className="rounded-xl bg-green-700 px-6 py-3 font-semibold text-white transition hover:bg-green-800 disabled:cursor-not-allowed disabled:opacity-60"
                  >
                    {uploading ? "Mengimport..." : "Import Data yang Sudah Direview"}
                  </button>
                </div>
              </div>

              <div className="overflow-x-auto rounded-xl border border-gray-200">
                <table className="min-w-max w-full text-sm">
                  <thead className="bg-green-700 text-white">
                    <tr>
                      <th className="sticky left-0 z-20 bg-green-700 px-3 py-3 text-left">Baris</th>
                      <th className="sticky left-[68px] z-20 bg-green-700 px-3 py-3 text-left">Status</th>
                      {previewColumns.map((column) => (
                        <th key={column} className="px-3 py-3 text-left uppercase">
                          {column}
                        </th>
                      ))}
                    </tr>
                  </thead>
                  <tbody>
                    {preview.rows.map((row, rowIndex) => {
                      const mergedRow = {
                        ...row,
                        data: reviewChanges[row.row_number]
                          ? { ...row.data, ...reviewChanges[row.row_number] }
                          : row.data,
                      };
                      const errors = getReviewErrors(mergedRow.data);
                      mergedRow.errors = errors;
                      mergedRow.valid = errors.length === 0;

                      return (
                      <tr key={`${row.row_number}-${rowIndex}`} className="border-b align-top">
                        <td className="sticky left-0 z-10 bg-white px-3 py-2 font-semibold">{row.row_number}</td>
                        <td className="sticky left-[68px] z-10 bg-white px-3 py-2">
                          <span className={`rounded-full px-3 py-1 text-xs font-semibold ${
                            mergedRow.valid ? "bg-green-100 text-green-700" : "bg-red-100 text-red-700"
                          }`}>
                            {mergedRow.valid ? "Valid" : mergedRow.errors.join(", ")}
                          </span>
                        </td>
                        {previewColumns.map((column) => (
                          <td key={column} className="px-3 py-2">
                            <input
                              value={mergedRow.data?.[column] ?? ""}
                              onChange={(event) => handleCellChange(rowIndex, column, event.target.value)}
                              className="w-36 rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600"
                            />
                          </td>
                        ))}
                      </tr>
                      );
                    })}
                  </tbody>
                </table>
              </div>
            </div>
          )}

          <UploadHistory key={refreshKey} />
        </div>
      </PageTransition>
    </AdminLayout>
  );
}

export default UploadSlip;
