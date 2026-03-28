# 🐳 Docker Deployment Guide - Booking Lapangan

Panduan lengkap untuk deploy aplikasi Booking Lapangan menggunakan Docker di VPS.

## 📋 Prerequisites

- VPS dengan Docker dan Docker Compose terinstall
- Minimal 2GB RAM dan 20GB storage
- Port 9777 dan 3307 tersedia

## 🚀 Quick Deploy

### 1. Clone Repository
```bash
git clone <repository-url>
cd booking-lapangan
```

### 2. Setup Environment
```bash
# Copy environment file
cp .env.production .env

# Generate application key (akan otomatis saat container start)
```

### 3. Deploy
```bash
# Make deploy script executable
chmod +x deploy.sh

# Run deployment
./deploy.sh
```

## 🔧 Manual Deployment

### 1. Build dan Start Containers
```bash
docker-compose up -d --build
```

### 2. Check Status
```bash
docker-compose ps
docker-compose logs app
```

### 3. Access Application
- **Web App**: http://your-vps-ip:9777
- **MySQL**: your-vps-ip:3307

## 📊 Container Services

### App Container (booking-lapangan-app)
- **Port**: 9777:80
- **Services**: Nginx + PHP-FPM + Laravel
- **Auto Features**:
  - Database migration
  - Seeding data
  - Cache optimization
  - Queue worker
  - Task scheduler

### MySQL Container (booking-lapangan-mysql)
- **Port**: 3307:3306
- **Database**: booking_lapangan
- **User**: booking_user
- **Password**: BookingLap2024_!

## 🛠️ Management Commands

### View Logs
```bash
# All services
docker-compose logs

# Specific service
docker-compose logs app
docker-compose logs mysql

# Follow logs
docker-compose logs -f app
```

### Execute Commands in Container
```bash
# Laravel artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan cache:clear

# Access container shell
docker-compose exec app bash
```

### Database Management
```bash
# Access MySQL
docker-compose exec mysql mysql -u booking_user -p booking_lapangan

# Backup database
docker-compose exec mysql mysqldump -u booking_user -p booking_lapangan > backup.sql

# Restore database
docker-compose exec -T mysql mysql -u booking_user -p booking_lapangan < backup.sql
```

### Container Management
```bash
# Stop containers
docker-compose down

# Restart containers
docker-compose restart

# Rebuild containers
docker-compose up -d --build

# Remove everything (including volumes)
docker-compose down -v
```

## 🔒 Security Configuration

### 1. Change Default Passwords
Edit `docker-compose.yml`:
```yaml
environment:
  - DB_PASSWORD=your-secure-password
  - MYSQL_PASSWORD=your-secure-password
  - MYSQL_ROOT_PASSWORD=your-secure-root-password
```

### 2. Firewall Setup
```bash
# Allow only necessary ports
ufw allow 9777/tcp
ufw allow 22/tcp
ufw enable
```

### 3. SSL Certificate (Optional)
Gunakan reverse proxy seperti Nginx atau Traefik dengan Let's Encrypt.

## 📈 Performance Optimization

### 1. Resource Limits
Edit `docker-compose.yml`:
```yaml
services:
  app:
    deploy:
      resources:
        limits:
          memory: 1G
          cpus: '0.5'
```

### 2. Database Optimization
```bash
# Optimize MySQL
docker-compose exec mysql mysql -u root -p -e "
SET GLOBAL innodb_buffer_pool_size = 512M;
SET GLOBAL query_cache_size = 64M;
"
```

## 🔍 Troubleshooting

### Container Won't Start
```bash
# Check logs
docker-compose logs app

# Check disk space
df -h

# Check memory
free -h
```

### Database Connection Issues
```bash
# Test MySQL connection
docker-compose exec app nc -z mysql 3306

# Check MySQL logs
docker-compose logs mysql
```

### Permission Issues
```bash
# Fix storage permissions
docker-compose exec app chown -R www-data:www-data /var/www/storage
docker-compose exec app chmod -R 775 /var/www/storage
```

### Application Errors
```bash
# Clear all caches
docker-compose exec app php artisan optimize:clear

# Check Laravel logs
docker-compose exec app tail -f /var/www/storage/logs/laravel.log
```

## 📊 Monitoring

### Health Check
```bash
# Simple health check
curl -f http://localhost:9777

# Detailed status
docker-compose exec app php artisan about
```

### Resource Usage
```bash
# Container stats
docker stats

# Disk usage
docker system df
```

## 🔄 Updates

### Update Application
```bash
# Pull latest code
git pull origin main

# Rebuild and restart
docker-compose up -d --build

# Run migrations
docker-compose exec app php artisan migrate --force
```

### Update Dependencies
```bash
# Update composer packages
docker-compose exec app composer update

# Rebuild container
docker-compose up -d --build app
```

## 📞 Support

Jika mengalami masalah:

1. Check logs: `docker-compose logs`
2. Verify environment: `docker-compose exec app php artisan about`
3. Test database: `docker-compose exec app php artisan migrate:status`
4. Check permissions: `ls -la storage/`

## 🎯 Default Access

- **Admin Panel**: http://your-vps-ip:9777/admin
- **Owner Login**: owner@booking.com / password
- **Admin Login**: admin1@booking.com / password
- **Staff Login**: staff1@booking.com / password

---

**Happy Deploying! 🚀**