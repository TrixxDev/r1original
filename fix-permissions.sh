#!/bin/bash
# Laravel permission fixer for /var/www/r1riepas

LARAVEL_DIR="/var/www/r1riepas"
WEB_USER="www-data"

echo "🔧 Fixing permissions in $LARAVEL_DIR ..."

# 1. Give ownership to web server
chown -R $WEB_USER:$WEB_USER $LARAVEL_DIR/storage
chown -R $WEB_USER:$WEB_USER $LARAVEL_DIR/bootstrap/cache

# 2. Fix schedules directory (for Excel exports)
chown -R $WEB_USER:$WEB_USER $LARAVEL_DIR/storage/app/schedules

# 3. Set safe writable permissions
chmod -R 775 $LARAVEL_DIR/storage
chmod -R 775 $LARAVEL_DIR/bootstrap/cache
chmod -R 775 $LARAVEL_DIR/storage/app/schedules

echo "✅ Permissions fixed successfully!"

