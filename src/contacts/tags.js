/**
 * CAMPUS Mail Engine — Tags Manager
 */
export class TagManager {
  constructor() { this.tags = new Map(); }

  addTag(contactId, tag) {
    if (!this.tags.has(contactId)) this.tags.set(contactId, new Set());
    this.tags.get(contactId).add(tag);
  }
  removeTag(contactId, tag) {
    this.tags.get(contactId)?.delete(tag);
  }
  getTags(contactId) { return Array.from(this.tags.get(contactId) || []); }
  getContactsByTag(contacts, tag) { return contacts.filter(c => c.tags?.includes(tag)); }
}
