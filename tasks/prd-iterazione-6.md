# PRD: Iterazione 6 – Feedback Post-Collaudo

## Introduction

Correzioni e miglioramenti richiesti dal cliente dopo il primo collaudo dell'applicazione:

1. Il form non deve più obbligare l'inserimento di un minore (attività aperta a tutti)
2. Il testo del consenso "ho letto la locandina" deve puntare al link delle attività sul sito CAI
3. Prima del form va inserito un link alla pagina di presentazione del progetto
4. La tabella `activities` deve includere nuovi metadati per ogni escursione (difficoltà, dislivello, lunghezza, acqua, descrizione itinerario, immagine)
5. Il seeder delle attività va aggiornato con i dati reali dal sito CAI (inclusa correzione orario Resinelli)
6. La pagina `/attivita` deve mostrare un bottone INFO che apre una modale con i dettagli tecnici dell'escursione
7. La locandina (Artifact HTML) va aggiornata con dislivello, lunghezza, tipo sentiero, orario corretto per Resinelli e indicazione pranzo al sacco

---

## Goals

- Rendere il form più inclusivo (nessun vincolo sull'inserimento di minori)
- Fornire agli utenti le informazioni necessarie per auto-valutare l'idoneità all'escursione
- Arricchire il database delle attività con metadati tecnici accurati e coerenti con il sito ufficiale
- Mostrare i dettagli di ogni escursione con un tocco aggiuntivo nell'interfaccia pubblica (bottone INFO)
- Allineare tutti i materiali (locandina, form, seeder) agli stessi dati aggiornati

---

## User Stories

### US-045: Minori non obbligatori nel form

**Description:** As a user without children, I want to be able to register without adding any minor so that the form is accessible to all participants.

**Acceptance Criteria:**
- [ ] In `app/Http/Controllers/RegistrationController.php`, il campo `minors` passa da `['required', 'array', 'min:1', 'max:3']` a `['nullable', 'array', 'max:3']`
- [ ] Rimosse le chiavi `'minors.required'` e `'minors.min'` dall'array `$messages` nel controller
- [ ] In `resources/views/registration/form.blade.php`, rimosso il blocco `@error('minors')` che mostra "È obbligatorio aggiungere almeno un minore."
- [ ] Rimosso `@error('minors') border-red-400 bg-red-50 @enderror` dalla classe del div card minori
- [ ] La validazione dei singoli campi minore (`minors.*.first_name`, etc.) rimane invariata: se si aggiunge un minore, i suoi campi sono obbligatori
- [ ] Verify in browser: submit del form senza minori → iscrizione salvata correttamente

---

### US-046: Consenso "gita alla mia portata" con link alle attività

**Description:** As a user, I want the consent text to link to the activities page on the CAI website so I can read the detailed itinerary information before declaring the hike is within my capabilities.

**Acceptance Criteria:**
- [ ] In `resources/views/registration/form.blade.php`, il testo del consenso `rules_accepted` (attualmente "Ho letto la locandina, compreso l'itinerario, il dislivello e la durata. Dichiaro che l'escursione è alla mia portata.") viene sostituito con:

  ```
  Ho consultato le <a href="https://organizzazione.cai.it/gr-lombardia/progetti-regione-lom/attivita/" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline hover:text-blue-800">informazioni sull'attività</a>, compreso l'itinerario, il dislivello e la durata. Dichiaro che l'escursione è alla mia portata.
  ```
- [ ] Il link si apre in una nuova scheda (`target="_blank"`)
- [ ] La voce `*` obbligatoria rimane invariata
- [ ] Verify in browser: il link è cliccabile e apre la pagina corretta

---

### US-047: Link al progetto prima del form

**Description:** As a user, I want to see a link to the project presentation page before starting registration so I understand what the event is about.

**Acceptance Criteria:**
- [ ] In `resources/views/registration/form.blade.php`, aggiungere **prima** dell'elemento `<form>` un banner informativo, ad esempio:

  ```html
  <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800">
    Prima di procedere con l'iscrizione, ti consigliamo di leggere
    <a href="https://organizzazione.cai.it/gr-lombardia/progetti-regione-lom/progetto/"
       target="_blank" rel="noopener noreferrer"
       class="font-semibold underline hover:text-blue-900">
      la presentazione del progetto
    </a>.
  </div>
  ```
- [ ] Il link apre in nuova scheda
- [ ] Il banner è visibile prima di qualsiasi campo del form
- [ ] Verify in browser: il banner appare nella pagina `/iscriviti/{activity}`

---

### US-048: Migration – nuovi metadati per le attività

**Description:** As a developer, I need to store structured technical details for each hiking activity so they can be displayed in the UI and kept consistent with the official CAI website.

**Acceptance Criteria:**
- [ ] Nuova migration `2026_06_27_000001_add_metadata_to_activities_table.php` che aggiunge le seguenti colonne `nullable` alla tabella `activities`:
  - `difficulty` — `string()->nullable()` — es. `"E – Escursionistico"`, `"T – Turistico"`
  - `elevation_gain` — `string()->nullable()` — es. `"343 m"`
  - `trail_length` — `string()->nullable()` — es. `"3 km – 3 ore"`
  - `water_description` — `string()->nullable()` — es. `"Sì"`, `"Al parcheggio"`
  - `itinerary_description` — `text()->nullable()`
  - `image_url` — `string()->nullable()`
- [ ] Il metodo `down()` rimuove le 6 colonne con `dropColumn([...])`
- [ ] Il modello `Activity` ha i 6 nuovi campi aggiunti in `$fillable`
- [ ] L'`ActivityResource` Filament aggiunge i campi editabili nel form (TextInput per i campi stringa, Textarea per `itinerary_description` e `image_url`)
- [ ] Il metodo `showActivities()` in `RegistrationController` include i nuovi campi nella mappa array restituita alla view
- [ ] La migration gira senza errori: `php artisan migrate`

---

### US-049: Seeder – aggiornamento dati reali dal sito CAI

**Description:** As a developer, I need the ActivitySeeder to contain the real data published on the CAI Lombardia website so the app is consistent with official information.

**Acceptance Criteria:**
- [ ] `ActivitySeeder.php` aggiornato con i dati sotto (tutti i campi, compresi i nuovi introdotti in US-048)
- [ ] **FIX orario Resinelli**: `Rifugio Carlo Porta` ha `meeting_time => '8:45'` (era `'9:00'`)
- [ ] **FIX luogo di partenza Branca**: aggiornato a `'Parcheggio Diga di San Giacomo, ticket giornaliero a Santa Caterina Valfurva'`
- [ ] Dati corretti per tutte e 5 le attività (vedi tabella sotto)
- [ ] I campi `image_url` restano `null` (le URL immagini complete non sono disponibili)
- [ ] Il seeder completa senza errori; la geocodifica Nominatim viene eseguita normalmente per il campo `meeting_place`

#### Dati attività da usare nel seeder

| Campo | Rifugio Branca | Rif. Alpe Corte | Rif. Menaggio | Rif. G. Pirlo allo Spino | Rif. Carlo Porta |
|---|---|---|---|---|---|
| `name` | `'Rifugio Branca'` | `'Rifugio Alpe Corte'` | `'Rifugio Menaggio'` | `'Rifugio G. Pirlo allo Spino'` | `'Rifugio Carlo Porta'` |
| `description` | `'Osservazione cambiamenti climatici, fauna/flora Parco Nazionale dello Stelvio, morfologia delle montagne'` | `'Forest Bathing con guida di Benessere Forestale'` | `'Riconoscimento fauna selvatica attraverso gli indici di presenza'` | `'Lettura del paesaggio con esperto'` | `'Osservazione aspetti botanici'` |
| `meeting_time` | `'9:00'` | `'8:45'` | `'8:45'` | `'8:45'` | `'8:45'` |
| `meeting_place` | `'Parcheggio Diga di San Giacomo, ticket giornaliero a Santa Caterina Valfurva'` | `'Parcheggio laghetto Valcanale (Valle Seriana)'` | `'Parcheggio Monti di Breglia'` | `'Colomber di San Michele – Gardone Riviera'` | `'Parcheggio via Piani dei Resinelli (a pagamento)'` |
| `difficulty` | `'E – Escursionistico'` | `'E – Escursionistico'` | `'E – Escursionistico'` | `'E – Escursionistico'` | `'T – Turistico'` |
| `elevation_gain` | `'343 m'` | `'350 m'` | `'650 m'` | `'950 m'` | `'200 m'` |
| `trail_length` | `'3 km – 3 ore'` | `'5 km – 3 ore'` | `'5 km – 4,5 ore'` | `'6 km – 3 ore'` | `'1,5 km – 2 ore'` |
| `water_description` | `'Al parcheggio'` | `'Sì'` | `'Sì'` | `'Sì'` | `'Sì'` |
| `itinerary_description` | `'Trekking per studiare l\'impatto dei cambiamenti climatici sui ghiacciai e la biodiversità del Parco Nazionale dello Stelvio. Il Rifugio Branca è il balcone panoramico davanti al ghiacciaio dei Forni, circondato da cime oltre 3500 m.'` | `'Pratica di origine giapponese con benefici su benessere psicofisico e riduzione dello stress. Rifugio a quota 1410 m immerso in pineta con pareti dolomitiche, prima tappa del Sentiero delle Orobie Orientali.'` | `'Laboratorio di monitoraggio non invasivo della fauna attraverso impronte e segni di presenza. Percorso panoramico tra betulle e ginestre con vista sul centro lago, Bellagio e Grigne.'` | `'Viaggio con architetto ambientale sulle trasformazioni del territorio da area antropizzata a natura selvaggia. Rifugio a 1165 m, ex caserma con osservatorio ornitologico, percorso su mulattiera militare lastricata.'` | `'Percorso con botanica su aspetti floristici con lettura di poesie di Antonia Pozzi. Percorso facile e agibile a tutti, immerso nel Bosco Giulia con rocce calcaree della Grigna meridionale.'` |
| `image_url` | `null` | `null` | `null` | `null` | `null` |
| `max_capacity` | `50` | `50` | `50` | `50` | `50` |

---

### US-050: Bottone INFO con modale dettagli attività

**Description:** As a user on the activity selection page, I want to click an INFO button to see detailed technical information about an activity (difficulty, elevation, etc.) before choosing it.

**Acceptance Criteria:**
- [ ] In `resources/views/activities.blade.php`, ogni card non-esaurita mostra un bottone **"INFO"** sotto il bottone "Scegli" (anche le card esaurite mostrano il bottone INFO)
- [ ] Il bottone INFO apre una modale Alpine.js con:
  - Nome dell'attività (heading della modale)
  - **Difficoltà** (`difficulty`)
  - **Dislivello** (`elevation_gain`)
  - **Lunghezza / Durata** (`trail_length`)
  - **Acqua** (`water_description`)
  - **Descrizione itinerario** (`itinerary_description`)
  - Un link "Scopri di più sul sito CAI →" che apre `https://organizzazione.cai.it/gr-lombardia/progetti-regione-lom/attivita/` in target blank
- [ ] La modale ha un bottone di chiusura (×) e si chiude anche cliccando fuori dal pannello
- [ ] Solo i campi valorizzati vengono mostrati (se `itinerary_description` è null, la riga non appare)
- [ ] Il bottone INFO ha stile distinto dal bottone SCEGLI (es. outline/secondary, non blu pieno)
- [ ] Il `showActivities()` in `RegistrationController` include i nuovi campi nel mapping array
- [ ] Verify in browser: apri la modale di ogni attività e verifica che i dati siano corretti

**Nota implementativa**: usare Alpine.js `x-data` sul wrapper grid con stato `{ openModal: null }`. Ogni bottone INFO setta `openModal = activity.id`. La modale è un overlay full-screen con click-outside che resetta `openModal = null`. L'array attività passato alla view deve includere i nuovi campi.

---

### US-051: Aggiornamento locandina (Artifact HTML)

**Description:** As the event organizer, I want the printable flyer to show accurate technical details (elevation gain, trail length, trail type, packed lunch, and the corrected Resinelli meeting time) so all printed and digital materials are consistent.

**Acceptance Criteria:**
- [ ] La locandina viene aggiornata come Artifact HTML Claude (riaprire o ricreare nella sessione Claude, poi esportare/stampare in PDF)
- [ ] Nella sezione dedicata al **Rifugio Branca** (o nella sezione dati tecnici comune se presente), aggiungere:
  - **Difficoltà**: E – Escursionistico
  - **Dislivello**: 343 m
  - **Lunghezza/Durata**: 3 km – 3 ore
- [ ] Nella lista di tutte le attività, l'**orario del Rifugio Carlo Porta** (Resinelli) è `8:45` (non `9:00`)
- [ ] Aggiungere nella sezione "Cosa portare" o equivalente: **Pranzo al sacco**
- [ ] Il PDF aggiornato viene salvato in `docs/volantino/` sovrascrivendo la versione precedente (o con nome `volantino-grmrl-2026-v2.pdf`)

---

## Functional Requirements

- FR-1: Il form accetta iscrizioni senza minori; se ne vengono aggiunti, i campi del minore restano obbligatori
- FR-2: Il consenso `rules_accepted` contiene un link HTML cliccabile a `https://organizzazione.cai.it/gr-lombardia/progetti-regione-lom/attivita/`
- FR-3: Un banner informativo con link al progetto appare prima del form su ogni pagina `/iscriviti/{activity}`
- FR-4: La tabella `activities` ha 6 nuove colonne nullable: `difficulty`, `elevation_gain`, `trail_length`, `water_description`, `itinerary_description`, `image_url`
- FR-5: Il seeder popola tutti i nuovi campi con i dati reali del sito CAI per tutte e 5 le attività
- FR-6: Il Rifugio Carlo Porta ha `meeting_time = '8:45'` nel seeder (correzione rispetto a precedente '9:00')
- FR-7: La pagina `/attivita` mostra per ogni card un bottone INFO che apre una modale con i dettagli tecnici e un link esterno al sito CAI
- FR-8: La locandina riporta dislivello, lunghezza, difficoltà, orario corretto Resinelli e pranzo al sacco

---

## Non-Goals

- Nessun upload di immagini lato admin (il campo `image_url` è testuale, nessun file storage)
- Nessuna pagina di dettaglio dedicata per ogni attività (la modale INFO è sufficiente)
- Nessuna validazione del formato `difficulty` (campo libero, gestito dal seeder)
- Nessuna modifica alla struttura dell'email di conferma per includere i nuovi metadati
- Nessuna modifica all'export Excel per includere i nuovi campi
- La locandina non richiede QR code aggiornato (la URL di iscrizione non cambia)

---

## Technical Considerations

- **Migration**: usare `after('longitude')` per posizionare i nuovi campi in fondo; la migration non è distruttiva (solo `add`)
- **Seeder re-run**: il seeder usa `Activity::create()`, quindi ogni `db:seed --class=ActivitySeeder` ri-crea le attività; in produzione eseguire con `--force` dopo `migrate`
- **Alpine.js modal**: la modale usa il pattern già adottato nel form (x-data, x-show, x-cloak). Il controller passa i nuovi campi nell'array `$activities` mappato in `showActivities()`.
- **Filament ActivityResource**: aggiungere i 6 campi al form dell'admin (TextInput per stringhe, Textarea per i testi); nessun impatto sulla tabella list (i nuovi campi non devono essere colonne della tabella admin)
- **Locandina**: è un Artifact HTML Claude, non un file nel repository. Per aggiornarlo aprire la sessione Claude che lo ha creato, o fornire l'URL dell'artifact e chiedere di aggiornarlo

---

## Success Metrics

- Un utente adulto senza figli completa l'iscrizione senza errori di validazione
- Il link nel consenso porta alla pagina delle attività CAI in una nuova scheda
- Cliccando INFO su ogni card si vedono i dati tecnici corretti (coerenti con il sito CAI)
- `db:seed` popola le 5 attività con tutti i nuovi campi valorizzati
- L'orario del Rifugio Carlo Porta è 8:45 sia nel DB che nella locandina

---

## Open Questions

- **`image_url`**: le URL complete delle immagini non sono disponibili dalla pagina delle attività. Confermare se inserirle manualmente in un secondo momento o recuperarle ispezionando il sito CAI.
- **Ordine attività in locandina**: la locandina mostra le attività in un ordine specifico? Va mantenuto o va allineato all'ordine del seeder?
- **`meeting_place` Branca**: il luogo attuale nel seeder era `'Parcheggio Diga S.Giacomo presso Rif. Forni'`. Il nuovo è più lungo e include l'indicazione del ticket parcheggio. Va confermato che il nuovo testo non spezzi il layout della locandina.
