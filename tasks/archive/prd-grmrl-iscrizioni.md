# PRD: GRMRL – Sistema di Iscrizione Giornata Regionale Montagne Regione Lombardia

## Introduzione

Realizzare un'applicazione web per raccogliere le iscrizioni all'evento "Respira la Montagna" (5 luglio 2026), organizzato dal Gruppo Regionale CAI Lombardia con il supporto di Montagna Servizi. L'evento si svolge in contemporanea in 5 rifugi lombardi (province di Bergamo, Como, Lecco, Sondrio e Brescia), con 50 posti disponibili per rifugio.

Il sistema sostituisce Typeform perché non supporta la chiusura automatica delle iscrizioni al raggiungimento del limite di posti per singola attività.

**Cliente:** Gruppo Regionale CAI Lombardia  
**Sviluppatore:** Montagna Servizi  
**URL produzione:** grmrl.montagneservizi.it  
**Email dedicata:** configurata via `.env` (`GRMRL_EMAIL`)  
**Telefono dedicato:** configurato via `.env` (`GRMRL_PHONE`), locale = `02 555555`  
**Orari supporto telefonico:** configurati via `.env` (`GRMRL_SUPPORT_HOURS`), mostrati nel form accanto al numero  
**Data evento:** domenica 5 luglio 2026, ore 9:00–16:00

---

## Obiettivi

- Raccogliere iscrizioni di adulti + minori (max 3 per adulto) per le 5 attività
- Chiudere automaticamente la selezione di un'attività al raggiungimento di 50 iscritti (adulti + minori contati insieme)
- Mostrare il numero di posti disponibili in tempo reale per ogni attività
- Inviare email di conferma all'iscritto con i dettagli dell'attività scelta
- Fornire un pannello admin per visualizzare ed esportare le iscrizioni

---

## Attività disponibili

| Rifugio | Attività | Orario ritrovo | Luogo partenza |
|---|---|---|---|
| Rifugio Branca | Osservazione cambiamenti climatici, fauna/flora Parco Nazionale dello Stelvio, morfologia delle montagne | 9:00 | Parcheggio Diga S.Giacomo presso Rif. Forni |
| Rifugio Alpe Corte | Forest Bathing con guida di Benessere Forestale | 8:45 | Parcheggio laghetto Valcanale (Valle Seriana) |
| Rifugio Menaggio | Riconoscimento fauna selvatica attraverso gli indici di presenza | 8:45 | Parcheggio Monti di Breglia |
| Rifugio G. Pirlo allo Spino | Lettura del paesaggio con esperto | 8:45 | Colomber di San Michele – Gardone Riviera |
| Rifugio Carlo Porta | Osservazione aspetti botanici | 9:00 | Ritrovo angolo via Pian dei Resinelli e via Carlo Mauri |

Limite per attività: **50 iscritti** (adulti + minori sommati). La selezione viene disabilitata al raggiungimento del limite.

---

## User Stories

### US-001: Setup progetto Docker + Laravel + Filament
**Descrizione:** Come sviluppatore, voglio un ambiente Docker funzionante con Laravel e Filament configurati, così da poter lavorare in locale e deployare in produzione.

**Acceptance Criteria:**
- [ ] `docker-compose.yml` con servizi: app (PHP/Laravel), db (MySQL), mailpit, nginx
- [ ] Laravel installato e funzionante su `http://localhost`
- [ ] Filament installato e accessibile su `/admin`
- [ ] MailPit accessibile su `http://localhost:8025`
- [ ] File `.env.example` con tutte le variabili necessarie documentate:
  - `GRMRL_PHONE` (default locale: `02 555555`)
  - `GRMRL_EMAIL`
  - `GRMRL_SUPPORT_HOURS` (es. "Lun–Ven 9:00–18:00")
  - `FILAMENT_ADMIN_EMAIL`
  - `FILAMENT_ADMIN_PASSWORD`
  - credenziali SMTP Google per produzione
- [ ] `README.md` con istruzioni per avviare l'ambiente locale

---

### US-002: Seed delle sezioni CAI e delle attività
**Descrizione:** Come sviluppatore, voglio che le sezioni CAI e le attività siano precaricate nel database, così da poterle usare nel form senza gestione manuale.

**Acceptance Criteria:**
- [ ] Migration: tabella `cai_sections` con colonne `id`, `code` (stringa, es. `9234001`), `name` (es. `SEZ. CHIETI`), `region`, `province`
- [ ] Seeder che importa le 999 sezioni dal file `2026_MS_Sezioni_SottoSezioni_GR_Gruppi Regionali ETS.xlsx` (foglio "Sezioni", colonne `codice` e `nominativo`, `regione`, `provincia`)
- [ ] Migration: tabella `activities` con colonne `id`, `name` (rifugio), `description` (attività), `meeting_time` (orario ritrovo, stringa es. "8:45"), `meeting_place` (luogo partenza), `max_capacity` (int, default 50), `is_active` (boolean, default true)
- [ ] Seeder che inserisce le 5 attività dalla tabella di questo PRD
- [ ] `php artisan db:seed` eseguito senza errori
- [ ] Typecheck/lint passa

---

### US-003: Schema database iscrizioni
**Descrizione:** Come sviluppatore, voglio le tabelle per memorizzare le iscrizioni degli adulti e dei minori.

**Acceptance Criteria:**
- [ ] Migration: tabella `registrations` con colonne:
  - `id`, `timestamps`
  - `first_name` (string)
  - `last_name` (string)
  - `email` (string)
  - `phone` (string, obbligatorio)
  - `birth_date` (date)
  - `is_cai_member` (boolean)
  - `cai_section_id` (FK → `cai_sections.id`, nullable, richiesto se socio)
  - `fiscal_code` (string, nullable, richiesto se non socio)
  - `activity_id` (FK → `activities.id`)
  - `privacy_accepted` (boolean)
  - `photo_release_accepted` (boolean)
  - `rules_accepted` (boolean)
  - `weather_cancellation_accepted` (boolean)
  - `equipment_check_accepted` (boolean)
- [ ] Migration: tabella `minors` con colonne:
  - `id`, `timestamps`
  - `registration_id` (FK → `registrations.id`, cascade delete)
  - `first_name` (string)
  - `last_name` (string)
  - `birth_date` (date)
  - `is_cai_member` (boolean)
  - `cai_section_id` (FK → `cai_sections.id`, nullable)
  - `fiscal_code` (string, nullable, richiesto se minore non socio)
- [ ] `php artisan migrate` eseguito senza errori

---

### US-004: Form frontend – Layout e header/footer
**Descrizione:** Come utente, voglio vedere un'interfaccia pulita con intestazione e piè di pagina istituzionale, così da capire subito chi organizza l'evento.

**Acceptance Criteria:**
- [ ] Header con: logo CAI Lombardia, titolo "Giornata Regionale della Montagna – Regione Lombardia (GRMRL)"
- [ ] Footer con: dati Gruppo Regionale CAI Lombardia (Via Petrella 19, Milano, gr_cai_lombardia@cai.it, Tel. +393274851177) + dicitura "Realizzato da Montagna Servizi"
- [ ] Numero di telefono dedicato, orari supporto e email visibili nella pagina (letti da `.env`: `GRMRL_PHONE`, `GRMRL_SUPPORT_HOURS`, `GRMRL_EMAIL`)
- [ ] Design mobile-first, responsive su desktop (Bootstrap 5 o Tailwind CSS)
- [ ] Typecheck/lint passa
- [ ] Verificare in browser: header e footer corretti su mobile e desktop

---

### US-005: Form frontend – Sezione dati adulto
**Descrizione:** Come utente adulto, voglio inserire i miei dati anagrafici per registrarmi all'evento.

**Acceptance Criteria:**
- [ ] Campi obbligatori: Nome, Cognome, Email (validazione formato email), Telefono, Data di nascita
- [ ] Checkbox "Sono socio CAI"
  - Se selezionata: appare dropdown con autocomplete per la sezione (ricerca su `name` e `code` tra le 999 sezioni CAI), obbligatorio
  - Se NON selezionata: appare campo "Codice Fiscale" (16 caratteri, validazione formato CF), obbligatorio + messaggio: *"In quanto non socio, sarai assicurato con polizza Soccorso Alpino, RC e Infortuni Combinazione A a carico del GR Lombardia"*
- [ ] Validazione client-side e server-side
- [ ] Typecheck/lint passa
- [ ] Verificare in browser: toggle socio/non socio funziona correttamente

---

### US-006: Form frontend – Sezione minori
**Descrizione:** Come adulto, voglio poter aggiungere fino a 3 minori che porto con me, così da iscriverli all'evento.

**Acceptance Criteria:**
- [ ] Pulsante "Aggiungi minore" visibile, max 3 click (il pulsante si disabilita al terzo minore)
- [ ] Per ogni minore: Nome, Cognome, Data di nascita (obbligatori)
- [ ] Checkbox "Il minore è socio CAI"
  - Se selezionata: dropdown autocomplete sezione CAI (obbligatorio)
  - Se NON selezionata: campo Codice Fiscale (obbligatorio)
- [ ] Pulsante "Rimuovi" per ciascun minore aggiunto
- [ ] Typecheck/lint passa
- [ ] Verificare in browser: aggiunta e rimozione minori funziona, max 3 rispettato

---

### US-007: Form frontend – Selezione attività con contatore posti
**Descrizione:** Come utente, voglio scegliere a quale attività partecipare vedendo i posti ancora disponibili per ciascuna, così da sapere se c'è ancora posto.

**Acceptance Criteria:**
- [ ] Le 5 attività mostrate come radio button o card selezionabili
- [ ] Per ogni attività vengono mostrati: nome rifugio, descrizione attività, orario ritrovo, luogo di partenza, posti disponibili (es. "12 posti disponibili")
- [ ] Il calcolo dei posti disponibili = `max_capacity` − (numero iscritti adulti + numero minori) per quell'attività
- [ ] Se i posti disponibili sono 0: la card è disabilitata e mostra "Attività al completo"
- [ ] I posti disponibili sono calcolati server-side al caricamento della pagina (non richiedono polling real-time)
- [ ] Typecheck/lint passa
- [ ] Verificare in browser: le card mostrano i posti corretti, le attività esaurite appaiono disabilitate

---

### US-008: Form frontend – Consensi e invio
**Descrizione:** Come utente, voglio accettare le condizioni necessarie e inviare la mia iscrizione.

**Acceptance Criteria:**
- [ ] Quattro checkbox obbligatori (tutti devono essere selezionati per procedere):
  1. "Accetto il trattamento dei dati personali ai sensi del GDPR e autorizzo la pubblicazione sul sito e sui social del GR CAI Lombardia di fotografie/riprese video in cui potrei comparire"
  2. "Ho letto la locandina, compreso itinerario, dislivello e durata. Dichiaro che l'escursione è alla mia portata"
  3. "Accetto che il Direttore di Escursione possa sospendere/interrompere l'escursione in caso di maltempo, rischio, agibilità del sentiero, ostacoli o imprevisti"
  4. "Accetto che il Direttore di Escursione possa impedirmi la partecipazione se non ho attrezzatura adeguata"
- [ ] Pulsante "Invia iscrizione" disabilitato finché tutti i checkbox non sono selezionati
- [ ] Al submit: validazione server-side di tutti i campi; in caso di errori, il form viene ripresentato con i messaggi di errore sui campi relativi
- [ ] Se l'attività scelta ha raggiunto il limite di 50 iscritti al momento della sottomissione: mostrare errore "Siamo spiacenti, i posti per questa attività sono esauriti. Scegli un'altra attività"
- [ ] In caso di successo: redirect a pagina di conferma con messaggio "Iscrizione completata con successo! Riceverai una email di conferma a [email]"
- [ ] Typecheck/lint passa
- [ ] Verificare in browser: submit con dati validi funziona e redirect a pagina di conferma

---

### US-009: Email di conferma all'iscritto
**Descrizione:** Come utente appena iscritto, voglio ricevere una email di conferma con il riepilogo della mia iscrizione e i dettagli dell'attività scelta.

**Acceptance Criteria:**
- [ ] Email inviata automaticamente dopo ogni iscrizione completata con successo
- [ ] Destinatario: email dell'adulto iscritto
- [ ] Mittente: `GRMRL_EMAIL` da `.env`
- [ ] Header email con: logo CAI Lombardia Regione Lombardia + dicitura "Realizzato da Montagna Servizi"
- [ ] Corpo email contiene:
  - Titolo: "Conferma iscrizione – Respira la Montagna – 5 luglio 2026"
  - Riepilogo dati adulto: Nome, Cognome, se socio CAI e relativa sezione
  - Riepilogo minori iscritti (se presenti): Nome, Cognome, data di nascita per ciascuno
  - Dettagli attività scelta: nome rifugio, descrizione attività, orario ritrovo, luogo di partenza
  - Frase informativa: "Ti ricordiamo di portare: giacca antivento/pioggia o mantella, capo caldo, cappello, occhiali da sole, crema solare, acqua (minimo 1 litro), pranzo al sacco, snack"
  - Riferimenti per informazioni: email e telefono dedicati (da `.env`)
- [ ] In ambiente locale: email intercettata da MailPit (visibile su `http://localhost:8025`)
- [ ] In ambiente produzione: inviata tramite SMTP Google con l'account configurato in `.env`
- [ ] Typecheck/lint passa

---

### US-010: Gestione capienza per attività
**Descrizione:** Come sistema, devo contare correttamente i posti occupati per ogni attività, considerando sia gli adulti sia i minori, e bloccare nuove iscrizioni quando si raggiunge il limite.

**Acceptance Criteria:**
- [ ] Metodo `Activity::availableSpots()` che ritorna `max_capacity - registrations_count` dove `registrations_count` conta: 1 per ogni adulto iscritto + numero minori di quell'iscrizione
- [ ] Metodo `Activity::isFull()` che ritorna `true` se `availableSpots() <= 0`
- [ ] La sottomissione del form esegue il controllo capienza **dentro una transaction** con lock (`lockForUpdate`) per evitare race condition su iscrizioni simultanee
- [ ] Se l'attività è piena al momento del lock: rollback e messaggio di errore all'utente
- [ ] Test unitario per `Activity::availableSpots()` con adulto + 2 minori (conta 3 posti)
- [ ] Typecheck/lint passa

---

### US-011: Pannello admin Filament – Visualizzazione iscrizioni
**Descrizione:** Come amministratore, voglio visualizzare tutte le iscrizioni nel pannello Filament, così da monitorare le registrazioni.

**Acceptance Criteria:**
- [ ] Resource Filament `RegistrationResource` con tabella che mostra:
  - Nome e cognome adulto
  - Email
  - Telefono
  - Attività scelta (nome rifugio)
  - Numero minori
  - Data e ora iscrizione
  - Se socio CAI (Sì/No) e sezione
- [ ] Filtro per attività
- [ ] Ricerca per nome, cognome, email
- [ ] Accesso protetto da credenziali in `.env` (`FILAMENT_ADMIN_EMAIL`, `FILAMENT_ADMIN_PASSWORD`)
- [ ] Seeder crea l'utente admin al primo `db:seed`
- [ ] Typecheck/lint passa
- [ ] Verificare in browser: pannello admin accessibile, tabella mostra i dati correttamente

---

### US-012: Pannello admin Filament – Export e lista per accompagnatori
**Descrizione:** Come amministratore, voglio esportare le iscrizioni in Excel e avere una vista stampabile per gli accompagnatori CAI.

**Acceptance Criteria:**
- [ ] Pulsante "Esporta Excel" nella tabella Filament che scarica tutte le iscrizioni (o filtrate per attività) in formato `.xlsx`
- [ ] L'export include: Nome adulto, Cognome adulto, Email, Telefono, Attività, Sezione CAI (o "Non socio"), e per ogni minore: Nome, Cognome, Data di nascita
- [ ] Vista stampabile (URL `/admin/registrations/print?activity_id=X`) che mostra per ogni iscritto: Nome e cognome adulto, email, telefono, elenco minori con nome e cognome
- [ ] La vista stampabile è formattata per la stampa (CSS `@media print`)
- [ ] Typecheck/lint passa
- [ ] Verificare in browser: export Excel scarica un file valido, vista stampabile si stampa correttamente

---

## Requisiti Funzionali

- **FR-1:** Il form deve essere accessibile all'URL radice (`/`) del dominio
- **FR-2:** Ogni iscrizione registra un adulto e da 0 a 3 minori; i posti occupati si contano come: 1 (adulto) + numero minori
- **FR-3:** Per gli adulti non soci CAI: obbligatorio Codice Fiscale; mostrare la nota assicurativa a schermo
- **FR-4:** Per i minori non soci CAI: obbligatorio Codice Fiscale
- **FR-5:** Per gli adulti soci CAI: obbligatorio selezionare la sezione con dropdown autocomplete (ricerca per nome o codice tra le 999 sezioni del file Excel)
- **FR-6:** Per i minori soci CAI: obbligatorio selezionare la sezione con dropdown autocomplete
- **FR-7:** Ogni attività ha un massimo di 50 posti; la selezione viene disabilitata quando i posti sono 0
- **FR-8:** Il numero di posti disponibili per ogni attività è visibile nel form al momento della scelta
- **FR-9:** Il controllo capienza è transazionale (lock for update) per prevenire overbooking concorrente
- **FR-10:** Al completamento dell'iscrizione: email di conferma inviata automaticamente all'adulto
- **FR-11:** Il numero di telefono, gli orari di supporto e l'email sono letti da variabili d'ambiente (`GRMRL_PHONE`, `GRMRL_SUPPORT_HOURS`, `GRMRL_EMAIL`) e mostrati nel form
- **FR-12:** Il pannello admin è accessibile su `/admin` con credenziali da `.env`
- **FR-13:** Dal pannello admin è possibile esportare le iscrizioni in Excel e stamparle per gli accompagnatori

---

## Non-Goals (Fuori Scope)

- Nessuna gestione di pagamenti o quote d'iscrizione
- Nessuna autenticazione per gli utenti che si iscrivono (il form è pubblico)
- Nessuna modifica o cancellazione dell'iscrizione da parte dell'utente dopo l'invio
- Nessuna notifica email agli amministratori per ogni nuova iscrizione (solo export manuale)
- Nessuna lista d'attesa per attività al completo
- Nessuna integrazione diretta con la piattaforma CAI (i dati vengono esportati manualmente)
- Nessun sistema multi-lingua (solo italiano)
- Nessun sistema di ruoli admin multipli

---

## Considerazioni di Design

- **Mobile-first:** il form deve essere usabile su smartphone senza scroll orizzontale
- **Header:** logo CAI Lombardia Regione Lombardia + titolo evento "Giornata Regionale della Montagna – Regione Lombardia | GRMRL"
- **Footer:** dati istituzionali CAI Lombardia + "Realizzato da Montagna Servizi"
- **Colori:** da allineare all'identità visiva CAI (blu istituzionale)
- **Accessibilità:** label associate a tutti i campi, errori di validazione visibili e descrittivi
- **Asset loghi:**
  - Logo CAI: `docs/materiale iniziale/CAI/Cai_Club_Alpino_Italiano_Stemma.png`
  - Logo Montagna Servizi: `docs/materiale iniziale/Montagna Servizi/LOGO_01.png`
  - Usare il logo CAI stemma nell'header e nell'intestazione dell'email; logo MS nel footer e nell'email

---

## Considerazioni Tecniche

- **Stack:** Docker · Laravel 11+ · Filament 3+ · MySQL 8 · Nginx
- **Mail locale:** MailPit (porta 8025 per UI, porta 1025 per SMTP)
- **Mail produzione:** Google SMTP con account `GRMRL_EMAIL` (configurazione in `.env`)
- **Sezioni CAI:** importate da `2026_MS_Sezioni_SottoSezioni_GR_Gruppi Regionali ETS.xlsx` (foglio "Sezioni", ~999 righe) tramite seeder; colonne usate: `codice`, `nominativo`, `regione`, `provincia`
- **Autocomplete sezioni:** componente Livewire o Alpine.js + endpoint `/api/sections?q=...` che filtra per `nominativo` LIKE o `codice` LIKE, max 15 risultati
- **Race condition iscrizioni:** usare `DB::transaction()` + `lockForUpdate()` sulla riga `activities` per il controllo capienza
- **Export Excel:** libreria `maatwebsite/excel` (Laravel Excel)
- **Variabili d'ambiente chiave:**
  ```
  GRMRL_PHONE=02 555555
  GRMRL_EMAIL=grmrl@montagneservizi.com
  GRMRL_SUPPORT_HOURS="Lun–Ven 9:00–18:00"
  FILAMENT_ADMIN_EMAIL=admin@montagneservizi.com
  FILAMENT_ADMIN_PASSWORD=secret
  MAIL_MAILER=smtp
  MAIL_HOST=mailpit         # locale
  MAIL_PORT=1025            # locale
  # In produzione: MAIL_HOST=smtp.gmail.com, MAIL_PORT=587, etc.
  ```

---

## Metriche di Successo

- Zero overbooking: nessuna attività supera i 50 iscritti anche con submit concorrenti
- L'iscritto riceve l'email di conferma entro 30 secondi dal submit
- Il form è compilabile su smartphone senza problemi di usabilità
- L'amministratore può esportare in Excel tutte le iscrizioni in meno di 1 minuto

---

## Domande Aperte

Nessuna. Il PRD è completo e pronto per l'implementazione.
