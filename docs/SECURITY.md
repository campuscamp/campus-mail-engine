# Sicurezza — CAMPUS Mail Engine

## Regole

1. **No secrets nel repo** — `.env` è in `.gitignore`, usare GitHub Secrets
2. **Input validation** — tutti gli input validati con Zod
3. **HTML escaping** — Handlebars escapa automaticamente
4. **Log sicuri** — password mai nei log, email mascherate opzionalmente
5. **GitHub Actions** — permissions minime (`contents: read`)
6. **Dipendenze** — `npm audit` nel CI
7. **Actions pinned** — versioni specifiche (v4)

## Anti-spam

- NO invii a contatti senza consenso
- NO elusione limiti provider
- NO falsificazione header
- NO spoofing
- NO liste acquistate senza base giuridica
- Suppression list obbligatoria
- Unsubscribe funzionante e immediato
