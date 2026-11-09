# 🛍️ AzureMerch - POS & Warehouse Management System

> **AzureMerch** adalah sistem aplikasi web berbasis **Laravel 10** yang mengintegrasikan sistem **Point of Sale (POS)** kasir ritel dengan **Manajemen Inventaris Gudang (Warehouse)** secara real-time, dirancang khusus untuk toko official merchandise (K-Pop albums, photocards, apparel, lightsticks, dan collectibles).

---

## 👥 Akun Default & Hak Akses (Credentials)

Sistem ini menggunakan **Role-Based Access Control (RBAC)** dengan middleware khusus untuk memastikan keamanan dan isolasi hak akses antar pengguna.

Berikut adalah daftar akun demo yang tersedia (dibuat via seeder `php artisan db:seed`):

| Role | Nama Pengguna | Username | Email | Password | Hak Akses Utama |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **👑 Administrator** | Min Yoongi | `admin` | `admin@azuremerch.com` | `password` | Executive Dashboard, Analitik Finansial, Laporan Penjualan, & Audit Log |
| **🛒 Kasir (Cashier)** | Kim Jennie | `kasir` | `kasir@azuremerch.com` | `password` | POS Desktop UI, Scan Barcode, Keranjang Kasir, Cetak Struk, Riwayat Transaksi |
| **📦 Staf Gudang** | Bang Chan | `gudang` | `gudang@azuremerch.com` | `password` | Mobile-First UI, CRUD Produk & Kategori, Inbound Shipment, Stock Opname & Logs |

---

## 🌟 Fitur Utama Berdasarkan Modul

### 1. 👑 Modul Administrator (`/admin`)
- **Dashboard Ringkasan Eksekutif:** Grafik pendapatan harian/bulanan, total penjualan, dan profit margin.
- **Laporan Penjualan & Finansial:** Rekapitulasi transaksi berdasarkan rentang tanggal, filter metode pembayaran, dan export data.
- **Audit & Security Logs:** Pencatatan setiap aktivitas penting (perubahan stok, otorisasi login, dan transaksi).

### 2. 🛒 Modul Kasir / POS (`/pos`)
- **UI Khusus Desktop:** Tampilan POS yang dioptimalkan untuk kasir toko ritel.
- **Pencarian Cepat & Scan Barcode:** Cari produk berdasarkan nama, kode SKU, atau pemindaian barcode langsung.
- **Manajemen Keranjang Belanja:** Perhitungan subtotal, diskon, pajak, dan kembalian secara otomatis.
- **Multi-Payment Method:** Mendukung pembayaran Tunai (Cash), QRIS, Transfer Bank, dan Kartu Debit/Kredit.
- **Struk Digital & Cetak:** Cetak struk belanja rapi dengan layout thermal printer receipt.
- **Histori Transaksi Kasir:** Melihat kembali riwayat struk penjualan yang telah selesai.

### 3. 📦 Modul Gudang / Inventory (`/warehouse`)
- **Desain Mobile-First:** Nyaman digunakan oleh staf gudang melalui smartphone / tablet saat melakukan pengecekan rak.
- **Katalog Produk & Kategori:** Kelola data produk (nama, artis/grup, SKU, barcode, harga modal, harga jual, foto produk).
- **Inbound Shipment (Stok Masuk):** Input penerimaan barang dari supplier/distributor ke gudang secara sistematis.
- **Stock Opname & Penyesuaian:** Menyesuaikan selisih stok fisik vs stok sistem dengan catatan alasan.
- **Kartu Stok / Stock Logs:** Pelacakan riwayat masuk dan keluarnya stok barang secara transparan.

### 4. 👤 Pengaturan Profil (`/profile`)
- Semua role yang sedang login dapat mengupdate informasi akun (nama, email, no. telepon, dan password).

---

## 🛠️ Teknologi yang Digunakan

- **Backend:** [PHP 8.1+](https://www.php.net/) & [Laravel 10](https://laravel.com/)
- **Frontend:** Blade Templating, Tailwind CSS, Alpine.js, Lucide Icons
- **Build Tool:** Vite
- **Database:** MySQL
- **Autentikasi:** Custom Laravel Session Authentication + Multi-Role Middleware (`UAdmin`, `UKasir`, `UGudang`)

---

## 🚀 Panduan Instalasi Lokal (Setup)

Bagi pengembang yang ingin menjalankan project ini di komputer lokal (misal menggunakan Laragon/XAMPP):

1. **Clone Repository**
   ```bash
   git clone https://github.com/naylancy/AzureMerch.git
   cd AzureMerch
   ```

2. **Install Dependensi PHP & Node.js**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**
   Salin file konfigurasi `.env.example`:
   ```bash
   cp .env.example .env
   ```
   Buka file `.env` dan sesuaikan koneksi database MySQL Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=azuremerch
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Migrasi Database & Seeding Data Awal**
   Perintah ini akan membuat semua tabel dan mengisi data akun pengguna, kategori, serta produk awal:
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Kompilasi Asset Frontend**
   ```bash
   npm run build
   # atau untuk mode development:
   # npm run dev
   ```

7. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   ```
   Buka browser di: **`http://127.0.0.1:8000`**

---

## 📄 Lisensi

Project ini dikembangkan untuk kebutuhan manajemen internal AzureMerch di bawah lisensi terbuka [MIT License](LICENSE).
