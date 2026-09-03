/**
 * CAMPUS Mail Engine — Campaign Engine
 * 
 * State machine for campaigns: DRAFT → READY → SCHEDULED → RUNNING → COMPLETED
 */

import { v4 as uuidv4 } from 'uuid';

const VALID_TRANSITIONS = {
  DRAFT:     ['READY', 'CANCELLED'],
  READY:     ['SCHEDULED', 'RUNNING', 'CANCELLED'],
  SCHEDULED: ['RUNNING', 'PAUSED', 'CANCELLED'],
  RUNNING:   ['PAUSED', 'COMPLETED', 'FAILED'],
  PAUSED:    ['RUNNING', 'CANCELLED'],
  COMPLETED: [],
  FAILED:    ['DRAFT'],
  CANCELLED: ['DRAFT'],
};

export const CampaignSchema = {
  create(data) {
    return {
      campaign_id: data.campaign_id || uuidv4(),
      name: data.name || 'Untitled Campaign',
      type: data.type || 'marketing',
      subject: data.subject || '',
      template: data.template || '',
      segment: data.segment || 'GENERAL',
      filters: data.filters || {},
      status: 'DRAFT',
      scheduled_at: data.scheduled_at || null,
      batch_size: data.batch_size || 10,
      rate_limit: data.rate_limit || 5,
      dry_run: data.dry_run ?? true,
      stats: { total: 0, sent: 0, failed: 0, opened: 0, clicked: 0 },
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString(),
    };
  }
};

export class CampaignEngine {
  constructor() {
    this.campaigns = new Map();
  }

  create(data) {
    const campaign = CampaignSchema.create(data);
    this.campaigns.set(campaign.campaign_id, campaign);
    return campaign;
  }

  get(id) { return this.campaigns.get(id) || null; }
  getAll() { return Array.from(this.campaigns.values()); }

  transition(id, newStatus) {
    const campaign = this.get(id);
    if (!campaign) throw new Error(`Campaign ${id} not found`);

    const allowed = VALID_TRANSITIONS[campaign.status];
    if (!allowed || !allowed.includes(newStatus)) {
      throw new Error(`Cannot transition from ${campaign.status} to ${newStatus}`);
    }

    campaign.status = newStatus;
    campaign.updated_at = new Date().toISOString();
    return campaign;
  }

  markReady(id) { return this.transition(id, 'READY'); }
  schedule(id, scheduledAt) {
    const c = this.transition(id, 'SCHEDULED');
    c.scheduled_at = scheduledAt || new Date().toISOString();
    return c;
  }
  start(id) { return this.transition(id, 'RUNNING'); }
  pause(id) { return this.transition(id, 'PAUSED'); }
  complete(id) { return this.transition(id, 'COMPLETED'); }
  fail(id) { return this.transition(id, 'FAILED'); }
  cancel(id) { return this.transition(id, 'CANCELLED'); }

  getScheduled() {
    return this.getAll().filter(c => c.status === 'SCHEDULED');
  }
  getReady() {
    return this.getAll().filter(c => c.status === 'READY' || c.status === 'SCHEDULED');
  }

  updateStats(id, stats) {
    const campaign = this.get(id);
    if (!campaign) return;
    Object.assign(campaign.stats, stats);
    campaign.updated_at = new Date().toISOString();
  }
}
