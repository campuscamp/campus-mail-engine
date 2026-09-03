#!/usr/bin/env node
/**
 * CAMPUS Mail Engine — Campaign Runner
 * Default: DRY_RUN=true
 */
import 'dotenv/config';
import { readFileSync } from 'fs';
import { CampaignEngine } from '../src/campaigns/campaign-engine.js';
import { ContactStore } from '../src/contacts/contact-model.js';
import { ConsentManager } from '../src/contacts/consent.js';
import { SuppressionList } from '../src/contacts/suppression.js';
import { Mailer } from '../src/mail/mailer.js';
import { DryRunProvider } from '../src/mail/provider-adapter.js';
import { createSmtpClientFromEnv } from '../src/mail/smtp-client.js';

async function main() {
  const dryRun = process.env.DRY_RUN !== 'false';
  const campaignFile = process.argv[2];

  console.log('=== CAMPUS Campaign Runner ===');
  console.log(`Dry Run: ${dryRun}\n`);

  if (!campaignFile) {
    console.log('Usage: node scripts/campaign-runner.js <campaign.json>');
    console.log('Set DRY_RUN=false to send real emails.');
    process.exit(0);
  }

  const campaignData = JSON.parse(readFileSync(campaignFile, 'utf-8'));
  const engine = new CampaignEngine();
  const campaign = engine.create(campaignData);
  console.log(`Campaign: ${campaign.name} (${campaign.campaign_id})`);
  console.log(`Segment: ${campaign.segment}`);
  console.log(`Status: ${campaign.status}\n`);

  if (dryRun) {
    console.log('[DRY RUN] Would send to segment:', campaign.segment);
    console.log('[DRY RUN] Template:', campaign.template);
    console.log('[DRY RUN] Subject:', campaign.subject);
    console.log('[DRY RUN] No emails sent.');
  }
}

main().catch(err => { console.error('FATAL:', err.message); process.exit(1); });
