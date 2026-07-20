const API_ORIGIN = import.meta.env.VITE_API_URL || "http://127.0.0.1:8000";
const API_BASE = `${API_ORIGIN.replace(/\/$/, "")}/api`;

class ApiService {
  constructor() {
    this.token = localStorage.getItem("token");
  }

  getToken() {
    return localStorage.getItem("token") || sessionStorage.getItem("token");
  }

  getHeaders(extra = {}) {
    const headers = {
      Accept: "application/json",
      ...extra,
    };

    const token = this.getToken();
    if (token) {
      headers.Authorization = `Bearer ${token}`;
    }

    return headers;
  }

  clearAuth() {
    this.token = null;
    localStorage.removeItem("token");
    sessionStorage.removeItem("token");
    localStorage.removeItem("user");
    localStorage.removeItem("role");
  }

  buildQuery(params = {}) {
    const query = new URLSearchParams();
    Object.entries(params).forEach(([key, value]) => {
      if (value !== undefined && value !== null && value !== "") {
        query.set(key, value);
      }
    });
    const result = query.toString();
    return result ? `?${result}` : "";
  }

  async request(method, path, data = null) {
    const config = {
      method,
      headers: this.getHeaders(),
    };

    if (data instanceof FormData) {
      config.body = data;
    } else if (data !== null && method !== "GET") {
      config.headers["Content-Type"] = "application/json";
      config.body = JSON.stringify(data);
    }

    let response;
    try {
      response = await fetch(`${API_BASE}${path}`, config);
    } catch {
      throw new Error("Tidak dapat terhubung ke server.");
    }
    const contentType = response.headers.get("content-type") || "";

    let payload;
    if (contentType.includes("application/json")) {
      payload = await response.json();
    } else {
      payload = await response.text();
    }

    if (!response.ok || (payload && payload.success === false)) {
      const validationMessage = payload?.errors
        ? Object.values(payload.errors).flat()[0]
        : null;
      const message =
        validationMessage ||
        payload?.message ||
        payload?.error ||
        response.statusText ||
        "Permintaan gagal.";

      const error = new Error(message);
      error.status = response.status;
      error.payload = payload;
      if (response.status === 401 && path !== "/login") {
        this.clearAuth();
        window.dispatchEvent(new Event("auth:unauthorized"));
      }
      throw error;
    }

    return payload;
  }

  // ==================== AUTH ====================

  async login(nip, password, remember = false) {
    const result = await this.request("POST", "/login", {
      nip,
      password,
      remember,
    });

    const token = result?.token || result?.data?.token || null;
    const user = result?.user || result?.data?.user || null;

    if (!token || !user?.role) {
      throw new Error("Respons login dari server tidak lengkap.");
    }

    this.token = token;
    localStorage.removeItem("token");
    sessionStorage.removeItem("token");
    (remember ? localStorage : sessionStorage).setItem("token", token);
    localStorage.setItem("user", JSON.stringify(user));
    localStorage.setItem("role", user.role);

    return result;
  }

  async forgotPassword(email) {
    return this.request("POST", "/forgot-password", { email });
  }

  async resetPassword({ token, email, password, passwordConfirmation }) {
    return this.request("POST", "/reset-password", {
      token,
      email,
      password,
      password_confirmation: passwordConfirmation,
    });
  }

  async logout() {
    try {
      await this.request("POST", "/logout");
    } finally {
      this.clearAuth();
    }
  }

  async getProfile() {
    return this.request("GET", "/profile");
  }

  // ==================== DASHBOARD ====================

  async getDashboard() {
    return this.request("GET", "/dashboard");
  }

  async searchGlobal(query) {
    return this.request("GET", `/search${this.buildQuery({ q: query })}`);
  }

  async getNotifikasi() {
    return this.request("GET", "/notifikasi");
  }

  async markAllNotifikasiRead() {
    return this.request("PATCH", "/notifikasi/read-all");
  }

  // ==================== PEGAWAI ====================

  async getPegawai(page = 1, params = {}) {
    return this.request("GET", `/pegawai${this.buildQuery({ page, ...params })}`);
  }

  async getPegawaiDetail(id) {
    return this.request("GET", `/pegawai/${id}`);
  }

  async createPegawai(data) {
    return this.request("POST", "/pegawai", data);
  }

  async updatePegawai(id, data) {
    return this.request("PATCH", `/pegawai/${id}`, data);
  }

  async deletePegawai(id) {
    return this.request("DELETE", `/pegawai/${id}`);
  }

  // ==================== SLIP GAJI ====================

  async getSlipGaji(params = {}) {
    return this.request("GET", `/slip-gaji${this.buildQuery(params)}`);
  }

  async getSlipDetail(id) {
    return this.request("GET", `/slip-gaji/${id}`);
  }

  async getSlipPdf(id) {
    return this.downloadPdf(`/slip-gaji/${id}/pdf`, `slip-gaji-${id}.pdf`);
  }

  async getRiwayatSlip(params = {}) {
    return this.request("GET", `/riwayat-slip${this.buildQuery(params)}`);
  }

  async getAdminRiwayatSlip(params = {}) {
    return this.getRiwayatSlip(params);
  }

  async getSlipSaya(params = {}) {
    return this.request("GET", `/slip-gaji${this.buildQuery(params)}`);
  }

  async getSlipSayaDetail(id) {
    return this.request("GET", `/slip-gaji/${id}`);
  }

  // ==================== PROFIL ====================

  async getProfil() {
    return this.request("GET", "/profil");
  }

  async gantiPassword(data) {
    return this.request("PATCH", "/ganti-password", data);
  }

  // ==================== SETTINGS ====================

  async getSettings() {
    return this.request("GET", "/settings");
  }

  async updateSettings(data) {
    return this.request("PATCH", "/settings", data);
  }

  // ==================== PENGUMUMAN ====================

  async getPengumuman(params = {}) {
    return this.request("GET", `/pengumuman${this.buildQuery(params)}`);
  }

  async createPengumuman(data) {
    return this.request("POST", "/pengumuman", data);
  }

  async updatePengumuman(id, data) {
    return this.request("PUT", `/pengumuman/${id}`, data);
  }

  async deletePengumuman(id) {
    return this.request("DELETE", `/pengumuman/${id}`);
  }

  // ==================== IMPORT GAJI ====================

  async getImportHistory(params = {}) {
    return this.request("GET", `/import-gaji${this.buildQuery(params)}`);
  }

  async importGaji({ file, bulan, tahun }) {
    const formData = new FormData();
    formData.append("file_excel", file);
    formData.append("bulan", bulan);
    formData.append("tahun", tahun);

    return this.request("POST", "/import-gaji", formData);
  }

  async previewImportGaji({ file, reviewToken, page = 1 }) {
    const formData = new FormData();
    if (file) {
      formData.append("file_excel", file);
    }
    if (reviewToken) {
      formData.append("review_token", reviewToken);
    }
    formData.append("page", page);

    return this.request("POST", "/import-gaji/preview", formData);
  }

  async cancelPreviewImportGaji(reviewToken) {
    return this.request("DELETE", "/import-gaji/preview", { review_token: reviewToken });
  }

  async importReviewedGaji({ bulan, tahun, rows, reviewToken }) {
    return this.request("POST", "/import-gaji/reviewed", {
      bulan,
      tahun,
      review_token: reviewToken,
      rows,
    });
  }

  // ==================== PDF DOWNLOAD ====================

  async downloadPdf(path, filename = "download.pdf") {
    const signedPath = path.replace(/\/pdf$/, "/pdf-url");
    const result = await this.request("GET", signedPath);
    const url = result?.data?.url || result?.url;

    if (!url) {
      throw new Error("Tautan unduhan PDF tidak tersedia.");
    }

    const link = document.createElement("a");
    link.href = url;
    link.rel = "noopener";
    document.body.appendChild(link);
    link.click();
    link.remove();
  }
}

export default new ApiService();
