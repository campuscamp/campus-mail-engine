/**
 * CAMPUS Mail Engine — SMTP Client (Nodemailer + Hostinger)
 */
import nodemailer from 'nodemailer';
import { MailProviderAdapter } from './provider-adapter.js';

export class SmtpClient extends MailProviderAdapter {
  constructor(config) {
    super(config);
    this.name = 'smtp';
    this.transporter = null;
  }

  _createTransporter() {
    const opts = {
      host: this.config.host,
      port: this.config.port,
      secure: this.config.secure, // true for 465, false for 587
      auth: {
        user: this.config.auth.user,
        pass: this.config.auth.pass,
      },
      pool: this.config.pool ?? true,
      maxConnections: this.config.maxConnections ?? 3,
      maxMessages: this.config.maxMessages ?? 50,
      logger: false,
      debug: false,
    };

    // STARTTLS for port 587
    if (!this.config.secure && this.config.starttls) {
      opts.requireTLS = true;
    }

    this.transporter = nodemailer.createTransport(opts);
    return this.transporter;
  }

  getTransporter() {
    if (!this.transporter) {
      this._createTransporter();
    }
    return this.transporter;
  }

  async send(message) {
    const transporter = this.getTransporter();
    const mailOptions = {
      from: message.from || this.config.defaultFrom,
      to: message.to,
      subject: message.subject,
      html: message.html,
      text: message.text,
      replyTo: message.replyTo || this.config.replyTo,
      headers: message.headers || {},
    };

    // Add List-Unsubscribe header for marketing emails
    if (message.unsubscribeUrl) {
      mailOptions.headers['List-Unsubscribe'] = `<${message.unsubscribeUrl}>`;
      mailOptions.headers['List-Unsubscribe-Post'] = 'List-Unsubscribe=One-Click';
    }

    const result = await transporter.sendMail(mailOptions);
    return {
      messageId: result.messageId,
      to: message.to,
      subject: message.subject,
      accepted: result.accepted,
      rejected: result.rejected,
      timestamp: new Date().toISOString(),
    };
  }

  async verify() {
    try {
      const transporter = this.getTransporter();
      await transporter.verify();
      return { success: true, provider: 'smtp', host: this.config.host, port: this.config.port };
    } catch (error) {
      return { success: false, provider: 'smtp', error: error.message };
    }
  }

  async close() {
    if (this.transporter) {
      this.transporter.close();
      this.transporter = null;
    }
  }
}

/**
 * Create SMTP client from environment variables
 */
export function createSmtpClientFromEnv() {
  return new SmtpClient({
    host: process.env.SMTP_HOST || 'smtp.hostinger.com',
    port: parseInt(process.env.SMTP_PORT || '465'),
    secure: process.env.SMTP_SECURE !== 'false',
    starttls: process.env.SMTP_STARTTLS === 'true',
    auth: {
      user: process.env.SMTP_USER || '',
      pass: process.env.SMTP_PASS || '',
    },
    defaultFrom: `${process.env.MAIL_FROM_NAME || 'CAMPUS'} <${process.env.MAIL_FROM || 'info@campus.camp'}>`,
    replyTo: process.env.MAIL_FROM || 'info@campus.camp',
    pool: true,
    maxConnections: 3,
    maxMessages: 50,
  });
}
