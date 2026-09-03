/**
 * CAMPUS.CAMP — Importazione Catalogo Completo (2.117+ Corsi) in SQLite
 */
import fs from 'fs';
import path from 'path';
import sqlite3 from 'sqlite3';

const dbPath = 'c:\\81PLUS_GLOBAL_MASTER\\campus.camp\\public_html\\storage\\database\\campus.sqlite';
const catalogSource = 'C:\\81PLUS_GLOBAL_MASTER\\81plus.net\\LISTINO81+\\data\\catalog_data.js';

console.log('📖 Lettura catalogo da:', catalogSource);
const code = fs.readFileSync(catalogSource, 'utf8');

const window = {};
eval(code);
const allData = window.LISTINO81_DATA || [];
const corsi = allData.filter(x => x.corso === 1 || x.tipo === 'corso');

console.log(`✅ Trovati ${corsi.length} corsi ufficiali nel catalogo!`);

const db = new sqlite3.Database(dbPath);

db.serialize(() => {
  console.log('⚡ Svuotamento e re-popolamento tabella courses...');
  db.run('DELETE FROM courses');

  const stmt = db.prepare(`
    INSERT INTO courses (
      code, title, faculty, school, cfp_credits, description, is_active
    ) VALUES (?, ?, ?, ?, ?, ?, 1)
  `);

  let count = 0;
  for (const c of corsi) {
    count++;
    const code = `CRS-${String(count).padStart(5, '0')}`;
    const title = c.n ? c.n.trim() : 'Corso Specialistico';
    const faculty = c.cat ? c.cat.trim() : (c.tier || 'Formazione Continua');
    const school = c.sub ? c.sub.trim() : (c.mode || 'Online');
    const cfp = c.eur ? Math.min(Math.max(Math.round(c.eur / 25), 2), 30) : 4;
    const desc = c.d || `Corso accademico specialistico in ${faculty}.`;

    stmt.run([code, title, faculty, school, cfp, desc]);
  }

  stmt.finalize((err) => {
    if (err) {
      console.error('❌ Errore inserimento corsi:', err);
    } else {
      console.log(`🎉 Inseriti con successo ${count} corsi nel database SQLite!`);
    }
    db.close();
  });
});
