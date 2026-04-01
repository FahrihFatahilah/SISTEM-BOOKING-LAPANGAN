# Sistem Membership Bulanan & Pengaturan Harga

## Fitur yang Telah Ditambahkan

### 1. Sistem Membership Bulanan
- **Paket Membership**: Admin dapat membuat berbagai paket (Basic, Premium, VIP)
- **Jadwal Tetap**: Member bermain di hari dan jam yang sama setiap minggu
- **Booking Otomatis**: Sistem otomatis membuat jadwal untuk seluruh periode membership
- **Manajemen Member**: Kelola member aktif, expired, dan cancelled

#### Contoh Paket:
- **Basic**: 2x/minggu, 1.5 jam, Rp 300.000/bulan
- **Premium**: 3x/minggu, 2 jam, Rp 500.000/bulan  
- **VIP**: 4x/minggu, 2 jam, Rp 750.000/bulan

### 2. Pengaturan Harga Per Jam
- **Aturan Harga Fleksibel**: Berbeda berdasarkan hari dan jam
- **Sistem Prioritas**: Aturan dengan prioritas tinggi diutamakan
- **Real-time Pricing**: Harga berubah otomatis sesuai waktu

#### Contoh Aturan:
- **Prime Time**: Senin-Jumat, 18:00-22:00, +50% dari harga normal
- **Weekend Premium**: Sabtu-Minggu, 08:00-22:00, +30% dari harga normal
- **Happy Hour**: Senin-Jumat, 13:00-17:00, -20% dari harga normal

### 3. Menu Admin Baru
- **Paket Membership**: `/admin/membership-packages`
- **Member Aktif**: `/admin/user-memberships`
- **Aturan Harga**: `/admin/pricing-rules`

### 4. Cara Kerja Membership
1. Admin membuat paket membership dengan durasi dan jumlah sesi
2. Admin mendaftarkan member dengan memilih:
   - Paket membership
   - Lapangan tetap
   - Hari bermain (misal: Senin, Rabu, Jumat)
   - Jam bermain tetap (misal: 19:00)
3. Sistem otomatis membuat jadwal booking untuk periode membership
4. Member bermain sesuai jadwal yang telah ditentukan

### 5. Status Booking Membership
- **Terjadwal**: Booking yang akan datang
- **Hari Ini**: Booking hari ini
- **Selesai**: Booking yang sudah selesai
- **Dibatalkan**: Booking yang dibatalkan
- **Terlewat**: Booking yang tidak hadir (no show)

### 6. API Endpoints
- `GET /admin/pricing/get-price`: Mendapatkan harga real-time berdasarkan lapangan, tanggal, dan jam
- `PATCH /admin/membership-bookings/{id}/status`: Update status booking membership

## Database Tables

### membership_packages
- Menyimpan paket membership dengan harga dan durasi

### user_memberships  
- Menyimpan membership user dengan jadwal mingguan tetap

### membership_bookings
- Menyimpan booking otomatis dari membership

### pricing_rules
- Menyimpan aturan harga berdasarkan hari dan jam

## Keunggulan Sistem

1. **Konsistensi Jadwal**: Member bermain di waktu yang sama setiap minggu
2. **Otomatisasi**: Booking dibuat otomatis untuk seluruh periode
3. **Fleksibilitas Harga**: Harga dapat disesuaikan berdasarkan waktu
4. **Manajemen Mudah**: Interface admin yang user-friendly
5. **Tracking Lengkap**: Monitor kehadiran dan status setiap sesi

## Instalasi

```bash
# Jalankan migration
php artisan migrate

# Jalankan seeder untuk data contoh
php artisan db:seed --class=MembershipPackageSeeder
php artisan db:seed --class=PricingRuleSeeder
```

Sistem ini memberikan solusi lengkap untuk manajemen membership bulanan dengan jadwal tetap dan pengaturan harga yang fleksibel berdasarkan waktu dan hari.