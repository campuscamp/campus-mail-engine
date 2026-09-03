/**
 * CAMPUS Mail Engine — Suppression List
 * 
 * Global suppression for bounced, unsubscribed, and complained addresses.
 */

export class SuppressionList {
  constructor() {
    this.suppressed = new Map(); // email -> { reason, timestamp, source }
  }

  add(email, reason, source = 'system') {
    const key = email.toLowerCase().trim();
    this.suppressed.set(key, {
      email: key,
      reason,
      source,
      timestamp: new Date().toISOString(),
    });
  }

  remove(email) {
    return this.suppressed.delete(email.toLowerCase().trim());
  }

  isSuppressed(email) {
    return this.suppressed.has(email.toLowerCase().trim());
  }

  get(email) {
    return this.suppressed.get(email.toLowerCase().trim()) || null;
  }

  getAll() {
    return Array.from(this.suppressed.values());
  }

  addBounce(email) {
    this.add(email, 'hard_bounce', 'bounce_handler');
  }

  addComplaint(email) {
    this.add(email, 'complaint', 'complaint_handler');
  }

  addUnsubscribe(email) {
    this.add(email, 'unsubscribe', 'unsubscribe_handler');
  }

  count() {
    return this.suppressed.size;
  }

  /**
   * Filter out suppressed from recipient list
   */
  filterRecipients(recipients) {
    return recipients.filter(r => !this.isSuppressed(r.email));
  }

  toJSON() {
    return this.getAll();
  }

  fromJSON(data) {
    this.suppressed.clear();
    for (const item of data) {
      this.suppressed.set(item.email.toLowerCase().trim(), item);
    }
  }
}
