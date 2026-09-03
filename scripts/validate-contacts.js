#!/usr/bin/env node
/**
 * CAMPUS Mail Engine — Validate Contacts
 */
import { isValidEmail } from '../src/mail/mailer.js';

const testEmails = [
  'valid@example.com',
  'info@campus.camp',
  'invalid-no-at',
  '@missing-local.com',
  'spaces in@email.com',
  'valid+tag@domain.co.uk',
  '',
  null,
];

console.log('=== CAMPUS Contact Validation ===\n');
for (const email of testEmails) {
  const valid = isValidEmail(email);
  console.log(`${valid ? '✅' : '❌'} ${email || '(empty)'}`);
}
