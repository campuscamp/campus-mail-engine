#!/usr/bin/env node
/**
 * CAMPUS Mail Engine — Send Test Email
 * 
 * Usage: node scripts/send-test.js <to@email.com> [subject]
 */
import 'dotenv/config';
import { readFileSync } from 'fs';
import { Mailer } from '../src/mail/mailer.js';
import { createSmtpClientFromEnv } from '../src/mail/smtp-client.js';

async function main() {
  const to = process.argv[2] || process.env.SMTP_USER;
  const subject = process.argv[3] || '🧪 CAMPUS Mail Engine — Test';

  if (!to) {
    console.error('Usage: node scripts/send-test.js <to@email.com> [subject]');
    process.exit(1);
  }

  const dryRun = process.env.DRY_RUN === 'true';
  console.log(`=== CAMPUS Send Test ===`);
  console.log(`To: ${to}`);
  console.log(`Subject: ${subject}`);
  console.log(`Dry Run: ${dryRun}\n`);

  const provider = createSmtpClientFromEnv();
  const mailer = new Mailer(provider, { dryRun });

  let template;
  try {
    template = readFileSync('templates/newsletter/base.html', 'utf-8');
  } catch {
    template = '<h1>Test CAMPUS Mail Engine</h1><p>Ciao {{first_name}}, questa è una email di test.</p>';
  }

  const result = await mailer.sendOne({
    to,
    subject,
    template,
    data: {
      first_name: 'Test',
      body: 'Questa è una email di test dal CAMPUS Mail Engine. Se la ricevi, il sistema funziona correttamente.',
      reason: 'stai testando il sistema CAMPUS Mail Engine',
      campus_url: process.env.CAMPUS_URL || 'https://campus.camp',
    },
    type: 'transactional',
  });

  console.log('Result:', JSON.stringify(result, null, 2));
  await mailer.close();
}

main().catch(err => { console.error('FATAL:', err.message); process.exit(1); });
