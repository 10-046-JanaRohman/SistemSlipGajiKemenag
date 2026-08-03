import { useCallback, useEffect, useState } from "react";
import { Edit3, Megaphone, Plus, Trash2, X } from "lucide-react";
import AdminLayout from "../../layouts/AdminLayout";
import PageTransition from "../../components/common/PageTransition";
import api from "../../services/api";

const emptyForm = { judul: "", isi: "" };

function Notifikasi() {
  const [items, setItems] = useState([]);
  const [form, setForm] = useState(emptyForm);
  const [editingId, setEditingId] = useState(null);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [message, setMessage] = useState("");
  const [error, setError] = useState("");

  const fetchItems = useCallback(async () => {
    setLoading(true);
    try {
      const result = await api.getPengumuman({ per_page: 100 });
      const payload = result?.data || result;
      setItems(Array.isArray(payload?.data) ? payload.data : Array.isArray(payload) ? payload : []);
    } catch (requestError) {
      setError(requestError.message || "Gagal memuat pengumuman.");
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchItems();
  }, [fetchItems]);

  const resetForm = () => {
    setForm(emptyForm);
    setEditingId(null);
  };

  const handleSubmit = async (event) => {
    event.preventDefault();
    setSaving(true);
    setMessage("");
    setError("");

    try {
      const result = editingId
        ? await api.updatePengumuman(editingId, form)
        : await api.createPengumuman(form);
      setMessage(result?.message || "Pengumuman berhasil disimpan.");
      resetForm();
      fetchItems();
    } catch (requestError) {
      setError(requestError.message || "Pengumuman gagal disimpan.");
    } finally {
      setSaving(false);
    }
  };

  const handleEdit = (item) => {
    setEditingId(item.id);
    setForm({ judul: item.judul || "", isi: item.isi || "" });
    setMessage("");
    setError("");
  };

  const handleDelete = async (id) => {
    if (!window.confirm("Hapus pengumuman ini?")) return;

    setMessage("");
    setError("");
    try {
      const result = await api.deletePengumuman(id);
      setMessage(result?.message || "Pengumuman berhasil dihapus.");
      if (editingId === id) resetForm();
      fetchItems();
    } catch (requestError) {
      setError(requestError.message || "Pengumuman gagal dihapus.");
    }
  };

  return (
    <AdminLayout>
      <PageTransition>
        <div className="mx-auto max-w-6xl space-y-8">
          <div>
            <h1 className="text-5xl font-bold text-slate-800">Pengumuman</h1>
            <p className="mt-2 text-gray-500">Terbitkan informasi yang akan terlihat di dashboard seluruh pegawai.</p>
          </div>

          <section className="rounded-2xl bg-white p-7 shadow-md">
            <div className="mb-6 flex items-center gap-3">
              <Megaphone className="text-green-700" size={26} />
              <h2 className="text-2xl font-bold">{editingId ? "Ubah Pengumuman" : "Buat Pengumuman"}</h2>
            </div>
            {message && <div className="mb-5 rounded-xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">{message}</div>}
            {error && <div className="mb-5 rounded-xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{error}</div>}
            <form onSubmit={handleSubmit} className="space-y-4">
              <input required value={form.judul} onChange={(event) => setForm((current) => ({ ...current, judul: event.target.value }))} placeholder="Judul pengumuman" className="h-11 w-full rounded-xl border border-gray-300 px-4 outline-none focus:border-green-700 focus:ring-2 focus:ring-green-100" />
              <textarea required rows="4" value={form.isi} onChange={(event) => setForm((current) => ({ ...current, isi: event.target.value }))} placeholder="Isi pengumuman" className="w-full rounded-xl border border-gray-300 p-4 outline-none focus:border-green-700 focus:ring-2 focus:ring-green-100" />
              <div className="flex justify-end gap-3">
                {editingId && <button type="button" onClick={resetForm} className="inline-flex items-center gap-2 rounded-xl bg-gray-100 px-5 py-3 font-semibold text-gray-700 hover:bg-gray-200"><X size={18} /> Batal</button>}
                <button disabled={saving} type="submit" className="inline-flex items-center gap-2 rounded-xl bg-green-700 px-5 py-3 font-semibold text-white hover:bg-green-800 disabled:opacity-60"><Plus size={18} /> {saving ? "Menyimpan..." : editingId ? "Simpan Perubahan" : "Terbitkan"}</button>
              </div>
            </form>
          </section>

          <section className="overflow-hidden rounded-2xl bg-white shadow-md">
            <div className="border-b border-gray-100 px-7 py-5"><h2 className="text-xl font-bold">Daftar Pengumuman</h2></div>
            {loading ? <p className="p-7 text-gray-500">Memuat pengumuman...</p> : items.length === 0 ? <p className="p-7 text-gray-500">Belum ada pengumuman yang diterbitkan.</p> : (
              <div className="divide-y divide-gray-100">
                {items.map((item) => (
                  <article key={item.id} className="flex gap-5 px-7 py-5">
                    <div className="min-w-0 flex-1"><h3 className="font-bold text-slate-800">{item.judul}</h3><p className="mt-1 whitespace-pre-line text-sm text-gray-600">{item.isi}</p><p className="mt-3 text-xs text-gray-400">{formatTanggal(item.published_at || item.created_at)}</p></div>
                    <div className="flex shrink-0 items-start gap-2"><button onClick={() => handleEdit(item)} className="rounded-lg p-2 text-blue-600 hover:bg-blue-50" aria-label="Ubah pengumuman"><Edit3 size={18} /></button><button onClick={() => handleDelete(item.id)} className="rounded-lg p-2 text-red-600 hover:bg-red-50" aria-label="Hapus pengumuman"><Trash2 size={18} /></button></div>
                  </article>
                ))}
              </div>
            )}
          </section>
        </div>
      </PageTransition>
    </AdminLayout>
  );
}

function formatTanggal(value) {
  if (!value) return "-";
  return new Intl.DateTimeFormat("id-ID", { day: "numeric", month: "long", year: "numeric" }).format(new Date(value));
}

export default Notifikasi;
