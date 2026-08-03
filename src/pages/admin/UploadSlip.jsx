import { useEffect, useState } from "react";
import AdminLayout from "../../layouts/AdminLayout";
import PageTransition from "../../components/common/PageTransition";
import api from "../../services/api";

import UploadHeader from "../../components/upload/UploadHeader";
import UploadDropzone from "../../components/upload/UploadDropzone";
import UploadNote from "../../components/upload/UploadNote";
import UploadButton from "../../components/upload/UploadButton";
import UploadHistory from "../../components/upload/UploadHistory";
import { formatPeriode } from "../../utils/formatPeriode";

const numericReviewColumns = new Set([
  "bulan", "tahun", "nogaji", "kdjns", "kdgol", "gjpokok", "tjistri",
  "tjanak", "tjupns", "tjstruk", "tjfungs", "tjdaerah", "tjpencil",
  "tjlain", "tjkompen", "pembul", "tjberas", "tjpph", "potpfkbul",
  "potpfk2", "potpfk10", "potpph", "potswrum", "potkelbtj", "potlain",
  "pottabrum", "bersih", "kdkawin", "kdjab", "thngj",
  "bpjs", "bpjs2",
]);
const reviewStorageKey = () => {
  try {
    const user = JSON.parse(localStorage.getItem("user") || "null");
    return `gaji-import-review:${user?.id || user?.nip || "anonymous"}`;
  } catch {
    return "gaji-import-review:anonymous";
  }
};

const clearSavedReview = () => localStorage.removeItem(reviewStorageKey());

const normalizeNumber = (value) => {
  if (value === null || value === undefined || value === "") return null;
  const raw = String(value).trim();
  if (!raw) return null;
  if (raw === "-" || raw === "—") return 0;
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
  const [message, setMessage] = useState("");
  const [refreshKey, setRefreshKey] = useState(0);
  const [preview, setPreview] = useState(null);
  const [previewPage, setPreviewPage] = useState(1);
  const [reviewToken, setReviewToken] = useState("");
  const [reviewChanges, setReviewChanges] = useState({});
  const [reviewHydrated, setReviewHydrated] = useState(false);
  const [activeReviews, setActiveReviews] = useState([]);
  const [loadingActiveReviews, setLoadingActiveReviews] = useState(false);
  const [periodReviewStatus, setPeriodReviewStatus] = useState(null);
  const [loadingPeriodReview, setLoadingPeriodReview] = useState(false);
  const currentUser = (() => {
    try {
      return JSON.parse(localStorage.getItem("user") || "null");
    } catch {
      return null;
    }
  })();
  const isSuperAdmin = currentUser?.role === "super_admin";

  const loadActiveReviews = async () => {
    if (!isSuperAdmin) return;

    setLoadingActiveReviews(true);
    try {
      const result = await api.getActiveImportReviews();
      const payload = result?.data || result;
      setActiveReviews(Array.isArray(payload) ? payload : []);
    } catch {
      setActiveReviews([]);
    } finally {
      setLoadingActiveReviews(false);
    }
  };

  const loadPeriodReviewStatus = async (selectedBulan = bulan, selectedTahun = tahun) => {
    if (!selectedBulan || !selectedTahun) {
      setPeriodReviewStatus(null);
      return null;
    }

    setLoadingPeriodReview(true);
    try {
      const result = await api.getImportReviewStatus(selectedBulan, selectedTahun);
      const payload = result?.data || result;
      setPeriodReviewStatus(payload);
      return payload;
    } catch {
      setPeriodReviewStatus(null);
      return null;
    } finally {
      setLoadingPeriodReview(false);
    }
  };

  const handleFileSelect = (selectedFile) => {
    setFile(selectedFile);
    setPreview(null);
    setPreviewPage(1);
    setReviewToken("");
    setReviewChanges({});
    clearSavedReview();
  };

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

    const lock = await loadPeriodReviewStatus();
    if (lock?.active) {
      const review = lock.review;
      const owner = review?.created_by_name || "admin lain";
      if (lock.can_take_over) {
        setMessage(`Periode ${formatPeriode(bulan, tahun)} sedang direview oleh ${owner}. Gunakan tombol Ambil Alih Review untuk melanjutkannya.`);
      } else if (lock.is_owner) {
        setMessage(`Periode ${formatPeriode(bulan, tahun)} sedang Anda review. Lanjutkan review yang sudah ada terlebih dahulu.`);
      } else {
        setMessage(`Periode ${formatPeriode(bulan, tahun)} sedang direview oleh ${owner}. Admin lain tidak dapat membuat review baru untuk periode ini.`);
      }
      return;
    }

    setUploading(true);
    setMessage("");
    setPreview(null);
    setPreviewPage(1);
    setReviewToken("");
    setReviewChanges({});
    clearSavedReview();

    try {
      const result = await api.previewImportGaji({ file, page: 1, bulan, tahun });
      const payload = result?.data || result;
      setPreview(payload);
      setReviewToken(payload?.review_token || "");
      setMessage(result?.message || "Preview Excel berhasil dibuat. Silakan cek dan edit data sebelum import.");
      loadActiveReviews();
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
          const cleanRow = { ...row };
          delete cleanRow.validDelta;
          delete cleanRow.invalidDelta;
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

  useEffect(() => {
    const restoreReview = async () => {
      let savedReview;

      try {
        savedReview = JSON.parse(localStorage.getItem(reviewStorageKey()) || "null");
      } catch {
        clearSavedReview();
      }

      if (!savedReview?.reviewToken) {
        setReviewHydrated(true);
        return;
      }

      setBulan(savedReview.bulan || "");
      setTahun(savedReview.tahun || new Date().getFullYear().toString());
      setReviewToken(savedReview.reviewToken);
      setReviewChanges(savedReview.reviewChanges || {});
      setPreviewPage(savedReview.previewPage || 1);
      setUploading(true);

      try {
        const result = await api.previewImportGaji({
          reviewToken: savedReview.reviewToken,
          page: savedReview.previewPage || 1,
        });
        const payload = result?.data || result;
        const rows = (payload?.rows || []).map((row) => {
          const edited = savedReview.reviewChanges?.[row.row_number];
          if (!edited) return row;

          const data = { ...row.data, ...edited };
          const errors = getReviewErrors(data);
          return { ...row, data, errors, valid: errors.length === 0 };
        });

        setPreview({ ...payload, rows });
        setMessage("Review sebelumnya dipulihkan. Anda dapat melanjutkan pemeriksaan data.");
      } catch (err) {
        clearSavedReview();
        setMessage(err.message || "Review sebelumnya tidak dapat dipulihkan. Silakan upload ulang file.");
      } finally {
        setUploading(false);
        setReviewHydrated(true);
      }
    };

    restoreReview();
  }, []);

  useEffect(() => {
    loadActiveReviews();
  }, []);

  useEffect(() => {
    loadPeriodReviewStatus();
  }, [bulan, tahun]);

  useEffect(() => {
    if (!reviewHydrated) return;

    if (!reviewToken) {
      clearSavedReview();
      return;
    }

    localStorage.setItem(reviewStorageKey(), JSON.stringify({
      reviewToken,
      bulan,
      tahun,
      previewPage,
      reviewChanges,
    }));
  }, [bulan, previewPage, reviewChanges, reviewHydrated, reviewToken, tahun]);

  const handleImportReviewed = async () => {
    if (!preview?.rows?.length) {
      setMessage("Silakan review Excel terlebih dahulu.");
      return;
    }

    try {
      const statusResult = await api.getImportPeriodStatus(bulan, tahun);
      const periodStatus = statusResult?.data || statusResult;

      if (periodStatus?.has_existing_slips) {
        const lastImport = periodStatus.last_import;
        const periode = new Date(Number(tahun), Number(bulan) - 1, 1).toLocaleDateString("id-ID", {
          month: "long",
          year: "numeric",
        });
        const operator = lastImport?.uploader?.name
          ? ` oleh ${lastImport.uploader.name}${lastImport.created_at ? ` pada ${new Date(lastImport.created_at).toLocaleString("id-ID")}` : ""}`
          : "";
        const confirmed = window.confirm(
          `Slip gaji periode ${periode} sudah pernah diimport${operator}.\n\n` +
          "Data pegawai yang ada pada file terbaru akan diperbarui. Slip pegawai yang tidak ada pada file terbaru tidak akan dihapus.\n\n" +
          "Lanjutkan import dan perbarui data?"
        );

        if (!confirmed) {
          setMessage("Import dibatalkan. Data slip gaji tidak berubah.");
          return;
        }
      }
    } catch (err) {
      setMessage(err.message || "Periode tidak valid. Silakan periksa kembali bulan dan tahun.");
      return;
    }

    setUploading(true);
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
      clearSavedReview();
      setRefreshKey((k) => k + 1);
      loadActiveReviews();
      loadPeriodReviewStatus();
    } catch (err) {
      setMessage(err.message || "Import hasil review gagal.");
    } finally {
      setUploading(false);
    }
  };

  const handleCancelReview = async () => {
    const token = reviewToken || preview?.review_token;
    if (!token || !window.confirm("Batalkan review ini? Semua perubahan yang belum diimport akan dihapus.")) return;

    setUploading(true);
    setMessage("");

    try {
      const result = await api.cancelPreviewImportGaji(token);
      setFile(null);
      setPreview(null);
      setPreviewPage(1);
      setReviewToken("");
      setReviewChanges({});
      clearSavedReview();
      setMessage(result?.message || "Review Excel dibatalkan.");
      loadActiveReviews();
      loadPeriodReviewStatus();
    } catch (err) {
      setMessage(err.message || "Gagal membatalkan review Excel.");
    } finally {
      setUploading(false);
    }
  };

  const handleOpenActiveReview = async (item) => {
    setUploading(true);
    setMessage("");

    try {
      const result = await api.previewImportGaji({ reviewToken: item.review_token, page: 1 });
      const payload = result?.data || result;
      setPreview(payload);
      setReviewToken(payload?.review_token || item.review_token);
      setPreviewPage(1);
      setReviewChanges({});
      setFile(null);
      if (payload?.bulan) setBulan(String(payload.bulan));
      if (payload?.tahun) setTahun(String(payload.tahun));
      setMessage(`Review milik ${item.created_by_name || "admin"} dibuka.`);
    } catch (err) {
      setMessage(err.message || "Review Excel tidak dapat dibuka.");
    } finally {
      setUploading(false);
    }
  };

  const handleTakeOverReview = async (item) => {
    const owner = item.created_by_name || "admin lain";
    if (!window.confirm(`Ambil alih review ${formatPeriode(item.bulan, item.tahun)} dari ${owner}?\n\nAdmin sebelumnya tidak dapat melanjutkan review ini.`)) {
      return;
    }

    setUploading(true);
    setMessage("");
    try {
      const result = await api.takeOverImportReview(item.review_token);
      const review = result?.data || item;
      await handleOpenActiveReview(review);
      setMessage(result?.message || "Review berhasil diambil alih.");
      loadActiveReviews();
      loadPeriodReviewStatus(review.bulan, review.tahun);
    } catch (err) {
      setMessage(err.message || "Review Excel tidak dapat diambil alih.");
    } finally {
      setUploading(false);
    }
  };

  const activePeriodReview = periodReviewStatus?.active ? periodReviewStatus.review : null;
  const reviewStartedAt = activePeriodReview?.created_at
    ? new Date(activePeriodReview.created_at).toLocaleString("id-ID", { dateStyle: "medium", timeStyle: "short" })
    : null;

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
            onFileSelect={handleFileSelect}
            onValidationError={setMessage}
            bulan={bulan}
            onBulanChange={setBulan}
            tahun={tahun}
            onTahunChange={setTahun}
          />
          <UploadNote />

          {activePeriodReview && !periodReviewStatus?.is_owner && (
            <div className="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900">
              <div>
                <p className="font-bold">Periode {formatPeriode(bulan, tahun)} sedang direview oleh {activePeriodReview.created_by_name}.</p>
                <p className="mt-1 text-amber-800">{reviewStartedAt ? `Dimulai ${reviewStartedAt}. ` : ""}Periode terkunci hingga review dibatalkan atau import selesai.</p>
              </div>
              {periodReviewStatus?.can_take_over && (
                <button type="button" onClick={() => handleTakeOverReview(activePeriodReview)} disabled={uploading} className="rounded-lg bg-amber-600 px-4 py-2 font-semibold text-white hover:bg-amber-700 disabled:opacity-60">
                  Ambil Alih Review
                </button>
              )}
            </div>
          )}

          {message && (
            <div className={`p-4 rounded-xl text-sm font-semibold ${
              message.toLowerCase().includes("berhasil") || message.toLowerCase().includes("sukses") || message.toLowerCase().includes("dibatalkan") || message.toLowerCase().includes("dipulihkan") || message.toLowerCase().includes("dibuka")
                ? "bg-green-50 text-green-700 border border-green-200"
                : "bg-red-50 text-red-700 border border-red-200"
            }`}>
              {message}
            </div>
          )}

          <UploadButton
            onClick={handlePreview}
            disabled={uploading || !file || loadingPeriodReview || Boolean(activePeriodReview)}
            loading={uploading}
            label={preview ? "Review Ulang Excel" : "Review Excel"}
          />

          {isSuperAdmin && (
            <div className="rounded-2xl bg-white p-6 shadow-md">
              <div className="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                  <h2 className="text-xl font-bold text-slate-800">Review Aktif Admin</h2>
                  <p className="mt-1 text-sm text-gray-500">Super Admin dapat membuka atau membatalkan review milik seluruh admin.</p>
                </div>
                <button
                  type="button"
                  onClick={loadActiveReviews}
                  disabled={loadingActiveReviews}
                  className="rounded-lg border border-green-700 px-4 py-2 text-sm font-semibold text-green-700 transition hover:bg-green-50 disabled:opacity-60"
                >
                  {loadingActiveReviews ? "Memuat..." : "Muat Ulang"}
                </button>
              </div>

              {!activeReviews.length ? (
                <p className="py-3 text-sm text-gray-500">Tidak ada review aktif dari admin.</p>
              ) : (
                <div className="overflow-x-auto">
                  <table className="w-full min-w-[760px] text-sm">
                    <thead className="border-b text-left text-gray-500">
                      <tr>
                        <th className="pb-3">File</th>
                        <th className="pb-3">Pembuat</th>
                        <th className="pb-3">Periode</th>
                        <th className="pb-3">Baris</th>
                        <th className="pb-3 text-right">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      {activeReviews.map((item) => (
                        <tr key={item.review_token} className="border-b last:border-none">
                          <td className="max-w-[260px] truncate py-3 font-medium">{item.nama_file}</td>
                          <td>{item.created_by_name}</td>
                          <td>{item.bulan ? formatPeriode(item.bulan, item.tahun) : "Belum dipilih"}</td>
                          <td>{item.total_baris || 0}</td>
                          <td className="py-3 text-right">
                            <button
                              type="button"
                              onClick={() => Number(item.created_by) === Number(currentUser?.id) ? handleOpenActiveReview(item) : handleTakeOverReview(item)}
                              disabled={uploading}
                              className="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60"
                            >
                              {Number(item.created_by) === Number(currentUser?.id) ? "Buka Review" : "Ambil Alih Review"}
                            </button>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              )}
            </div>
          )}

          {preview && (
            <div className="space-y-4 rounded-2xl bg-white p-6 shadow-md">
              <div>
                <div>
                  <h2 className="text-2xl font-bold text-slate-800">Review Data Excel</h2>
                  <p className="text-sm text-gray-500">
                    Total {preview.total} baris, valid {preview.valid}, perlu diperbaiki {preview.invalid}.
                    {preview.total > preview.rows.length
                      ? ` Halaman ${previewPage} dari ${totalPages}, menampilkan ${preview.rows.length} baris.`
                      : ""}
                  </p>
                </div>
              </div>

              <div className="border-t border-slate-100 bg-slate-50/70 px-4 py-4 sm:px-5">
                <div className="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                  <button
                    type="button"
                    onClick={handleCancelReview}
                    disabled={uploading}
                    className="w-full rounded-xl border border-red-200 bg-white px-5 py-3 font-semibold text-red-700 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
                  >
                    Batal Review
                  </button>
                  <div className="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center lg:ml-auto lg:flex-nowrap">
                    <div className="grid grid-cols-2 gap-3">
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
                    </div>
                    <button
                      type="button"
                      onClick={handleImportReviewed}
                      disabled={uploading || !reviewToken}
                      className="w-full rounded-xl bg-green-700 px-6 py-3 font-semibold text-white transition hover:bg-green-800 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
                    >
                      {uploading ? "Mengimport..." : "Import Data Valid"}
                    </button>
                  </div>
                </div>
              </div>

              <div className="overflow-x-auto rounded-xl border border-gray-200">
                <table className="min-w-max w-full text-sm">
                  <thead className="bg-green-700 text-white">
                    <tr>
                      <th className="px-3 py-3 text-left">Baris</th>
                      <th className="min-w-[36rem] px-3 py-3 text-left">Status</th>
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
                        <td className="px-3 py-2 font-semibold">{row.row_number}</td>
                        <td className="min-w-[36rem] px-3 py-2">
                          <span className={`inline-block rounded-full px-3 py-1 text-xs font-semibold ${
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
