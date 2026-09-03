# Integrazione con campuscamp — CAMPUS Mail Engine

## Panoramica

Il repository [campuscamp/campuscamp](https://github.com/campuscamp/campuscamp) è il frontend/piattaforma principale di CAMPUS.

Il **campus-mail-engine** è un servizio indipendente che può essere invocato da campuscamp tramite:

## 1. Repository Dispatch

Il repo padre può triggerare workflow del mail engine:

```yaml
# Nel repo campuscamp - .github/workflows/trigger-mail.yml
- name: Trigger mail engine
  uses: peter-evans/repository-dispatch@v3
  with:
    token: ${{ secrets.REPO_TOKEN }}
    repository: campuscamp/campus-mail-engine
    event-type: send-campaign
    client-payload: '{"segment": "NEWSLETTER", "template": "newsletter"}'
```

## 2. Workflow Dispatch

Invocazione manuale o programmatica dei workflow:
- `send-test.yml` — invio test singolo
- `campaign-dispatch.yml` — esecuzione campagna

## 3. API Adapter (futuro)

Endpoint REST per invio programmatico:

```
POST /api/send
POST /api/campaign/create
POST /api/contact/subscribe
POST /api/contact/unsubscribe
GET  /api/health
```

## 4. Event Adapter (futuro)

Eventi condivisi tra i sistemi:

```
campuscamp → contact.created → campus-mail-engine
campuscamp → user.registered → campus-mail-engine
campus-mail-engine → email.sent → campuscamp
campus-mail-engine → email.bounced → campuscamp
```

## 5. Queue/Webhook (futuro)

Il frontend invia webhook al mail engine per:
- Nuova registrazione → onboarding sequence
- Iscrizione newsletter → double opt-in flow
- Richiesta informazioni → follow-up automatico

## Principio

**Non accoppiare direttamente il core al frontend.**
Il mail engine è un servizio autonomo, invocabile tramite interfacce standard.
