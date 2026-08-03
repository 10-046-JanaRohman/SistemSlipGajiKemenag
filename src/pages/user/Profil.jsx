import { useState, useEffect } from "react";
import { Building2, IdCard, KeyRound, LockKeyhole, Mail, Pencil, Phone, Save, UserRound, X } from "lucide-react";
import UserLayout from "../../layouts/UserLayout";
import PageTransition from "../../components/common/PageTransition";
import api from "../../services/api";

const emptyPasswordForm = {
  current_password: "",
  password: "",
  password_confirmation: "",
};

function Profil() {
  const [user, setUser] = useState(null);
  const [pegawai, setPegawai] = useState(null);
  const [loading, setLoading] = useState(true);
  const [form, setForm] = useState(emptyPasswordForm);
  const [submitting, setSubmitting] = useState(false);
  const [profileForm, setProfileForm] = useState({ email: "", no_hp: "" });
  const [profileSubmitting, setProfileSubmitting] = useState(false);
  const [profileMessage, setProfileMessage] = useState("");
  const [profileError, setProfileError] = useState("");
  const [editingProfile, setEditingProfile] = useState(false);
  const [message, setMessage] = useState("");
  const [error, setError] = useState("");

  useEffect(() => {
    const fetchProfil = async () => {
      try {
        const result = await api.getProfil();
        const payload = result?.data || result;
        setUser(payload?.user || payload);
        setPegawai(payload?.pegawai || null);
        setProfileForm({
          email: payload?.user?.email || payload?.email || "",
          no_hp: payload?.pegawai?.no_hp || "",
        });
      } catch {
        try {
          const stored = localStorage.getItem("user");
          if (stored) setUser(JSON.parse(stored));
        } catch {
          setUser(null);
        }
      } finally {
        setLoading(false);
      }
    };

    fetchProfil();
  }, []);

  const handlePasswordChange = (event) => {
    const { name, value } = event.target;
    setForm((current) => ({ ...current, [name]: value }));
  };

  const handleProfileChange = (event) => {
    const { name, value } = event.target;
    setProfileForm((current) => ({ ...current, [name]: value }));
  };

  const handleProfileSubmit = async (event) => {
    event.preventDefault();
    setProfileMessage("");
    setProfileError("");
    setProfileSubmitting(true);

    try {
      const result = await api.updateProfil(profileForm);
      const payload = result?.data || result;
      const updatedUser = payload?.user || user;
      const updatedPegawai = payload?.pegawai || pegawai;

      setUser(updatedUser);
      setPegawai(updatedPegawai);
      setProfileForm({
        email: updatedUser?.email || "",
        no_hp: updatedPegawai?.no_hp || "",
      });

      const storedUser = localStorage.getItem("user");
      if (storedUser) {
        localStorage.setItem("user", JSON.stringify({ ...JSON.parse(storedUser), ...updatedUser }));
      }

      setProfileMessage(result?.message || "Profil berhasil diperbarui.");
      setEditingProfile(false);
    } catch (requestError) {
      setProfileError(requestError.message || "Profil gagal diperbarui.");
    } finally {
      setProfileSubmitting(false);
    }
  };

  const handlePasswordSubmit = async (event) => {
    event.preventDefault();
    setMessage("");
    setError("");

    if (form.password !== form.password_confirmation) {
      setError("Konfirmasi sandi baru harus sama.");
      return;
    }

    setSubmitting(true);
    try {
      const result = await api.gantiPassword(form);
      setMessage(result?.message || "Sandi berhasil diubah.");
      setForm(emptyPasswordForm);
    } catch (requestError) {
      setError(requestError.message || "Sandi gagal diubah.");
    } finally {
      setSubmitting(false);
    }
  };

  const nama = pegawai?.nama || user?.name || user?.nama || "Pegawai";
  const initial = nama.charAt(0).toUpperCase() || "P";

  return (
    <UserLayout>
      <PageTransition>
        <div className="mx-auto max-w-6xl space-y-8">
          <div>
            <h1 className="text-5xl font-bold text-slate-800">Profil Saya</h1>
            <p className="mt-2 text-gray-500">Informasi akun dan data kepegawaian.</p>
          </div>

          <section className="rounded-2xl bg-white p-8 shadow-md">
            {loading ? (
              <p className="text-gray-500">Memuat profil...</p>
            ) : (
              <>
                <div className="flex items-center justify-between gap-5 border-b border-gray-100 pb-7">
                  <div className="flex items-center gap-5">
                  <div className="flex h-24 w-24 shrink-0 items-center justify-center rounded-full bg-green-700 text-4xl font-bold text-white">
                    {initial}
                  </div>
                  <div>
                    <h2 className="text-3xl font-bold text-slate-800">{nama}</h2>
                    <p className="mt-1 text-gray-500">Pegawai</p>
                  </div>
                  </div>
                  {!editingProfile && (
                    <button type="button" onClick={() => { setProfileMessage(""); setProfileError(""); setEditingProfile(true); }} className="inline-flex items-center gap-2 rounded-xl border border-green-700 px-4 py-2.5 font-semibold text-green-700 transition hover:bg-green-50">
                      <Pencil size={17} /> Edit Profil
                    </button>
                  )}
                </div>

                {profileMessage && <div className="mt-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">{profileMessage}</div>}
                {profileError && <div className="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{profileError}</div>}

                <div className="mt-8 grid gap-x-16 gap-y-7 md:grid-cols-2">
                  <ProfileItem icon={<IdCard size={19} />} label="NIP" value={pegawai?.nip || user?.nip || "-"} />
                  <ProfileItem icon={<UserRound size={19} />} label="Jabatan" value={pegawai?.jabatan || "-"} />
                  <ProfileItem icon={<Building2 size={19} />} label="Unit Kerja" value={pegawai?.unit_kerja || pegawai?.keterangan_satuan_kerja || "KEMENAG PROV. LAMPUNG"} />
                  {editingProfile ? (
                    <EditProfileFields form={profileForm} onChange={handleProfileChange} />
                  ) : (
                    <>
                      <ProfileItem icon={<Mail size={19} />} label="Email" value={user?.email || "-"} />
                      <ProfileItem icon={<Phone size={19} />} label="No. HP" value={pegawai?.no_hp || "-"} />
                    </>
                  )}
                  <ProfileItem icon={<UserRound size={19} />} label="Role" value="Pegawai" />
                </div>

                {editingProfile && (
                  <form onSubmit={handleProfileSubmit} className="mt-7 flex flex-wrap justify-end gap-3 border-t border-gray-100 pt-6">
                    <button type="button" onClick={() => setEditingProfile(false)} disabled={profileSubmitting} className="inline-flex items-center gap-2 rounded-xl border border-gray-300 px-5 py-3 font-semibold text-slate-700 hover:bg-gray-50 disabled:opacity-60">
                      <X size={18} /> Batal
                    </button>
                    <button type="submit" disabled={profileSubmitting} className="inline-flex items-center gap-2 rounded-xl bg-green-700 px-5 py-3 font-semibold text-white transition hover:bg-green-800 disabled:cursor-not-allowed disabled:opacity-60">
                      <Save size={18} /> {profileSubmitting ? "Menyimpan..." : "Simpan"}
                    </button>
                  </form>
                )}
              </>
            )}
          </section>

          <section className="rounded-2xl bg-white p-8 shadow-md">
            <div className="mb-7 flex items-center gap-3">
              <LockKeyhole className="text-green-700" size={25} />
              <div>
                <h2 className="text-2xl font-bold text-slate-800">Keamanan Akun</h2>
                <p className="text-sm text-gray-500">Gunakan sandi baru yang kuat dan jangan bagikan kepada orang lain.</p>
              </div>
            </div>

            {message && <div className="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">{message}</div>}
            {error && <div className="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{error}</div>}

            <form onSubmit={handlePasswordSubmit} className="grid gap-5 md:grid-cols-3">
              <PasswordField label="Sandi Lama" name="current_password" value={form.current_password} onChange={handlePasswordChange} />
              <PasswordField label="Sandi Baru" name="password" value={form.password} onChange={handlePasswordChange} />
              <PasswordField label="Konfirmasi Sandi Baru" name="password_confirmation" value={form.password_confirmation} onChange={handlePasswordChange} />

              <div className="md:col-span-3 flex justify-end">
                <button type="submit" disabled={submitting} className="inline-flex items-center gap-2 rounded-xl bg-green-700 px-6 py-3 font-semibold text-white transition hover:bg-green-800 disabled:cursor-not-allowed disabled:opacity-60">
                  <KeyRound size={18} />
                  {submitting ? "Menyimpan..." : "Ubah Sandi"}
                </button>
              </div>
            </form>
          </section>
        </div>
      </PageTransition>
    </UserLayout>
  );
}

function ProfileItem({ icon, label, value }) {
  return (
    <div className="flex gap-3">
      <span className="mt-1 text-green-700">{icon}</span>
      <div>
        <p className="text-sm text-gray-500">{label}</p>
        <p className="mt-1 font-semibold text-slate-800">{value}</p>
      </div>
    </div>
  );
}

function EditProfileFields({ form, onChange }) {
  return (
    <>
      <label className="block">
        <span className="mb-2 block text-sm font-semibold text-slate-700">Email</span>
        <input required type="email" name="email" value={form.email} onChange={onChange} className="h-11 w-full rounded-xl border border-gray-300 px-4 outline-none transition focus:border-green-700 focus:ring-2 focus:ring-green-100" />
      </label>
      <label className="block">
        <span className="mb-2 block text-sm font-semibold text-slate-700">No. HP</span>
        <input type="tel" name="no_hp" value={form.no_hp} onChange={onChange} placeholder="Contoh: 081234567890" className="h-11 w-full rounded-xl border border-gray-300 px-4 outline-none transition focus:border-green-700 focus:ring-2 focus:ring-green-100" />
      </label>
    </>
  );
}

function PasswordField({ label, name, value, onChange }) {
  return (
    <label className="block">
      <span className="mb-2 block text-sm font-semibold text-slate-700">{label}</span>
      <input required minLength="8" type="password" name={name} value={value} onChange={onChange} className="h-11 w-full rounded-xl border border-gray-300 px-4 outline-none transition focus:border-green-700 focus:ring-2 focus:ring-green-100" />
    </label>
  );
}

export default Profil;
