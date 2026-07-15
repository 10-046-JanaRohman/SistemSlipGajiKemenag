import { Megaphone, Clock } from "lucide-react";

const announcements = [
  {
    title: "Slip Gaji Juli 2026 Telah Tersedia",
    time: "10 menit yang lalu",
  },
  {
    title: "Pastikan data profil Anda sudah benar.",
    time: "Kemarin",
  },
  {
    title: "Layanan sistem akan dilakukan pemeliharaan pada akhir pekan.",
    time: "3 hari yang lalu",
  },
];

function UserAnnouncement() {
  return (
    <div className="bg-white rounded-2xl shadow-md p-8">

      {/* Header */}
      <div className="flex items-center gap-3 mb-6">

        <Megaphone
          className="text-green-700"
          size={26}
        />

        <h2 className="text-2xl font-bold text-slate-800">
          Pengumuman
        </h2>

      </div>

      {/* List */}
      <div className="space-y-5">

        {announcements.map((item, index) => (

          <div
            key={index}
            className="border-l-4 border-green-700 pl-4"
          >

            <h3 className="font-semibold text-slate-800">
              {item.title}
            </h3>

            <div className="flex items-center gap-2 mt-2 text-gray-500 text-sm">

              <Clock size={14} />

              {item.time}

            </div>

          </div>

        ))}

      </div>

    </div>
  );
}

export default UserAnnouncement;