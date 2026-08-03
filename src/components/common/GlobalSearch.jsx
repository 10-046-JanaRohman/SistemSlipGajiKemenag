import { Search } from "lucide-react";
import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import api from "../../services/api";

function GlobalSearch({ placeholder = "Cari nama atau NIP..." }) {
  const [query, setQuery] = useState("");
  const [results, setResults] = useState([]);
  const [open, setOpen] = useState(false);
  const [loading, setLoading] = useState(false);
  const navigate = useNavigate();

  useEffect(() => {
    const term = query.trim();
    if (term.length < 2) {
      setResults([]);
      return undefined;
    }

    const timer = window.setTimeout(async () => {
      setLoading(true);
      try {
        const result = await api.searchGlobal(term);
        setResults(result?.data || result || []);
        setOpen(true);
      } catch {
        setResults([]);
      } finally {
        setLoading(false);
      }
    }, 300);

    return () => window.clearTimeout(timer);
  }, [query]);

  const selectResult = (url) => {
    setOpen(false);
    setQuery("");
    navigate(url);
  };

  return (
    <div className="relative min-w-0">
      <Search size={18} className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" />
      <input
        type="search"
        value={query}
        onChange={(event) => setQuery(event.target.value)}
        onFocus={() => query.trim().length >= 2 && setOpen(true)}
        placeholder={placeholder}
        className="h-11 w-[min(42vw,18rem)] min-w-0 rounded-xl bg-gray-100 pl-11 pr-4 text-sm outline-none focus:ring-2 focus:ring-green-700 sm:w-56 lg:w-72"
      />
      {open && (
        <div className="absolute right-0 z-30 mt-2 w-[min(24rem,calc(100vw-2rem))] overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl">
          {loading ? <p className="px-4 py-3 text-sm text-gray-500">Mencari...</p> : results.length ? results.map((item) => (
            <button key={`${item.type}-${item.id}`} type="button" onMouseDown={() => selectResult(item.url)} className="block w-full border-b border-gray-100 px-4 py-3 text-left hover:bg-green-50 last:border-0">
              <p className="font-semibold text-slate-800">{item.title}</p><p className="text-sm text-gray-500">{item.subtitle}</p>
            </button>
          )) : <p className="px-4 py-3 text-sm text-gray-500">Data tidak ditemukan.</p>}
        </div>
      )}
    </div>
  );
}

export default GlobalSearch;
