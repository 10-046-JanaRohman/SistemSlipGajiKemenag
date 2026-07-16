import { useEffect, useState } from "react";
import { Clock, Megaphone } from "lucide-react";
import api from "../../services/api";

function UserAnnouncement() {
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchPengumuman = async () => {
      try {
        const result = await api.getPengumuman({ per_page: 3 });
        const payload = result?.data || result;
        setItems(Array.isArray(payload?.data) ? payload.data : Array.isArray(payload) ? payload : []);
      } catch {
        setItems([]);
      } finally {
        setLoading(false);
      }
    };

    fetchPengumuman();
  }, []);

  return (
    <div className="rounded-2xl bg-white p-8 shadow-md">
      <div className="mb-6 flex items-center gap-3">
        <Megaphone className="text-green-700" size={26} />
        <h2 className="text-2xl font-bold text-slate-800">Pengumuman</h2>
      </div>

      {loading ? (
        <p className="text-sm text-gray-500">Memuat pengumuman...</p>
      ) : items.length === 0 ? (
        <p className="rounded-xl border border-dashed border-gray-200 p-4 text-sm text-gray-500">Belum ada pengumuman.</p>
      ) : (
        <div className="space-y-5">
          {items.map((item) => (
            <article key={item.id} className="border-l-4 border-green-700 pl-4">
              <h3 className="font-semibold text-slate-800">{item.judul}</h3>
              <p className="mt-1 line-clamp-2 text-sm text-gray-600">{item.isi}</p>
              <div className="mt-2 flex items-center gap-2 text-sm text-gray-500">
                <Clock size={14} />
                {formatTanggal(item.published_at || item.created_at)}
              </div>
            </article>
          ))}
        </div>
      )}
    </div>
  );
}

function formatTanggal(value) {
  if (!value) return "-";

  return new Intl.DateTimeFormat("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  }).format(new Date(value));
}

export default UserAnnouncement;
