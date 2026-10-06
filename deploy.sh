#!/bin/bash

KEY_PATH="$HOME/.ssh/aws_deploy_key.pem"
VPS_USER="ubuntu"
VPS_IP="16.78.248.95"
APP_DIR="/var/www/kopsyah-bmi"

echo "Memastikan permission SSH key aman..."
chmod 600 "$KEY_PATH"

echo "Memulai proses deploy ke VPS ($VPS_IP)..."

ssh -o StrictHostKeyChecking=accept-new -i "$KEY_PATH" "${VPS_USER}@${VPS_IP}" << 'EOF'
    echo "Masuk ke direktori aplikasi..."
    cd /var/www/kopsyah-bmi || exit

    echo "Menarik update terbaru dari GitHub (git pull)..."
    git pull origin main

    echo "Menginstall dependensi PHP (Composer)..."
    composer install --no-dev --optimize-autoloader

    echo "Menjalankan migrasi database (jika ada)..."
    php artisan migrate --force

    echo "Membersihkan dan mengoptimalkan cache..."
    php artisan optimize:clear
    php artisan optimize

    echo "Menginstall dependensi Node.js & mem-build aset frontend..."
    npm install
    npm run build

    echo "Deploy selesai! 🎉"
EOF
