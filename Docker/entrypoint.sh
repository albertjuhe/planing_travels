#!/bin/bash
set -e

git config --global --add safe.directory /var/www/html

wait_for() {
    local name=$1
    local cmd=$2
    local retries=30
    local i=0

    echo "Waiting for $name..."
    while ! eval "$cmd" > /dev/null 2>&1; do
        i=$((i + 1))
        if [ $i -ge $retries ]; then
            echo "$name not ready after $retries attempts, giving up."
            exit 1
        fi
        echo "  $name not ready yet (attempt $i/$retries), retrying in 5s..."
        sleep 5
    done
    echo "$name is ready."
}

wait_for "MySQL" "php -r \"new PDO('mysql:host=${MYSQL_HOST:-mysql};dbname=${MYSQL_DATABASE:-travelGuuid}', '${MYSQL_USER:-root}', '${MYSQL_PASSWORD:-root}');\""

wait_for "Elasticsearch" "curl -sf http://elasticsearch:9200/_cluster/health?wait_for_status=yellow"

echo "Running populate-el..."
cd /var/www/html
php bin/console app:populate-travel-elasticsearch
echo "populate-el done."

exec apache2-foreground
