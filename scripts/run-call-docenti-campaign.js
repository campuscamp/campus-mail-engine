#!/usr/bin/env node
/**
 * CAMPUS Mail Engine — Dispatcher della Campagna Nazionale Call Docenti
 * 
 * Invia l'email persuasiva istituzionale agli Ordini, Collegi, Albi e Associazioni
 * 
 * Utilizzo:
 *   node scripts/run-call-docenti-campaign.js --dry-run
 *   node scripts/run-call-docenti-campaign.js --test info@campus.camp
 *   node scripts/run-call-docenti-campaign.js --live
 */

import 'dotenv/config';
import { readFileSync } from 'fs';
import { Mailer } from '../src/mail/mailer.js';
import { createSmtpClientFromEnv } from '../src/mail/smtp-client.js';
import { DryRunProvider } from '../src/mail/provider-adapter.js';
import { CENSUS_CONTACTS } from '../src/contacts/census-database.js';

async function main() {
  const args = process.argv.slice(2);
  const isDryRun = args.includes('--dry-run') || (!args.includes('--live') && !args.includes('--test'));
  const testEmail = args.find((arg, i) => args[i - 1] === '--test');

  console.log('================================================================');
  console.log('🏛️  CAMPUS MAIL ENGINE — CAMPAGNA NAZIONALE CALL DOCENTI & ENTI');
  console.log('================================================================\n');
  console.log(`Modalità: ${isDryRun ? '🔍 DRY RUN (Simulazione Protetta)' : testEmail ? `🧪 TEST SINGOLO SU ${testEmail}` : '🚀 INVIO LIVE MASSIVO'}`);
  console.log(`Contatti censiti nel database: ${CENSUS_CONTACTS.length}`);

  // Carica il template HTML persuasivo
  const templateHtml = readFileSync('templates/call-docenti-persuasiva.html', 'utf-8');

  // Istanzia il provider (DryRun o SMTP Reale)
  const provider = isDryRun 
    ? new DryRunProvider() 
    : createSmtpClientFromEnv();

  const mailer = new Mailer(provider, { 
    dryRun: isDryRun,
    mailFrom: process.env.MAIL_FROM || 'info@campus.camp',
    mailFromName: process.env.MAIL_FROM_NAME || 'CAMPUS — Rettorato e Direzione Accademica',
  });

  // Seleziona la lista dei destinatari
  let recipients = [];
  if (testEmail) {
    recipients = [{
      id: 'TEST-001',
      organization: 'Ordine di Esempio e Test — Provincia di Rovigo',
      target_role: 'Presidente e Consiglio Direttivo',
      email: testEmail,
      level: 'TEST',
      priority: 'TEST'
    }];
  } else {
    recipients = CENSUS_CONTACTS;
  }

  console.log(`\nInizio elaborazione per ${recipients.length} destinatari...\n`);

  let sentCount = 0;
  let errorCount = 0;

  for (let i = 0; i < recipients.length; i++) {
    const contact = recipients[i];
    const subject = `[Avviso Istituzionale] Bando Reclutamento Docenti CAMPUS per gli iscritti di ${contact.organization} — Affissione e Circolare`;

    const templateData = {
      organization: contact.organization,
      target_role: contact.target_role || 'Presidente e Segreteria',
      manifesto_url: 'https://campus.camp/manifesto-docenti-a4.html',
      candidatura_url: 'https://campus.camp/faculty',
      privacy_url: 'https://campus.camp/privacy',
      unsubscribe_url: `https://campus.camp/unsubscribe?id=${encodeURIComponent(contact.id)}`
    };

    try {
      console.log(`[${i + 1}/${recipients.length}] Invio a: ${contact.organization} <${contact.email}>`);
      
      const result = await mailer.sendOne({
        to: contact.email,
        subject: subject,
        template: templateHtml,
        data: templateData,
        type: 'institutional',
        attachments: [
          {
            filename: 'Manifesto_Ufficiale_Reclutamento_Docenti_CAMPUS_A4.pdf',
            path: 'c:/81PLUS_GLOBAL_MASTER/campus.camp/campus-mail-engine/assets/Manifesto_Ufficiale_Call_Docenti_A4.pdf',
            contentType: 'application/pdf'
          }
        ]
      });

      if (result.messageId || result.success || result.dryRun) {
        sentCount++;
        console.log(`   ✅ Successo: ${result.messageId || 'DRY-RUN-OK'}`);
      } else {
        errorCount++;
        console.error(`   ❌ Fallito: ${result.error || 'Invio non accettato'}`);
      }

      // Rispetta il rate-limiting prudente (evita picchi aggressivi verso i server di posta degli ordini)
      if (!isDryRun && i < recipients.length - 1) {
        const delayMs = parseInt(process.env.SEND_DELAY_MS || '3000', 10);
        await new Promise(r => setTimeout(r, delayMs));
      }

    } catch (err) {
      errorCount++;
      console.error(`   ❌ Eccezione durante l'invio a ${contact.email}:`, err.message);
    }
  }

  console.log('\n================================================================');
  console.log(`🏁 RIEPILOGO CAMPAGNA:`);
  console.log(`   Totale destinatari elaborati: ${recipients.length}`);
  console.log(`   Inviati con successo:         ${sentCount}`);
  console.log(`   Errori:                       ${errorCount}`);
  console.log('================================================================\n');

  await mailer.close();
}

main().catch(err => {
  console.error('FATAL ERROR:', err);
  process.exit(1);
});
