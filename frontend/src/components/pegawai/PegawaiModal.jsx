import { useEffect, useState } from "react";
import { X } from "lucide-react";

const initialForm = {
  nip: "",
  nama: "",
  jabatan: "",
  golongan: "",
  status_pegawai: "PEGAWAI",
};

function PegawaiModal({ open, mode = "create", pegawai, loading, error, onClose, onSubmit }) {
  const [form, setForm] = useState(initialForm);

  useEffect(() => {
    if (!open) return;

    setForm({
      nip: pegawai?.nip || "",
      nama: pegawai?.nama || "",
      jabatan: pegawai?.jabatan || "",
      golongan: pegawai?.golongan || "",
      status_pegawai: pegawai?.status_pegawai || "PEGAWAI",
    });
  }, [open, pegawai]);

  if (!open) return null;

  const handleChange = (event) => {
    const { name, value } = event.target;
    setForm((current) => ({ ...current, [name]: value }));
  };

  const handleSubmit = (event) => {
    event.preventDefault();
    onSubmit(form);
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
      <div className="w-full max-w-2xl rounded-2xl bg-white shadow-xl">
        <div className="flex items-center justify-between border-b px-6 py-4">
          <h2 className="text-2xl font-bold text-slate-800">
            {mode === "edit" ? "Edit Pegawai" : "Tambah Pegawai"}
          </h2>
          <button
            type="button"
            onClick={onClose}
            className="rounded-lg p-2 text-gray-500 hover:bg-gray-100"
            aria-label="Tutup modal"
          >
            <X size={20} />
          </button>
        </div>

        <form onSubmit={handleSubmit} className="space-y-5 px-6 py-6">
          {error && (
            <div className="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
              {error}
            </div>
          )}

          <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
            <Field label="NIP" name="nip" value={form.nip} onChange={handleChange} required />
            <Field label="Nama" name="nama" value={form.nama} onChange={handleChange} required />
            <Field label="Jabatan" name="jabatan" value={form.jabatan} onChange={handleChange} />
            <Field label="Golongan" name="golongan" value={form.golongan} onChange={handleChange} />
            <Field label="Status Pegawai" name="status_pegawai" value={form.status_pegawai} onChange={handleChange} />
          </div>

          <div className="flex justify-end gap-3 pt-2">
            <button
              type="button"
              onClick={onClose}
              className="rounded-xl border border-gray-200 px-5 py-2.5 font-semibold text-gray-700 hover:bg-gray-50"
            >
              Batal
            </button>
            <button
              type="submit"
              disabled={loading}
              className="rounded-xl bg-green-700 px-5 py-2.5 font-semibold text-white hover:bg-green-800 disabled:cursor-not-allowed disabled:opacity-60"
            >
              {loading ? "Menyimpan..." : "Simpan"}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

function Field({ label, name, value, onChange, required = false }) {
  return (
    <label className="block">
      <span className="mb-2 block text-sm font-semibold text-gray-700">{label}</span>
      <input
        name={name}
        value={value}
        onChange={onChange}
        required={required}
        className="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-600"
      />
    </label>
  );
}

export default PegawaiModal;
