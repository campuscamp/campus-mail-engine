#!/usr/bin/env node
/**
 * CAMPUS Mail Engine — SMTP Connection Test
 */
import 'dotenv/config';
import { createSmtpClientFromEnv } from '../src/mail/smtp-client.js';

async function main() {
  console.log('=== CAMPUS SMTP Connection Test ===\n');
  console.log(`Host: ${process.env.SMTP_HOST}`);
  console.log(`Port: ${process.env.SMTP_PORT}`);
  console.log(`User: ${process.env.SMTP_USER}`);
  console.log(`Pass: ${'*'.repeat(Math.min(process.env.SMTP_PASS?.length || 0, 20))}`);
  console.log('');

  const smtp = createSmtpClientFromEnv();
  const result = await smtp.verify();
  console.log('Result:', JSON.stringify(result, null, 2));
  await smtp.close();
  process.exit(result.success ? 0 : 1);
}

main().catch(err => { console.error('FATAL:', err.message); process.exit(1); });
