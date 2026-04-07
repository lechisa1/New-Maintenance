#!/bin/sh
set -e
# Ensure storage and bootstrap/cache are writable when container starts.
# Run as root (Docker will start container as root unless overridden) so chown can work.
if [ "$(id -u)" = '0' ]; then
  chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
  chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true
fi

exec "$@"
