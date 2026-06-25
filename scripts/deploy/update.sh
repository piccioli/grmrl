#!/usr/bin/env bash
# =============================================================================
# GRMRL – Script di aggiornamento produzione
#
# Aggiorna l'applicazione all'ultima versione del branch configurato.
#
# ESECUZIONE (da remoto via SSH):
#   ssh user@host 'cd /var/www/grmrl && bash scripts/deploy/update.sh'
#
# ESECUZIONE (direttamente sul server):
#   cd /var/www/grmrl && bash scripts/deploy/update.sh
#
# Lo script è idempotente: eseguirlo più volte consecutive non causa errori.
# =============================================================================
set -euo pipefail

# ---------------------------------------------------------------------------
# Variabili configurabili
# ---------------------------------------------------------------------------
DEPLOY_PATH="/var/www/grmrl"
GIT_BRANCH="main"
APP_CONTAINER="app"
# ---------------------------------------------------------------------------

cd "${DEPLOY_PATH}"

echo "==> Aggiornamento codice da origin/${GIT_BRANCH}..."
git pull origin "${GIT_BRANCH}"

echo "==> Installazione dipendenze Composer (solo produzione)..."
docker compose -f production.compose.yml exec -T "${APP_CONTAINER}" \
    composer install --no-dev --optimize-autoloader

echo "==> Esecuzione migrazioni database..."
docker compose -f production.compose.yml exec -T "${APP_CONTAINER}" \
    php artisan migrate --force

echo "==> Ottimizzazione applicazione (config/route/view cache)..."
docker compose -f production.compose.yml exec -T "${APP_CONTAINER}" \
    php artisan optimize

echo "==> Riavvio code worker..."
docker compose -f production.compose.yml exec -T "${APP_CONTAINER}" \
    php artisan queue:restart

echo ""
echo "==> Aggiornamento completato con successo!"
