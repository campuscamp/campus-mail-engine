/**
 * CAMPUS Deployer — FTP Auto-Deploy verso Hostinger
 * Carica file e cartelle in public_html su campus.camp
 */
import basicFtp from 'basic-ftp';
import path from 'path';

export async function deployToCampus(localPath, remoteName = null) {
  const client = new basicFtp.Client();
  client.ftp.verbose = false;

  const targetName = remoteName || path.basename(localPath);

  try {
    console.log(`📡 Connessione FTP a ftp.campus.camp (92.113.18.68)...`);
    await client.access({
      host: process.env.FTP_HOST || '92.113.18.68',
      user: process.env.FTP_USER || 'u173050672.campus.camp',
      password: process.env.FTP_PASS || 'h29031976T.',
      port: 21,
      secure: false,
    });

    console.log(`🚀 Upload in corso: ${localPath} ➔ public_html/${targetName}`);
    const remoteDir = targetName.includes('/') ? targetName.substring(0, targetName.lastIndexOf('/')) : null;
    const filename = targetName.includes('/') ? targetName.substring(targetName.lastIndexOf('/') + 1) : targetName;
    if (remoteDir) {
      await client.ensureDir(remoteDir);
      await client.uploadFrom(localPath, filename);
    } else {
      await client.uploadFrom(localPath, targetName);
    }
    console.log(`✅ Upload completato con successo! Disponibile su: https://campus.camp/${targetName}`);

  } catch (err) {
    console.error(`❌ Errore durante il deploy FTP:`, err.message);
    throw err;
  } finally {
    client.close();
  }
}

// Se invocato direttamente da riga di comando
const isMain = process.argv[1]?.endsWith('deploy-ftp.js');
if (isMain) {
  const localFile = process.argv[2];
  const remoteFile = process.argv[3];

  if (!localFile) {
    console.log('Utilizzo: node scripts/deploy-ftp.js <file_locale> [nome_remoto]');
    process.exit(1);
  }

  deployToCampus(localFile, remoteFile)
    .then(() => process.exit(0))
    .catch(() => process.exit(1));
}
