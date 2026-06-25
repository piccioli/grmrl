#!/usr/bin/env bash
set -euo pipefail

echo "==> Pulizia config cache"
docker compose exec app php artisan config:clear

echo "==> Migrate fresh + seed"
docker compose exec app php artisan migrate:fresh --seed --force

echo "==> Done. DB ripristinato con i dati del seeder."
