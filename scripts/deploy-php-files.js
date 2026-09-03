import basicFtp from 'basic-ftp';

async function deploy() {
  const client = new basicFtp.Client();
  await client.access({
    host: '92.113.18.68',
    user: 'u173050672.campus.camp',
    password: 'h29031976T.',
    port: 21,
    secure: false
  });

  const files = [
    'index.php', 'about.php', 'courses.php', 'faculty.php', 'apply.php',
    'thank-you.php', 'status.php', 'dashboard.php', 'contact.php',
    'faculties.php', 'organizations.php', 'porto-viro.php'
  ];

  for (const f of files) {
    await client.uploadFrom('c:/81PLUS_GLOBAL_MASTER/campus.camp/public_html/' + f, f);
    console.log('✅ Caricato:', f);
  }

  await client.uploadFrom('c:/81PLUS_GLOBAL_MASTER/campus.camp/public_html/includes/header.php', 'includes/header.php');
  await client.uploadFrom('c:/81PLUS_GLOBAL_MASTER/campus.camp/public_html/includes/config.php', 'includes/config.php');
  await client.uploadFrom('c:/81PLUS_GLOBAL_MASTER/campus.camp/public_html/includes/footer.php', 'includes/footer.php');
  await client.uploadFrom('c:/81PLUS_GLOBAL_MASTER/campus.camp/public_html/includes/database.php', 'includes/database.php');
  console.log('✅ Includes caricati con successo!');

  client.close();
}

deploy().catch(console.error);
