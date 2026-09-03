/**
 * Export census contacts to JSON and CSV formats
 */
import { writeFileSync } from 'fs';
import { CENSUS_CONTACTS } from '../src/contacts/census-database.js';

// 1. Export JSON
writeFileSync(
  'data/census_contacts_master.json',
  JSON.stringify(CENSUS_CONTACTS, null, 2),
  'utf-8'
);

// 2. Export CSV
const headers = [
  'id',
  'type',
  'category',
  'organization',
  'target_role',
  'email',
  'pec',
  'website',
  'level',
  'region',
  'province',
  'priority',
  'docenti_target'
];

const csvRows = [
  headers.join(','),
  ...CENSUS_CONTACTS.map(c => 
    headers.map(h => `"${String(c[h] || '').replace(/"/g, '""')}"`).join(',')
  )
];

writeFileSync(
  'data/census_contacts_master.csv',
  csvRows.join('\n'),
  'utf-8'
);

console.log(`✅ Esportati con successo ${CENSUS_CONTACTS.length} contatti istituzionali in data/census_contacts_master.json e data/census_contacts_master.csv`);
