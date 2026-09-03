# Architettura CAMPUS Mail Engine

## Principi

1. **Adapter-based** — il provider email è intercambiabile (SMTP oggi, API ESP domani)
2. **Dry Run first** — nessun invio reale senza configurazione esplicita
3. **Compliance by design** — consenso, suppression, audit log integrati nel core
4. **Event-driven** — ogni azione emette un evento per il Digital Twin

## Moduli

### mail/
- `provider-adapter.js` — Interfaccia base + DryRunProvider
- `smtp-client.js` — Nodemailer con config Hostinger
- `mailer.js` — Orchestratore centrale
- `queue.js` — Coda batch con rate limiting
- `rate-limit.js` — Limiti per minuto/ora/giorno + warm-up
- `retry.js` — Exponential backoff

### contacts/
- `contact-model.js` — Schema Zod, CRUD, dedup, query segmenti
- `consent.js` — Grant/withdraw, double opt-in, audit log
- `suppression.js` — Suppression list globale
- `segments.js` — 20 segmenti CAMPUS
- `tags.js` — Tagging flessibile

### campaigns/
- `campaign-engine.js` — State machine campagne
- `sequence-engine.js` — Drip campaign + automation
- `personalization.js` — Merge tags + scheduler

### tracking/
- `events.js` — Event bus Digital Twin
- `clicks.js` — Click tracking + metriche

## Migrazione futura a ESP

Per passare da SMTP Hostinger a un ESP (SendGrid, Resend, Postmark):

1. Creare un nuovo adapter in `src/mail/` (es. `sendgrid-adapter.js`)
2. Implementare `send()` e `verify()` dell'interfaccia `MailProviderAdapter`
3. Configurare il nuovo provider nell'environment
4. Nessuna modifica al resto del sistema
