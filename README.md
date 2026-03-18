# 🏟️ Aplikasi Booking Lapangan Multi Cabang

Sistem manajemen booking lapangan berbasis web dengan fitur realtime, multi-role user, dan laporan lengkap.

## ✨ Fitur Utama

### 👥 Multi Role User System
- **Owner**: Akses penuh ke semua cabang dan laporan
- **Admin**: Kelola cabang, lapangan, booking, dan user staff
- **Staff**: Input booking manual dan lihat jadwal lapangan

### 🏢 Multi Cabang & Multi Lapangan
- Setiap cabang memiliki jam operasional sendiri
- Setiap lapangan memiliki harga per jam yang berbeda
- Status aktif/nonaktif untuk cabang dan lapangan

### 📅 Sistem Booking Lengkap
- Booking berdasarkan cabang, lapangan, tanggal, dan waktu
- Validasi otomatis untuk mencegah bentrok jadwal
- Status booking: Pending → Berjalan → Selesai
- Auto update status berdasarkan waktu real

### 🔴 Live Booking Realtime
- Monitoring booking hari ini secara realtime
- Auto refresh setiap 10 detik
- Notifikasi otomatis saat booking selesai
- Update status manual dengan sekali klik

### 📊 Laporan & Analytics
- Laporan booking per tanggal, lapangan, cabang
- Laporan pendapatan harian dan bulanan
- Export ke PDF dan Excel
- Dashboard dengan statistik lengkap

### 🎨 Modern Admin Panel
- UI responsive dengan Tailwind CSS
- Animasi smooth dan loading skeleton
- Dark/Light theme support
- Mobile-friendly design

## 🚀 Instalasi

### Prerequisites
- PHP 8.1+
- Composer
- MySQL 5.7+
- Node.js & NPM (untuk asset compilation)

### Langkah Instalasi

1. **Clone Repository**
```bash
git clone <repository-url>
cd booking-lapangan
```

2. **Install Dependencies**
```bash
composer install
npm install
```

3. **Environment Setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database Configuration**
Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=booking_lapangan
DB_USERNAME=root
DB_PASSWORD=
```

5. **Run Migrations & Seeders**
```bash
php artisan migrate
php artisan db:seed
```

6. **Compile Assets**
```bash
npm run dev
# atau untuk production
npm run build
```

7. **Start Development Server**
```bash
php artisan serve
```

8. **Setup Scheduler (Optional)**
Untuk auto update status booking, tambahkan ke Windows Task Scheduler:
```
Program: C:\path\to\php.exe
Arguments: C:\path\to\project\artisan schedule:run
```
Atau jalankan manual:
```bash
php artisan schedule:work
```

## 👤 Akun Demo

### Owner (Full Access)
- **Email**: owner@booking.com
- **Password**: password
- **Akses**: Semua cabang dan fitur

### Admin Cabang Jakarta
- **Email**: admin1@booking.com
- **Password**: password
- **Akses**: Cabang Jakarta saja

### Staff Cabang Jakarta
- **Email**: staff1@booking.com
- **Password**: password
- **Akses**: Booking dan jadwal saja

## 🏗️ Arsitektur Aplikasi

### Database Schema
```
users (id, name, email, password, branch_id)
├── roles & permissions (Spatie)
├── branches (id, name, address, phone, open_time, close_time)
├── fields (id, branch_id, name, description, price_per_hour)
└── bookings (id, field_id, user_id, customer_name, customer_phone, 
              booking_date, start_time, end_time, total_price, status)
```

### Folder Structure
```
app/
├── Http/Controllers/Admin/
│   ├── DashboardController.php
│   ├── BookingController.php
│   ├── LiveBookingController.php
│   ├── ReportController.php
│   ├── BranchController.php
│   ├── FieldController.php
│   └── UserController.php
├── Models/
│   ├── User.php
│   ├── Branch.php
│   ├── Field.php
│   └── Booking.php
├── Jobs/
│   └── UpdateBookingStatus.php
└── Console/Commands/
    └── UpdateBookingStatusCommand.php

resources/views/admin/
├── layouts/app.blade.php
├── dashboard.blade.php
├── bookings/
├── live-booking/
├── reports/
└── branches/
```

## 🔧 Konfigurasi

### Role & Permissions
Aplikasi menggunakan Spatie Laravel Permission dengan 3 role utama:

**Owner Permissions:**
- Semua permission (view, create, edit, delete untuk semua modul)

**Admin Permissions:**
- view/create/edit branches, fields, bookings, users
- view/export reports
- view live bookings

**Staff Permissions:**
- view fields, bookings
- create bookings
- view live bookings

### Scheduler Configuration
Auto update status booking setiap menit:
```php
// routes/console.php
Schedule::command('booking:update-status')->everyMinute();
```

### Realtime Features
Live booking menggunakan AJAX polling setiap 10 detik:
```javascript
setInterval(refreshData, 10000);
```

## 📱 API Endpoints

### Booking Management
```
GET    /admin/bookings              # List bookings
POST   /admin/bookings              # Create booking
GET    /admin/bookings/{id}         # Show booking
PUT    /admin/bookings/{id}         # Update booking
DELETE /admin/bookings/{id}         # Delete booking
```

### Live Booking
```
GET    /admin/live-booking          # Live booking page
GET    /admin/live-booking/data     # Get realtime data
PATCH  /admin/live-booking/{id}/status # Update status
```

### Reports
```
GET    /admin/reports               # Reports dashboard
GET    /admin/reports/booking       # Booking report
GET    /admin/reports/revenue       # Revenue report
GET    /admin/reports/booking/pdf   # Export PDF
GET    /admin/reports/booking/excel # Export Excel
```

### API Helpers
```
GET    /api/fields/by-branch/{id}   # Get fields by branch
GET    /api/field-availability/{id} # Check field availability
```

## 🎯 Fitur Unggulan

### 1. Realtime Live Booking
- Auto refresh data setiap 10 detik
- Notifikasi push saat booking selesai
- Status indicator dengan warna
- Time remaining calculator

### 2. Smart Booking Validation
- Cek ketersediaan lapangan realtime
- Validasi jam operasional cabang
- Prevent double booking
- Auto calculate total price

### 3. Comprehensive Reports
- Filter by date range, branch, field
- Export to PDF with custom template
- Excel export with formatted data
- Revenue analytics with charts

### 4. Modern UI/UX
- Responsive design for all devices
- Loading skeletons for better UX
- Smooth animations and transitions
- Intuitive navigation

## 🔒 Security Features

- Role-based access control
- CSRF protection
- SQL injection prevention
- XSS protection
- Secure password hashing

## 🚀 Performance Optimizations

- Database query optimization with eager loading
- AJAX pagination for large datasets
- Caching for frequently accessed data
- Optimized asset compilation

## 🐛 Troubleshooting

### Common Issues

**1. Scheduler not working**
```bash
# Check if scheduler is running
php artisan schedule:list

# Run manually
php artisan booking:update-status
```

**2. Permission denied errors**
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
```

**3. Database connection issues**
- Check `.env` database configuration
- Ensure MySQL service is running
- Verify database exists

## 📞 Support

Untuk bantuan teknis atau pertanyaan:
- Email: support@booking-lapangan.com
- Documentation: [Link to docs]
- Issues: [GitHub Issues]

## 📄 License

This project is licensed under the MIT License.

---

**Developed with ❤️ using Laravel 10 & Modern Web Technologies**