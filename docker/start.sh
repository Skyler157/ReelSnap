#!/usr/bin/env sh
set -eu

PORT="${PORT:-10000}"

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

if [ -z "${APP_KEY:-}" ]; then
  echo "APP_KEY is not set. Set APP_KEY in Render environment variables."
  exit 1
fi

php artisan package:discover --ansi

echo "Waiting for database connection..."
ATTEMPTS=0
until php artisan migrate --force; do
  ATTEMPTS=$((ATTEMPTS + 1))
  if [ "$ATTEMPTS" -ge 10 ]; then
    echo "Migration failed after 10 attempts."
    exit 1
  fi
  echo "Migration attempt $ATTEMPTS failed. Retrying in 5s..."
  sleep 5
done

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec php -S "0.0.0.0:${PORT}" -t public
