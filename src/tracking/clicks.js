/**
 * CAMPUS Mail Engine — Click Tracking & Metrics
 */
export class ClickTracker {
  constructor() { this.clicks = []; }
  track(campaignId, contactId, url) {
    this.clicks.push({ campaignId, contactId, url, timestamp: new Date().toISOString() });
  }
  getByCampaign(campaignId) { return this.clicks.filter(c => c.campaignId === campaignId); }
}

export class MetricsAggregator {
  constructor() { this.metrics = new Map(); }

  record(campaignId, metric, value = 1) {
    if (!this.metrics.has(campaignId)) {
      this.metrics.set(campaignId, { sent: 0, failed: 0, opened: 0, clicked: 0, bounced: 0, unsubscribed: 0 });
    }
    const m = this.metrics.get(campaignId);
    m[metric] = (m[metric] || 0) + value;
  }

  get(campaignId) { return this.metrics.get(campaignId) || null; }

  getReport(campaignId) {
    const m = this.get(campaignId);
    if (!m) return null;
    const total = m.sent + m.failed;
    return {
      ...m,
      total,
      deliveryRate: total > 0 ? ((m.sent / total) * 100).toFixed(1) + '%' : '0%',
      openRate: m.sent > 0 ? ((m.opened / m.sent) * 100).toFixed(1) + '%' : '0%',
      clickRate: m.sent > 0 ? ((m.clicked / m.sent) * 100).toFixed(1) + '%' : '0%',
    };
  }
}
