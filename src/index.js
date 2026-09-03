/**
 * CAMPUS Mail Engine — Main Entry Point
 */
export { Mailer, isValidEmail, maskEmail } from './mail/mailer.js';
export { SmtpClient, createSmtpClientFromEnv } from './mail/smtp-client.js';
export { MailProviderAdapter, DryRunProvider } from './mail/provider-adapter.js';
export { RateLimiter } from './mail/rate-limit.js';
export { RetryHandler } from './mail/retry.js';
export { SendQueue } from './mail/queue.js';
export { ContactStore, ContactSchema } from './contacts/contact-model.js';
export { ConsentManager } from './contacts/consent.js';
export { SuppressionList } from './contacts/suppression.js';
export { SegmentManager } from './contacts/segments.js';
export { TagManager } from './contacts/tags.js';
export { CampaignEngine, CampaignSchema } from './campaigns/campaign-engine.js';
export { SequenceEngine, AutomationEngine } from './campaigns/sequence-engine.js';
export { Personalization, Scheduler } from './campaigns/personalization.js';
export { EventBus, EVENT_TYPES } from './tracking/events.js';
export { ClickTracker, MetricsAggregator } from './tracking/clicks.js';
export { healthCheck } from './api/health.js';
