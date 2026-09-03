/**
 * CAMPUS Mail Engine — Personalization & Scheduler
 */

export class Personalization {
  static merge(template, contact) {
    const data = {
      first_name: contact.first_name || '',
      last_name: contact.last_name || '',
      organization: contact.organization || '',
      email: contact.email || '',
      role: contact.role || '',
      ...contact,
    };
    return data;
  }
}

export class Scheduler {
  constructor(campaignEngine) {
    this.campaignEngine = campaignEngine;
  }

  getDueCampaigns() {
    const now = new Date();
    return this.campaignEngine.getScheduled().filter(c => {
      if (!c.scheduled_at) return false;
      return new Date(c.scheduled_at) <= now;
    });
  }
}
