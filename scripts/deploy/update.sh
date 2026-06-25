#!/usr/bin/env bash
# =============================================================================
# GRMRL – Script di aggiornamento produzione
#
# Aggiorna l'applicazione all'ultima versione del branch configurato.
# Ricostruisce l'immagine Docker per includere nuovi asset frontend e codice.
#
# ESECUZIONE (da locale):
#   bash scripts/deploy/update.sh
#
# ESECUZIONE (direttamente sul server):
#   cd /root/grmrl && bash scripts/deploy/update.sh
# =============================================================================
set -euo pipefail

# ---------------------------------------------------------------------------
# Variabili configurabili
# ---------------------------------------------------------------------------
DEPLOY_PATH="/root/grmrl"
GIT_BRANCH="main"
APP_CONTAINER="app"
COMPOSE_FILE="production.compose.yml"
PROJECT_NAME="grmrl"
# ---------------------------------------------------------------------------

cd "${DEPLOY_PATH}"

echo "==> Aggiornamento codice da origin/${GIT_BRANCH}..."
git pull origin "${GIT_BRANCH}"

echo "==> Stop container..."
docker compose -f "${COMPOSE_FILE}" down

echo "==> Rimozione volume public_files (verrà ripopolato dal nuovo build)..."
docker volume rm "${PROJECT_NAME}_public_files" 2>/dev/null || true

echo "==> Build e avvio container con nuovo codice..."
docker compose -f "${COMPOSE_FILE}" up -d --build

echo "==> Esecuzione migrazioni database..."
docker compose -f "${COMPOSE_FILE}" exec -T "${APP_CONTAINER}" \
    php artisan migrate --force

echo "==> Ottimizzazione applicazione (config/route/view cache)..."
docker compose -f "${COMPOSE_FILE}" exec -T "${APP_CONTAINER}" \
    php artisan optimize

echo "==> Riavvio code worker..."
docker compose -f "${COMPOSE_FILE}" exec -T "${APP_CONTAINER}" \
    php artisan queue:restart

echo ""
echo "==> Aggiornamento completato con successo!"
