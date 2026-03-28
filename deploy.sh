#!/bin/bash

# Deploy script untuk Booking Lapangan
echo "🚀 Starting deployment..."

# Stop existing containers
echo "📦 Stopping existing containers..."
docker-compose down

# Remove old images (optional)
echo "🗑️ Cleaning up old images..."
docker image prune -f

# Build and start containers
echo "🔨 Building and starting containers..."
docker-compose up -d --build

# Wait for containers to be ready
echo "⏳ Waiting for containers to be ready..."
sleep 30

# Check container status
echo "📊 Container status:"
docker-compose ps

# Show logs
echo "📝 Recent logs:"
docker-compose logs --tail=50

echo "✅ Deployment completed!"
echo "🌐 Application is running on: http://your-vps-ip:9777"
echo "🗄️ MySQL is running on: your-vps-ip:3307"

# Health check
echo "🏥 Running health check..."
if curl -f http://localhost:9777 > /dev/null 2>&1; then
    echo "✅ Application is healthy!"
else
    echo "❌ Application health check failed!"
    echo "📝 Check logs with: docker-compose logs app"
fi