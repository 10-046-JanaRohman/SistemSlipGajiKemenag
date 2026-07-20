import { Bell } from "lucide-react";
import { useEffect, useState } from "react";
import api from "../../services/api";

function NotificationBell() {
  const [open, setOpen] = useState(false);
  const [items, setItems] = useState([]);
  const [unreadCount, setUnreadCount] = useState(0);
  const [loading, setLoading] = useState(false);

  const loadNotifications = async () => {
    setLoading(true);
    try {
      const result = await api.getNotifikasi();
      setItems(result?.data || []);
      setUnreadCount(result?.unread_count || 0);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { loadNotifications(); }, []);

  const openPanel = () => {
    setOpen((current) => !current);
    if (!open) loadNotifications();
  };

  const markAllRead = async () => {
    await api.markAllNotifikasiRead();
    setItems((current) => current.map((item) => ({ ...item, dibaca: true })));
    setUnreadCount(0);
  };

  return (
    <div className="relative">
      <button type="button" onClick={openPanel} className="relative flex h-11 w-11 items-center justify-center rounded-xl bg-gray-100 transition hover:bg-gray-200" aria-label="Buka notifikasi">
        <Bell size={20} />
        {unreadCount > 0 && <span className="absolute right-1.5 top-1.5 min-w-2 rounded-full bg-red-500 px-1 text-[10px] leading-4 text-white">{unreadCount > 9 ? "9+" : unreadCount}</span>}
      </button>
      {open && (
        <section className="absolute right-0 z-30 mt-2 w-[min(24rem,calc(100vw-2rem))] overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl">
          <div className="flex items-center justify-between border-b px-4 py-3"><h3 className="font-bold">Notifikasi</h3>{unreadCount > 0 && <button type="button" onClick={markAllRead} className="text-sm font-semibold text-green-700">Tandai dibaca</button>}</div>
          {loading ? <p className="px-4 py-4 text-sm text-gray-500">Memuat notifikasi...</p> : items.length ? <div className="max-h-80 overflow-y-auto">{items.map((item) => <article key={item.id} className={`border-b px-4 py-3 last:border-0 ${item.dibaca ? "bg-white" : "bg-green-50"}`}><p className="font-semibold text-slate-800">{item.judul}</p><p className="mt-1 text-sm text-gray-600">{item.isi}</p></article>)}</div> : <p className="px-4 py-4 text-sm text-gray-500">Belum ada notifikasi.</p>}
        </section>
      )}
    </div>
  );
}

export default NotificationBell;
