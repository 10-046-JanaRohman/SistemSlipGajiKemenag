import {
  Lock,
  KeyRound,
} from "lucide-react";

function UserProfileSecurity() {
  return (
    <div className="bg-white rounded-2xl shadow-md p-8">

      <h2 className="text-2xl font-bold mb-6">
        Keamanan Akun
      </h2>

      <div className="flex justify-between items-center border rounded-xl p-5">

        <div className="flex items-center gap-4">

          <Lock
            size={24}
            className="text-green-700"
          />

          <div>

            <p className="font-semibold">
              Password
            </p>

            <p className="text-gray-500 text-sm">
              Terakhir diubah 30 hari yang lalu.
            </p>

          </div>

        </div>

        <button className="bg-green-700 hover:bg-green-800 text-white px-6 py-3 rounded-xl flex items-center gap-2">

          <KeyRound size={18} />

          Ubah Password

        </button>

      </div>

    </div>
  );
}

export default UserProfileSecurity;