# Dokumentasi API - Sistem Manajemen Inventaris

Dokumentasi ini berisi detail endpoint REST API yang digunakan dalam aplikasi.
Semua request dan response menggunakan format `application/json`.

## Base URL
- **Lokal:** `http://localhost:8000/api`
- **Production:** `https://inventaris-management-be-production-2e30.up.railway.app/api`

## Autentikasi
API ini menggunakan **Sanctum (Bearer Token)**. Setelah login, Anda akan mendapatkan `access_token` yang harus disertakan di setiap request yang membutuhkan autorisasi.
**Format Header:**
```
Authorization: Bearer <access_token>
Accept: application/json
```

---

## 1. Authentication

### Login
- **Endpoint:** `POST /login`
- **Akses:** Publik
- **Body Parameter:**
  ```json
  {
      "email": "admin@test.com",
      "password": "password123"
  }
  ```
- **Response Sukses (200 OK):**
  ```json
  {
      "access_token": "1|abcdefghijklmnopqrstuvwxyz...",
      "token_type": "Bearer",
      "data": {
          "id": 1,
          "name": "Admin User",
          "email": "admin@test.com",
          "role": "Admin"
      }
  }
  ```

### Register
- **Endpoint:** `POST /register`
- **Akses:** Publik
- **Body Parameter:**
  ```json
  {
      "name": "John Doe",
      "email": "johndoe@test.com",
      "password": "password123"
  }
  ```

### Logout
- **Endpoint:** `POST /logout`
- **Akses:** Bearer Token
- **Response:** `204 No Content`

---

## 2. Master Barang (Products)

### Get Semua Barang
- **Endpoint:** `GET /products`
- **Akses:** Token (Admin, Staff)
- **Query Params:**
  - `search` (opsional): kata kunci pencarian.
  - `limit` (opsional): batas data per halaman (default 4).

### Tambah Barang
- **Endpoint:** `POST /products`
- **Akses:** Token (Admin, Staff)
- **Content-Type:** `multipart/form-data`
- **Parameter:**
  - `kode_barang` (string, required)
  - `nama_barang` (string, required)
  - `category_id` (integer, required)
  - `stok` (integer, required)
  - `lokasi_penyimpanan` (string, required)
  - `kondisi_barang` (string, required)
  - `image` (file image, opsional)

### Edit Barang
- **Endpoint:** `POST /products/{id}` (Gunakan body `_method=PUT` jika mengirimkan *form-data*)
- **Akses:** Token (Admin, Staff)

### Hapus Barang
- **Endpoint:** `DELETE /products/{id}`
- **Akses:** Token (Admin, Staff)

---

## 3. Peminjaman Barang (Borrowings)

### Get Riwayat Peminjaman
- **Endpoint:** `GET /borrowings`
- **Akses:** Token (Admin, Manager, Staff)

### Pinjam Barang
- **Endpoint:** `POST /borrowings`
- **Akses:** Token (Admin, Manager, Staff)
- **Body Parameter:**
  ```json
  {
      "nama_peminjam": "Budi Santoso",
      "tanggal_pinjam": "2026-07-09",
      "product_ids": [1, 2]
  }
  ```
  *Sistem otomatis memotong stok barang yang dipinjam.*

### Kembalikan Barang
- **Endpoint:** `POST /borrowings/{id}/return`
- **Akses:** Token (Admin, Manager)
- **Body Parameter:**
  ```json
  {
      "tanggal_kembali": "2026-07-10"
  }
  ```
  *Sistem otomatis mengembalikan jumlah stok barang.*

---

## 4. Dashboard & Laporan

### Get Data Dashboard
- **Endpoint:** `GET /dashboard`
- **Akses:** Token (Admin, Manager, Staff)
- **Response (200 OK):**
  ```json
  {
      "total_barang": 100,
      "barang_dipinjam": 5,
      "barang_tersedia": 95,
      "low_stock_products": [
         { "id": 1, "nama_barang": "Pensil", "stok": 3 }
      ],
      "grafik_peminjaman_bulanan": [
         { "month": "06", "total": 12 }
      ]
  }
  ```
