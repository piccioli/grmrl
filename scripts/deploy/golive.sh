#!/usr/bin/env bash
# =============================================================================
# GRMRL – Go-Live Script (primo deploy in produzione)
#
# PRIMA DI ESEGUIRE:
#   1. Crea il file .env sul server:
#        ssh msuat "cp /root/grmrl/.env.production.example /root/grmrl/.env"
#      Poi modifica il .env con i valori reali (APP_KEY, DB_PASSWORD, MAIL_*, ecc.)
#      Genera APP_KEY con: ssh msuat "cd /root/grmrl && docker compose -f production.compose.yml run --rm app php artisan key:generate --show"
#   2. Docker e Docker Compose devono essere installati sul server
#   3. Se il repo è privato: usa un token GitHub nell'URL oppure configura una deploy key
#   4. Dopo il golive, configura nginx + SSL:
#        sudo cp /root/grmrl/docker/nginx-vhost.conf /etc/nginx/sites-available/grmrl
#        sudo ln -s /etc/nginx/sites-available/grmrl /etc/nginx/sites-enabled/grmrl
#        sudo nginx -t && sudo systemctl reload nginx
#        sudo certbot --nginx -d grmrl.montagnaservizi.com
#
# ESECUZIONE:
#   bash scripts/deploy/golive.sh
# =============================================================================
set -euo pipefail

# ---------------------------------------------------------------------------
# Variabili configurabili
# ---------------------------------------------------------------------------
SSH_ALIAS="msuat"
DEPLOY_PATH="/root/grmrl"
REPO_URL="https://github.com/piccioli/grmrl.git"
GIT_BRANCH="main"
# ---------------------------------------------------------------------------

run_remote() {
    ssh "${SSH_ALIAS}" "$@"
}

echo "==> Connessione al server via ${SSH_ALIAS}"

echo "==> Clonazione del repository in ${DEPLOY_PATH}..."
run_remote "git clone --branch ${GIT_BRANCH} ${REPO_URL} ${DEPLOY_PATH} || (cd ${DEPLOY_PATH} && git fetch origin && git checkout ${GIT_BRANCH} && git reset --hard origin/${GIT_BRANCH})"

echo "==> Verifica presenza del file .env..."
run_remote "test -f ${DEPLOY_PATH}/.env || { echo 'ERRORE: .env non trovato in ${DEPLOY_PATH}.'; echo 'Esegui: cp ${DEPLOY_PATH}/.env.production.example ${DEPLOY_PATH}/.env e compilalo.'; exit 1; }"

echo "==> Avvio dei container Docker (build inclusa – può richiedere alcuni minuti)..."
run_remote "cd ${DEPLOY_PATH} && docker compose -f production.compose.yml up -d --build"

echo "==> Attesa avvio database..."
run_remote "cd ${DEPLOY_PATH} && docker compose -f production.compose.yml exec -T app php artisan migrate:status > /dev/null 2>&1 || sleep 10"

echo "==> Esecuzione migrazioni database..."
run_remote "cd ${DEPLOY_PATH} && docker compose -f production.compose.yml exec -T app php artisan migrate --force"

echo "==> Esecuzione seeder database..."
run_remote "cd ${DEPLOY_PATH} && docker compose -f production.compose.yml exec -T app php artisan db:seed --force"

echo "==> Ottimizzazione applicazione (config/route/view cache)..."
run_remote "cd ${DEPLOY_PATH} && docker compose -f production.compose.yml exec -T app php artisan optimize"

echo ""
echo "==> Go-live completato!"
echo "    Container attivi su 127.0.0.1:8080"
echo ""
echo "==> PASSI SUCCESSIVI:"
echo "    1. Configura nginx host + SSL (vedi istruzioni in testa a questo script)"
echo "    2. Verifica l'app su https://grmrl.montagnaservizi.com"
