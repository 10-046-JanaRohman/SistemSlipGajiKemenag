import { User, Briefcase, Building2, IdCard } from "lucide-react";

function UserProfileCard({ user, data }) {
  const pegawai = data?.pegawai || data?.slip_terakhir?.pegawai || {};
  const nama = pegawai?.nama || user?.nama || user?.name || "Pegawai";
  const nip = pegawai?.nip || user?.nip || "-";
  const role = ["pegawai", "user"].includes(user?.role) ? "Pegawai" : "-";

  const jabatan = pegawai?.jabatan || user?.jabatan || "-";
  const unitKerja = pegawai?.unit_kerja || pegawai?.keterangan_satuan_kerja || user?.unit_kerja || "-";

  return (
    <div className="bg-white rounded-2xl shadow-md p-8">
      {/* Avatar */}
      <div className="flex flex-col items-center">

        <div className="w-24 h-24 rounded-full bg-green-700 flex items-center justify-center text-white">

          <User size={42} />
        </div>

        <h2 className="mt-5 text-2xl font-bold text-slate-800">
          {nama}
        </h2>

        <p className="text-gray-500">
          {role}
        </p>

      </div>

      {/* Data */}
      <div className="mt-8 space-y-5">

        <Item
          icon={<IdCard size={18} />}
          label="NIP"
          value={nip}
        />

        <Item
          icon={<Briefcase size={18} />}
          label="Jabatan"
          value={jabatan}
        />

        <Item
          icon={<Building2 size={18} />}
          label="Unit Kerja"
          value={unitKerja}
        />

      </div>

    </div>
  );
}

function Item({ icon, label, value }) {
  return (
    <div className="flex items-start gap-4">

      <div className="text-green-700 mt-1">
        {icon}
      </div>

      <div>

        <p className="text-sm text-gray-500">
          {label}
        </p>

        <p className="font-semibold text-slate-800">
          {value}
        </p>

      </div>

    </div>
  );
}

export default UserProfileCard;
