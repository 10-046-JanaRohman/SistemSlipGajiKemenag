# Dokumentasi API - Sistem Slip Gaji Kemenag Lampung

## Base URL
```
http://127.0.0.1:8000/api
```

## Authentication
API menggunakan Laravel Sanctum untuk authentication. Setelah login, simpan token dan kirim di header setiap request:

```
Authorization: Bearer {token}
Content-Type: application/json
```

---

## 1. LOGIN

**Endpoint:** `POST /api/login`

**Request Body:**
```json
{
    "email": "admin@kemenag.go.id",
    "password": "password"
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Login berhasil.",
    "token": "1|laravel_sanctum_token_here",
    "user": {
        "id": 1,
        "name": "Admin Keuangan",
        "email": "admin@kemenag.go.id",
        "role": "admin",
        "pegawai": {
            "id": 1,
            "nip": "198512122025210102",
            "nama": "ALFICHRI PRATAMA HAMZAH",
            "jabatan": "Kepala Seksi",
            "golongan": "III/a",
            "unit_kerja": "KEMENAG PROV. LAMPUNG"
        }
    }
}
```

**Response Error (401):**
```json
{
    "success": false,
    "message": "Email atau password salah."
}
```

---

## 2. LOGOUT

**Endpoint:** `POST /api/logout`

**Headers:** Authorization: Bearer {token}

**Response Success (200):**
```json
{
    "success": true,
    "message": "Logout berhasil."
}
```

---

## 3. PROFILE

**Endpoint:** `GET /api/profile`

**Headers:** Authorization: Bearer {token}

**Response Success (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Admin Keuangan",
        "email": "admin@kemenag.go.id",
        "role": "admin",
        "pegawai": {
            "id": 1,
            "nip": "198512122025210102",
            "nama": "ALFICHRI PRATAMA HAMZAH",
            "jabatan": "Kepala Seksi",
            "golongan": "III/a",
            "unit_kerja": "KEMENAG PROV. LAMPUNG"
        }
    }
}
```

---

## 4. DASHBOARD

**Endpoint:** `GET /api/dashboard`

**Headers:** Authorization: Bearer {token}

**Response untuk PEGAWAI (200):**
```json
{
    "success": true,
    "message": "Dashboard berhasil diambil.",
    "data": {
        "total_slip": 5,
        "gaji_terakhir": 3545398,
        "slip_terakhir": {
            "id": 1,
            "bulan": 3,
            "tahun": 2026,
            "tanggal_terbit": "2026-07-06",
            "gaji_bersih": 3545398
        },
        "status_slip": "Sudah Terbit"
    }
}
```

**Response untuk ADMIN (200):**
```json
{
    "success": true,
    "message": "Dashboard berhasil diambil.",
    "data": {
        "total_pegawai": 1245,
        "total_slip_keseluruhan": 956,
        "total_gaji_keseluruhan": 2850000000,
        "total_slip_periode": 910,
        "total_gaji_periode": 2715000000,
        "belum_terbit": 46,
        "import_terakhir": {
            "id": 1,
            "bulan": "Maret",
            "tahun": 2026,
            "nama_file": "lampiran_gaji_maret.xlsx",
            "berhasil": 910,
            "gagal": 0
        },
        "slip_terbaru": [
            {
                "id": 1,
                "pegawai": {
                    "nama": "ALFICHRI PRATAMA HAMZAH",
                    "nip": "198512122025210102"
                },
                "bulan": 3,
                "tahun": 2026,
                "gaji_bersih": 3545398
            }
        ]
    }
}
```

---

## 5. SLIP GAJI (LIST)

**Endpoint:** `GET /api/slip-gaji`

**Headers:** Authorization: Bearer {token}

**Query Parameters (opsional):**
- `bulan` (integer) - Filter by bulan (1-12)
- `tahun` (integer) - Filter by tahun
- `search` (string) - Search by nama/NIP pegawai (hanya untuk admin)
- `page` (integer) - Nomor halaman

**Response untuk PEGAWAI (200):**
```json
{
    "success": true,
    "message": "Data slip gaji berhasil diambil.",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "pegawai_id": 466,
                "bulan": 3,
                "tahun": 2026,
                "tanggal_terbit": "2026-07-06",
                "gaji_pokok": 3203600,
                "tunjangan": 633504,
                "potongan": 291706,
                "gaji_bersih": 3545398,
                "pegawai": {
                    "nama": "ALFICHRI PRATAMA HAMZAH",
                    "nip": "198512122025210102",
                    "jabatan": "Kepala Seksi"
                }
            }
        ],
        "total": 5,
        "per_page": 10,
        "last_page": 1
    }
}
```

**Response untuk ADMIN (dengan search):**
```json
{
    "success": true,
    "message": "Data slip gaji berhasil diambil.",
    "data": {
        "current_page": 1,
        "data": [...],
        "total": 100,
        "per_page": 10,
        "last_page": 10
    }
}
```

---

## 6. SLIP GAJI (DETAIL)

**Endpoint:** `GET /api/slip-gaji/{id}`

**Headers:** Authorization: Bearer {token}

**Response Success (200):**
```json
{
    "success": true,
    "message": "Detail slip gaji.",
    "data": {
        "slip": {
            "id": 1,
            "pegawai_id": 466,
            "bulan": 3,
            "tahun": 2026,
            "tanggal_terbit": "2026-07-06",
            "gaji_pokok": 3203600,
            "tunjangan": 633504,
            "potongan": 291706,
            "gaji_bersih": 3545398,
            "pegawai": {
                "nama": "ALFICHRI PRATAMA HAMZAH",
                "nip": "198512122025210102",
                "jabatan": "Kepala Seksi",
                "golongan": "III/a",
                "unit_kerja": "KEMENAG PROV. LAMPUNG"
            }
        },
        "rincian": {
            "pegawai": {
                "nama": "ALFICHRI PRATAMA HAMZAH",
                "nip": "198512122025210102",
                "golongan": "III/a",
                "jabatan": "Kepala Seksi",
                "unit_kerja": "KEMENAG PROV. LAMPUNG",
                "bank": "BNI",
                "rekening": "1234567890"
            },
            "pendapatan": {
                "Gaji Pokok": 3203600,
                "Tunjangan Jabatan": 500000,
                "Tunjangan Beras": 133504
            },
            "potongan": {
                "PPh Pasal 21": 250000,
                "BPJS": 41706
            },
            "total_pendapatan": 3837104,
            "total_potongan": 291706,
            "gaji_bersih": 3545398
        }
    }
}
```

**Response Error (403) - Pegawai akses slip orang lain:**
```json
{
    "success": false,
    "message": "Anda tidak memiliki akses ke slip ini."
}
```

---

## 7. RIWAYAT SLIP

**Endpoint:** `GET /api/riwayat-slip`

**Headers:** Authorization: Bearer {token}

**Query Parameters (opsional):**
- `bulan` (integer) - Filter by bulan (1-12)
- `tahun` (integer) - Filter by tahun
- `page` (integer) - Nomor halaman

**Response untuk PEGAWAI (200):**
```json
{
    "success": true,
    "message": "Riwayat slip berhasil diambil.",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "bulan": 3,
                "tahun": 2026,
                "tanggal_terbit": "2026-07-06",
                "gaji_bersih": 3545398,
                "pegawai": {
                    "nama": "ALFICHRI PRATAMA HAMZAH"
                }
            }
        ],
        "total": 5,
        "per_page": 10,
        "last_page": 1
    }
}
```

---

## 8. GANTI PASSWORD

**Endpoint:** `PATCH /api/ganti-password`

**Headers:** 
- Authorization: Bearer {token}
- Content-Type: application/json

**Request Body:**
```json
{
    "current_password": "old_password",
    "password": "new_password",
    "password_confirmation": "new_password"
}
```

**Validation Rules:**
- `current_password` - required, harus sesuai dengan password saat ini
- `password` - required, min 8 karakter, confirmed
- `password_confirmation` - required, harus sama dengan password

**Response Success (200):**
```json
{
    "success": true,
    "message": "Password berhasil diubah."
}
```

**Response Error (422):**
```json
{
    "message": "Password lama harus diisi.",
    "errors": {
        "current_password": ["Password lama harus diisi."]
    }
}
```

---

## 9. PEGAWAI (LIST)

**Endpoint:** `GET /api/pegawai`

**Headers:** Authorization: Bearer {token}

**Query Parameters (opsional):**
- `search` (string) - Search by nama atau NIP

**Response Success (200):**
```json
{
    "success": true,
    "message": "Data pegawai berhasil diambil.",
    "data": [
        {
            "id": 1,
            "nip": "198512122025210102",
            "nama": "ALFICHRI PRATAMA HAMZAH",
            "jabatan": "Kepala Seksi",
            "golongan": "III/a",
            "unit_kerja": "KEMENAG PROV. LAMPUNG",
            "no_hp": "081234567890",
            "npwp": "1234567890",
            "rekening": "1234567890",
            "nama_bank": "BNI"
        }
    ]
}
```

---

## 10. PEGAWAI (DETAIL)

**Endpoint:** `GET /api/pegawai/{id}`

**Headers:** Authorization: Bearer {token}

**Response Success (200):**
```json
{
    "success": true,
    "message": "Detail pegawai.",
    "data": {
        "id": 1,
        "nip": "198512122025210102",
        "nama": "ALFICHRI PRATAMA HAMZAH",
        "jabatan": "Kepala Seksi",
        "golongan": "III/a",
        "unit_kerja": "KEMENAG PROV. LAMPUNG",
        "no_hp": "081234567890",
        "npwp": "1234567890",
        "rekening": "1234567890",
        "nama_bank": "BNI",
        "tempat_lahir": "Bandar Lampung",
        "tanggal_lahir": "1985-12-12",
        "jenis_kelamin": "L",
        "agama": "Islam",
        "status_pegawai": "PEGAWAI",
        "alamat": "Jl. Contoh No. 123"
    }
}
```

---

## ERROR RESPONSES

### 401 Unauthorized
```json
{
    "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
    "success": false,
    "message": "Anda tidak memiliki akses ke slip ini."
}
```

### 404 Not Found
```json
{
    "message": "No query results for model [App\\Models\\SlipGaji] 999"
}
```

### 422 Validation Error
```json
{
    "message": "Password lama harus diisi.",
    "errors": {
        "current_password": ["Password lama harus diisi."]
    }
}
```

### 500 Server Error
```json
{
    "message": "Server Error",
    "error": "Exception message here"
}
```

---

## CATATAN PENTING:

1. **Role-based access:**
   - Pegawai hanya bisa melihat data miliknya sendiri
   - Admin bisa melihat semua data
   - Token expired setelah logout atau session habis

2. **Pagination:**
   - Default: 10 items per page
   - Response includes: current_page, data, total, per_page, last_page

3. **Format Tanggal:**
   - Format: YYYY-MM-DD (ISO 8601)
   - Display di frontend bisa di-format sesuai kebutuhan

4. **Format Uang:**
   - Simpan sebagai integer/float (contoh: 3545398)
   - Display di frontend: Rp 3.545.398

5. **Bulan:**
   - Disimpan sebagai integer (1-12)
   - Display di frontend: gunakan Carbon untuk konversi ke nama bulan

6. **Token Management:**
   - Simpan token di localStorage/sessionStorage
   - Kirim di header setiap request
   - Hapus token saat logout

---

## CONTOH IMPLEMENTASI REACT

### Login
```javascript
const login = async (email, password) => {
    const response = await fetch('http://127.0.0.1:8000/api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, password })
    });
    
    const data = await response.json();
    if (data.success) {
        localStorage.setItem('token', data.token);
        localStorage.setItem('user', JSON.stringify(data.user));
    }
    return data;
};
```

### Get Dashboard
```javascript
const getDashboard = async () => {
    const token = localStorage.getItem('token');
    const response = await fetch('http://127.0.0.1:8000/api/dashboard', {
        headers: {
            'Authorization': `Bearer ${token}`,
        }
    });
    
    const data = await response.json();
    return data;
};
```

### Get Slip Gaji
```javascript
const getSlipGaji = async (page = 1, filters = {}) => {
    const token = localStorage.getItem('token');
    let url = `http://127.0.0.1:8000/api/slip-gaji?page=${page}`;
    
    if (filters.bulan) url += `&bulan=${filters.bulan}`;
    if (filters.tahun) url += `&tahun=${filters.tahun}`;
    if (filters.search) url += `&search=${filters.search}`;
    
    const response = await fetch(url, {
        headers: {
            'Authorization': `Bearer ${token}`,
        }
    });
    
    const data = await response.json();
    return data;
};
```

### Ganti Password
```javascript
const gantiPassword = async (currentPassword, newPassword) => {
    const token = localStorage.getItem('token');
    const response = await fetch('http://127.0.0.1:8000/api/ganti-password', {
        method: 'PATCH',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            current_password: currentPassword,
            password: newPassword,
            password_confirmation: newPassword
        })
    });
    
    const data = await response.json();
    return data;
};
```

---

## TESTING TOOLS

- Postman Collection: [Import dari file JSON]
- Thunder Client (VS Code Extension)
- Insomnia

---

## SUPPORT

Untuk pertanyaan atau issue, hubungi tim backend Kemenag Lampung.

**Last Updated:** 2026-07-07
**Version:** 1.0.0