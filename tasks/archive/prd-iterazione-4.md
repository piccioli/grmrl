# PRD: Iterazione 4 – Fix form, export/stampa, email duplicate, recap completo

## Introduction

Questa iterazione risolve una serie di bug e lacune funzionali nel flusso di registrazione e nelle funzioni admin:

1. I dati inseriti nel form (sezione CAI adulto e minori) vengono persi in caso di errore di validazione
2. Le pagine `/admin/registrations/export` e `/admin/registrations/print` restituiscono 404
3. Non viene impedita la doppia registrazione con la stessa email
4. Il recap nella pagina `/conferma` e nell'email non mostra tutti i dati (mancano data di nascita, socio sì/no, codice fiscale per adulto e minori)

---

## Goals

- Nessun dato inserito nel form viene perso in caso di errore di validazione
- L'export Excel e la stampa PDF funzionano correttamente dall'area admin
- Una stessa email non può essere usata per due iscrizioni distinte
- Il recap post-registrazione (pagina e email) mostra tutti i dati dell'adulto e di ogni minore

---

## User Stories

### US-029: Persistenza sezione CAI adulto su errore form
**Description:** As a user, I want the CAI section I selected to remain visible after a failed form submission so I don't have to search and select it again.

**Acceptance Criteria:**
- [ ] Dopo un errore di validazione, il campo sezione CAI adulto mostra la sezione precedentemente selezionata (nome visibile nell'autocomplete)
- [ ] L'ID della sezione è correttamente pre-valorizzato nell'input hidden `cai_section_id`
- [ ] Il comportamento è coerente con il resto dei campi che usano `old()`
- [ ] Typecheck/lint passa
- [ ] Verify in browser using dev-browser skill

**Note tecniche:** Il form usa Alpine.js per l'autocomplete della sezione. L'inizializzazione di Alpine (`x-data`) deve leggere `old('cai_section_id')` e il nome della sezione corrispondente (passato dal controller o ricavato via query su `CaiSection::find(old('cai_section_id'))`).

---

### US-030: Persistenza dati minori su errore form (inclusa sezione CAI)
**Description:** As a user, I want all the minor data I entered (including their CAI section) to remain after a failed form submission so I don't have to re-enter everything.

**Acceptance Criteria:**
- [ ] Dopo un errore di validazione, tutti i minori precedentemente aggiunti vengono ripristinati (nome, cognome, data di nascita, socio sì/no, codice fiscale)
- [ ] Per ogni minore che era socio CAI, la sezione CAI selezionata rimane visibile nell'autocomplete
- [ ] Il numero di minori mostrati corrisponde a quello precedentemente inserito
- [ ] I campi condizionali (sezione / codice fiscale) si mostrano/nascondono correttamente in base al flag `is_cai_member`
- [ ] Typecheck/lint passa
- [ ] Verify in browser using dev-browser skill

**Note tecniche:** Alpine.js inizializza `minors` da un array vuoto. Deve essere inizializzato invece con `old('minors', [])` passato come JSON. Per ogni minore con `cai_section_id`, occorre precaricare anche il nome della sezione (array `sections` pre-idratato con `CaiSection::whereIn('id', [...ids...])->get()`).

---

### US-031: Validazione email duplicata on blur
**Description:** As a user, I want to be immediately notified when I enter an email already used for another registration so I can correct it before submitting.

**Acceptance Criteria:**
- [ ] Nuovo endpoint `GET /api/check-email?email=...` restituisce JSON `{"exists": true/false}`
- [ ] Quando il focus lascia il campo email, parte una fetch verso l'endpoint
- [ ] Se `exists: true`, compare un messaggio di errore inline sotto il campo: "Questo indirizzo email è già stato utilizzato per un'iscrizione. Ogni partecipante deve usare un'email univoca."
- [ ] Se `exists: false`, il messaggio di errore scompare (se presente)
- [ ] Se l'email non è valida (non supera il formato base), non parte la chiamata
- [ ] Il submit del form è bloccato se è presente questo errore inline
- [ ] Typecheck/lint passa
- [ ] Verify in browser using dev-browser skill

---

### US-032: Validazione email duplicata al submit (server-side)
**Description:** As a developer, I want the server to reject duplicate emails on form submission as a safety net, even if the client-side check was bypassed.

**Acceptance Criteria:**
- [ ] La validazione server-side nel metodo `store()` include la regola `unique:registrations,email`
- [ ] In caso di email duplicata al submit, l'utente vede il messaggio di errore sul campo email
- [ ] I dati del form vengono preservati (withInput) anche per questo errore
- [ ] Typecheck/lint passa

---

### US-033: Export Excel `/admin/registrations/export`
**Description:** As an admin, I want to download a complete Excel export of registrations so I can analyze data offline.

**Acceptance Criteria:**
- [ ] `GET /admin/registrations/export` risponde con download di un file `.xlsx` (non 404)
- [ ] Il file include tutte le colonne già definite in `RegistrationsExport` (adulto + 3 slot minori)
- [ ] Il parametro opzionale `?activity_id=X` filtra correttamente per attività
- [ ] Il download funziona senza errori anche se non ci sono registrazioni (file vuoto con solo header)
- [ ] Typecheck/lint passa

**Note tecniche:** Verificare che il package `maatwebsite/excel` sia installato (`composer require maatwebsite/excel`). Il controller e la classe Export esistono già.

---

### US-034: Stampa PDF `/admin/registrations/print`
**Description:** As an admin, I want to generate a print-ready PDF of registrations so I can avere un elenco cartaceo alle escursioni.

**Acceptance Criteria:**
- [ ] `GET /admin/registrations/print` risponde con download di un file PDF (non 404)
- [ ] Il PDF contiene una tabella con: Nome, Cognome, Email, Telefono, Attività, Socio CAI, Sezione, Minori (nome + data)
- [ ] Il parametro opzionale `?activity_id=X` filtra per attività
- [ ] Intestazione del documento: "Iscrizioni – Giornata Regionale della Montagna" + data di generazione
- [ ] Il PDF è generato con `barryvdh/laravel-dompdf`
- [ ] Typecheck/lint passa

**Note tecniche:** Installare `composer require barryvdh/laravel-dompdf`. Creare view `resources/views/admin/print.blade.php`. Il metodo `print()` in `ExportController` deve usare `Pdf::loadView(...)->download(...)`.

---

### US-035: Recap completo nella pagina `/conferma`
**Description:** As a user, I want the confirmation page to show all the data I entered so I can verify everything was recorded correctly.

**Acceptance Criteria:**
- [ ] Sezione adulto mostra: Nome, Cognome, Data di nascita (gg/mm/aaaa), Email, Telefono, Socio CAI (Sì/No), Sezione CAI (se socio) o Codice Fiscale (se non socio)
- [ ] Per ogni minore: Nome, Cognome, Data di nascita (gg/mm/aaaa), Socio CAI (Sì/No), Sezione CAI (se socio) o Codice Fiscale (se non socio)
- [ ] I dati vengono caricati dalla relazione `$registration->load('caiSection', 'minors.caiSection')`
- [ ] Se una sezione non è presente (non socio), mostra "Non socio CAI"
- [ ] Typecheck/lint passa
- [ ] Verify in browser using dev-browser skill

**Campi attualmente mancanti:** `birth_date` (adulto), `is_cai_member` (adulto), `fiscal_code` (adulto), `is_cai_member` (minori), `fiscal_code` (minori).

---

### US-036: Recap completo nell'email di conferma
**Description:** As a user, I want the confirmation email to show all the data I entered so I have a complete record of my registration.

**Acceptance Criteria:**
- [ ] Sezione adulto nell'email mostra: Nome, Cognome, Data di nascita, Email, Telefono, Socio CAI (Sì/No), Sezione CAI (se socio) o Codice Fiscale (se non socio)
- [ ] Per ogni minore nell'email: Nome, Cognome, Data di nascita, Socio CAI (Sì/No), Sezione CAI (se socio) o Codice Fiscale (se non socio)
- [ ] Il Mailable passa il record `$registration` con le relazioni caricate (`caiSection`, `minors.caiSection`)
- [ ] Il layout email rimane consistente con lo stile esistente (box per ogni sezione)
- [ ] Typecheck/lint passa

**Campi attualmente mancanti nell'email:** `birth_date` (adulto), `phone` (adulto), `fiscal_code` (adulto), `is_cai_member` esplicito (adulto), `fiscal_code` (minori).

---

## Functional Requirements

- FR-1: Il form di registrazione deve ripristinare tutti i campi Alpine.js (sezione adulto e dati minori incluse sezioni) tramite `old()` iniettato come JSON nell'inizializzazione Alpine
- FR-2: Il controller deve arricchire la risposta di errore con i nomi delle sezioni pre-selezionate (per adulto e minori) così da poter inizializzare l'autocomplete senza ulteriori chiamate AJAX
- FR-3: Endpoint `GET /api/check-email` deve essere protetto da rate limiting (max 10 req/minuto per IP) e rispondere solo con `{"exists": bool}`
- FR-4: La regola `unique:registrations,email` deve essere aggiunta nella validazione di `store()`
- FR-5: `maatwebsite/excel` deve essere installato e configurato; l'export scarica il file con nome `iscrizioni-YYYY-MM-DD.xlsx`
- FR-6: `barryvdh/laravel-dompdf` deve essere installato; il PDF viene scaricato con nome `iscrizioni-YYYY-MM-DD.pdf`
- FR-7: La pagina `/conferma` carica `$registration` con `->load('caiSection', 'minors.caiSection')` prima di passarlo alla view
- FR-8: Il Mailable `RegistrationConfirmation` passa `$registration` con relazioni caricate alla view email

---

## Non-Goals

- Nessuna modifica al pannello Filament admin esistente
- Nessuna funzionalità di modifica/cancellazione iscrizioni lato utente
- Nessun sistema di notifica per email duplicate (solo blocco + messaggio)
- L'export non include i campi consenso (privacy, foto, ecc.)
- La stampa PDF non include funzionalità di anteprima in browser

---

## Technical Considerations

- **Alpine.js e `old()`:** Il modo più pulito è passare dal controller un array `$preloadedSections` con i dati delle sezioni già selezionate (id + nome), e iniettarlo come `@json()` nell'attributo `x-data` del form
- **Rate limiting email check:** Usare `RateLimiter` di Laravel nel route o middleware `throttle:10,1`
- **DomPDF:** Per tabelle con molte righe, impostare `options.chunkSize` e usare `@page { size: A4 landscape; }` nel CSS del template PDF
- **Compatibilità `withInput` + Alpine:** `old('minors')` restituisce un array PHP con indici numerici; passarlo a Alpine come `JSON.parse('{{ json_encode(old("minors", [])) }}')`

---

## Success Metrics

- Zero perdita di dati del form dopo un errore di validazione
- Export e stampa funzionanti al 100% senza errori 500
- Nessuna registrazione duplicata con stessa email nel DB
- Il recap (pagina + email) mostra tutti i campi richiesti

---

## Open Questions

- L'export Excel deve includere anche i 5 consensi come colonne boolean? (attualmente non inclusi in `RegistrationsExport`)
- Il PDF di stampa deve includere un QR code o codice identificativo per ogni riga?
- L'endpoint `/api/check-email` deve escludere la propria email in caso di editing futuro dell'iscrizione?
