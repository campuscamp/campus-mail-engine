# Modello Campagne — CAMPUS Mail Engine

## Struttura Campagna

```json
{
  "campaign_id": "uuid",
  "name": "Newsletter Settembre 2026",
  "type": "marketing",
  "subject": "Ciao {{first_name}}, novità dal CAMPUS",
  "template": "newsletter/base.html",
  "segment": "NEWSLETTER",
  "filters": {},
  "status": "DRAFT",
  "scheduled_at": "2026-09-15T08:00:00Z",
  "batch_size": 10,
  "rate_limit": 5,
  "dry_run": true,
  "stats": { "total": 0, "sent": 0, "failed": 0 },
  "created_at": "2026-09-01T00:00:00Z",
  "updated_at": "2026-09-01T00:00:00Z"
}
```

## State Machine

```
DRAFT ──→ READY ──→ SCHEDULED ──→ RUNNING ──→ COMPLETED
  ↑         │          │            │
  │         │          │            ↓
  │         │          ↓          PAUSED ──→ RUNNING
  │         │        PAUSED          │
  │         │          │             ↓
  └─────────┴──────────┴──────── CANCELLED
                                     │
  FAILED ←─── RUNNING               │
    │                                │
    └────────→ DRAFT ←───────────────┘
```

## Tipi di campagna

| Tipo | Consenso | Unsubscribe | Footer |
|------|----------|-------------|--------|
| `transactional` | No | No | Semplice |
| `institutional` | No | Sì | Standard |
| `marketing` | Sì | Sì | Completo GDPR |
