# GDPR Compliance — CAMPUS Mail Engine

## Implementazione

### Consenso (Art. 6-7)
- Consenso esplicito con timestamp e fonte
- Versione informativa tracciata
- Possibilità di ritiro in qualsiasi momento

### Double Opt-In
- Token univoco per conferma
- Email di conferma prima dell'attivazione

### Diritto di Cancellazione (Art. 17)
- `ConsentManager.eraseContact()` rimuove il contatto
- Audit log mantiene solo dati anonimizzati

### Minimizzazione (Art. 5)
- Solo dati necessari nel modello contatto
- Nessun dato sensibile richiesto

### Audit Log
Ogni operazione di consenso è tracciata:
- ID azione, timestamp, tipo, contact_id, metadati
- Non cancellabile (append-only)

### Suppression
- Hard bounce → suppression automatica
- Complaint → suppression automatica
- Unsubscribe → immediato, senza conferma aggiuntiva

## Contatto DPO
Per richieste GDPR: info@campus.camp
