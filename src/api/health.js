/**
 * CAMPUS Mail Engine — Health Check
 */
import 'dotenv/config';
import { createSmtpClientFromEnv } from '../mail/smtp-client.js';

export async function healthCheck() {
  const checks = { timestamp: new Date().toISOString(), status: 'ok', checks: {} };

  // Environment
  checks.checks.env = {
    SMTP_HOST: !!process.env.SMTP_HOST,
    SMTP_USER: !!process.env.SMTP_USER,
    SMTP_PASS: !!process.env.SMTP_PASS,
    MAIL_FROM: !!process.env.MAIL_FROM,
  };

  // SMTP
  if (process.env.SMTP_PASS && process.env.SMTP_PASS !== 'YOUR_SMTP_PASSWORD_HERE') {
    try {
      const smtp = createSmtpClientFromEnv();
      const result = await smtp.verify();
      checks.checks.smtp = result;
      await smtp.close();
    } catch (e) {
      checks.checks.smtp = { success: false, error: e.message };
      checks.status = 'degraded';
    }
  } else {
    checks.checks.smtp = { success: false, error: 'SMTP_PASS not configured' };
    checks.status = 'not_configured';
  }

  return checks;
}

// Run if called directly
const isMain = process.argv[1]?.endsWith('health.js');
if (isMain) {
  healthCheck().then(r => {
    console.log(JSON.stringify(r, null, 2));
    process.exit(r.status === 'ok' ? 0 : 1);
  });
}
