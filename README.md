# ☕ NONGKRONG.id — Smart Cafe POS & Self-Ordering System

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-3%2F4-4E56A6?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-Supabase-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)](https://supabase.com)
[![Midtrans](https://img.shields.io/badge/Payment-Midtrans_QRIS-004AAD?style=for-the-badge)](https://midtrans.com)
[![Vercel](https://img.shields.io/badge/Deployed_on-Vercel-000000?style=for-the-badge&logo=vercel&logoColor=white)](https://vercel.com)

> **Tugas Final Project Semester 1 — Web Development & Pemrograman Web**  
> Aplikasi Web Manajemen Kafe Modern All-in-One: Integrasi Self-Ordering Meja dengan QR Code, Point of Sale (POS) Kasir, Kitchen Kanban Board, Cleaning Board, Konsultasi AI Barista, dan Pembayaran Digital QRIS Midtrans.

🌐 **Live Demo Aplikasi:** [https://nongkrong-id-three.vercel.app](https://nongkrong-id-three.vercel.app)

---

## 📌 1. Deskripsi Singkat Aplikasi

**NONGKRONG.id** adalah sistem informasi dan operasional kafe terpadu yang dirancang untuk memodernisasi alur pemesanan dan manajemen kafe secara *end-to-end*. 

Sistem ini memfasilitasi 2 sisi utama:
1. **Sisi Pelanggan (Customer Self-Service):** Pelanggan dapat memesan langsung dari meja menggunakan ponsel pintar (cukup scan QR Meja atau input 5 digit kode akses), menjelajahi menu digital interaktif dengan filter rasa & kategori, berkonsultasi menu ke **AI Barista Assistant**, membayar instan via **QRIS Midtrans** atau Tunai di Kasir, serta memantau status pembuatan pesanan secara *real-time*.
2. **Sisi Operasional Staf & Manajemen (Back-Office / POS):** Mengintegrasikan sistem kasir (Point of Sale), papan antrean dapur & barista berbasis Kanban (*Kitchen Kanban*), pemantauan kebersihan meja (*Cleaning Board*), serta dasbor analitik omzet dan manajemen menu/meja bagi pemilik kafe (Owner/Admin).

---

## ✨ 2. Fitur Utama Berdasarkan Hak Akses (Role)

Aplikasi memiliki **5 Role Pengguna** dengan batasan akses dan antarmuka khusus:

### 🛍️ A. Pelanggan (Customer / Self-Ordering Meja)
* **Akses Meja Aman:** Validasi meja menggunakan QR Code token atau 5 digit kode unik meja (mencegah salah meja).
* **Katalog Menu Digital:** Tampilan responsif dengan kategori (*Signature Coffee, Espresso Based, Non-Coffee, Artisan Tea, Pastry, Meals*), label profil rasa (*flavor tags*), status ketersediaan, dan pencarian instan.
* **AI Barista Assistant:** Fitur rekomendasi minuman cerdas berbasis preferensi rasa (manis, pahit, *creamy*, asam) langsung di dalam aplikasi.
* **Keranjang Belanja Real-Time:** Penambahan catatan khusus (*less sugar, extra shot*), perhitungan subtotal & Pajak Restoran (PB1 10%) otomatis.
* **Pembayaran Ganda (Dual Gateway):**
  * **QRIS Otomatis:** Integrasi Midtrans Core API & Snap Pop-up dengan barcode QRIS resmi yang dapat di-scan oleh GoPay, OVO, Dana, BCA Mobile, Livin', dll.
  * **Bayar Tunai:** Tiket QR bayar ke kasir.
* **Live Order Tracking:** Halaman pelacak progres pesanan (*Pending ➔ Diproses ➔ Siap Disajikan ➔ Selesai*) dengan polling otomatis tanpa *reload* halaman.

### 💵 B. Kasir (Cashier POS)
* **Interactive Table Floor Plan:** Peta status meja kafe secara visual (*Kosong, Terisi, Kotor, Reservasi*).
* **Direct Order Register:** Pembuatan pesanan langsung di tempat (*dine-in* maupun *take-away*).
* **Konfirmasi Pembayaran Kasir:** Scan tiket tunai pelanggan dan tombol verifikasi pembayaran lunas.
* **Cetak Struk Transaksi:** Ringkasan pembayaran lengkap dengan nomor meja dan kode pesanan.

### ☕ C. Barista & Tim Dapur (Kitchen Kanban Board)
* **Kanban Alur Pesanan:** Kartu pesanan terbagi dalam kolom:
  1. *Pesanan Masuk (Pending)*
  2. *Sedang Dirakit (Processing / In Progress)*
  3. *Siap Disajikan (Ready)*
  4. *Selesai (Completed)*
* **Detail Item Pesanan:** Penandaan jelas untuk varian minuman barista vs makanan dapur.
* **Pembaruan Status 1-Klik:** Mengubah status pembuatan pesanan yang langsung terupdate ke layar pelacakan pelanggan.

### 🧹 D. Tim Pembersih (Cleaning Board)
* **Papan Kebersihan Meja:** Menampilkan meja-meja yang berstatus **KOTOR** setelah pelanggan selesai makan.
* **Tandai Selesai:** Mengubah status meja dari *Kotor* kembali menjadi *Kosong & Siap Ditempati* dengan 1 klik.

### 📊 E. Pemilik & Admin (Owner Dashboard)
* **Analitik Penjualan Real-Time:** Grafik pendapatan harian/bulanan, total transaksi, total produk terjual, dan rata-rata nilai transaksi (*AOV*).
* **Manajemen Menu & Kategori:** Tambah, edit, upload foto menu, atur profil rasa, harga, dan ketersediaan menu.
* **Manajemen Meja & QR Code Generator:** Tambah meja, atur kapasitas, dan cetak stiker kartu meja ber-QR Code otomatis (format resolusi tinggi).
* **Manajemen Karyawan:** Pendaftaran dan kontrol akun staf beserta perannya.
* **Konfigurasi AI:** Pengaturan prompt dan engine AI Barista.

---

## 🛠️ 3. Teknologi yang Digunakan (Tech Stack)

| Kategori | Teknologi / Library | Keterangan |
| :--- | :--- | :--- |
| **Framework Backend** | **Laravel 12.x / 13.x (PHP 8.4)** | Arsitektur MVC modern dengan Eloquent ORM & Middleware RBAC |
| **Fullstack Reactivity** | **Livewire 3 / 4 & Alpine.js** | Komponen interaktif reaktif tanpa reload halaman (*SPA Experience*) |
| **Styling & UI** | **Tailwind CSS v4 & Poppins/Sora** | Desain responsif (*mobile-first* & *desktop-first*) modern |
| **Database** | **PostgreSQL (Supabase)** | Database cloud dengan Transaction Pooler (Port 6543) |
| **Payment Gateway** | **Midtrans (Snap & Core API)** | Pembayaran digital dinamis QRIS & E-Wallet |
| **Asset Bundler** | **Vite 6** | Kompilasi aset CSS dan JS ultra-cepat |
| **Hosting & Cloud** | **Vercel Serverless (sin1)** | Edge deployment di region Singapura berdampingan dengan database |

---

## 🔑 4. Akun Demo untuk Pengujian Dosen / Reviewer

Gunakan akun di bawah ini untuk menguji hak akses masing-masing role pada menu **Login Karyawan** di halaman Portal:

| Role | Email / Username | Password | Halaman Redirect Utama |
| :--- | :--- | :--- | :--- |
| **Owner / Admin** | `allam@gmail.com` | `123456` | `/admin/dashboard` |
| **Admin Cadangan** | `admin1@smartcafe.id` | `password123` | `/admin/dashboard` |
| **Kasir POS** | `kasir@smartcafe.id` | `password123` | `/staff/pos/tables` |
| **Barista** | `barista@smartcafe.id` | `password123` | `/staff/pos/kitchen-board` |
| **Tim Dapur** | `dapur@smartcafe.id` | `password123` | `/staff/pos/kitchen-board` |
| **Tim Pembersih** | `cleaner@smartcafe.id` | `password123` | `/staff/cleaning-board` |

> *Catatan: Untuk mencoba alur Pelanggan, langsung akses halaman utama dan klik tombol **"Pesan Cepat"** (Pilih Meja `01` s/d `05` atau `VIP-1`).*

---

## 📂 5. Struktur Folder Project

```text
nongkrong_id/
├── app/
│   ├── Http/Controllers/       # Webhook Midtrans & Controller API
│   ├── Livewire/               # Komponen Fullstack Interaktif
│   │   ├── Admin/              # Dashboard, Analytics, Menu, Meja, User, AI Config
│   │   ├── Login/              # Portal & Login Modal Karyawan
│   │   ├── Staff/              # Kasir POS, Kitchen Kanban, Cleaning Board
│   │   └── User/               # Pemilihan Meja, Katalog Menu, Live Tracking
│   ├── Models/                 # Eloquent Models (User, Order, Menu, Table, etc.)
│   └── Services/               # MidtransService & QrCodeService
├── config/                     # Konfigurasi Layanan & Database
├── database/
│   ├── migrations/             # Skema Struktur Tabel Database
│   ├── seeders/                # Dummy Data Awal Menu, Meja, dan Staf
│   └── supabase_schema_and_seed.sql # Skema SQL Produksi Supabase
├── public/
│   ├── assets/                 # Logo Vektor, Ikon, Ilustrasi WebP/SVG Ringan
│   ├── images/menus/           # Foto Menu Asli Kafe
│   └── vendor/livewire/        # Aset Statis Core Livewire CDN
├── resources/
│   ├── css/                    # Tailwind CSS v4 Stylesheet
│   └── views/
│       ├── components/         # Komponen Reusable (<x-icon>, Buttons, Cards)
│       ├── layouts/            # Master Template Blade (app.blade.php)
│       └── livewire/           # Template Tampilan Antarmuka Komponen
├── routes/
│   ├── web.php                 # Rute URL Web & Role Middleware Group
│   └── console.php             # Perintah Artisan Kustom
├── tests/
│   └── Feature/                # 116 Automated Unit & Feature Tests
├── vercel.json                 # Konfigurasi Serverless Hosting Vercel
└── README.md                   # Dokumentasi Lengkap Proyek
```

---

## 💻 6. Panduan Instalasi Lokal (Local Setup)

Bagi yang ingin menjalankan proyek ini di komputer lokal:

### Prasyarat Sistem:
* PHP >= 8.2 (Disarankan PHP 8.4)
* Composer 2.x
* Node.js >= 18.x & NPM
* Database MySQL atau PostgreSQL / SQLite

### Langkah-langkah Instalasi:

1. **Clone Repository:**
   ```bash
   git clone https://github.com/allam1768/Nongkrong.id.git
   cd Nongkrong.id
   ```

2. **Install Dependensi PHP & Node.js:**
   ```bash
   composer install
   npm install
   ```

3. **Pengaturan File Environment:**
   Duplikat file konfigurasi `.env`:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi Database (.env):**
   Sesuaikan koneksi database Anda pada file `.env`:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=nongkrong_id
   DB_USERNAME=postgres
   DB_PASSWORD=secret
   ```
   *(Atau gunakan SQLite dengan mengisi `DB_CONNECTION=sqlite`)*

5. **Migrasi Database & Seeder:**
   Jalankan migrasi untuk membuat tabel dan mengisi data awal:
   ```bash
   php artisan migrate --seed
   ```

6. **Kompilasi Aset Frontend:**
   ```bash
   npm run build
   # Atau untuk mode pengembangan aktif:
   npm run dev
   ```

7. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
   Buka browser di: `http://localhost:8000`

---

## 🧪 7. Pengujian Otomatis (Automated Testing)

Project ini dilengkapi dengan **116 automated feature and unit tests** (438 assertions) yang mencakup seluruh alur krusial:
* Autentikasi dan proteksi middleware role staf/admin.
* Alur validasi token meja dan customer self-order.
* Perhitungan kalkulasi keranjang, pajak PB1, dan subtotal pesanan.
* Integrasi payload gateway pembayaran Midtrans.
* Siklus hidup status pesanan dapur dan status meja.

Jalankan pengujian dengan perintah:
```bash
php artisan test
```

Hasil:
```text
Pass: 116 passed (438 assertions)
Duration: ~10s
```

---

## 👨‍💻 8. Identitas Mahasiswa

* **Nama:** Allam Permata Putra
* **NIM:** *(Sesuai NIM Mahasiswa)*
* **Program Studi / Kelas:** Teknik Informatika / Sistem Informasi — Semester 1
* **Mata Kuliah:** Pemrograman Web / Dasar Pemrograman Web
* **Dosen Pengampu:** *(Sesuai Dosen Mata Kuliah)*

---

*Dikembangkan dengan penuh dedikasi sebagai Final Project Semester 1.*  
*© 2026 NONGKRONG.id — All Rights Reserved.*
