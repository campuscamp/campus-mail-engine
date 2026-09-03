/**
 * CAMPUS Mail Engine — Comprehensive Test Suite
 */
import { describe, it, expect, beforeEach } from 'vitest';
import { isValidEmail, maskEmail, Mailer } from '../src/mail/mailer.js';
import { DryRunProvider } from '../src/mail/provider-adapter.js';
import { RateLimiter } from '../src/mail/rate-limit.js';
import { RetryHandler } from '../src/mail/retry.js';
import { ContactStore } from '../src/contacts/contact-model.js';
import { ConsentManager } from '../src/contacts/consent.js';
import { SuppressionList } from '../src/contacts/suppression.js';
import { CampaignEngine } from '../src/campaigns/campaign-engine.js';
import { EventBus } from '../src/tracking/events.js';
import { MetricsAggregator } from '../src/tracking/clicks.js';
import { SendQueue } from '../src/mail/queue.js';

// ========================
// EMAIL VALIDATION
// ========================
describe('Email Validation', () => {
  it('accepts valid emails', () => {
    expect(isValidEmail('info@campus.camp')).toBe(true);
    expect(isValidEmail('user+tag@example.com')).toBe(true);
    expect(isValidEmail('name@sub.domain.co.uk')).toBe(true);
  });

  it('rejects invalid emails', () => {
    expect(isValidEmail('')).toBe(false);
    expect(isValidEmail(null)).toBe(false);
    expect(isValidEmail(undefined)).toBe(false);
    expect(isValidEmail('no-at-sign')).toBe(false);
    expect(isValidEmail('@no-local.com')).toBe(false);
    expect(isValidEmail('spaces in@email.com')).toBe(false);
    expect(isValidEmail('a'.repeat(255) + '@test.com')).toBe(false);
  });
});

// ========================
// EMAIL MASKING
// ========================
describe('Email Masking', () => {
  it('masks email correctly', () => {
    expect(maskEmail('mario.rossi@campus.camp')).toMatch(/^m.*i@campus\.camp$/);
    expect(maskEmail('ab@test.com')).toBe('**@test.com');
  });

  it('handles edge cases', () => {
    expect(maskEmail(null)).toBe('***');
    expect(maskEmail('')).toBe('***');
  });
});

// ========================
// CONTACT MODEL
// ========================
describe('Contact Model', () => {
  let store;

  beforeEach(() => { store = new ContactStore(); });

  it('creates valid contact', () => {
    const contact = store.add({ email: 'test@campus.camp', first_name: 'Test' });
    expect(contact.id).toBeDefined();
    expect(contact.email).toBe('test@campus.camp');
    expect(contact.consent_status).toBe('pending');
    expect(contact.status).toBe('pending');
  });

  it('rejects invalid email', () => {
    expect(() => store.add({ email: 'invalid' })).toThrow();
  });

  it('deduplicates by email', () => {
    store.add({ email: 'dup@campus.camp' });
    expect(() => store.add({ email: 'dup@campus.camp' })).toThrow(/already exists/);
  });

  it('finds by email case-insensitive', () => {
    store.add({ email: 'Test@Campus.Camp' });
    const found = store.findByEmail('test@campus.camp');
    expect(found).not.toBeNull();
  });

  it('filters marketing eligible', () => {
    store.add({ email: 'a@t.com', consent_status: 'granted', status: 'active', segments: ['NEWSLETTER'] });
    store.add({ email: 'b@t.com', consent_status: 'pending', status: 'active' });
    store.add({ email: 'c@t.com', consent_status: 'granted', status: 'unsubscribed' });

    const eligible = store.getMarketingEligible();
    expect(eligible).toHaveLength(1);
    expect(eligible[0].email).toBe('a@t.com');
  });

  it('filters by segment', () => {
    store.add({ email: 'a@t.com', consent_status: 'granted', status: 'active', segments: ['FACULTY'] });
    store.add({ email: 'b@t.com', consent_status: 'granted', status: 'active', segments: ['STUDENTS'] });

    const faculty = store.getMarketingEligible('FACULTY');
    expect(faculty).toHaveLength(1);
    expect(faculty[0].email).toBe('a@t.com');
  });

  it('imports batch with dedup', () => {
    const results = store.importBatch([
      { email: 'a@t.com' },
      { email: 'b@t.com' },
      { email: 'a@t.com' }, // duplicate
      { email: 'invalid' }, // invalid
    ]);
    expect(results.imported).toBe(2);
    expect(results.duplicates).toBe(1);
    expect(results.invalid).toBe(1);
  });
});

// ========================
// CONSENT
// ========================
describe('Consent Manager', () => {
  let store, consent;

  beforeEach(() => {
    store = new ContactStore();
    consent = new ConsentManager(store);
  });

  it('grants consent', () => {
    const contact = store.add({ email: 'a@t.com' });
    consent.grantConsent(contact.id, 'web_form', '1.0');
    const updated = store.get(contact.id);
    expect(updated.consent_status).toBe('granted');
    expect(updated.status).toBe('active');
    expect(updated.consent_at).toBeDefined();
  });

  it('withdraws consent', () => {
    const contact = store.add({ email: 'a@t.com', consent_status: 'granted', status: 'active' });
    consent.withdrawConsent(contact.id);
    const updated = store.get(contact.id);
    expect(updated.consent_status).toBe('withdrawn');
    expect(updated.status).toBe('unsubscribed');
  });

  it('blocks marketing when denied', () => {
    const contact = store.add({ email: 'a@t.com', consent_status: 'denied', status: 'active' });
    expect(consent.canReceiveMarketing(contact.id)).toBe(false);
  });

  it('allows marketing when granted', () => {
    const contact = store.add({ email: 'a@t.com', consent_status: 'granted', status: 'active' });
    expect(consent.canReceiveMarketing(contact.id)).toBe(true);
  });

  it('unsubscribes by email', () => {
    store.add({ email: 'a@t.com', consent_status: 'granted', status: 'active' });
    consent.unsubscribe('a@t.com');
    const updated = store.findByEmail('a@t.com');
    expect(updated.status).toBe('unsubscribed');
  });

  it('creates audit log', () => {
    const contact = store.add({ email: 'a@t.com' });
    consent.grantConsent(contact.id, 'web_form');
    const log = consent.getAuditLog(contact.id);
    expect(log.length).toBeGreaterThanOrEqual(1);
    expect(log[0].action).toBe('consent.granted');
  });

  it('erases contact (GDPR)', () => {
    const contact = store.add({ email: 'a@t.com' });
    consent.eraseContact(contact.id);
    expect(store.get(contact.id)).toBeNull();
  });
});

// ========================
// SUPPRESSION
// ========================
describe('Suppression List', () => {
  let suppression;

  beforeEach(() => { suppression = new SuppressionList(); });

  it('adds and checks suppression', () => {
    suppression.addBounce('bounced@t.com');
    expect(suppression.isSuppressed('bounced@t.com')).toBe(true);
    expect(suppression.isSuppressed('ok@t.com')).toBe(false);
  });

  it('is case-insensitive', () => {
    suppression.add('Test@T.COM', 'manual');
    expect(suppression.isSuppressed('test@t.com')).toBe(true);
  });

  it('filters recipients', () => {
    suppression.addBounce('bad@t.com');
    const filtered = suppression.filterRecipients([
      { email: 'good@t.com' },
      { email: 'bad@t.com' },
      { email: 'ok@t.com' },
    ]);
    expect(filtered).toHaveLength(2);
  });
});

// ========================
// RATE LIMITING
// ========================
describe('Rate Limiter', () => {
  it('allows within limits', () => {
    const limiter = new RateLimiter({ emailsPerMinute: 5, emailsPerHour: 100, dailyCap: 500 });
    expect(limiter.canSend()).toBe(true);
  });

  it('blocks when minute limit reached', () => {
    const limiter = new RateLimiter({ emailsPerMinute: 2, emailsPerHour: 100, dailyCap: 500 });
    limiter.record();
    limiter.record();
    expect(limiter.canSend()).toBe(false);
  });

  it('returns wait time', () => {
    const limiter = new RateLimiter({ emailsPerMinute: 1, emailsPerHour: 100, dailyCap: 500 });
    limiter.record();
    expect(limiter.getWaitTimeMs()).toBeGreaterThan(0);
  });

  it('applies warmup cap', () => {
    const limiter = new RateLimiter({
      emailsPerMinute: 100,
      emailsPerHour: 1000,
      dailyCap: 500,
      warmup: { day_1_3: 5 },
      warmupStartDate: new Date().toISOString(),
    });
    for (let i = 0; i < 5; i++) limiter.record();
    expect(limiter.canSend()).toBe(false);
  });

  it('reports status', () => {
    const limiter = new RateLimiter({ emailsPerMinute: 5 });
    const status = limiter.getStatus();
    expect(status.minute.limit).toBe(5);
    expect(status.canSend).toBe(true);
  });
});

// ========================
// CAMPAIGN STATE MACHINE
// ========================
describe('Campaign Engine', () => {
  let engine;

  beforeEach(() => { engine = new CampaignEngine(); });

  it('creates campaign in DRAFT', () => {
    const c = engine.create({ name: 'Test', subject: 'Subject' });
    expect(c.status).toBe('DRAFT');
    expect(c.dry_run).toBe(true);
  });

  it('transitions DRAFT → READY → SCHEDULED → RUNNING → COMPLETED', () => {
    const c = engine.create({ name: 'Flow Test' });
    engine.markReady(c.campaign_id);
    expect(engine.get(c.campaign_id).status).toBe('READY');
    engine.schedule(c.campaign_id);
    expect(engine.get(c.campaign_id).status).toBe('SCHEDULED');
    engine.start(c.campaign_id);
    expect(engine.get(c.campaign_id).status).toBe('RUNNING');
    engine.complete(c.campaign_id);
    expect(engine.get(c.campaign_id).status).toBe('COMPLETED');
  });

  it('rejects invalid transitions', () => {
    const c = engine.create({ name: 'Invalid' });
    expect(() => engine.complete(c.campaign_id)).toThrow(); // DRAFT → COMPLETED not allowed
  });

  it('pauses and resumes', () => {
    const c = engine.create({ name: 'Pause Test' });
    engine.markReady(c.campaign_id);
    engine.schedule(c.campaign_id);
    engine.start(c.campaign_id);
    engine.pause(c.campaign_id);
    expect(engine.get(c.campaign_id).status).toBe('PAUSED');
    engine.start(c.campaign_id);
    expect(engine.get(c.campaign_id).status).toBe('RUNNING');
  });

  it('gets scheduled campaigns', () => {
    const c1 = engine.create({ name: 'C1' });
    const c2 = engine.create({ name: 'C2' });
    engine.markReady(c1.campaign_id);
    engine.schedule(c1.campaign_id, new Date().toISOString());
    expect(engine.getScheduled()).toHaveLength(1);
  });
});

// ========================
// DRY RUN MAILER
// ========================
describe('Dry Run Mailer', () => {
  it('sends without SMTP', async () => {
    const mailer = new Mailer(new DryRunProvider(), { dryRun: true });
    const result = await mailer.sendOne({
      to: 'test@campus.camp',
      subject: 'Test {{first_name}}',
      template: '<p>Ciao {{first_name}}</p>',
      data: { first_name: 'Mario' },
      type: 'transactional',
    });
    expect(result.dryRun).toBe(true);
    expect(result.to).toBe('test@campus.camp');
  });

  it('renders template variables', async () => {
    const mailer = new Mailer(new DryRunProvider(), { dryRun: true });
    const template = '<p>Ciao {{first_name}} di {{organization}}</p>';
    const html = mailer.renderTemplate(template, { first_name: 'Mario', organization: 'CAMPUS' });
    expect(html).toContain('Mario');
    expect(html).toContain('CAMPUS');
  });

  it('emits events on send', async () => {
    const mailer = new Mailer(new DryRunProvider(), { dryRun: true });
    await mailer.sendOne({
      to: 'test@campus.camp',
      subject: 'Test',
      template: '<p>Test</p>',
      type: 'transactional',
    });
    const events = mailer.getEvents();
    expect(events.length).toBeGreaterThanOrEqual(1);
    expect(events[0].type).toBe('email.sent');
  });
});

// ========================
// RETRY
// ========================
describe('Retry Handler', () => {
  it('retries on retryable error', async () => {
    const handler = new RetryHandler({ retryLimit: 2, retryDelayMs: 10 });
    let attempts = 0;
    const result = await handler.executeWithRetry(async () => {
      attempts++;
      if (attempts < 2) throw Object.assign(new Error('timeout'), { code: 'ETIMEDOUT' });
      return 'success';
    });
    expect(result).toBe('success');
    expect(attempts).toBe(2);
  });

  it('does not retry non-retryable errors', async () => {
    const handler = new RetryHandler({ retryLimit: 3, retryDelayMs: 10 });
    let attempts = 0;
    await expect(handler.executeWithRetry(async () => {
      attempts++;
      throw new Error('permanent failure');
    })).rejects.toThrow('permanent failure');
    expect(attempts).toBe(1);
  });
});

// ========================
// EVENT BUS
// ========================
describe('Event Bus', () => {
  it('emits and captures events', () => {
    const bus = new EventBus();
    bus.emit('email.sent', 'contact-1', { to: 'a@t.com' });
    expect(bus.count()).toBe(1);
    expect(bus.getEvents('email.sent')).toHaveLength(1);
  });

  it('calls listeners', () => {
    const bus = new EventBus();
    let received = null;
    bus.on('contact.created', (event) => { received = event; });
    bus.emit('contact.created', 'id-1', { email: 'a@t.com' });
    expect(received).not.toBeNull();
    expect(received.type).toBe('contact.created');
  });
});

// ========================
// METRICS
// ========================
describe('Metrics Aggregator', () => {
  it('records and reports metrics', () => {
    const metrics = new MetricsAggregator();
    metrics.record('c1', 'sent', 10);
    metrics.record('c1', 'failed', 2);
    metrics.record('c1', 'opened', 5);
    const report = metrics.getReport('c1');
    expect(report.total).toBe(12);
    expect(report.deliveryRate).toBe('83.3%');
  });
});

// ========================
// QUEUE
// ========================
describe('Send Queue', () => {
  it('enqueues and processes', async () => {
    const limiter = new RateLimiter({ emailsPerMinute: 100 });
    const queue = new SendQueue(limiter);
    queue.enqueue({ to: 'a@t.com', subject: 'Test' });
    queue.enqueue({ to: 'b@t.com', subject: 'Test' });

    const results = await queue.process(
      async (msg) => ({ messageId: 'test', to: msg.to }),
      { batchSize: 10, batchDelayMs: 0 }
    );

    expect(results).toHaveLength(2);
    expect(results.filter(r => r.status === 'sent')).toHaveLength(2);
  });
});
