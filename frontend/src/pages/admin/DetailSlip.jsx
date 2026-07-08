import AdminLayout from "../../layouts/AdminLayout";
import PageTransition from "../../components/common/PageTransition";

function DetailSlip() {
  return (
    <AdminLayout>
        <PageTransition>
      <div className="space-y-8">

        {/* Judul */}

        <div>

          <h1 className="text-5xl font-bold text-slate-800">
            Detail Slip Gaji
          </h1>

          <p className="text-gray-500 mt-2">
            Informasi lengkap slip gaji pegawai.
          </p>

        </div>

        {/* Card Data Pegawai */}

        <div className="bg-white rounded-2xl shadow p-8">

          <h2 className="text-2xl font-bold mb-6">
            Data Pegawai
          </h2>

          <div className="grid grid-cols-2 gap-5">

            <Item label="Nama" value="Ahmad Fauzi" />

            <Item label="NIP" value="19871231" />

            <Item label="Jabatan" value="Analis Keuangan" />

            <Item label="Golongan" value="III/b" />

            <Item label="Unit Kerja" value="Kanwil Kemenag Lampung" />

            <Item label="Periode" value="Juli 2026" />

          </div>

        </div>

        {/* Card Rincian */}

        <div className="bg-white rounded-2xl shadow p-8">

          <h2 className="text-2xl font-bold mb-6">

            Rincian Gaji

          </h2>

          <div className="space-y-4">

            <Row title="Gaji Pokok" value="Rp5.000.000" />

            <Row title="Tunjangan" value="Rp800.000" />

            <Row title="Transport" value="Rp200.000" />

            <Row title="Lembur" value="Rp100.000" />

            <hr />

            <Row title="BPJS" value="- Rp150.000" />

            <Row title="Pajak" value="- Rp250.000" />

            <hr />

            <Row
              title="TOTAL DITERIMA"
              value="Rp5.700.000"
              bold
            />

          </div>

        </div>

        {/* Tombol */}

        <div className="flex gap-4">

          <button className="bg-green-700 hover:bg-green-800 text-white px-8 py-3 rounded-xl font-semibold">

            Download PDF

          </button>

          <button
            onClick={() => window.history.back()}
            className="bg-gray-200 hover:bg-gray-300 px-8 py-3 rounded-xl font-semibold"
          >

            Kembali

          </button>

        </div>

      </div>
        </PageTransition>
    </AdminLayout>
  );
}

function Item({ label, value }) {
  return (
    <div>

      <p className="text-gray-500 text-sm">
        {label}
      </p>

      <p className="font-semibold text-lg">
        {value}
      </p>

    </div>
  );
}

function Row({ title, value, bold }) {
  return (
    <div className="flex justify-between">

      <span className={bold ? "font-bold text-xl" : ""}>
        {title}
      </span>

      <span className={bold ? "font-bold text-xl text-green-700" : ""}>
        {value}
      </span>

    </div>
  );
}

export default DetailSlip;