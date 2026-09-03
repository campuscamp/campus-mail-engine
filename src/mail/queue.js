/**
 * CAMPUS Mail Engine — Send Queue
 * 
 * In-memory queue with batch processing and rate limiting.
 * Designed for adapter-based persistence in production.
 */

import { v4 as uuidv4 } from 'uuid';

export class SendQueue {
  constructor(rateLimiter) {
    this.queue = [];
    this.processing = false;
    this.rateLimiter = rateLimiter;
    this.results = [];
  }

  enqueue(message) {
    const item = {
      id: uuidv4(),
      message,
      status: 'queued',
      attempts: 0,
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString(),
    };
    this.queue.push(item);
    return item.id;
  }

  enqueueBatch(messages) {
    return messages.map(msg => this.enqueue(msg));
  }

  async process(sendFn, options = {}) {
    if (this.processing) {
      throw new Error('Queue is already processing');
    }

    this.processing = true;
    const batchSize = options.batchSize || 10;
    const batchDelay = options.batchDelayMs || 5000;
    const results = [];

    try {
      while (this.queue.length > 0) {
        // Get next batch
        const batch = this.queue.splice(0, batchSize);

        for (const item of batch) {
          // Check rate limit
          if (!this.rateLimiter.canSend()) {
            const wait = this.rateLimiter.getWaitTimeMs();
            console.log(`[QUEUE] Rate limit reached. Waiting ${Math.round(wait / 1000)}s...`);
            // Put remaining back in queue
            this.queue.unshift(...batch.slice(batch.indexOf(item)));
            await new Promise(r => setTimeout(r, Math.min(wait, 60_000)));
            break;
          }

          try {
            item.status = 'sending';
            item.attempts++;
            item.updatedAt = new Date().toISOString();

            const result = await sendFn(item.message);

            item.status = 'sent';
            item.result = result;
            item.sentAt = new Date().toISOString();
            this.rateLimiter.record();
          } catch (error) {
            item.status = 'failed';
            item.error = error.message;
            item.updatedAt = new Date().toISOString();
          }

          results.push({ ...item });
        }

        // Batch delay
        if (this.queue.length > 0) {
          await new Promise(r => setTimeout(r, batchDelay));
        }
      }
    } finally {
      this.processing = false;
    }

    this.results = results;
    return results;
  }

  getStatus() {
    return {
      queued: this.queue.length,
      processing: this.processing,
      completed: this.results.length,
      sent: this.results.filter(r => r.status === 'sent').length,
      failed: this.results.filter(r => r.status === 'failed').length,
    };
  }

  clear() {
    this.queue = [];
    this.results = [];
  }
}
