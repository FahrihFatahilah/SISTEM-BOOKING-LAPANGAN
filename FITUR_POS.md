# Fitur POS (Point of Sale) - Dokumentasi

## Fitur yang Ditambahkan

### 1. Manajemen Produk
- **Tambah Produk**: Menambahkan produk baru dengan kode, nama, harga beli, harga jual, stok, dan minimal stok
- **Edit Produk**: Mengubah informasi produk yang sudah ada
- **Hapus Produk**: Menghapus produk dari sistem
- **Monitoring Stok**: Sistem akan menandai produk dengan stok rendah (low stock warning)
- **Multi-Cabang**: Setiap produk terkait dengan cabang tertentu

### 2. Point of Sale (POS)
- **Interface Kasir**: Tampilan kasir yang user-friendly untuk transaksi penjualan
- **Pencarian Produk**: Fitur search untuk mencari produk dengan cepat
- **Keranjang Belanja**: Menambah/mengurangi/menghapus item dari keranjang
- **Perhitungan Otomatis**: 
  - Subtotal
  - Diskon
  - Pajak (%)
  - Total
  - Kembalian
- **Metode Pembayaran**: Cash, Card, Transfer
- **Validasi Stok**: Sistem akan mencegah penjualan jika stok tidak mencukupi
- **Auto-reduce Stock**: Stok otomatis berkurang setelah transaksi berhasil

### 3. Riwayat Penjualan
- **Daftar Transaksi**: Melihat semua transaksi penjualan
- **Detail Transaksi**: Melihat detail lengkap setiap transaksi
- **Filter & Pagination**: Navigasi mudah untuk riwayat transaksi

### 4. Print Thermal Receipt
- **Format Struk 80mm**: Desain struk thermal printer standar 80mm
- **Informasi Lengkap**:
  - Nama & alamat cabang
  - Nomor invoice
  - Tanggal & waktu transaksi
  - Nama kasir
  - Detail item (nama, qty, harga, subtotal)
  - Subtotal, diskon, pajak, total
  - Jumlah bayar & kembalian
  - Metode pembayaran
- **Auto Print**: Otomatis membuka dialog print setelah transaksi
- **Print Ulang**: Bisa print ulang dari riwayat transaksi

## Database Schema

### Tabel: products
- `id`: Primary key
- `code`: Kode produk (unique)
- `name`: Nama produk
- `description`: Deskripsi produk
- `purchase_price`: Harga beli
- `selling_price`: Harga jual
- `stock`: Jumlah stok
- `min_stock`: Minimal stok (untuk warning)
- `branch_id`: Foreign key ke tabel branches
- `is_active`: Status aktif/nonaktif
- `created_at`, `updated_at`: Timestamps

### Tabel: sales
- `id`: Primary key
- `invoice_number`: Nomor invoice (unique, auto-generated)
- `branch_id`: Foreign key ke tabel branches
- `user_id`: Foreign key ke tabel users (kasir)
- `subtotal`: Subtotal sebelum diskon & pajak
- `tax`: Jumlah pajak
- `discount`: Jumlah diskon
- `total`: Total akhir
- `paid`: Jumlah yang dibayar
- `change`: Kembalian
- `payment_method`: Metode pembayaran (cash/card/transfer)
- `notes`: Catatan tambahan
- `created_at`, `updated_at`: Timestamps

### Tabel: sale_items
- `id`: Primary key
- `sale_id`: Foreign key ke tabel sales
- `product_id`: Foreign key ke tabel products
- `product_name`: Nama produk (snapshot)
- `price`: Harga saat transaksi (snapshot)
- `quantity`: Jumlah item
- `subtotal`: Subtotal item (price × quantity)
- `created_at`, `updated_at`: Timestamps

## Routes

### POS Routes
```php
GET  /admin/pos                    - Halaman kasir POS
POST /admin/pos                    - Proses transaksi penjualan
GET  /admin/pos/sales              - Riwayat penjualan
GET  /admin/pos/{sale}             - Detail transaksi
GET  /admin/pos/print/{sale}       - Print struk thermal
```

### Product Routes
```php
GET    /admin/products              - Daftar produk
GET    /admin/products/create       - Form tambah produk
POST   /admin/products              - Simpan produk baru
GET    /admin/products/{id}/edit    - Form edit produk
PUT    /admin/products/{id}         - Update produk
DELETE /admin/products/{id}         - Hapus produk
```

## Cara Penggunaan

### 1. Menambah Produk
1. Login sebagai Owner/Admin
2. Klik menu "Produk" di sidebar
3. Klik tombol "Tambah Produk"
4. Isi form:
   - Kode Produk (contoh: PRD001)
   - Nama Produk
   - Harga Beli
   - Harga Jual
   - Stok awal
   - Minimal Stok (untuk warning)
   - Pilih Cabang
   - Deskripsi (opsional)
5. Klik "Simpan"

### 2. Melakukan Transaksi POS
1. Klik menu "POS" di sidebar
2. Cari produk menggunakan search box atau klik langsung pada produk
3. Produk akan masuk ke keranjang
4. Atur quantity jika perlu
5. Masukkan diskon (jika ada)
6. Masukkan pajak dalam % (jika ada)
7. Masukkan jumlah uang yang dibayar
8. Sistem akan otomatis menghitung kembalian
9. Pilih metode pembayaran
10. Klik "Proses Pembayaran"
11. Struk akan otomatis terbuka untuk di-print

### 3. Print Thermal
- Setelah transaksi berhasil, browser akan otomatis membuka halaman print
- Pastikan thermal printer sudah terkoneksi
- Pilih printer thermal Anda
- Klik Print
- Untuk print ulang, buka menu "POS" → "Riwayat Penjualan" → Klik tombol "Print"

## Fitur Keamanan
- Semua route dilindungi dengan middleware `auth` dan `role`
- Hanya Owner, Admin, dan Staff yang bisa akses POS
- Hanya Owner dan Admin yang bisa manage produk
- Validasi stok sebelum transaksi
- Transaction rollback jika terjadi error

## Tips
1. **Stok Rendah**: Produk dengan stok ≤ minimal stok akan ditandai dengan badge "Low" berwarna kuning
2. **Invoice Number**: Format INV-YYYYMMDD-XXXX (auto-generated)
3. **Snapshot Data**: Nama produk dan harga disimpan di sale_items untuk menjaga integritas data historis
4. **Thermal Printer**: Pastikan menggunakan thermal printer dengan lebar 80mm untuk hasil optimal

## Troubleshooting

### Stok tidak berkurang setelah transaksi
- Pastikan transaksi berhasil (cek di riwayat penjualan)
- Cek log error di storage/logs/laravel.log

### Print tidak muncul
- Pastikan browser mengizinkan popup
- Pastikan printer sudah terkoneksi
- Coba print ulang dari riwayat penjualan

### Error "Stok tidak mencukupi"
- Cek stok produk di menu Produk
- Update stok jika perlu
- Pastikan produk aktif (is_active = true)
