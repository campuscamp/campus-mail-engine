/**
 * CAMPUS Mail Engine — Event Bus (Digital Twin Events)
 */
import { v4 as uuidv4 } from 'uuid';

export class EventBus {
  constructor() { this.events = []; this.listeners = new Map(); }

  emit(type, entityId, metadata = {}) {
    const event = {
      event_id: uuidv4(),
      type,
      entity_id: entityId,
      timestamp: new Date().toISOString(),
      source: 'campus-mail-engine',
      metadata,
    };
    this.events.push(event);
    const handlers = this.listeners.get(type) || [];
    handlers.forEach(fn => fn(event));
    return event;
  }

  on(type, handler) {
    if (!this.listeners.has(type)) this.listeners.set(type, []);
    this.listeners.get(type).push(handler);
  }

  getEvents(type = null) {
    if (type) return this.events.filter(e => e.type === type);
    return [...this.events];
  }

  count() { return this.events.length; }
}

// Predefined CAMPUS event types
export const EVENT_TYPES = {
  CONTACT_CREATED: 'contact.created',
  CONTACT_CONSENT_GRANTED: 'contact.consent_granted',
  CONTACT_UNSUBSCRIBED: 'contact.unsubscribed',
  CAMPAIGN_CREATED: 'campaign.created',
  CAMPAIGN_SCHEDULED: 'campaign.scheduled',
  EMAIL_QUEUED: 'email.queued',
  EMAIL_SENT: 'email.sent',
  EMAIL_FAILED: 'email.failed',
  EMAIL_BOUNCED: 'email.bounced',
  EMAIL_CLICKED: 'email.clicked',
  EMAIL_OPENED: 'email.opened',
};
