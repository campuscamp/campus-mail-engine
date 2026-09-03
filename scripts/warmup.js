#!/usr/bin/env node
/**
 * CAMPUS Mail Engine — Warm-up Runner
 */
import 'dotenv/config';

const schedule = {
  day_1_3: parseInt(process.env.WARMUP_DAY_1_3 || '20'),
  day_4_7: parseInt(process.env.WARMUP_DAY_4_7 || '40'),
  day_8_14: parseInt(process.env.WARMUP_DAY_8_14 || '75'),
  day_15_plus: parseInt(process.env.WARMUP_DAY_15_PLUS || '150'),
};

console.log('=== CAMPUS Warm-up Schedule ===\n');
console.log('Day  1-3:  ', schedule.day_1_3, 'emails/day');
console.log('Day  4-7:  ', schedule.day_4_7, 'emails/day');
console.log('Day  8-14: ', schedule.day_8_14, 'emails/day');
console.log('Day 15+:   ', schedule.day_15_plus, 'emails/day');
console.log('\n⚠️  Verify actual Hostinger limits in hPanel before production use.');
