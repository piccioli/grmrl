# PRD: Iterazione 5 – Mappe, Eliminazione Iscrizioni, Deploy

## Introduction

Quattro feature distinte:
1. Le card degli eventi mostrano il luogo di partenza come link Google Maps, con coordinate reali geocodificate via Nominatim al seeding. L'admin vede una mappa Leaflet nella pagina di modifica dell'attività.
2. L'admin può eliminare un'iscrizione con richiesta di conferma; l'iscritto riceve email di notifica.
3. Script di go-live per il primo deploy in produzione su VPS con SSH + Docker Compose.
4. Script di aggiornamento produzione (manuale, eseguito sul server via SSH).

---

## Goals

- Rendere i luoghi di partenza verificabili e navigabili direttamente dalle card eventi
- Dare all'admin controllo completo sulle iscrizioni (inclusa eliminazione)
- Standardizzare e documentare la procedura di deploy e aggiornamento produzione

---

## User Stories

### US-037: Aggiungere lat/lng alla tabella activities

**Description:** As a developer, I need to store coordinates for each activity so they can be used in map links and embedded maps.

**Acceptance Criteria:**
- [ ] Nuova migration: aggiunge colonne `latitude DECIMAL(10,7) nullable` e `longitude DECIMAL(10,7) nullable` alla tabella `activities`
- [ ] Modello `Activity` aggiornato con i nuovi campi nel `$fillable`
- [ ] La migration gira senza errori su un DB pulito

---

### US-038: Geocodificare le attività via Nominatim nel seeder

**Description:** As a developer, I need the seeder to automatically fetch coordinates for each activity's meeting place so the data is ready for maps.

**Acceptance Criteria:**
- [ ] `ActivitySeeder` chiama l'API Nominatim (`https://nominatim.openstreetmap.org/search?q=...&format=json`) per ogni attività, con header `User-Agent: GRMRL/1.0`
- [ ] Le coordinate vengono salvate nei campi `latitude` e `longitude`
- [ ] Se Nominatim non trova risultati per un'attività, il seeder logga un warning e lascia `null` (non blocca l'esecuzione)
- [ ] Una pausa di almeno 1 secondo tra una chiamata e l'altra (rispetta le usage policy Nominatim)
- [ ] Le coordinate vengono visualizzate a console durante il seeding (es. `Rifugio Branca → 46.5123, 10.4567`)

---

### US-039: Luogo di partenza cliccabile nelle card pubbliche

**Description:** As a user, I want to click the meeting place on an activity card so I can open it directly in Google Maps.

**Acceptance Criteria:**
- [ ] Nella view `activities.blade.php`, il testo del luogo di ritrovo diventa un `<a>` che apre Google Maps
- [ ] Se `latitude` e `longitude` sono valorizzati: il link punta a `https://www.google.com/maps?q={lat},{lng}`
- [ ] Se le coordinate sono `null`: il testo rimane non cliccabile (nessun link)
- [ ] Il link si apre in una nuova scheda (`target="_blank"`, `rel="noopener noreferrer"`)
- [ ] Verify in browser usando il dev-browser skill

---

### US-040: Mappa Leaflet nella pagina di modifica attività (admin)

**Description:** As an admin, I want to see a Leaflet map showing the meeting point when editing an activity so I can verify the geocoded coordinates visually.

**Acceptance Criteria:**
- [ ] La pagina di modifica attività (`/admin/activities/{id}/edit`) mostra una mappa Leaflet con un marker nel punto geocodificato
- [ ] La mappa è visibile solo se `latitude` e `longitude` non sono `null`
- [ ] La mappa usa tile OpenStreetMap (nessuna API key richiesta)
- [ ] Leaflet è caricato via CDN nel componente Blade, non richiede npm
- [ ] Aggiunto campo `latitude` e `longitude` in sola lettura nel form admin (TextInput disabled) per mostrare i valori numerici
- [ ] Verify in browser usando il dev-browser skill

---

### US-041: Eliminazione iscrizione dall'admin con conferma

**Description:** As an admin, I want to delete a registration with a confirmation dialog so I don't accidentally remove the wrong record.

**Acceptance Criteria:**
- [ ] La tabella iscrizioni (`/admin/registrations`) mostra l'azione "Elimina" per ogni riga
- [ ] Cliccando "Elimina" appare un modal di conferma Filament con messaggio: `"Sei sicuro di voler eliminare l'iscrizione di {nome} {cognome}? L'operazione non è reversibile."`
- [ ] Confermando, l'iscrizione e i suoi minori associati vengono eliminati dal DB (hard delete)
- [ ] I minori associati vengono eliminati prima dell'adulto (rispetta i vincoli di FK)
- [ ] Dopo l'eliminazione, la tabella si aggiorna automaticamente
- [ ] Verify in browser usando il dev-browser skill

---

### US-042: Email di notifica cancellazione all'iscritto

**Description:** As a registered user, I want to receive an email when my registration is deleted by an admin so I know my spot has been cancelled.

**Acceptance Criteria:**
- [ ] Nuova Mailable `RegistrationCancellation` con view `emails.registration-cancellation`
- [ ] L'email viene inviata all'indirizzo dell'iscritto dopo la cancellazione
- [ ] L'email contiene: nome dell'iscritto, nome dell'attività, invito a re-iscriversi se ci sono posti
- [ ] Oggetto email: `"Cancellazione iscrizione – Respira la Montagna – 5 luglio 2026"`
- [ ] Stessa struttura grafica dell'email di conferma (logo CAI, logo MS)
- [ ] L'email è visibile in Mailpit su `http://localhost:8025` in locale

---

### US-043: Script go-live per il primo deploy in produzione

**Description:** As a developer, I want a single script that automates the first deploy on the production VPS so the procedure is reproducible and documented.

**Acceptance Criteria:**
- [ ] Script `scripts/deploy/golive.sh` con variabili configurabili in testa: `SSH_USER`, `SSH_HOST`, `SSH_PORT`, `DEPLOY_PATH`, `REPO_URL`, `GIT_BRANCH`
- [ ] Il repo contiene `.env.production.example` con tutte le chiavi e valori placeholder; il commento in testa allo script spiega che l'operatore deve compilarlo e rinominarlo `.env` sul server prima di lanciare il golive
- [ ] Lo script esegue via SSH: clone del repo, verifica che `.env` esista (altrimenti esce con errore chiaro), `docker compose -f production.compose.yml up -d --build`, `php artisan migrate --force`, `php artisan db:seed --force`, `php artisan optimize`
- [ ] Prima di ogni step critico stampa cosa sta per fare (es. `→ Running migrations...`)
- [ ] Se uno step fallisce, lo script si ferma con `exit 1` e stampa quale step ha fallito
- [ ] In testa allo script: commento che documenta i prerequisiti (Docker installato sul server, `.env.production` preparato localmente, accesso SSH con chiave configurato)
- [ ] Lo script è eseguibile localmente (`chmod +x`)

---

### US-044: Script di aggiornamento produzione

**Description:** As a developer, I want a script to run on the production server that updates the app to the latest version so deployments are fast and consistent.

**Acceptance Criteria:**
- [ ] Script `scripts/deploy/update.sh` con variabili configurabili: `DEPLOY_PATH`, `GIT_BRANCH`, `APP_CONTAINER` (nome container Docker dell'app)
- [ ] Lo script esegue in sequenza: `git pull origin $GIT_BRANCH`, `docker compose -f production.compose.yml exec $APP_CONTAINER composer install --no-dev --optimize-autoloader`, `php artisan migrate --force`, `php artisan optimize`, `php artisan queue:restart`
- [ ] Ogni step è preceduto da output descrittivo
- [ ] Se uno step fallisce lo script si ferma con `exit 1`
- [ ] In testa: commento con istruzioni su come eseguirlo (`ssh user@host 'cd /path && bash scripts/deploy/update.sh'`)
- [ ] Lo script è idempotente: eseguirlo due volte consecutive non causa errori

---

## Functional Requirements

- FR-1: La tabella `activities` deve avere colonne `latitude` e `longitude` (DECIMAL, nullable)
- FR-2: Il seeder usa Nominatim con User-Agent e pausa 1s tra le richieste
- FR-3: Il link Google Maps nelle card usa `?q=lat,lng` se le coordinate esistono, altrimenti testo semplice
- FR-4: La mappa Leaflet in admin usa tile OSM, nessuna API key
- FR-5: L'eliminazione di un'iscrizione cancella anche i minori associati (cascade o eliminazione esplicita)
- FR-6: L'email di cancellazione viene inviata sincronamente (no queue) per semplicità
- FR-7: Gli script di deploy usano variabili configurabili, non hardcoded
- FR-8: Gli script si fermano al primo errore (`set -e`)

---

## Non-Goals

- Nessun sistema di geofencing o validazione delle coordinate
- Nessuna geocodifica automatica on-save dall'admin (solo nel seeder)
- Nessun soft delete delle iscrizioni
- Nessun log di audit delle eliminazioni
- Nessun rollback automatico del deploy
- GitHub Actions / CI automatico (il trigger è manuale)

---

## Technical Considerations

- **Nominatim**: API pubblica, no key. Rate limit: 1 req/s. User-Agent obbligatorio. Query: `meeting_place` + `"Italia"` per maggiore precisione.
- **Leaflet**: caricare CSS/JS da CDN (`unpkg.com/leaflet`) nella view Blade dell'admin, non via Vite. Il componente è isolato alla pagina edit attività.
- **Filament DeleteAction**: usare `Tables\Actions\DeleteAction::make()` con `->requiresConfirmation()` e `->modalDescription(fn($record) => ...)`. Aggiungere un `after()` hook per inviare l'email.
- **Script deploy**: il server di produzione ha Docker Compose disponibile. Il file compose di produzione potrebbe differire da `docker-compose.yml` locale (es. no mailpit, no volumi di sviluppo) — gli script devono specificare quale file compose usare o usare il default.

---

## Success Metrics

- Tutte le 5 attività nel seeder hanno coordinate valide dopo `db:seed`
- Cliccando un luogo di partenza si apre Google Maps centrato correttamente
- L'admin può eliminare un'iscrizione in meno di 3 click
- L'iscritto riceve l'email di cancellazione entro pochi secondi
- `golive.sh` completa il primo deploy senza intervento manuale (oltre alla configurazione variabili)
- `update.sh` completa un aggiornamento in meno di 2 minuti

---

## Decisioni

- **`.env` produzione**: il repo contiene `.env.production.example` con tutte le chiavi e valori placeholder. Il `golive.sh` copia questo file come `.env` sul server; l'operatore riempie i valori reali prima di lanciare lo script.
- **Compose produzione**: esiste un file `production.compose.yml` separato (senza mailpit, con configurazioni adatte alla produzione). Entrambi gli script (`golive.sh` e `update.sh`) usano `-f production.compose.yml`.
- **Email cancellazione**: messaggio generico, nessun campo motivo.
