import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { User, Lock, Eye, EyeOff, Loader2, AlertCircle } from "lucide-react";
import Button from "../common/Button";
import api from "../../services/api";

function LoginRight() {
  const navigate = useNavigate();

  const [nip, setNip] = useState("");
  const [password, setPassword] = useState("");
  const [showPassword, setShowPassword] = useState(false);
  const [remember, setRemember] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");

    if (!nip.trim()) {
      setError("Silakan masukkan NIP atau Username.");
      return;
    }
    if (!password.trim()) {
      setError("Silakan masukkan Password.");
      return;
    }

    setLoading(true);

    try {
      const result = await api.login(nip.trim(), password, remember);
      const role = (result?.user?.role || result?.data?.user?.role || "").toLowerCase();

      if (role === "admin" || role === "super_admin") {
        navigate("/admin/dashboard");
      } else if (role === "pegawai" || role === "user") {
        navigate("/user/dashboard");
      } else {
        navigate("/");
      }
    } catch (err) {
      setError(err.message || "Login gagal. Periksa NIP dan Password Anda.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="flex w-full items-center justify-center bg-white px-6 py-10 sm:px-10 md:w-1/2">
      <div className="w-full max-w-[430px]">

        {/* Judul */}
        <h2 className="text-4xl font-bold text-slate-800 sm:text-5xl">
          Selamat Datang
        </h2>

        <p className="text-gray-500 mt-3 mb-10 text-lg">
          Silakan masuk untuk melanjutkan.
        </p>

        {/* Error */}
        {error && (
          <div className="mb-6 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <AlertCircle size={18} className="shrink-0" />
            <span>{error}</span>
          </div>
        )}

        {/* NIP / Username */}
        <label className="text-sm font-semibold text-gray-700">
          Username / NIP
        </label>

        <div className="flex items-center mt-2 mb-6 border border-gray-300 rounded-xl px-4 h-16 focus-within:border-green-600">
          <User size={20} className="text-gray-400" />

          <input
            type="text"
            value={nip}
            onChange={(e) => setNip(e.target.value)}
            placeholder="Masukkan Username atau NIP"
            className="ml-3 w-full outline-none text-lg"
            disabled={loading}
            autoFocus
          />
        </div>

        {/* Password */}
        <label className="text-sm font-semibold text-gray-700">
          Password
        </label>

        <div className="flex items-center mt-2 border border-gray-300 rounded-xl px-4 h-16 focus-within:border-green-600">
          <Lock size={20} className="text-gray-400" />

          <input
            type={showPassword ? "text" : "password"}
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            placeholder="Masukkan Password"
            className="ml-3 w-full outline-none text-lg"
            disabled={loading}
          />

          <button
            type="button"
            onClick={() => setShowPassword(!showPassword)}
            className="ml-2 text-gray-400 hover:text-gray-600"
            tabIndex={-1}
          >
            {showPassword ? <EyeOff size={20} /> : <Eye size={20} />}
          </button>
        </div>

        {/* Ingat Saya */}
        <div className="mt-6 flex flex-wrap items-center justify-between gap-3">

          <label className="flex items-center gap-3 cursor-pointer">

            <input
              type="checkbox"
              checked={remember}
              onChange={(event) => setRemember(event.target.checked)}
              className="w-4 h-4 accent-green-700"
            />

            <span className="text-gray-700 whitespace-nowrap">
              Ingat Saya
            </span>

          </label>

          <button
            type="button"
            onClick={() => navigate("/lupa-password")}
            className="text-green-700 font-medium hover:underline whitespace-nowrap"
          >
            Lupa Password?
          </button>

        </div>

        {/* Tombol */}
        <Button
          type="submit"
          disabled={loading}
          className="w-full mt-8 h-16 rounded-2xl flex items-center justify-center gap-2"
        >
          {loading ? (
            <>
              <Loader2 size={22} className="animate-spin" />
              Memproses...
            </>
          ) : (
            "Masuk"
          )}
        </Button>

      </div>
    </form>
  );
}

export default LoginRight;
