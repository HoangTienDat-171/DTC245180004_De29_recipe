#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

if [ ! -f .env ]; then
    echo "ERROR: Missing .env file"
    exit 1
fi

set -a
source .env
set +a

if [[ ! "${MYSQL_EXPORTER_PASSWORD:-}" =~ ^[a-fA-F0-9]{32,64}$ ]]; then
    echo "ERROR: MYSQL_EXPORTER_PASSWORD must be a random hex password"
    exit 1
fi

sudo docker compose -f compose.yaml exec -T mysql sh -c \
'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" exec mysql -uroot' <<SQL
CREATE USER IF NOT EXISTS 'recipe_monitor'@'%'
IDENTIFIED BY '${MYSQL_EXPORTER_PASSWORD}'
WITH MAX_USER_CONNECTIONS 3;

ALTER USER 'recipe_monitor'@'%'
IDENTIFIED BY '${MYSQL_EXPORTER_PASSWORD}';

GRANT PROCESS, REPLICATION CLIENT, SELECT
ON *.* TO 'recipe_monitor'@'%';
SQL

echo "MySQL monitoring user configured successfully"
