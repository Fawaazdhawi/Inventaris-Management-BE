# Sistem Manajemen Inventaris

Sistem Manajemen Inventaris ini merupakan solusi pencatatan inventaris PT Telkomsel berbasis Web untuk mengatasi masalah kehilangan data aset, duplikasi, dan sulitnya memantau stok barang. 

Sistem ini terbagi menjadi dua bagian utama:
1. **Backend (REST API):** Dibangun menggunakan Laravel 11.
2. **Frontend (UI):** Dibangun menggunakan React + Vite & Tailwind CSS.

---

## 1. Cara Instalasi (Sesuai Syarat Rubrik)
Walaupun aplikasi ini telah di-deploy secara online, berikut adalah langkah instalasi jika ingin dijalankan secara lokal:
1. *Clone* repository ini ke komputer lokal.
2. Karena proyek ini sudah dibekali arsitektur **Docker**, Anda cukup membuka terminal di folder root backend ini lalu jalankan:
   ```bash
   docker-compose up -d --build
   ```
3. Masuk ke dalam *container* backend untuk menginstal dependensi:
   ```bash
   docker exec -it inventaris_backend bash
   composer install
   php artisan migrate:fresh --seed
   php artisan storage:link
   exit
   ```
4. Buka folder frontend dan jalankan `npm install` untuk mengunduh semua pustaka antarmuka.

---

## 2. Cara Menjalankan Project
* **Versi Online (Live Demo):** 
  Aplikasi ini sudah di-deploy dan dapat diakses publik melalui tautan yang dilampirkan pada hasil pengumpulan tugas.
* **Versi Lokal:** 
  - Backend berjalan secara otomatis melalui Docker di alamat `http://localhost:8000`.
  - Untuk Frontend, jalankan `npm run dev` pada terminal folder frontend, lalu akses `http://localhost:5173` di web browser Anda.

---

## 3. Akun Login Testing
Silakan gunakan salah satu akun di bawah ini untuk menguji fitur aplikasi:

| Role Akses | Email Login | Password | Keterangan Akses |
|------------|-------------|----------|------------------|
| **Admin** | `admin@test.com` | `admin123` | Memiliki akses penuh, termasuk mengelola Data Pengguna (User Management). |
| **Manager** | `manager@test.com` | `manager123` | Hak akses baca-saja; hanya bisa melihat Dashboard, Statistik, dan Laporan. |
| **Staff** | `staff@test.com` | `staff123` | Bisa menambah/mengubah barang dan melakukan proses peminjaman. |

---

## Fitur dan Spesifikasi Teknis
- **Database:** PostgreSQL (berisi minimal tabel *users, roles, products, categories, borrowings*).
- **Notifikasi Stok Menipis:** Menggunakan WebSockets (Laravel Reverb / Echo) untuk *real-time update* jika stok di bawah 5 item.
- **Export Laporan:** Dapat diekspor menjadi format PDF dari halaman Dashboard.
