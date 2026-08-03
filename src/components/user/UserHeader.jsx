function UserHeader({ user }) {
  const nama = user?.nama || user?.name || "Pegawai";

  return (
    <div>
      <h1 className="text-3xl font-bold text-slate-800 sm:text-5xl">Selamat Datang, {nama}</h1>
      <p className="mt-2 text-lg text-gray-500">Semoga harimu menyenangkan.</p>
    </div>
  );
}

export default UserHeader;
