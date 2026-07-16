import {
  FileText,
  History,
  CircleCheck,
  ChevronRight,
} from "lucide-react";
import { Link } from "react-router-dom";

function UserStats({ data, loading }) {
  const d = data || {};
  const totalSlip = d.total_slip ?? 0;
  const statusSlip = d.status_slip || "Belum Ada Slip";
  if (loading) {
    return (
      <div className="grid grid-cols-3 gap-6">
        {[1, 2, 3].map((i) => (
          <div key={i} className="bg-white rounded-2xl shadow-md p-7 animate-pulse">
            <div className="h-8 w-8 bg-gray-200 rounded mb-4"></div>
            <div className="h-4 bg-gray-200 rounded w-1/2 mb-3"></div>
            <div className="h-8 bg-gray-200 rounded w-1/3"></div>
          </div>
        ))}
      </div>
    );
  }

  return (
    <div className="grid grid-cols-3 gap-6">
      <Card
        icon={<FileText size={30} />}
        title="Slip Gaji"
        value={`${totalSlip} Slip`}
        to="/user/slip"
      />

      <Card
        icon={<History size={30} />}
        title="Riwayat"
        value={`${totalSlip} Data`}
        to="/user/riwayat"
      />

      <Card
        icon={<CircleCheck size={30} />}
        title="Status"
        value={statusSlip}
        to="/user/slip"
      />

    </div>
  );
}

function Card({
  icon,
  title,
  value,
  to,
}) {
  return (
    <Link
      to={to}
      className="block rounded-2xl focus:outline-none focus-visible:ring-2 focus-visible:ring-green-700 focus-visible:ring-offset-2"
    >
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
    </Link>
  );
}

export default UserStats;
