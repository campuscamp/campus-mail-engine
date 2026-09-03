/**
 * CAMPUS Mail Engine — Contact Model
 * 
 * Contact schema with consent tracking, GDPR compliance,
 * segmentation, tagging and suppression.
 */

import { v4 as uuidv4 } from 'uuid';
import { z } from 'zod';

// --- Zod Schema ---

export const ContactSchema = z.object({
  id: z.string().uuid().default(() => uuidv4()),
  email: z.string().email().max(254),
  first_name: z.string().max(100).optional().default(''),
  last_name: z.string().max(100).optional().default(''),
  organization: z.string().max(200).optional().default(''),
  role: z.string().max(100).optional().default(''),
  segments: z.array(z.string()).default([]),
  tags: z.array(z.string()).default([]),
  source: z.string().max(100).default('manual'),
  consent_status: z.enum(['pending', 'granted', 'denied', 'withdrawn']).default('pending'),
  consent_source: z.string().max(200).optional().default(''),
  consent_at: z.string().datetime().optional().nullable().default(null),
  consent_version: z.string().max(50).optional().default('1.0'),
  unsubscribe_at: z.string().datetime().optional().nullable().default(null),
  status: z.enum(['active', 'unsubscribed', 'bounced', 'suppressed', 'invalid', 'pending']).default('pending'),
  score: z.number().int().min(0).default(0),
  created_at: z.string().datetime().default(() => new Date().toISOString()),
  updated_at: z.string().datetime().default(() => new Date().toISOString()),
});

// --- Contact Store (In-memory / JSON adapter) ---

export class ContactStore {
  constructor() {
    this.contacts = new Map();
  }

  add(data) {
    const contact = ContactSchema.parse(data);

    // Deduplication by email
    if (this.findByEmail(contact.email)) {
      throw new Error(`Contact with email ${contact.email} already exists`);
    }

    this.contacts.set(contact.id, contact);
    return contact;
  }

  update(id, data) {
    const existing = this.contacts.get(id);
    if (!existing) throw new Error(`Contact ${id} not found`);

    const updated = ContactSchema.parse({
      ...existing,
      ...data,
      id: existing.id,
      created_at: existing.created_at,
      updated_at: new Date().toISOString(),
    });

    this.contacts.set(id, updated);
    return updated;
  }

  get(id) {
    return this.contacts.get(id) || null;
  }

  findByEmail(email) {
    for (const contact of this.contacts.values()) {
      if (contact.email.toLowerCase() === email.toLowerCase()) {
        return contact;
      }
    }
    return null;
  }

  getAll() {
    return Array.from(this.contacts.values());
  }

  /**
   * Get contacts eligible for marketing emails
   */
  getMarketingEligible(segment = null) {
    return this.getAll().filter(c => {
      if (c.consent_status !== 'granted') return false;
      if (c.status !== 'active') return false;
      if (segment && !c.segments.includes(segment)) return false;
      return true;
    });
  }

  /**
   * Get contacts by segment
   */
  getBySegment(segment) {
    return this.getAll().filter(c => c.segments.includes(segment));
  }

  /**
   * Get contacts by tag
   */
  getByTag(tag) {
    return this.getAll().filter(c => c.tags.includes(tag));
  }

  delete(id) {
    return this.contacts.delete(id);
  }

  count() {
    return this.contacts.size;
  }

  /**
   * Import contacts from array with dedup
   */
  importBatch(contactsArray) {
    const results = { imported: 0, duplicates: 0, invalid: 0, errors: [] };

    for (const data of contactsArray) {
      try {
        this.add(data);
        results.imported++;
      } catch (error) {
        if (error.message.includes('already exists')) {
          results.duplicates++;
        } else {
          results.invalid++;
          results.errors.push({ email: data.email, error: error.message });
        }
      }
    }

    return results;
  }

  /**
   * Export to plain array
   */
  toJSON() {
    return this.getAll();
  }

  /**
   * Load from array
   */
  fromJSON(data) {
    this.contacts.clear();
    for (const item of data) {
      const contact = ContactSchema.parse(item);
      this.contacts.set(contact.id, contact);
    }
    return this.count();
  }
}
