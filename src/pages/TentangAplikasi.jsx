import { Code2, GraduationCap, UsersRound } from "lucide-react";
import AdminLayout from "../layouts/AdminLayout";
import UserLayout from "../layouts/UserLayout";
import PageTransition from "../components/common/PageTransition";

const developers = [
  { name: "Jana Rohman Wasiso", role: "Mahasiswa Magang" },
  { name: "Adelia Ramadani", role: "Mahasiswi Magang" },
];

function TentangAplikasi({ role }) {
  const Layout = role === "user" ? UserLayout : AdminLayout;

  return (
    <Layout>
      <PageTransition>
        <div className="w-full space-y-8">
          <div>
            <h1 className="text-4xl font-bold text-slate-800 sm:text-5xl">Tentang Website</h1>
            <p className="mt-2 text-gray-500">Informasi Sistem Dashboard Slip Gaji.</p>
          </div>

          <section className="overflow-hidden rounded-2xl bg-white shadow-md">
            <div className="bg-green-800 px-6 py-7 text-white sm:px-8">
              <div className="flex items-center gap-3 text-green-100">
                <Code2 size={24} />
                <span className="text-sm font-semibold">SISTEM INFORMASI</span>
              </div>
              <h2 className="mt-3 text-2xl font-bold sm:text-3xl">Website Slip Gaji Kemenag Provinsi Lampung</h2>
            </div>

            <div className="space-y-8 p-6 sm:p-8">
              <div>
                <h3 className="text-xl font-bold text-slate-800">Tentang Website</h3>
                <p className="mt-2 leading-relaxed text-gray-600">
                  Website ini membantu pengelolaan dan akses informasi slip gaji pegawai secara lebih teratur dan efisien.
                </p>
              </div>

              <div className="border-t border-gray-100 pt-7">
                <div className="flex items-center gap-3">
                  <UsersRound className="text-green-700" size={24} />
                  <div>
                    <h3 className="text-xl font-bold text-slate-800">Dikembangkan oleh Mahasiswa INSTITUT TEKNOLOGI SUMATERA</h3>
                    <p className="text-sm text-gray-500">Teknik Informatika</p>
                  </div>
                </div>

                <div className="mt-5 grid gap-4 sm:grid-cols-2">
                  {developers.map((developer) => (
                    <div key={developer.name} className="rounded-xl border border-green-100 bg-green-50 p-5">
                      <GraduationCap className="text-green-700" size={24} />
                      <p className="mt-3 font-semibold text-green-950">{developer.name}</p>
                      <p className="mt-1 text-sm text-green-700">{developer.role}</p>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </section>
        </div>
      </PageTransition>
    </Layout>
  );
}

export default TentangAplikasi;
