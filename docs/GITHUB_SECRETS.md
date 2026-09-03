# GitHub Secrets — CAMPUS Mail Engine

## Configurazione

Vai su: **Repository → Settings → Secrets and variables → Actions**

### Secrets obbligatori

| Nome | Valore | Note |
|------|--------|------|
| `SMTP_HOST` | `smtp.hostinger.com` | Host SMTP |
| `SMTP_PORT` | `465` | Porta (465 SSL o 587 STARTTLS) |
| `SMTP_USER` | `info@campus.camp` | Account email |
| `SMTP_PASS` | ⚠️ **DA INSERIRE** | Password da hPanel |
| `MAIL_FROM` | `info@campus.camp` | Indirizzo mittente |
| `MAIL_FROM_NAME` | `CAMPUS` | Nome mittente |

### Come aggiungere un secret

1. Repository → Settings → Secrets and variables → Actions
2. "New repository secret"
3. Name: `SMTP_HOST`
4. Secret: `smtp.hostinger.com`
5. "Add secret"

### Sicurezza

- I secrets sono criptati e non leggibili dopo il salvataggio
- Solo i workflow possono accedervi via `${{ secrets.NOME }}`
- Non appaiono nei log (GitHub li maschera automaticamente)
- Non committare MAI la password nel codice
