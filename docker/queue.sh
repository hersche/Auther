#!/bin/sh
while true;
do
    php /var/www/yamo-api/artisan queue:work --daemon
done