# PRD: Form Flow Redesign – Landing, Selezione Attività e Miglioramenti

## Introduction

Attualmente il sito mostra il form di iscrizione direttamente sulla home page. Si vuole introdurre un flusso a tre step:

1. **Landing page** – breve presentazione dell'iniziativa con bottone "Inizia"
2. **Selezione attività** – card delle 5 iniziative con posti disponibili, quella esaurita non selezionabile
3. **Form iscrizione** – dati personali e accompagnatori, con attività pre-selezionata

Parallelamente: gestione attività da Filament, contatti di supporto MS in header, validazione in italiano, almeno un minore obbligatorio, seeder che simula un'attività esaurita.

## Goals

- Migliorare l'esperienza utente con un flusso guidato
- Rendere le attività gestibili dall'admin senza toccare il codice
- Mostrare contatti di supporto Montagna Servizi (non CAI regionale)
- Impedire iscrizioni ad attività esaurite già in fase di selezione
- Garantire che ogni iscritto porti almeno un minore
- Fornire un seeder di test che blocca una delle cinque attività

## User Stories

### US-016: Landing page con bottone "Inizia"
**Description:** As a visitor, I want to land on a welcoming page that describes the initiative so that I understand what I'm signing up for before starting.

**Acceptance Criteria:**
- [ ] La route `GET /` mostra la nuova landing page (non più il form)
- [ ] La pagina contiene: titolo evento, breve testo descrittivo, data e luogo dell'evento, bottone "Inizia"
- [ ] Il bottone "Inizia" rimanda a `GET /attivita`
- [ ] La view estende il layout `layouts.app` esistente (header/footer invariati)
- [ ] Verify in browser using dev-browser skill

### US-017: Pagina selezione attività con card
**Description:** As a visitor, I want to see the 5 available activities as cards so that I can choose the one I prefer before filling in personal data.

**Acceptance Criteria:**
- [ ] La route `GET /attivita` mostra una pagina con le 5 card attività
- [ ] Ogni card mostra: nome attività, luogo di ritrovo (`meeting_place`), orario (`meeting_time`), descrizione, posti disponibili rimasti
- [ ] Le card di attività esaurite (`isFull() === true`) sono visivamente disabilitate (grigie, testo "Esaurita") e non cliccabili
- [ ] Cliccando una card disponibile si va su `GET /iscriviti/{activity}`
- [ ] Se tutte le attività sono esaurite viene mostrato un messaggio "Tutte le iniziative sono al completo"
- [ ] Verify in browser using dev-browser skill

### US-018: Routing e form con attività pre-selezionata
**Description:** As a visitor, I want the form to already have the activity I chose pre-selected so that I don't have to re-choose it.

**Acceptance Criteria:**
- [ ] Route `GET /iscriviti/{activity}` mostra il form con l'attività identificata dall'`id` passato nell'URL
- [ ] Se l'`{activity}` non esiste o è esaurita, redirect a `/attivita` con messaggio flash di errore
- [ ] Il form mostra il nome dell'attività selezionata in modo non modificabile (campo hidden `activity_id`)
- [ ] Route `POST /iscriviti/{activity}` gestisce il submit (sostituisce `POST /`)
- [ ] La route legacy `GET /` e `POST /` vengono rimosse o redirezionate a `/`
- [ ] Verify in browser using dev-browser skill

### US-019: Validazione form in italiano e minore obbligatorio
**Description:** As a visitor, I want validation messages in Italian so that I can understand what's wrong, and the form must require at least one minor.

**Acceptance Criteria:**
- [ ] Tutti i messaggi di errore di validazione del `RegistrationController` sono in italiano
- [ ] È obbligatorio aggiungere almeno un minore: se `minors` è vuoto o assente la validazione fallisce con messaggio "È obbligatorio aggiungere almeno un minore"
- [ ] Nessun messaggio di errore in inglese è visibile all'utente finale
- [ ] I messaggi esistenti nel form `.blade.php` rimangono funzionanti
- [ ] Verify in browser using dev-browser skill

### US-020: Contatti di supporto in header da .env
**Description:** As a visitor, I want to see the Montagna Servizi support contacts in the header so that I can get help if needed.

**Acceptance Criteria:**
- [ ] Aggiungere a `.env.example` le variabili `GRMRL_SUPPORT_PHONE` e `GRMRL_SUPPORT_EMAIL`
- [ ] Aggiornare `config/grmrl.php` con le chiavi `support_phone` e `support_email`
- [ ] L'header del layout mostra `config('grmrl.support_phone')` e `config('grmrl.support_email')`
- [ ] I valori di default in `.env.example` sono: `GRMRL_SUPPORT_PHONE=+39 02 555 555` e `GRMRL_SUPPORT_EMAIL=grmrl@montagnaservizi.com`
- [ ] Rimuovere o non mostrare più `GRMRL_PHONE` e `GRMRL_EMAIL` (numero/email CAI regionale) dall'header
- [ ] Verify in browser using dev-browser skill

### US-021: ActivityResource in Filament
**Description:** As an admin, I want to manage activities from the Filament panel so that I can update descriptions, times, and capacity without touching the code.

**Acceptance Criteria:**
- [ ] Esiste `app/Filament/Resources/ActivityResource.php`
- [ ] La tabella lista mostra: nome, luogo di ritrovo, orario, capienza, posti rimasti, stato (attiva/inattiva)
- [ ] Il form di creazione/modifica espone i campi: `name`, `description`, `meeting_time`, `meeting_place`, `max_capacity`, `is_active`
- [ ] Il campo `is_active` è un toggle
- [ ] Le azioni disponibili sono: view, edit (no delete per sicurezza)
- [ ] Verify in browser using dev-browser skill

### US-022: Seeder attività esaurita (FakeRegistrationsSeeder)
**Description:** As a developer, I want a seeder that fills a random activity to 50 participants so that I can test the "sold out" UI without manual data entry.

**Acceptance Criteria:**
- [ ] Esiste `database/seeders/FakeRegistrationsSeeder.php`
- [ ] Il seeder sceglie casualmente una delle 5 attività
- [ ] Crea tante iscrizioni fake (adulto + 1 minore ciascuna) fino a raggiungere esattamente `max_capacity = 50` posti occupati
- [ ] Le iscrizioni fake hanno dati plausibili (nome, cognome, CF, sezione CAI esistente)
- [ ] Il seeder è invocabile standalone: `php artisan db:seed --class=FakeRegistrationsSeeder`
- [ ] Il seeder NON è incluso nel `DatabaseSeeder` principale (solo uso manuale/test)
- [ ] L'attività riempita risulta `isFull() === true` dopo l'esecuzione

## Functional Requirements

- FR-1: `GET /` mostra la landing page con bottone "Inizia" che porta a `GET /attivita`
- FR-2: `GET /attivita` mostra le card delle 5 attività con posti disponibili in tempo reale
- FR-3: Attività con `isFull() === true` non sono selezionabili nella pagina card
- FR-4: `GET /iscriviti/{activity}` e `POST /iscriviti/{activity}` gestiscono form e submit con attività pre-selezionata
- FR-5: Se si tenta di accedere al form di un'attività esaurita, redirect a `/attivita` con errore flash
- FR-6: La validazione del form restituisce solo messaggi in italiano
- FR-7: Il campo `minors` deve contenere almeno un elemento, altrimenti la validazione fallisce
- FR-8: L'header mostra `GRMRL_SUPPORT_PHONE` e `GRMRL_SUPPORT_EMAIL` (non i contatti CAI regionali)
- FR-9: Le attività sono gestibili da Filament (`ActivityResource`) con campi: nome, descrizione, orario, luogo, capienza, attiva/inattiva
- FR-10: `FakeRegistrationsSeeder` riempie una attività random a 50 posti per test

## Non-Goals

- Nessun sistema di pagamento o prenotazione con conferma manuale
- Nessuna gestione di liste d'attesa
- Nessuna modifica all'email di conferma già funzionante
- Nessuna autenticazione lato front-end
- Nessun cambio alla struttura del DB delle attività (i campi esistenti sono sufficienti)

## Technical Considerations

- Il modello `Activity` ha già i campi necessari (`description`, `meeting_time`, `meeting_place`, `max_capacity`, `is_active`) — **nessuna migration necessaria**
- Il metodo `availableSpots()` e `isFull()` esistono già su `Activity` — riusarli nelle card
- La validazione italiana si ottiene pubblicando i lang files di Laravel: `php artisan lang:publish` (selezionando `it`) oppure aggiungendo manualmente i messaggi custom nell'array `messages` del controller
- Aggiornare `.env` locale con i nuovi valori di `GRMRL_SUPPORT_PHONE` e `GRMRL_SUPPORT_EMAIL`
- Il `FakeRegistrationsSeeder` deve calcolare quante iscrizioni creare in base alla capienza: se max_capacity=50 e ogni iscrizione occupa adulto+1 minore=2 posti, bastano 25 iscrizioni; ma se si vuole esattamente 50 posti occupati si possono fare 50 iscrizioni ognuna senza minori, oppure un mix — la logica deve usare `availableSpots()` in loop

## Success Metrics

- Un visitatore completa l'iscrizione in ≤ 3 click prima del form (landing → selezione → form)
- Un'attività esaurita non è accessibile né da card né da URL diretto
- L'admin può aggiornare la descrizione di un'attività da Filament senza toccare codice
- `FakeRegistrationsSeeder` produce un'attività con `isFull() === true` verificabile immediatamente

## Open Questions

- Il testo della landing page (titolo, body) deve essere hardcodato nella view o configurabile da `.env`/Filament? Per ora si assume hardcodato nella view — da rivalutare se serve flessibilità.
- L'header deve mostrare entrambi telefono ed email affiancati, o solo uno dei due? Si assume entrambi — da confermare in fase di design.
