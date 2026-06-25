#!/usr/bin/env bash
# =============================================================================
# GRMRL – Go-Live Script (primo deploy in produzione)
#
# PRIMA DI ESEGUIRE:
#   1. Copia .env.production.example sul server come .env nella directory DEPLOY_PATH
#      e compilalo con i valori reali (APP_KEY, DB_PASSWORD, MAIL_*, ecc.)
#   2. Assicurati che Docker e Docker Compose siano installati sul server
#   3. Assicurati che l'utente SSH abbia accesso al repo (deploy key / token)
#
# ESECUZIONE:
#   bash scripts/deploy/golive.sh
# =============================================================================
set -euo pipefail

# ---------------------------------------------------------------------------
# Variabili configurabili
# ---------------------------------------------------------------------------
SSH_USER="deploy"
SSH_HOST="your-server-ip-or-hostname"
SSH_PORT="22"
DEPLOY_PATH="/var/www/grmrl"
REPO_URL="git@github.com:your-org/grmrl.git"
GIT_BRANCH="main"
# ---------------------------------------------------------------------------

run_remote() {
    ssh -p "$SSH_PORT" "${SSH_USER}@${SSH_HOST}" "$@"
}

echo "==> Connessione a ${SSH_USER}@${SSH_HOST}:${SSH_PORT}"

echo "==> Clonazione del repository in ${DEPLOY_PATH}..."
run_remote "git clone --branch ${GIT_BRANCH} ${REPO_URL} ${DEPLOY_PATH} || (cd ${DEPLOY_PATH} && git fetch origin && git checkout ${GIT_BRANCH} && git reset --hard origin/${GIT_BRANCH})"

echo "==> Verifica presenza del file .env..."
run_remote "test -f ${DEPLOY_PATH}/.env || { echo 'ERRORE: .env non trovato in ${DEPLOY_PATH}. Copia e compila .env.production.example prima di procedere.'; exit 1; }"

echo "==> Avvio dei container Docker (build inclusa)..."
run_remote "cd ${DEPLOY_PATH} && docker compose -f production.compose.yml up -d --build"

echo "==> Esecuzione migrazioni database..."
run_remote "cd ${DEPLOY_PATH} && docker compose -f production.compose.yml exec -T app php artisan migrate --force"

echo "==> Esecuzione seeder database..."
run_remote "cd ${DEPLOY_PATH} && docker compose -f production.compose.yml exec -T app php artisan db:seed --force"

echo "==> Ottimizzazione applicazione (config/route/view cache)..."
run_remote "cd ${DEPLOY_PATH} && docker compose -f production.compose.yml exec -T app php artisan optimize"

echo ""
echo "==> Go-live completato con successo!"
echo "    Applicazione disponibile su http://${SSH_HOST}"
