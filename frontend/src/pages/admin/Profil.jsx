import { useState, useEffect } from "react";
import AdminLayout from "../../layouts/AdminLayout";
import PageTransition from "../../components/common/PageTransition";
import api from "../../services/api";

function Profil() {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [passwordForm, setPasswordForm] = useState({
    current_password: "",
    password: "",
    password_confirmation: "",
  });
  const [passwordMessage, setPasswordMessage] = useState("");
  const [passwordError, setPasswordError] = useState("");
  const [savingPassword, setSavingPassword] = useState(false);

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

  const handlePasswordChange = (event) => {
    const { name, value } = event.target;
    setPasswordForm((current) => ({ ...current, [name]: value }));
  };

  const handleChangePassword = async (event) => {
    event.preventDefault();
    setPasswordMessage("");
    setPasswordError("");

    if (passwordForm.password !== passwordForm.password_confirmation) {
      setPasswordError("Konfirmasi password baru tidak sama.");
      return;
    }

    setSavingPassword(true);
    try {
      const result = await api.gantiPassword(passwordForm);
      setPasswordMessage(result?.message || "Password berhasil diubah.");
      setPasswordForm({ current_password: "", password: "", password_confirmation: "" });
    } catch (error) {
      setPasswordError(error.message || "Password gagal diubah.");
    } finally {
      setSavingPassword(false);
    }
  };

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

          <form onSubmit={handleChangePassword} className="bg-white rounded-2xl shadow p-8">
            <h2 className="text-2xl font-bold">Ganti Password</h2>
            <p className="mt-2 text-sm text-gray-500">Gunakan password baru yang kuat. Password minimal terdiri dari 8 karakter.</p>

            {passwordMessage && (
              <div className="mt-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">
                {passwordMessage}
              </div>
            )}

            {passwordError && (
              <div className="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                {passwordError}
              </div>
            )}

            <div className="mt-6 grid gap-5">
              <PasswordInput
                label="Password Lama"
                name="current_password"
                value={passwordForm.current_password}
                onChange={handlePasswordChange}
              />
              <PasswordInput
                label="Password Baru"
                name="password"
                value={passwordForm.password}
                onChange={handlePasswordChange}
              />
              <PasswordInput
                label="Konfirmasi Password Baru"
                name="password_confirmation"
                value={passwordForm.password_confirmation}
                onChange={handlePasswordChange}
              />
            </div>

            <div className="mt-6 flex justify-end">
              <button
                type="submit"
                disabled={savingPassword}
                className="rounded-xl bg-green-700 px-5 py-3 font-semibold text-white transition hover:bg-green-800 disabled:cursor-not-allowed disabled:opacity-60"
              >
                {savingPassword ? "Menyimpan..." : "Simpan Password Baru"}
              </button>
            </div>
          </form>

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

function PasswordInput({ label, name, value, onChange }) {
  return (
    <label className="block">
      <span className="mb-2 block text-sm font-semibold text-slate-700">{label}</span>
      <input
        type="password"
        name={name}
        value={value}
        onChange={onChange}
        required
        autoComplete={name === "current_password" ? "current-password" : "new-password"}
        className="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-100"
      />
    </label>
  );
}

export default Profil;
