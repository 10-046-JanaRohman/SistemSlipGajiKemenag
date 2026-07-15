import {
  FileText,
  History,
 CircleCheck,
  ChevronRight,
} from "lucide-react";

function UserStats() {
  return (
    <div className="grid grid-cols-3 gap-6">

      <Card
        icon={<FileText size={30} />}
        title="Slip Gaji"
        value="12 Slip"
      />

      <Card
        icon={<History size={30} />}
        title="Riwayat"
        value="12 Data"
      />

      <Card
        icon={<CircleCheck size={30} />}
        title="Status"
        value="Aktif"
      />

    </div>
  );
}

function Card({
  icon,
  title,
  value,
}) {
  return (
    <div className="
      bg-white
      rounded-2xl
      shadow-md
      p-7
      cursor-pointer
      hover:shadow-xl
      hover:-translate-y-1
      transition-all
      duration-300
    ">

      <div className="text-green-700">

        {icon}

      </div>

      <h3 className="mt-5 text-lg font-semibold text-slate-800">

        {title}

      </h3>

      <p className="text-3xl font-bold mt-2">

        {value}

      </p>

      <div className="flex items-center justify-end mt-8 text-green-700">

        <span className="text-sm font-medium">

          Lihat

        </span>

        <ChevronRight
          size={18}
          className="ml-1"
        />

      </div>

    </div>
  );
}

export default UserStats;