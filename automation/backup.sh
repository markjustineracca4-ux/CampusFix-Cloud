#!/bin/bash

set -e

BACKUP_DIR="/opt/campusfix/backups"
TIMESTAMP=$(date +"%Y-%m-%d_%H-%M-%S")
FILE="$BACKUP_DIR/campusfix_$TIMESTAMP.sql"

mkdir -p "$BACKUP_DIR"

docker exec campusfix-db \
  mysqldump -u campusfix_user -pcampusfix_password campusfix > "$FILE"

find "$BACKUP_DIR" -type f -name "campusfix_*.sql" -mtime +7 -delete

echo "Backup created: $FILE"
