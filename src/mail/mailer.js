/**
 * CAMPUS Mail Engine — Mailer (Orchestrator)
 * 
 * Central orchestrator that ties together provider, templates,
 * personalization, queue, rate limiting and compliance.
 */

import Handlebars from 'handlebars';
import { v4 as uuidv4 } from 'uuid';
import { RateLimiter } from './rate-limit.js';
import { RetryHandler } from './retry.js';
import { SendQueue } from './queue.js';
import { DryRunProvider } from './provider-adapter.js';

export class Mailer {
  constructor(provider, config = {}) {
    this.provider = config.dryRun ? new DryRunProvider() : provider;
    this.dryRun = config.dryRun ?? (process.env.DRY_RUN === 'true');
    this.rateLimiter = new RateLimiter(config.rateLimiting || {});
    this.retryHandler = new RetryHandler(config);
    this.queue = new SendQueue(this.rateLimiter);
    this.events = [];

    if (this.dryRun && !(this.provider instanceof DryRunProvider)) {
      this.provider = new DryRunProvider();
    }
  }

  /**
   * Render template with Handlebars
   */
  renderTemplate(templateHtml, data) {
    const compiled = Handlebars.compile(templateHtml);
    return compiled(data);
  }

  /**
   * Build footer for marketing emails
   */
  _buildFooter(data) {
    return `
      <div style="margin-top:32px;padding-top:16px;border-top:1px solid #e5e7eb;font-size:12px;color:#6b7280;text-align:center;">
        <p><strong>CAMPUS</strong> · <a href="${data.campus_url || 'https://campus.camp'}" style="color:#6b7280;">campus.camp</a></p>
        <p>Ricevi questa email perché ${data.reason || 'sei iscritto alla nostra comunicazione'}.</p>
        <p>
          <a href="${data.unsubscribe_url || '#'}" style="color:#6b7280;">Cancella iscrizione</a> ·
          <a href="${data.privacy_url || 'https://campus.camp/privacy'}" style="color:#6b7280;">Privacy</a>
        </p>
      </div>
    `;
  }

  /**
   * Send a single email
   */
  async sendOne(options) {
    const {
      to, subject, template, data = {}, type = 'marketing',
      campaignId = null, contactId = null,
    } = options;

    // Validate email
    if (!to || !isValidEmail(to)) {
      throw new Error(`Invalid email: ${to}`);
    }

    // Merge template data
    const mergeData = {
      campus_url: process.env.CAMPUS_URL || 'https://campus.camp',
      unsubscribe_url: `${process.env.UNSUBSCRIBE_BASE_URL || 'https://campus.camp/unsubscribe'}?email=${encodeURIComponent(to)}&cid=${campaignId || ''}`,
      privacy_url: process.env.PRIVACY_URL || 'https://campus.camp/privacy',
      ...data,
    };

    // Render HTML
    let html = this.renderTemplate(template, mergeData);

    // Append footer for marketing emails
    if (type === 'marketing') {
      html += this._buildFooter(mergeData);
    }

    const message = {
      to,
      subject: this.renderTemplate(subject, mergeData),
      html,
      text: html.replace(/<[^>]*>/g, ''), // Strip HTML for text version
      from: `${process.env.MAIL_FROM_NAME || 'CAMPUS'} <${process.env.MAIL_FROM || 'info@campus.camp'}>`,
      unsubscribeUrl: type === 'marketing' ? mergeData.unsubscribe_url : null,
      headers: {
        'X-Campaign-ID': campaignId || 'none',
        'X-Contact-ID': contactId || 'none',
        'X-Mailer': 'CAMPUS-Mail-Engine/1.0',
      },
    };

    // Send with retry
    const result = await this.retryHandler.executeWithRetry(
      () => this.provider.send(message),
      { to }
    );

    // Emit event
    this._emitEvent('email.sent', {
      messageId: result.messageId,
      to,
      subject,
      campaignId,
      contactId,
      dryRun: this.dryRun,
    });

    return result;
  }

  /**
   * Send batch of emails
   */
  async sendBatch(recipients, options) {
    const { subject, template, type = 'marketing', campaignId = null } = options;
    const results = { sent: 0, failed: 0, skipped: 0, errors: [] };

    for (const recipient of recipients) {
      // Rate limiting
      if (!this.rateLimiter.canSend()) {
        const wait = this.rateLimiter.getWaitTimeMs();
        console.log(`[MAILER] Rate limit hit. Waiting ${Math.round(wait / 1000)}s...`);
        await new Promise(r => setTimeout(r, Math.min(wait, 60_000)));
      }

      try {
        await this.sendOne({
          to: recipient.email,
          subject,
          template,
          data: recipient,
          type,
          campaignId,
          contactId: recipient.id,
        });
        results.sent++;
        this.rateLimiter.record();
      } catch (error) {
        results.failed++;
        results.errors.push({ email: maskEmail(recipient.email), error: error.message });
      }
    }

    return results;
  }

  _emitEvent(type, data) {
    const event = {
      event_id: uuidv4(),
      type,
      entity_id: data.contactId || data.campaignId || null,
      timestamp: new Date().toISOString(),
      source: 'campus-mail-engine',
      metadata: data,
    };
    this.events.push(event);
    return event;
  }

  getEvents() {
    return [...this.events];
  }

  async verify() {
    return this.provider.verify();
  }

  async close() {
    return this.provider.close();
  }
}

// --- Utilities ---

export function isValidEmail(email) {
  if (!email || typeof email !== 'string') return false;
  const re = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
  return re.test(email) && email.length <= 254;
}

export function maskEmail(email) {
  if (!email) return '***';
  const [local, domain] = email.split('@');
  if (!domain) return '***';
  const masked = local.length <= 2 ? '*'.repeat(local.length) : local[0] + '*'.repeat(local.length - 2) + local[local.length - 1];
  return `${masked}@${domain}`;
}
