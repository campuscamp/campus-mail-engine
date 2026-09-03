# CAMPUS Mail Engine

> **CAMPUS Mail Engine** — Sistema email e marketing automation per campus.camp
>
> Discere · Docere · Crescere

[![CI](https://github.com/campuscamp/campus-mail-engine/actions/workflows/ci.yml/badge.svg)](https://github.com/campuscamp/campus-mail-engine/actions/workflows/ci.yml)

## Cos'è

Sistema completo di email e marketing automation per l'ecosistema CAMPUS:

- **Email transazionali** — conferme, reset password, notifiche
- **Newsletter** — comunicazioni periodiche alla community
- **Campagne segmentate** — Faculty, studenti, aziende, PA, partner, territorio
- **Drip campaign** — sequenze email automatizzate
- **Lead scoring** — punteggio contatti configurabile
- **Marketing automation** — trigger, condizioni, azioni automatiche

## Architettura

```
┌──────────────────────────────────────────┐
│           CAMPUS Mail Engine             │
├──────────────────────────────────────────┤
│                                          │
│  ┌────────────┐    ┌──────────────────┐  │
│  │  Contacts  │    │   Campaigns      │  │
│  │  Consent   │    │   Sequences      │  │
│  │  Segments  │    │   Automation     │  │
│  │  Tags      │    │   Lead Scoring   │  │
│  │  Suppress  │    │   Personalize    │  │
│  └─────┬──────┘    └────────┬─────────┘  │
│        │                    │            │
│  ┌─────▼────────────────────▼─────────┐  │
│  │          Mailer (Orchestrator)      │  │
│  │  Template │ Queue │ Rate Limit     │  │
│  └─────────────────┬─────────────────┘  │
│                    │                     │
│  ┌─────────────────▼─────────────────┐  │
│  │      Provider Adapter Layer        │  │
│  │  SMTP(Hostinger) │ DryRun │ ESP   │  │
│  └─────────────────┬─────────────────┘  │
│                    │                     │
│  ┌─────────────────▼─────────────────┐  │
│  │    Event Bus (Digital Twin)        │  │
│  │  email.sent │ contact.created │ …  │  │
│  └───────────────────────────────────┘  │
│                                          │
└──────────────────────────────────────────┘
```

## Quick Start

### 1. Clone e installa

```bash
git clone https://github.com/campuscamp/campus-mail-engine.git
cd campus-mail-engine
cp .env.example .env
npm install
```

### 2. Configura SMTP

Modifica `.env` con le credenziali Hostinger:

```
SMTP_HOST=smtp.hostinger.com
SMTP_PORT=465
SMTP_SECURE=true
SMTP_USER=info@campus.camp
SMTP_PASS=la-tua-password
```

### 3. Test (Dry Run)

```bash
# Test suite completa
npm test

# Dry run — non invia nulla
DRY_RUN=true node scripts/send-test.js test@email.com
```

### 4. Test SMTP

```bash
# Verifica connessione
npm run test:smtp

# Invia email test reale
DRY_RUN=false npm run send:test -- test@email.com
```

## Modalità Dry Run

**Il sistema parte SEMPRE in dry run.** Nessuna email viene inviata finché:

1. `DRY_RUN=false` è configurato esplicitamente
2. Le credenziali SMTP sono valide
3. I contatti hanno consenso marketing

```bash
# Dry run (default) — valida senza inviare
npm run campaign:dry -- config/campaigns.example.json

# Invio reale
DRY_RUN=false npm run campaign:run -- config/campaigns.example.json
```

## Segmenti CAMPUS

20 segmenti preconfigurati:

| ID | Nome |
|----|------|
| GENERAL | Tutti i contatti |
| NEWSLETTER | Iscritti newsletter |
| FACULTY | Docenti e formatori |
| STUDENTS | Studenti iscritti |
| LEARNERS | Partecipanti a corsi |
| SCHOOLS | Istituti scolastici |
| UNIVERSITIES | Atenei |
| COMPANIES | Aziende |
| PROFESSIONALS | Liberi professionisti |
| PROFESSIONAL_BODIES | Ordini e collegi |
| PUBLIC_ADMINISTRATION | PA |
| MUNICIPALITIES | Comuni |
| PARTNERS | Partner operativi |
| SPONSORS | Sponsor e donatori |
| TERRITORY | Stakeholder territoriali |
| COMMUNITY | Community CAMPUS |
| EVENTS | Partecipanti a eventi |
| RESEARCH | Ricercatori |
| MEDIA | Giornalisti e media |
| INSTITUTIONAL | Comunicazioni istituzionali |

## Campagne

Stati campagna: `DRAFT → READY → SCHEDULED → RUNNING → COMPLETED`

```json
{
  "name": "Newsletter Settembre",
  "type": "marketing",
  "subject": "Ciao {{first_name}}, novità da CAMPUS",
  "template": "newsletter/base.html",
  "segment": "NEWSLETTER",
  "batch_size": 10,
  "rate_limit": 5,
  "dry_run": true
}
```

## Lead Scoring

Punteggio configurabile in `config/lead-scoring.json`:

| Evento | Punti |
|--------|-------|
| Signup | +5 |
| Email aperta | +1 |
| Click email | +3 |
| Registrazione evento | +10 |
| Candidatura Faculty | +20 |
| Richiesta aziendale | +25 |
| Unsubscribe | STOP marketing |

## GDPR & Compliance

- ✅ Consenso esplicito con audit log
- ✅ Double opt-in
- ✅ Unsubscribe obbligatorio
- ✅ Suppression list globale (bounce, complaint)
- ✅ Deduplicazione contatti
- ✅ Right to erasure (Art. 17)
- ✅ Nessun invio a contatti non consensati
- ✅ Footer GDPR obbligatorio nelle email marketing
- ✅ List-Unsubscribe header (RFC 8058)

## GitHub Actions

| Workflow | Trigger | Funzione |
|----------|---------|----------|
| `ci.yml` | push/PR | Lint, test, security audit |
| `send-test.yml` | Manuale | Invia UNA email test |
| `campaign-dispatch.yml` | Manuale | Esegui campagna (dry_run=true default) |
| `scheduled-campaigns.yml` | Cron 08:00 UTC | Processa campagne SCHEDULED |
| `health-check.yml` | Cron ogni 6h | Verifica SMTP e sistema |

## GitHub Secrets

Configura in: Repository → Settings → Secrets → Actions

| Secret | Valore |
|--------|--------|
| `SMTP_HOST` | `smtp.hostinger.com` |
| `SMTP_PORT` | `465` |
| `SMTP_USER` | `info@campus.camp` |
| `SMTP_PASS` | ⚠️ Password Hostinger |
| `MAIL_FROM` | `info@campus.camp` |
| `MAIL_FROM_NAME` | `CAMPUS` |

## Rate Limiting & Warm-up

Limiti configurabili (prudenti di default):

```
BATCH_SIZE=10
EMAILS_PER_MINUTE=5
EMAILS_PER_HOUR=100
DAILY_CAP=500
```

Warm-up iniziale:

| Periodo | Email/giorno |
|---------|-------------|
| Giorno 1-3 | 20 |
| Giorno 4-7 | 40 |
| Giorno 8-14 | 75 |
| Giorno 15+ | 150 |

> ⚠️ **I limiti reali devono essere verificati nel piano Hostinger/hPanel.**

## Integrazione con campuscamp

Il repository principale [campuscamp/campuscamp](https://github.com/campuscamp/campuscamp) può integrare il Mail Engine tramite:

- **Repository dispatch** — trigger workflow da repo padre
- **API/webhook** — endpoint futuro per invio da frontend
- **Event adapter** — eventi condivisi tra i due sistemi

Vedi: [docs/INTEGRATION_CAMPUSCAMP.md](docs/INTEGRATION_CAMPUSCAMP.md)

## Troubleshooting

| Problema | Soluzione |
|----------|----------|
| SMTP timeout | Verificare firewall, provare porta 587 |
| Auth fallita | Controllare credenziali in hPanel |
| Rate limit | Ridurre EMAILS_PER_MINUTE |
| Bounce | Verificare MX/SPF/DKIM del dominio |
| Email in spam | Configurare DKIM + DMARC |

## Stack

- **Runtime**: Node.js 20+ LTS
- **SMTP**: Nodemailer
- **Template**: Handlebars
- **Validation**: Zod
- **Test**: Vitest
- **CI/CD**: GitHub Actions
- **Storage**: JSON/in-memory (adapter-ready per SQLite/DB)

## Licenza

MIT — [LICENSE](LICENSE)

---

**CAMPUS** · campus.camp · Discere · Docere · Crescere
