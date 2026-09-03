/**
 * CAMPUS Mail Engine — Retry Logic
 * 
 * Exponential backoff retry for failed sends.
 */

export class RetryHandler {
  constructor(config = {}) {
    this.maxRetries = config.retryLimit ?? 3;
    this.baseDelayMs = config.retryDelayMs ?? 60_000;
    this.maxDelayMs = config.maxDelayMs ?? 600_000;
    this.retryableErrors = new Set([
      'ECONNREFUSED', 'ETIMEDOUT', 'ECONNRESET', 'ESOCKET',
      'EENVELOPE', 'EMESSAGE', 'EAUTH',
    ]);
  }

  isRetryable(error) {
    if (!error) return false;
    // Check error code
    if (error.code && this.retryableErrors.has(error.code)) return true;
    // Check for temporary SMTP errors (4xx)
    if (error.responseCode && error.responseCode >= 400 && error.responseCode < 500) return true;
    // Network errors
    if (error.message?.includes('ECONNREFUSED')) return true;
    if (error.message?.includes('ETIMEDOUT')) return true;
    if (error.message?.includes('rate limit')) return true;
    return false;
  }

  getDelayMs(attempt) {
    // Exponential backoff with jitter
    const delay = Math.min(
      this.baseDelayMs * Math.pow(2, attempt),
      this.maxDelayMs
    );
    // Add 10% jitter
    const jitter = delay * 0.1 * Math.random();
    return Math.floor(delay + jitter);
  }

  shouldRetry(attempt, error) {
    return attempt < this.maxRetries && this.isRetryable(error);
  }

  async executeWithRetry(fn, context = {}) {
    let lastError;
    for (let attempt = 0; attempt <= this.maxRetries; attempt++) {
      try {
        return await fn();
      } catch (error) {
        lastError = error;
        if (!this.shouldRetry(attempt, error)) {
          throw error;
        }
        const delay = this.getDelayMs(attempt);
        console.warn(
          `[RETRY] Attempt ${attempt + 1}/${this.maxRetries} failed for ${context.to || 'unknown'}. ` +
          `Retrying in ${Math.round(delay / 1000)}s. Error: ${error.message}`
        );
        await new Promise(resolve => setTimeout(resolve, delay));
      }
    }
    throw lastError;
  }
}
