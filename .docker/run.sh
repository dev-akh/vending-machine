#!/bin/sh

set -e

cd /var/www/html || exit 1

log() {
    echo "[INIT] $1"
}

runLaravelSetup() {
    php artisan key:generate --force
}

main() {
    runLaravelSetup
    log "Initial setup complete. Container is ready."
}

main
