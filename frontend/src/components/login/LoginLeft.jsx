import logoKemenag from "../../assets/images/logo-kemenag.png";

function LoginLeft() {
  return (
    <div className="hidden w-1/2 flex-col items-center bg-gradient-to-b from-green-100 to-white px-12 pt-16 md:flex">

      <img
        src={logoKemenag}
        alt="Logo Kemenag"
        className="w-24"
      />

      <h2 className="text-green-800 font-bold text-xl mt-6">
        KEMENTERIAN AGAMA
      </h2>

      <p className="text-green-700 text-lg">
        PROVINSI LAMPUNG
      </p>

      <h1 className="text-4xl font-extrabold text-slate-800 text-center leading-tight mt-12">
        SISTEM DASHBOARD
        <br />
        SLIP GAJI KARYAWAN
      </h1>

      <p className="text-gray-500 text-center mt-6 leading-8 text-lg max-w-md">
        Sistem informasi untuk melihat,
        mengelola, dan mengunduh slip gaji
        karyawan Kantor Wilayah
        Kementerian Agama Provinsi Lampung.
      </p>

    </div>
  );
}

export default LoginLeft;
