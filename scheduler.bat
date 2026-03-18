@echo off
cd /d "c:\bultang\booking-lapangan"
php artisan schedule:run >> storage/logs/scheduler.log 2>&1