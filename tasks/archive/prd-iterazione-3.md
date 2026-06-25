# PRD: Iterazione 3 – Fix testi, bottone, footer, validazioni e pagina conferma

## Introduzione

Terza iterazione di miglioramenti al flusso di iscrizione. Corregge la denominazione dell'organizzazione,
migliora il bottone della landing, allinea i contatti nel footer, rende visibili gli errori di validazione
per i minori e la sezione CAI, e aggiunge il recap completo alla pagina di conferma post-iscrizione.

## Goals

- Usare il nome ufficiale corretto dell'organizzazione su tutta l'app
- Rendere il bottone "INIZIA" semanticamente corretto e con spaziatura adeguata
- Allineare email e telefono del footer ai valori già usati nell'header
- Mostrare un messaggio di errore esplicito quando non è stato aggiunto nessun minore
- Bloccare lato server l'invio se un minore socio CAI non ha la sezione selezionata
- Mostrare sulla pagina `/conferma` lo stesso riepilogo già presente nell'email

## User Stories

### US-023: Correzione denominazione organizzazione
**Description:** As a developer, I want to fix the organization name so that the correct official name is used consistently.

**Acceptance Criteria:**
- [ ] Sostituire ogni occorrenza di "Club Alpino Italiano – Sezione Lombardia" con "Club Alpino Italiano – Gruppo Regionale Lombardia" in tutti i file blade
- [ ] Verificare anche in eventuali file PHP (controller, mail, config)
- [ ] Nessuna altra occorrenza del testo vecchio rimane nel codebase (escl. vendor)
- [ ] Verify in browser using dev-browser skill

### US-024: Bottone "INIZIA" maiuscolo con padding corretto
**Description:** As a visitor, I want the CTA button on the landing page to be clearly styled and readable so that it attracts attention.

**Acceptance Criteria:**
- [ ] Il link `<a>` nella landing è convertito in un vero `<button type="button">` che naviga via JS (`window.location`) oppure rimane `<a>` ma con `role="button"` e padding simmetrico
- [ ] Il testo del bottone è "INIZIA" (tutto maiuscolo, non via CSS `uppercase` su testo minuscolo – il testo nel template deve essere in maiuscolo)
- [ ] Il padding è visivamente equilibrato (es. `px-12 py-4` o equivalente) e il bottone ha dimensione adeguata
- [ ] Verify in browser using dev-browser skill

### US-025: Footer con contatti allineati all'header
**Description:** As a user, I want the footer to show the same phone and email as the header so that the contact information is consistent.

**Acceptance Criteria:**
- [ ] In `resources/views/layouts/app.blade.php`, il footer usa `config('grmrl.support_phone')` e `config('grmrl.support_email')` al posto dei valori hardcoded attuali (`gr_cai_lombardia@cai.it` e `+393274851177`)
- [ ] I link `href="tel:..."` e `href="mailto:..."` usano gli stessi valori config
- [ ] Verify in browser using dev-browser skill

### US-026: Errore visibile quando non è stato aggiunto nessun minore
**Description:** As a user who forgets to add a minor, I want to see a clear error message so that I understand what is missing.

**Acceptance Criteria:**
- [ ] Il messaggio di errore Laravel per `minors` (già definito come "È obbligatorio aggiungere almeno un minore.") viene mostrato nel form con stile rosso, vicino alla sezione "Minori accompagnati"
- [ ] Aggiungere `@error('minors') ... @enderror` nella view `registration/form.blade.php` sopra o sotto il bottone "Aggiungi minore"
- [ ] La sezione minori ha bordo/sfondo rosso visibile quando c'è questo errore
- [ ] Verify in browser using dev-browser skill

### US-027: Validazione server-side sezione CAI per minori soci
**Description:** As a developer, I want the server to validate that a minor who is a CAI member has a section selected so that the data is consistent.

**Acceptance Criteria:**
- [ ] In `RegistrationController::store()`, aggiungere validazione condizionale per `minors.*.cai_section_id`: obbligatorio quando `minors.*.is_cai_member` è truthy, deve esistere in `cai_sections`
- [ ] Aggiungere il messaggio di errore in italiano corrispondente (es. "La sezione CAI del minore è obbligatoria per i soci.")
- [ ] Il form mostra l'errore correttamente anche per i minori (attualmente non ci sono `@error` per i campi dinamici dei minori – verificare e aggiungere se necessario)
- [ ] La stessa validazione già esistente per l'adulto (`cai_section_id` richiesto se `is_cai_member`) rimane invariata

### US-028: Pagina /conferma con riepilogo completo
**Description:** As a user who just registered, I want the confirmation page to show the full recap of my registration so that I can verify the data without opening the email.

**Acceptance Criteria:**
- [ ] `RegistrationController::store()` salva l'ID dell'iscrizione in sessione (`session(['registration_id' => $registration->id])`) insieme all'email già esistente
- [ ] `RegistrationController::confirm()` recupera il record `Registration` con le relazioni `activity`, `minors`, `minors.caiSection`, `caiSection` e lo passa alla view; se non trovato, mostra solo il messaggio di successo senza recap
- [ ] La view `registration/confirm.blade.php` mostra:
  - Nome e cognome dell'adulto iscritto
  - Sezione CAI (se socio) o "Non socio CAI"
  - Lista dei minori (nome, cognome, data di nascita, sezione CAI o non socio)
  - Box attività selezionata: nome, orario ritrovo, luogo di partenza
- [ ] Il riepilogo è visivamente coerente con lo stile dell'email (box colorato per l'attività, lista minori in card grigie)
- [ ] Verify in browser using dev-browser skill

## Functional Requirements

- FR-1: La stringa "Sezione Lombardia" non deve apparire in nessun file dell'app (escl. vendor/storage)
- FR-2: Il bottone INIZIA nella landing deve avere il testo in maiuscolo direttamente nel template HTML
- FR-3: Il footer del layout usa esclusivamente valori da `config('grmrl.support_phone')` e `config('grmrl.support_email')`
- FR-4: Se il form viene inviato senza minori, l'errore `minors` viene renderizzato visibilmente nella sezione "Minori accompagnati"
- FR-5: Il controller valida che ogni minore con `is_cai_member = 1` abbia `cai_section_id` valorizzato e valido
- FR-6: L'ID dell'iscrizione creata viene messo in sessione e usato nella pagina di conferma per mostrare il recap

## Non-Goals

- Non si modifica il contenuto o il layout dell'email di conferma
- Non si aggiungono nuovi campi al form di iscrizione
- Non si modifica la logica di disponibilità posti
- Non si cambia il layout o la struttura del footer (solo i valori di telefono/email)

## Technical Considerations

- La view `confirm.blade.php` deve gestire il caso in cui la sessione non contenga `registration_id` (utente arriva direttamente sull'URL) mostrando solo il messaggio generico
- Per i minori dinamici (Alpine.js), gli errori server-side per `minors.*.cai_section_id` non sono visualizzabili inline sui campi – è sufficiente mostrarli come lista di errori generali sopra il form oppure nella sezione minori
- US-027 richiede di leggere il valore di `minors.*.is_cai_member` dal request in modo corretto (il checkbox invia `"1"` o è assente)

## Success Metrics

- Zero occorrenze di "Sezione Lombardia" nel codebase
- La pagina `/conferma` mostra nome, attività e minori senza aprire l'email
- Un utente che dimentica di aggiungere un minore vede immediatamente il messaggio di errore

## Open Questions

- Il bottone INIZIA deve navigare alla stessa route `activities` con `<a>` o cambiare in `<button>` con redirect JS? (Raccomandato: mantenere `<a>` per accessibilità/SEO, aggiungere `role="button"` se necessario)
