import { useState } from "react";
import { Link, useSearchParams } from "react-router-dom";
import api from "../services/api";

function ResetPassword() {
  const [params] = useSearchParams();
  const [password, setPassword] = useState("");
  const [confirmation, setConfirmation] = useState("");
  const [loading, setLoading] = useState(false);
  const [message, setMessage] = useState("");
  const [error, setError] = useState("");
  const token = params.get("token") || "";
  const email = params.get("email") || "";

  const submit = async (event) => {
    event.preventDefault(); setLoading(true); setMessage(""); setError("");
    try { const result = await api.resetPassword({ token, email, password, passwordConfirmation: confirmation }); setMessage(result?.message || "Password berhasil diubah."); } catch (requestError) { setError(requestError.message || "Password gagal diubah."); } finally { setLoading(false); }
  };

  return <main className="flex min-h-screen items-center justify-center bg-gradient-to-br from-green-50 to-green-200 p-6"><form onSubmit={submit} className="w-full max-w-md rounded-3xl bg-white p-10 shadow-2xl"><h1 className="text-3xl font-bold text-slate-800">Atur Ulang Password</h1>{!token || !email ? <p className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700">Tautan reset password tidak lengkap.</p> : <><p className="mt-3 text-sm text-gray-500">Akun: {email}</p>{message && <p className="mt-5 rounded-xl bg-green-50 p-3 text-sm text-green-700">{message}</p>}{error && <p className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700">{error}</p>}<label className="mt-5 block text-sm font-semibold">Password baru</label><input required minLength="8" type="password" value={password} onChange={(event) => setPassword(event.target.value)} className="mt-2 h-12 w-full rounded-xl border border-gray-300 px-4" /><label className="mt-4 block text-sm font-semibold">Konfirmasi password baru</label><input required minLength="8" type="password" value={confirmation} onChange={(event) => setConfirmation(event.target.value)} className="mt-2 h-12 w-full rounded-xl border border-gray-300 px-4" /><button disabled={loading || Boolean(message)} className="mt-6 h-12 w-full rounded-xl bg-green-700 font-semibold text-white disabled:opacity-60">{loading ? "Menyimpan..." : "Simpan Password Baru"}</button></>}<Link to="/" className="mt-5 block text-center font-medium text-green-700 hover:underline">Ke halaman login</Link></form></main>;
}

export default ResetPassword;
