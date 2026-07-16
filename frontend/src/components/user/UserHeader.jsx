function UserHeader({ user }) {
  const nama = user?.nama || user?.name || "Pegawai";

  return (
    <div>
      <h1 className="text-5xl font-bold text-slate-800">Selamat Datang, {nama}</h1>
      <p className="mt-2 text-lg text-gray-500">Semoga harimu menyenangkan.</p>
    </div>
  );
}

export default UserHeader;
