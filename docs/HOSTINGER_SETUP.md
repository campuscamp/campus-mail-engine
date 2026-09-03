# Hostinger SMTP Setup

## Configurazione

### Porta 465 (SSL) — Consigliata
```
SMTP_HOST=smtp.hostinger.com
SMTP_PORT=465
SMTP_SECURE=true
SMTP_USER=info@campus.camp
SMTP_PASS=password-da-hpanel
```

### Porta 587 (STARTTLS) — Alternativa
```
SMTP_HOST=smtp.hostinger.com
SMTP_PORT=587
SMTP_SECURE=false
SMTP_STARTTLS=true
SMTP_USER=info@campus.camp
SMTP_PASS=password-da-hpanel
```

## Dove trovare la password
1. Accedi a hPanel Hostinger
2. Email → Email Accounts
3. Cerca info@campus.camp
4. La password è quella impostata alla creazione dell'account email

## Limiti Hostinger
⚠️ I limiti di invio variano per piano:
- Verificare in hPanel → Email → Limiti di invio
- I valori nel sistema sono conservativi (default)
- Non superare mai i limiti del piano

## DNS necessari
Per la deliverability, verificare in hPanel:
- **MX** — correttamente puntato a Hostinger
- **SPF** — `v=spf1 include:_spf.hostinger.com ~all`
- **DKIM** — configurabile da hPanel → DNS Zone
- **DMARC** — `v=DMARC1; p=none; rua=mailto:info@campus.camp`
