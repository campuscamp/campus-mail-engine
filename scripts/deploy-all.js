/**
 * CAMPUS.CAMP — Full Site FTP Deployer verso Hostinger public_html
 */
import basicFtp from 'basic-ftp';
import path from 'path';
import fs from 'fs';

async function deployAll() {
  const client = new basicFtp.Client();
  client.ftp.verbose = true;

  const localDir = 'c:\\81PLUS_GLOBAL_MASTER\\campus.camp\\public_html';

  console.log('================================================================');
  console.log('🚀 CAMPUS.CAMP — DEPLOY TOTALE SITO SU HOSTINGER (public_html)');
  console.log('================================================================\n');

  try {
    console.log('📡 Connessione FTP in corso...');
    await client.access({
      host: process.env.FTP_HOST || '92.113.18.68',
      user: process.env.FTP_USER || 'u173050672.campus.camp',
      password: process.env.FTP_PASS || 'h29031976T.',
      port: 21,
      secure: false
    });

    console.log('✅ Connesso! Caricamento ricorsivo di public_html in corso...\n');

    // basic-ftp uploadDir synchronizes local directory to remote directory
    await client.uploadFromDir(localDir);

    console.log('\n================================================================');
    console.log('🎉 DEPLOY COMPLETATO CON SUCCESSO SU HOSTINGER!');
    console.log('   Dominio: https://campus.camp');
    console.log('   Faculty: https://campus.camp/faculty.php');
    console.log('   Apply:   https://campus.camp/apply.php');
    console.log('   Admin:   https://campus.camp/admin/index.php (mirco / mirco)');
    console.log('================================================================\n');

  } catch (err) {
    console.error('❌ Errore durante il deploy:', err);
    process.exit(1);
  } finally {
    client.close();
  }
}

deployAll();
