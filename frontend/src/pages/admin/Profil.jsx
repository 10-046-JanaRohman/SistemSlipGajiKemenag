import { useState, useEffect } from "react";
import AdminLayout from "../../layouts/AdminLayout";
import PageTransition from "../../components/common/PageTransition";
import api from "../../services/api";

function Profil() {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchProfil = async () => {
      try {
        const result = await api.getProfil();
        setUser(result?.data || result?.user || result);
      } catch {
        // fallback ke localStorage
        try {
          const stored = localStorage.getItem("user");
          if (stored) setUser(JSON.parse(stored));
        } catch {}
      } finally {
        setLoading(false);
      }
    };
    fetchProfil();
  }, []);

  return (
    <AdminLayout>
      <PageTransition>
        <div className="max-w-3xl mx-auto space-y-8">

          <div>
            <h1 className="text-5xl font-bold text-slate-800">Profil</h1>
            <p className="text-gray-500 mt-2">Informasi profil pengguna.</p>
          </div>

          <div className="bg-white rounded-2xl shadow p-8">
            <h2 className="text-2xl font-bold mb-6">Data Pengguna</h2>

            {loading ? (
              <p className="text-gray-500">Memuat...</p>
            ) : user ? (
              <div className="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <Item label="Nama" value={user.name || user.nama || "-"} />
                <Item label="NIP" value={user.nip || "-"} />
                <Item label="Email" value={user.email || "-"} />
                <Item label="Role" value={user.role || "-"} />
              </div>
            ) : (
              <p className="text-gray-500">Data tidak tersedia.</p>
            )}
          </div>

        </div>
      </PageTransition>
    </AdminLayout>
  );
}

function Item({ label, value }) {
  return (
    <div>
      <p className="text-gray-500 text-sm">{label}</p>
      <p className="font-semibold text-lg">{value}</p>
    </div>
  );
}

export default Profil;
