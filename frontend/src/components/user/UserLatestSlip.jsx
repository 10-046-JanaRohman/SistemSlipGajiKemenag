import {
  Calendar,
  Wallet,
  Download,
  Eye,
} from "lucide-react";

function UserLatestSlip() {
  return (
    <div className="bg-white rounded-2xl shadow-md p-8">

      {/* Judul */}
      <div className="flex justify-between items-center">

        <div>

          <h2 className="text-2xl font-bold text-slate-800">
            Slip Gaji Terbaru
          </h2>

          <p className="text-gray-500 mt-1">
            Informasi slip gaji periode terbaru.
          </p>

        </div>

        <span className="bg-green-100 text-green-700 px-4 py-2 rounded-full font-semibold">
          Tersedia
        </span>

      </div>

      {/* Isi */}
      <div className="grid grid-cols-2 gap-8 mt-8">

        <div>

          <div className="flex items-center gap-3 mb-6">

            <Calendar
              size={22}
              className="text-green-700"
            />

            <div>

              <p className="text-gray-500 text-sm">
                Periode
              </p>

              <h3 className="text-xl font-bold">
                Juli 2026
              </h3>

            </div>

          </div>

          <div className="flex items-center gap-3">

            <Wallet
              size={22}
              className="text-green-700"
            />

            <div>

              <p className="text-gray-500 text-sm">
                Total Diterima
              </p>

              <h2 className="text-3xl font-bold text-green-700">
                Rp5.700.000
              </h2>

            </div>

          </div>

        </div>

        {/* Tombol */}
        <div className="flex flex-col justify-center gap-4">

          <button className="bg-green-700 hover:bg-green-800 text-white py-3 rounded-xl flex justify-center items-center gap-2 transition">

            <Eye size={20} />

            Lihat Slip

          </button>

          <button className="border border-green-700 text-green-700 hover:bg-green-50 py-3 rounded-xl flex justify-center items-center gap-2 transition">

            <Download size={20} />

            Download PDF

          </button>

        </div>

      </div>

    </div>
  );
}

export default UserLatestSlip;