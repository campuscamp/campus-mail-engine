/**
 * CAMPUS Mail Engine — Rate Limiter
 * 
 * Configurable rate limiting per minute, hour, and day.
 * Warm-up support for new sender reputation.
 */

export class RateLimiter {
  constructor(config = {}) {
    this.emailsPerMinute = config.emailsPerMinute ?? 5;
    this.emailsPerHour = config.emailsPerHour ?? 100;
    this.dailyCap = config.dailyCap ?? 500;
    this.batchSize = config.batchSize ?? 10;
    this.batchDelayMs = config.batchDelayMs ?? 5000;

    // Counters
    this.minuteCount = 0;
    this.hourCount = 0;
    this.dayCount = 0;

    // Timestamps
    this.minuteStart = Date.now();
    this.hourStart = Date.now();
    this.dayStart = Date.now();

    // Warm-up
    this.warmup = config.warmup || null;
    this.warmupStartDate = config.warmupStartDate ? new Date(config.warmupStartDate) : null;
  }

  _resetIfNeeded() {
    const now = Date.now();
    if (now - this.minuteStart >= 60_000) {
      this.minuteCount = 0;
      this.minuteStart = now;
    }
    if (now - this.hourStart >= 3_600_000) {
      this.hourCount = 0;
      this.hourStart = now;
    }
    if (now - this.dayStart >= 86_400_000) {
      this.dayCount = 0;
      this.dayStart = now;
    }
  }

  canSend() {
    this._resetIfNeeded();
    const effectiveDailyCap = this._getEffectiveDailyCap();
    return (
      this.minuteCount < this.emailsPerMinute &&
      this.hourCount < this.emailsPerHour &&
      this.dayCount < effectiveDailyCap
    );
  }

  record() {
    this.minuteCount++;
    this.hourCount++;
    this.dayCount++;
  }

  getWaitTimeMs() {
    this._resetIfNeeded();
    if (this.minuteCount >= this.emailsPerMinute) {
      return 60_000 - (Date.now() - this.minuteStart);
    }
    if (this.hourCount >= this.emailsPerHour) {
      return 3_600_000 - (Date.now() - this.hourStart);
    }
    const effectiveDailyCap = this._getEffectiveDailyCap();
    if (this.dayCount >= effectiveDailyCap) {
      return 86_400_000 - (Date.now() - this.dayStart);
    }
    return 0;
  }

  _getEffectiveDailyCap() {
    if (!this.warmup || !this.warmupStartDate) {
      return this.dailyCap;
    }
    const daysSinceStart = Math.floor((Date.now() - this.warmupStartDate.getTime()) / 86_400_000);
    if (daysSinceStart < 3) return this.warmup.day_1_3 ?? 20;
    if (daysSinceStart < 7) return this.warmup.day_4_7 ?? 40;
    if (daysSinceStart < 14) return this.warmup.day_8_14 ?? 75;
    return this.warmup.day_15_plus ?? 150;
  }

  getStatus() {
    this._resetIfNeeded();
    return {
      minute: { sent: this.minuteCount, limit: this.emailsPerMinute },
      hour: { sent: this.hourCount, limit: this.emailsPerHour },
      day: { sent: this.dayCount, limit: this._getEffectiveDailyCap(), baseCap: this.dailyCap },
      canSend: this.canSend(),
      warmupActive: !!(this.warmup && this.warmupStartDate),
    };
  }

  reset() {
    this.minuteCount = 0;
    this.hourCount = 0;
    this.dayCount = 0;
    this.minuteStart = Date.now();
    this.hourStart = Date.now();
    this.dayStart = Date.now();
  }
}
