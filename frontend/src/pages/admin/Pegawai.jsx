import { useState, useEffect, useCallback } from "react";
import { useSearchParams } from "react-router-dom";
import AdminLayout from "../../layouts/AdminLayout";
import PageTransition from "../../components/common/PageTransition";

import PegawaiHeader from "../../components/pegawai/PegawaiHeader";
import PegawaiToolbar from "../../components/pegawai/PegawaiToolbar";
import PegawaiTable from "../../components/pegawai/PegawaiTable";
import PegawaiPagination from "../../components/pegawai/PegawaiPagination";
import PegawaiModal from "../../components/pegawai/PegawaiModal";
import api from "../../services/api";

function Pegawai() {
  const [searchParams] = useSearchParams();
  const [data, setData] = useState([]);
  const [meta, setMeta] = useState({ current_page: 1, last_page: 1, total: 0 });
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState(() => searchParams.get("search") || "");
  const [page, setPage] = useState(1);
  const [modalOpen, setModalOpen] = useState(false);
  const [modalMode, setModalMode] = useState("create");
  const [selectedPegawai, setSelectedPegawai] = useState(null);
  const [saving, setSaving] = useState(false);
  const [modalError, setModalError] = useState("");
  const [message, setMessage] = useState("");

  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const result = await api.getPegawai(page, { search });
      const payload = result?.data || result;
      setData(Array.isArray(payload?.data) ? payload.data : Array.isArray(payload) ? payload : []);
      setMeta({
        current_page: payload?.current_page || 1,
        last_page: payload?.last_page || 1,
        total: payload?.total || 0,
      });
    } catch {
      setData([]);
    } finally {
      setLoading(false);
    }
  }, [page, search]);

  useEffect(() => {
    fetchData();
  }, [fetchData]);

  const handleSearch = (value) => {
    setSearch(value);
    setPage(1);
  };

  const openCreateModal = () => {
    setSelectedPegawai(null);
    setModalMode("create");
    setModalError("");
    setModalOpen(true);
  };

  const openEditModal = (pegawai) => {
    setSelectedPegawai(pegawai);
    setModalMode("edit");
    setModalError("");
    setModalOpen(true);
  };

  const handleSubmit = async (form) => {
    setSaving(true);
    setModalError("");
    setMessage("");

    try {
      if (modalMode === "edit" && selectedPegawai?.id) {
        await api.updatePegawai(selectedPegawai.id, form);
        setMessage("Pegawai berhasil diperbarui.");
      } else {
        await api.createPegawai(form);
        setMessage("Pegawai berhasil ditambahkan.");
      }
      setModalOpen(false);
      fetchData();
    } catch (error) {
      setModalError(error.message || "Gagal menyimpan pegawai.");
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async (pegawai) => {
    if (!pegawai?.id) return;
    const confirmed = window.confirm(`Hapus pegawai ${pegawai.nama || pegawai.nip}?`);
    if (!confirmed) return;

    setMessage("");
    try {
      await api.deletePegawai(pegawai.id);
      setMessage("Pegawai berhasil dihapus.");
      fetchData();
    } catch (error) {
      setMessage(error.message || "Gagal menghapus pegawai.");
    }
  };

  return (
    <AdminLayout>
      <PageTransition>

        <div className="space-y-8">

          <PegawaiHeader onAdd={openCreateModal} />

          {message && (
            <div className={`rounded-xl border px-4 py-3 text-sm font-semibold ${
              message.toLowerCase().includes("berhasil")
                ? "border-green-200 bg-green-50 text-green-700"
                : "border-red-200 bg-red-50 text-red-700"
            }`}>
              {message}
            </div>
          )}

          <PegawaiToolbar search={search} onSearch={handleSearch} />

          <PegawaiTable
            data={data}
            loading={loading}
            onEdit={openEditModal}
            onDelete={handleDelete}
          />

          <PegawaiPagination
            meta={meta}
            page={page}
            onPageChange={setPage}
          />

          <PegawaiModal
            open={modalOpen}
            mode={modalMode}
            pegawai={selectedPegawai}
            loading={saving}
            error={modalError}
            onClose={() => setModalOpen(false)}
            onSubmit={handleSubmit}
          />

        </div>

      </PageTransition>
    </AdminLayout>
  );
}

export default Pegawai;
