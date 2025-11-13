#!/usr/bin/env bash

echo "Running setup commands..."

# Limpa e recria caches de produção (melhora a performance)
php artisan config:cache
php artisan route:cache

echo "Running database migrations..."
# Roda as migrações (o --force evita o prompt em produção)
php artisan migrate --force

# Inicia o Supervisor, que gerencia Nginx e PHP-FPM
echo "Starting Supervisor (Nginx & PHP-FPM)..."
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf