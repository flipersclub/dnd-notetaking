#!/usr/bin/env bash

set -e

role=${CONTAINER_ROLE:-app}

if [ "$role" = "app" ]; then
    echo "Starting app..."
    /usr/local/bin/apache-expose-envvars.sh;
    exec apache2-foreground

elif [ "$role" = "queue" ]; then

    echo "Running the queue..."
    exec sudo "-E" "-H" "-u" "www-data" "--" "php" "/var/www/html/artisan" "queue:$CONTAINER_QUEUE_TYPE" "--verbose" "--tries=3" "--timeout=10800";

elif [ "$role" = "scheduler" ]; then

    while [ true ]
    do
      sudo -E -H -u www-data -- php /var/www/html/artisan schedule:run --verbose --no-interaction &
      sleep 60
    done

else
    echo "Could not match the container role \"$role\""
    exit 1
fi