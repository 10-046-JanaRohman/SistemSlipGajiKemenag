import { useEffect, useState } from "react";
import AdminLayout from "../../layouts/AdminLayout";
import api from "../../services/api";

function Pengaturan() {
  const [form, setForm] = useState({
    pdf_bendahara_nama: "",
    pdf_bendahara_nip: "",
  });
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [message, setMessage] = useState("");
  const user = (() => {
    try {
      return JSON.parse(localStorage.getItem("user") || "null");
    } catch {
      return null;
    }
  })();
  const isSuperAdmin = user?.role === "super_admin";

  useEffect(() => {
    let active = true;

    const fetchSettings = async () => {
      setLoading(true);
      try {
        const result = await api.getSettings();
        const payload = result?.data || {};
        if (active) {
          setForm({
            pdf_bendahara_nama: payload.pdf_bendahara_nama || "",
            pdf_bendahara_nip: payload.pdf_bendahara_nip || "",
          });
        }
      } catch (error) {
        if (active) setMessage(error.message || "Gagal memuat pengaturan.");
      } finally {
        if (active) setLoading(false);
      }
    };

    fetchSettings();

    return () => {
      active = false;
    };
  }, []);

  const handleSubmit = async (event) => {
    event.preventDefault();
    setSaving(true);
    setMessage("");

    try {
      const result = await api.updateSettings(form);
      setMessage(result?.message || "Pengaturan berhasil disimpan.");
    } catch (error) {
      setMessage(error.message || "Gagal menyimpan pengaturan.");
    } finally {
      setSaving(false);
    }
  };

  return (
    <AdminLayout>
      <div className="space-y-8">
        <div>
          <h1 className="text-5xl font-bold">Pengaturan</h1>
          <p className="text-gray-500 mt-2">
            Kelola informasi bendahara yang muncul pada PDF slip gaji.
          </p>
        </div>

        {message && (
          <div className={`rounded-xl border px-4 py-3 text-sm font-semibold ${
            message.toLowerCase().includes("berhasil")
              ? "border-green-200 bg-green-50 text-green-700"
              : "border-red-200 bg-red-50 text-red-700"
          }`}>
            {message}
          </div>
        )}

        <form onSubmit={handleSubmit} className="max-w-3xl rounded-2xl bg-white p-8 shadow">
          <h2 className="text-2xl font-bold text-slate-800">Tanda Tangan PDF</h2>
          <p className="mt-2 text-sm text-gray-500">
            Hanya super admin yang dapat mengubah nama dan NIP bendahara.
          </p>

          <div className="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
            <label className="block">
              <span className="mb-2 block text-sm font-semibold text-gray-700">Nama Bendahara</span>
              <input
                value={form.pdf_bendahara_nama}
                onChange={(event) => setForm((current) => ({ ...current, pdf_bendahara_nama: event.target.value }))}
                disabled={!isSuperAdmin || loading}
                placeholder="Nama Bendahara"
                className="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-600 disabled:bg-gray-100"
              />
            </label>

            <label className="block">
              <span className="mb-2 block text-sm font-semibold text-gray-700">NIP Bendahara</span>
              <input
                value={form.pdf_bendahara_nip}
                onChange={(event) => setForm((current) => ({ ...current, pdf_bendahara_nip: event.target.value }))}
                disabled={!isSuperAdmin || loading}
                placeholder="NIP Bendahara"
                className="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-600 disabled:bg-gray-100"
              />
            </label>
          </div>

          <div className="mt-8 flex justify-end">
            <button
              type="submit"
              disabled={!isSuperAdmin || loading || saving}
              className="rounded-xl bg-green-700 px-6 py-3 font-semibold text-white hover:bg-green-800 disabled:cursor-not-allowed disabled:opacity-60"
            >
              {saving ? "Menyimpan..." : "Simpan Pengaturan"}
            </button>
          </div>
        </form>
      </div>
    </AdminLayout>
  );
}

export default Pengaturan;
