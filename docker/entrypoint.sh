#!/bin/sh
set -e

cd /var/www/html

# ── 1. Resolve APP_URL from Back4App's PORT / HOST env vars ─────────────────
# Back4App injects BACK4APP_DOMAIN. Fall back to the build-time value.
if [ -n "$BACK4APP_DOMAIN" ]; then
    export APP_URL="https://${BACK4APP_DOMAIN}"
fi

# ── 2. Write a fresh .env if one was not volume-mounted ─────────────────────
# All sensitive values are expected as environment variables injected by
# Back4App (Container Settings → Environment Variables).
cat > /var/www/html/.env <<EOF
APP_NAME="LibraryMS"
APP_ENV=production
APP_KEY=${APP_KEY:-base64:s5rDTvMM+CvGXpFCczBm9e9Dptsz27wMJyhhMlCIsJQ=}
APP_DEBUG=false
APP_URL=${APP_URL:-http://localhost}

APP_LOCALE=en
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

SESSION_DRIVER=file
SESSION_LIFETIME=120

CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local

MAIL_MAILER=log

GOOGLE_CLIENT_ID=${GOOGLE_CLIENT_ID:-}
GOOGLE_CLIENT_SECRET=${GOOGLE_CLIENT_SECRET:-}
GOOGLE_REDIRECT_URI=${APP_URL:-http://localhost}/auth/google/callback

ANTHROPIC_API_KEY=${ANTHROPIC_API_KEY:-}
EOF

# ── 3. Ensure storage / bootstrap directories exist and are writable ─────────
mkdir -p storage/framework/{cache,sessions,testing,views} \
         storage/logs \
         bootstrap/cache \
         database

touch database/database.sqlite
chown -R www-data:www-data storage bootstrap/cache database
chmod -R 775 storage bootstrap/cache database

# ── 4. Run Laravel bootstrap commands ────────────────────────────────────────
php artisan config:clear   2>/dev/null || true
php artisan config:cache   2>/dev/null || true
php artisan route:cache    2>/dev/null || true
php artisan view:cache     2>/dev/null || true

# Migrate (--force bypasses the production prompt)
php artisan migrate --force 2>&1

# Seed only if the books table is empty (idempotent)
BOOK_COUNT=$(php artisan tinker --no-interaction --execute="echo App\Models\Book::count();" 2>/dev/null | tr -d '[:space:]' || echo "0")
if [ "$BOOK_COUNT" = "0" ] || [ -z "$BOOK_COUNT" ]; then
    echo "Seeding database..."
    php artisan db:seed --force 2>&1
fi

# Ensure storage symlink exists
php artisan storage:link --force 2>/dev/null || true

# ── 5. Hand off to supervisord (nginx + php-fpm) ─────────────────────────────
echo "Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
