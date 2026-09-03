/**
 * CAMPUS Mail Engine — Consent Manager
 * 
 * GDPR-compliant consent tracking with audit log,
 * double opt-in support, and right to erasure.
 */

import { v4 as uuidv4 } from 'uuid';

export class ConsentManager {
  constructor(contactStore) {
    this.contactStore = contactStore;
    this.auditLog = [];
  }

  /**
   * Grant marketing consent
   */
  grantConsent(contactId, source, version = '1.0') {
    const contact = this.contactStore.get(contactId);
    if (!contact) throw new Error(`Contact ${contactId} not found`);

    const now = new Date().toISOString();
    this.contactStore.update(contactId, {
      consent_status: 'granted',
      consent_source: source,
      consent_at: now,
      consent_version: version,
      status: 'active',
    });

    this._log('consent.granted', contactId, { source, version });
    return true;
  }

  /**
   * Deny / withdraw consent
   */
  withdrawConsent(contactId, reason = 'user_request') {
    const contact = this.contactStore.get(contactId);
    if (!contact) throw new Error(`Contact ${contactId} not found`);

    this.contactStore.update(contactId, {
      consent_status: 'withdrawn',
      status: 'unsubscribed',
      unsubscribe_at: new Date().toISOString(),
    });

    this._log('consent.withdrawn', contactId, { reason });
    return true;
  }

  /**
   * Unsubscribe (keeps contact but stops marketing)
   */
  unsubscribe(email, reason = 'user_unsubscribe') {
    const contact = this.contactStore.findByEmail(email);
    if (!contact) return false;

    this.contactStore.update(contact.id, {
      status: 'unsubscribed',
      unsubscribe_at: new Date().toISOString(),
    });

    this._log('contact.unsubscribed', contact.id, { reason });
    return true;
  }

  /**
   * Check if contact can receive marketing emails
   */
  canReceiveMarketing(contactId) {
    const contact = this.contactStore.get(contactId);
    if (!contact) return false;
    return contact.consent_status === 'granted' && contact.status === 'active';
  }

  /**
   * Check consent by email
   */
  canReceiveMarketingByEmail(email) {
    const contact = this.contactStore.findByEmail(email);
    if (!contact) return false;
    return this.canReceiveMarketing(contact.id);
  }

  /**
   * Double opt-in: create pending token
   */
  createDoubleOptInToken(contactId) {
    const token = uuidv4();
    this._log('consent.double_optin_requested', contactId, { token });
    return token;
  }

  /**
   * Double opt-in: confirm
   */
  confirmDoubleOptIn(contactId, token, source = 'double_opt_in_email') {
    this.grantConsent(contactId, source);
    this._log('consent.double_optin_confirmed', contactId, { token });
    return true;
  }

  /**
   * Right to erasure (GDPR Art. 17)
   */
  eraseContact(contactId, reason = 'gdpr_erasure_request') {
    const contact = this.contactStore.get(contactId);
    if (!contact) return false;

    this._log('contact.erased', contactId, {
      reason,
      email_masked: contact.email.replace(/(.{2}).*(@.*)/, '$1***$2'),
    });

    this.contactStore.delete(contactId);
    return true;
  }

  _log(action, contactId, data = {}) {
    this.auditLog.push({
      id: uuidv4(),
      action,
      contact_id: contactId,
      timestamp: new Date().toISOString(),
      data,
    });
  }

  getAuditLog(contactId = null) {
    if (contactId) {
      return this.auditLog.filter(l => l.contact_id === contactId);
    }
    return [...this.auditLog];
  }
}
