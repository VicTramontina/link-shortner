#!/bin/bash

# Fix storage permissions for www-data (PHP-FPM user)
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Start cron daemon
cron

# Start PHP-FPM (foreground)
php-fpm
