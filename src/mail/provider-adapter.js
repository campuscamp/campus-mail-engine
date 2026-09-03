/**
 * CAMPUS Mail Engine — Provider Adapter Interface
 * 
 * Abstract interface for mail providers.
 * Currently implements SMTP (Nodemailer/Hostinger).
 * Designed for future migration to ESP APIs (SendGrid, Resend, Postmark, etc.)
 * without changing the rest of the architecture.
 */

export class MailProviderAdapter {
  constructor(config) {
    this.config = config;
    this.name = 'base';
  }

  async send(message) {
    throw new Error('send() must be implemented by provider');
  }

  async verify() {
    throw new Error('verify() must be implemented by provider');
  }

  async close() {
    // Optional cleanup
  }

  getInfo() {
    return {
      provider: this.name,
      host: this.config?.host || 'unknown',
      port: this.config?.port || 0,
    };
  }
}

/**
 * Dry Run Provider — logs instead of sending
 */
export class DryRunProvider extends MailProviderAdapter {
  constructor() {
    super({});
    this.name = 'dry-run';
    this.sentMessages = [];
  }

  async send(message) {
    const result = {
      messageId: `dry-run-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`,
      to: message.to,
      subject: message.subject,
      timestamp: new Date().toISOString(),
      dryRun: true,
    };
    this.sentMessages.push(result);
    return result;
  }

  async verify() {
    return { success: true, provider: 'dry-run', message: 'Dry run mode — no SMTP connection needed' };
  }

  getSentMessages() {
    return [...this.sentMessages];
  }

  reset() {
    this.sentMessages = [];
  }
}
