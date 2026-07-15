import { User, Lock, Eye } from "lucide-react";
import Button from "../common/Button";

function LoginRight() {
  return (
    <div className="w-1/2 flex items-center justify-center bg-white">
      <div className="w-[430px]">

        {/* Judul */}
        <h2 className="text-5xl font-bold text-slate-800">
          Selamat Datang
        </h2>

        <p className="text-gray-500 mt-3 mb-10 text-lg">
          Silakan masuk untuk melanjutkan.
        </p>

        {/* Username */}
        <label className="text-sm font-semibold text-gray-700">
          Username / NIP
        </label>

        <div className="flex items-center mt-2 mb-6 border border-gray-300 rounded-xl px-4 h-16 focus-within:border-green-600">
          <User size={20} className="text-gray-400" />

          <input
            type="text"
            placeholder="Masukkan Username atau NIP"
            className="ml-3 w-full outline-none text-lg"
          />
        </div>

        {/* Password */}
        <label className="text-sm font-semibold text-gray-700">
          Password
        </label>

        <div className="flex items-center mt-2 border border-gray-300 rounded-xl px-4 h-16 focus-within:border-green-600">
          <Lock size={20} className="text-gray-400" />

          <input
            type="password"
            placeholder="Masukkan Password"
            className="ml-3 w-full outline-none text-lg"
          />
        </div>

        {/* Ingat Saya */}
        <div className="flex items-center justify-between mt-6">

          <label className="flex items-center gap-3 cursor-pointer">

            <input
              type="checkbox"
              className="w-4 h-4 accent-green-700"
            />

            <span className="text-gray-700 whitespace-nowrap">
              Ingat Saya
            </span>

          </label>

          <a
            href="#"
            className="text-green-700 font-medium hover:underline whitespace-nowrap"
          >
            Lupa Password?
          </a>

        </div>

        {/* Tombol */}
        <Button className="w-full mt-8 h-16 rounded-2xl">
          Masuk
        </Button>

      </div>
    </div>
  );
}

export default LoginRight;