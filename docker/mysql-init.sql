-- Create database if not exists
CREATE DATABASE IF NOT EXISTS booking_lapangan;

-- Create user if not exists
CREATE USER IF NOT EXISTS 'booking_user'@'%' IDENTIFIED BY 'BookingLap2024_!';

-- Grant privileges
GRANT ALL PRIVILEGES ON booking_lapangan.* TO 'booking_user'@'%';

-- Flush privileges
FLUSH PRIVILEGES;