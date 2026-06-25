# GRMRL – Giornata Regionale della Montagna – Regione Lombardia

Sistema di iscrizione online per l'evento **Respira la Montagna** del 5 luglio 2026, organizzato dal GR CAI Lombardia.

## Requisiti

- Docker Desktop
- Docker Compose

## Setup locale

```bash
# 1. Copia il file di configurazione
cp .env.example .env

# 2. Avvia i container Docker
docker-compose up -d

# 3. Installa le dipendenze PHP
docker-compose exec app composer install

# 4. Genera la chiave dell'applicazione
docker-compose exec app php artisan key:generate

# 5. Esegui le migrazioni e i seeder
docker-compose exec app php artisan migrate --seed
```

## Accesso

| Servizio | URL |
|----------|-----|
| Applicazione pubblica | http://localhost |
| Pannello admin Filament | http://localhost/admin |
| MailPit (email di test) | http://localhost:8025 |

Le credenziali admin sono quelle configurate in `.env`:
- **Email**: valore di `FILAMENT_ADMIN_EMAIL`
- **Password**: valore di `FILAMENT_ADMIN_PASSWORD`

## Variabili d'ambiente principali

| Variabile | Descrizione |
|-----------|-------------|
| `FILAMENT_ADMIN_EMAIL` | Email dell'utente admin Filament |
| `FILAMENT_ADMIN_PASSWORD` | Password dell'utente admin Filament |
| `GRMRL_PHONE` | Numero di telefono supporto |
| `GRMRL_EMAIL` | Email supporto |
| `GRMRL_SUPPORT_HOURS` | Orari supporto |
| `MAIL_MAILER` | Driver email (`smtp` in produzione, `log` per debug) |
| `MAIL_HOST` | Server SMTP |
| `MAIL_PORT` | Porta SMTP |
| `MAIL_USERNAME` | Username SMTP |
| `MAIL_PASSWORD` | Password SMTP |
| `MAIL_FROM_ADDRESS` | Indirizzo mittente email |

## Stack tecnologico

- **Backend**: Laravel 13 (PHP 8.4)
- **Admin**: Filament 3.3
- **Database**: MySQL 8
- **Email (test)**: MailPit
- **Web server**: Nginx
- **Containerizzazione**: Docker
