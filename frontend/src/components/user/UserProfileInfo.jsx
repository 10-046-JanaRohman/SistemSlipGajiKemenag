import {
  User,
  IdCard,
  Briefcase,
  Building2,
  Mail,
  Phone,
} from "lucide-react";

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

function UserProfileInfo() {
  return (
    <div className="bg-white rounded-2xl shadow-md p-8">

      <div className="flex items-center gap-6 mb-8">

        <div className="w-24 h-24 rounded-full bg-green-700 flex items-center justify-center text-white text-4xl font-bold">
          A
        </div>

        <div>

          <h2 className="text-3xl font-bold">
            Ahmad Fauzi
          </h2>

          <p className="text-gray-500">
            Pegawai
          </p>

        </div>

      </div>

      <div className="grid grid-cols-2 gap-8">

        <Item
          icon={<IdCard size={20} />}
          label="NIP"
          value="198712310001"
        />

        <Item
          icon={<Briefcase size={20} />}
          label="Jabatan"
          value="Analis Keuangan"
        />

        <Item
          icon={<Building2 size={20} />}
          label="Unit Kerja"
          value="Kanwil Kemenag Provinsi Lampung"
        />

        <Item
          icon={<Mail size={20} />}
          label="Email"
          value="ahmad@email.com"
        />

        <Item
          icon={<Phone size={20} />}
          label="No. HP"
          value="081234567890"
        />

        <Item
          icon={<User size={20} />}
          label="Role"
          value="Pegawai"
        />

      </div>

    </div>
  );
}

export default UserProfileInfo;