/**
 * Test di connessione FTP Hostinger per campus.camp
 */
import basicFtp from 'basic-ftp';

async function testFtp() {
  const client = new basicFtp.Client();
  client.ftp.verbose = true;

  const configs = [
    {
      host: '92.113.18.68',
      user: 'u173050672.campus.camp',
      password: 'h29031976T.',
      port: 21,
      secure: false
    },
    {
      host: '92.113.18.68',
      user: 'u173050672.campuscamp',
      password: 'h29031976T.',
      port: 21,
      secure: false
    },
    {
      host: 'ftp.campus.camp',
      user: 'u173050672.campus.camp',
      password: 'h29031976T.',
      port: 21,
      secure: false
    }
  ];

  for (let i = 0; i < configs.length; i++) {
    const cfg = configs[i];
    console.log(`\n--- Test Configurazione ${i + 1} (${cfg.host} - ${cfg.user}) ---`);
    try {
      await client.access({
        host: cfg.host,
        user: cfg.user,
        password: cfg.password,
        port: cfg.port,
        secure: cfg.secure
      });
      console.log('✅ Connessione FTP Riuscita!');
      const list = await client.list();
      console.log('File nella directory corrente:');
      for (const item of list) {
        console.log(` - ${item.name} (${item.isDirectory ? 'DIR' : 'FILE'}) [${item.size} bytes]`);
      }
      client.close();
      return;
    } catch (err) {
      console.error(`❌ Errore con configurazione ${i + 1}:`, err.message);
      client.close();
    }
  }
}

testFtp();
