import { fileURLToPath } from 'url';
import path from 'path';
import fs from 'fs';
import { CENSUS_CONTACTS } from '../src/contacts/census-database.js';

const records = [];

// 1. CENSUS CONTACTS (58)
for (const c of CENSUS_CONTACTS) {
  let group = 'ORDINI_COLLEGI_ALBI';
  if (['ASSOCIAZIONE_CATEGORIA', 'ASSOCIAZIONE_L4'].includes(c.type)) {
    group = 'ASSOCIAZIONI_CATEGORIA';
  } else if (c.type === 'CCIAA') {
    group = 'CAMERE_COMMERCIO';
  } else if (c.type === 'FONDO_INTERPROFESSIONALE') {
    group = 'FONDI_INTERPROFESSIONALI';
  } else if (c.type === 'FEDERAZIONE_PROFESSIONALE') {
    group = 'ORDINI_COLLEGI_ALBI';
  }

  records.push({
    id: c.id,
    category_group: group,
    entity_type: c.type,
    organization_name: c.organization,
    acronym: c.id.split('-')[0],
    sector: c.category || 'GENERALE',
    contact_role: c.target_role || 'Presidente e Segreteria',
    email: c.email || '',
    pec: c.pec || '',
    phone: c.phone || '',
    website: c.website || '',
    territory_level: c.level || 'NAZIONALE',
    region: c.region || 'NAZIONALE',
    province: c.province || 'RM',
    address: c.address || '',
    description: c.docenti_target || '',
    status: 'ATTIVO'
  });
}

// 2. Grandi Ordini e Collegi Territoriali Strategici
const territorialOrders = [
  { id: 'ORD-ING-ROMA', type: 'ORDINE', org: 'Ordine degli Ingegneri della Provincia di Roma', email: 'segreteria@ording.roma.it', pec: 'ordine.roma@ingpec.eu', site: 'https://www.ording.roma.it', reg: 'Lazio', prov: 'RM', sector: 'INGEGNERIA_TECNICA' },
  { id: 'ORD-ING-MILANO', type: 'ORDINE', org: 'Ordine degli Ingegneri della Provincia di Milano', email: 'segreteria@ordineingegneri.milano.it', pec: 'ordine.milano@ingpec.eu', site: 'https://www.ordineingegneri.milano.it', reg: 'Lombardia', prov: 'MI', sector: 'INGEGNERIA_TECNICA' },
  { id: 'ORD-ING-NAPOLI', type: 'ORDINE', org: 'Ordine degli Ingegneri della Provincia di Napoli', email: 'segreteria@ordingnapoli.it', pec: 'ordine.napoli@ingpec.eu', site: 'https://www.ordingnapoli.it', reg: 'Campania', prov: 'NA', sector: 'INGEGNERIA_TECNICA' },
  { id: 'ORD-ING-TORINO', type: 'ORDINE', org: 'Ordine degli Ingegneri della Provincia di Torino', email: 'segreteria@ording.torino.it', pec: 'ordine.torino@ingpec.eu', site: 'https://www.ording.torino.it', reg: 'Piemonte', prov: 'TO', sector: 'INGEGNERIA_TECNICA' },
  { id: 'ORD-ING-VENEZIA', type: 'ORDINE', org: 'Ordine degli Ingegneri della Città Metropolitana di Venezia', email: 'ordine@ordineingegnerivenezia.org', pec: 'ordine.venezia@ingpec.eu', site: 'https://www.ordineingegnerivenezia.org', reg: 'Veneto', prov: 'VE', sector: 'INGEGNERIA_TECNICA' },
  { id: 'ORD-ING-PADOVA', type: 'ORDINE', org: 'Ordine degli Ingegneri della Provincia di Padova', email: 'segreteria@ordineingegneri.padova.it', pec: 'ordine.padova@ingpec.eu', site: 'https://www.ordineingegneri.padova.it', reg: 'Veneto', prov: 'PD', sector: 'INGEGNERIA_TECNICA' },
  { id: 'ORD-ING-ROVIGO', type: 'ORDINE', org: 'Ordine degli Ingegneri della Provincia di Rovigo (Delta del Po)', email: 'segreteria@ordingrovigo.it', pec: 'ordine.rovigo@ingpec.eu', site: 'https://www.ordingrovigo.it', reg: 'Veneto', prov: 'RO', sector: 'INGEGNERIA_TECNICA' },
  { id: 'ORD-ING-VERONA', type: 'ORDINE', org: 'Ordine degli Ingegneri della Provincia di Verona', email: 'segreteria@ordineingegneri.vr.it', pec: 'ordine.verona@ingpec.eu', site: 'https://www.ordineingegneri.vr.it', reg: 'Veneto', prov: 'VR', sector: 'INGEGNERIA_TECNICA' },
  { id: 'ORD-ING-BOLOGNA', type: 'ORDINE', org: 'Ordine degli Ingegneri della Provincia di Bologna', email: 'segreteria@ordineingegneri.bo.it', pec: 'ordine.bologna@ingpec.eu', site: 'https://www.ordineingegneri.bo.it', reg: 'Emilia-Romagna', prov: 'BO', sector: 'INGEGNERIA_TECNICA' },
  { id: 'ORD-ING-FIRENZE', type: 'ORDINE', org: 'Ordine degli Ingegneri della Provincia di Firenze', email: 'segreteria@ordineingegneri.fi.it', pec: 'ordine.firenze@ingpec.eu', site: 'https://www.ordineingegneri.fi.it', reg: 'Toscana', prov: 'FI', sector: 'INGEGNERIA_TECNICA' },

  { id: 'ORD-ARCH-ROMA', type: 'ORDINE', org: 'Ordine degli Architetti P.P.C. di Roma e Provincia', email: 'protocollo@architettiroma.it', pec: 'ordine.roma@archiworldpec.it', site: 'https://www.architettiroma.it', reg: 'Lazio', prov: 'RM', sector: 'INGEGNERIA_TECNICA' },
  { id: 'ORD-ARCH-MILANO', type: 'ORDINE', org: 'Ordine degli Architetti P.P.C. della Provincia di Milano', email: 'segreteria@ordinearchitetti.mi.it', pec: 'ordine.milano@archiworldpec.it', site: 'https://www.ordinearchitetti.mi.it', reg: 'Lombardia', prov: 'MI', sector: 'INGEGNERIA_TECNICA' },
  { id: 'ORD-ARCH-VENEZIA', type: 'ORDINE', org: 'Ordine degli Architetti P.P.C. della Provincia di Venezia', email: 'ordine@ordinearchitetti.ve.it', pec: 'ordine.venezia@archiworldpec.it', site: 'https://www.ordinearchitetti.ve.it', reg: 'Veneto', prov: 'VE', sector: 'INGEGNERIA_TECNICA' },
  { id: 'ORD-ARCH-ROVIGO', type: 'ORDINE', org: 'Ordine degli Architetti P.P.C. della Provincia di Rovigo', email: 'architetti@rovigo.awn.it', pec: 'oappc.rovigo@archiworldpec.it', site: 'https://www.ordinearchitetti.rovigo.it', reg: 'Veneto', prov: 'RO', sector: 'INGEGNERIA_TECNICA' },
  { id: 'ORD-ARCH-PADOVA', type: 'ORDINE', org: 'Ordine degli Architetti P.P.C. della Provincia di Padova', email: 'ordine@ordinearchitetti.pd.it', pec: 'oappc.padova@archiworldpec.it', site: 'https://www.ordinearchitetti.pd.it', reg: 'Veneto', prov: 'PD', sector: 'INGEGNERIA_TECNICA' },

  { id: 'ORD-COMM-ROMA', type: 'ORDINE', org: 'Ordine dei Dottori Commercialisti e degli E.C. di Roma', email: 'segreteria@odcec.roma.it', pec: 'odcecroma@pec.odcec.roma.it', site: 'https://www.odcec.roma.it', reg: 'Lazio', prov: 'RM', sector: 'ECONOMIA_GIURIDICA' },
  { id: 'ORD-COMM-MILANO', type: 'ORDINE', org: 'Ordine dei Dottori Commercialisti e degli E.C. di Milano', email: 'segreteria@odcec.mi.it', pec: 'protocollo@pec.odcec.mi.it', site: 'https://www.odcec.mi.it', reg: 'Lombardia', prov: 'MI', sector: 'ECONOMIA_GIURIDICA' },
  { id: 'ORD-COMM-VENEZIA', type: 'ORDINE', org: 'Ordine dei Dottori Commercialisti e degli E.C. di Venezia', email: 'info@commercialistivenezia.it', pec: 'odcecvenezia@pec.commercialistivenezia.it', site: 'https://www.commercialistivenezia.it', reg: 'Veneto', prov: 'VE', sector: 'ECONOMIA_GIURIDICA' },
  { id: 'ORD-COMM-ROVIGO', type: 'ORDINE', org: 'Ordine dei Dottori Commercialisti e degli E.C. di Rovigo', email: 'segreteria@commercialistirovigo.it', pec: 'odcecrovigo@pec.commercialistirovigo.it', site: 'https://www.commercialistirovigo.it', reg: 'Veneto', prov: 'RO', sector: 'ECONOMIA_GIURIDICA' },

  { id: 'ORD-AVV-ROMA', type: 'ORDINE', org: 'Consiglio dell\'Ordine degli Avvocati di Roma', email: 'urp@ordineavvocatiroma.org', pec: 'urp@cert.ordineavvocatiroma.org', site: 'https://www.ordineavvocatiroma.it', reg: 'Lazio', prov: 'RM', sector: 'ECONOMIA_GIURIDICA' },
  { id: 'ORD-AVV-MILANO', type: 'ORDINE', org: 'Consiglio dell\'Ordine degli Avvocati di Milano', email: 'urp@ordineavvocatimilano.it', pec: 'segreteria@cert.ordineavvocatimilano.it', site: 'https://www.ordineavvocatimilano.it', reg: 'Lombardia', prov: 'MI', sector: 'ECONOMIA_GIURIDICA' },
  { id: 'ORD-AVV-ROVIGO', type: 'ORDINE', org: 'Consiglio dell\'Ordine degli Avvocati di Rovigo', email: 'segreteria@ordineavvocatirovigo.it', pec: 'ordine@pec.ordineavvocatirovigo.it', site: 'https://www.ordineavvocatirovigo.it', reg: 'Veneto', prov: 'RO', sector: 'ECONOMIA_GIURIDICA' },

  { id: 'COL-GEOM-ROVIGO', type: 'COLLEGIO', org: 'Collegio Provinciale Geometri e Geometri Laureati di Rovigo', email: 'sede@geometri.ro.it', pec: 'collegio.rovigo@geopec.it', site: 'https://www.geometri.ro.it', reg: 'Veneto', prov: 'RO', sector: 'INGEGNERIA_TECNICA' },
  { id: 'COL-GEOM-VENEZIA', type: 'COLLEGIO', org: 'Collegio Provinciale Geometri e Geometri Laureati di Venezia', email: 'segreteria@collegiogeometri.ve.it', pec: 'collegio.venezia@geopec.it', site: 'https://www.collegiogeometri.ve.it', reg: 'Veneto', prov: 'VE', sector: 'INGEGNERIA_TECNICA' },
  { id: 'ORD-PER-ROVIGO', type: 'ORDINE', org: 'Ordine dei Periti Industriali della Provincia di Rovigo', email: 'segreteria@peritiindustrialirovigo.it', pec: 'ordine.rovigo@pec.cnpi.it', site: 'https://www.peritiindustrialirovigo.it', reg: 'Veneto', prov: 'RO', sector: 'INGEGNERIA_TECNICA' }
];

for (const o of territorialOrders) {
  records.push({
    id: o.id,
    category_group: 'ORDINI_COLLEGI_ALBI',
    entity_type: o.type,
    organization_name: o.org,
    acronym: o.id.split('-')[1],
    sector: o.sector,
    contact_role: 'Presidente e Segreteria Generale',
    email: o.email,
    pec: o.pec,
    phone: '',
    website: o.site,
    territory_level: 'PROVINCIALE',
    region: o.reg,
    province: o.prov,
    address: '',
    description: 'Iscritti all\'albo provinciale convocabili per docenze e CFP',
    status: 'ATTIVO'
  });
}

// 3. Grandi Imprese Nazionali & Regionali (Corporate Academy & Formazione Finanziata)
const corporateEntities = [
  { id: 'CORP-ENEL', org: 'Enel S.p.A.', email: 'corporate.training@enel.com', pec: 'enelspa@pec.enel.it', site: 'https://www.enel.it', reg: 'NAZIONALE', prov: 'RM', sector: 'ENERGIA_AMBIENTE', desc: 'Gruppo energetico multinazionale e reti di distribuzione elettrica' },
  { id: 'CORP-ENI', org: 'Eni S.p.A.', email: 'academy@eni.com', pec: 'eni@pec.eni.it', site: 'https://www.eni.com', reg: 'NAZIONALE', prov: 'RM', sector: 'ENERGIA_AMBIENTE', desc: 'Transizione energetica, bioraffinerie, chimica verde ed esplorazione' },
  { id: 'CORP-LEONARDO', org: 'Leonardo S.p.A.', email: 'hr.training@leonardo.com', pec: 'leonardo@pec.leonardo.com', site: 'https://www.leonardo.com', reg: 'NAZIONALE', prov: 'RM', sector: 'AEROSPAZIO_DIFESA', desc: 'Aerospazio, difesa, cybersecurity e telecomunicazioni ad alta affidabilità' },
  { id: 'CORP-FINCANTIERI', org: 'Fincantieri S.p.A.', email: 'fincantieri.academy@fincantieri.it', pec: 'fincantieri@pec.fincantieri.it', site: 'https://www.fincantieri.com', reg: 'NAZIONALE', prov: 'TS', sector: 'MANIFATTURA_COSTRUZIONI', desc: 'Cantieristica navale di eccellenza mondiale e difesa marittima' },
  { id: 'CORP-FS', org: 'Ferrovie dello Stato Italiane S.p.A.', email: 'formazione.gruppo@fsitaliane.it', pec: 'fsitaliane@pec.fsitaliane.it', site: 'https://www.fsitaliane.it', reg: 'NAZIONALE', prov: 'RM', sector: 'LOGISTICA_INFRASTRUTTURE', desc: 'Infrastrutture ferroviarie ad alta velocità, mobilità integrata e logistica' },
  { id: 'CORP-TERNA', org: 'Terna S.p.A.', email: 'terna.academy@terna.it', pec: 'terna@pec.terna.it', site: 'https://www.terna.it', reg: 'NAZIONALE', prov: 'RM', sector: 'ENERGIA_AMBIENTE', desc: 'Gestore della rete di trasmissione elettrica nazionale in alta tensione' },
  { id: 'CORP-SNAM', org: 'Snam S.p.A.', email: 'snam.academy@snam.it', pec: 'snam@pec.snam.it', site: 'https://www.snam.it', reg: 'NAZIONALE', prov: 'MI', sector: 'ENERGIA_AMBIENTE', desc: 'Infrastrutture energetiche, trasporto gas, idrogeno e stoccaggio' },
  { id: 'CORP-POSTE', org: 'Poste Italiane S.p.A.', email: 'formazione.corporate@posteitaliane.it', pec: 'posteitaliane@pec.posteitaliane.it', site: 'https://www.poste.it', reg: 'NAZIONALE', prov: 'RM', sector: 'SERVIZI_FINANZA', desc: 'Servizi postali, finanziari, assicurativi e digitali capillari in tutta Italia' },
  { id: 'CORP-TIM', org: 'TIM S.p.A.', email: 'tim.academy@telecomitalia.it', pec: 'telecomitalia@pec.telecomitalia.it', site: 'https://www.gruppotim.it', reg: 'NAZIONALE', prov: 'RM', sector: 'DIGITALE_INNOVAZIONE', desc: 'Telecomunicazioni, cloud computing, reti in fibra ottica e 5G' },
  { id: 'CORP-WEBUILD', org: 'Webuild S.p.A.', email: 'hr.training@webuildgroup.com', pec: 'webuild@pec.webuildgroup.com', site: 'https://www.webuildgroup.com', reg: 'NAZIONALE', prov: 'MI', sector: 'MANIFATTURA_COSTRUZIONI', desc: 'Leader mondiale nella costruzione di grandi infrastrutture idrauliche e di trasporto' },
  { id: 'CORP-CDP', org: 'Cassa Depositi e Prestiti S.p.A.', email: 'comunicazione@cdp.it', pec: 'cdp@pec.cdp.it', site: 'https://www.cdp.it', reg: 'NAZIONALE', prov: 'RM', sector: 'SERVIZI_FINANZA', desc: 'Istituzione finanziaria nazionale per lo sviluppo e il sostegno a imprese e PA' },
  { id: 'CORP-A2A', org: 'A2A S.p.A.', email: 'academy@a2a.eu', pec: 'a2a@pec.a2a.eu', site: 'https://www.a2a.eu', reg: 'Lombardia', prov: 'MI', sector: 'ENERGIA_AMBIENTE', desc: 'Life company, gestione rifiuti, transizione energetica e ciclo idrico' },
  { id: 'CORP-HERA', org: 'Gruppo Hera S.p.A.', email: 'heracademy@gruppohera.it', pec: 'heraspa@pec.gruppohera.it', site: 'https://www.gruppohera.it', reg: 'Emilia-Romagna', prov: 'BO', sector: 'ENERGIA_AMBIENTE', desc: 'Multiutility dei servizi ambientali, idrici ed energetici nel Nord Italia' }
];

for (const c of corporateEntities) {
  records.push({
    id: c.id,
    category_group: 'IMPRESE_PA',
    entity_type: 'IMPRESA',
    organization_name: c.org,
    acronym: c.id.replace('CORP-', ''),
    sector: c.sector,
    contact_role: 'Direzione Risorse Umane & Corporate Academy',
    email: c.email,
    pec: c.pec,
    phone: '',
    website: c.site,
    territory_level: c.reg === 'NAZIONALE' ? 'NAZIONALE' : 'REGIONALE',
    region: c.reg,
    province: c.prov,
    address: '',
    description: c.desc,
    status: 'ATTIVO'
  });
}

// 4. Pubbliche Amministrazioni, Ministeri ed Enti Territoriali
const publicEntities = [
  { id: 'PA-MUR', org: 'Ministero dell\'Università e della Ricerca (MUR)', email: 'urp@mur.gov.it', pec: 'dgist@postacert.istruzione.it', site: 'https://www.mur.gov.it', reg: 'NAZIONALE', prov: 'RM', sector: 'PUBBLICA_AMMINISTRAZIONE', desc: 'Dicastero governativo per l\'alta formazione, atenei ed enti di ricerca' },
  { id: 'PA-LAVORO', org: 'Ministero del Lavoro e delle Politiche Sociali', email: 'urp@lavoro.gov.it', pec: 'dgpoliticheattive@pec.lavoro.gov.it', site: 'https://www.lavoro.gov.it', reg: 'NAZIONALE', prov: 'RM', sector: 'PUBBLICA_AMMINISTRAZIONE', desc: 'Sicurezza sul lavoro, fondi interprofessionali, vigilanza e previdenza' },
  { id: 'PA-MIMIT', org: 'Ministero delle Imprese e del Made in Italy (MIMIT)', email: 'urp@mimit.gov.it', pec: 'gabinetto@pec.mimit.gov.it', site: 'https://www.mimit.gov.it', reg: 'NAZIONALE', prov: 'RM', sector: 'PUBBLICA_AMMINISTRAZIONE', desc: 'Sviluppo economico, piano transizione 5.0 e proprietà intellettuale' },
  { id: 'PA-REGIONE-VENETO', org: 'Regione del Veneto — Direzione Formazione e Istruzione', email: 'formazioneistruzione@regione.veneto.it', pec: 'formazioneistruzione@pec.regione.veneto.it', site: 'https://www.regione.veneto.it', reg: 'Veneto', prov: 'VE', sector: 'PUBBLICA_AMMINISTRAZIONE', desc: 'Ente regionale di programmazione formativa, FSE+ e valorizzazione del territorio' },
  { id: 'PA-PROVINCIA-ROVIGO', org: 'Provincia di Rovigo', email: 'urp@provincia.rovigo.it', pec: 'ufficioprotocollo@cert.provincia.rovigo.it', site: 'https://www.provincia.rovigo.it', reg: 'Veneto', prov: 'RO', sector: 'PUBBLICA_AMMINISTRAZIONE', desc: 'Ente di area vasta del Polesine e della terra tra i due fiumi (Adige e Po)' },
  { id: 'PA-COMUNE-PORTOVIRO', org: 'Comune di Porto Viro', email: 'protocollo@comune.portoviro.ro.it', pec: 'comune.portoviro@pec.it', site: 'https://www.comune.portoviro.ro.it', reg: 'Veneto', prov: 'RO', sector: 'PUBBLICA_AMMINISTRAZIONE', desc: 'Comune pilota del Polo Accademico CAMPUS nel cuore del Parco del Delta del Po' },
  { id: 'PA-ARPAV', org: 'ARPAV — Agenzia Regionale per la Prevenzione e Protezione Ambientale del Veneto', email: 'urp@arpa.veneto.it', pec: 'protocollo@pec.arpav.it', site: 'https://www.arpa.veneto.it', reg: 'Veneto', prov: 'PD', sector: 'PUBBLICA_AMMINISTRAZIONE', desc: 'Monitoraggio ambientale, qualità delle acque, suolo ed emissioni' },
  { id: 'PA-BONIFICA-DELTA', org: 'Consorzio di Bonifica Delta del Po', email: 'info@bonificadeltadelpo.it', pec: 'protocollo@pec.bonificadeltadelpo.it', site: 'https://www.bonificadeltadelpo.it', reg: 'Veneto', prov: 'RO', sector: 'PUBBLICA_AMMINISTRAZIONE', desc: 'Gestione idraulica della bonifica, difesa costiera e cuneo salino' },
  { id: 'PA-PARCO-DELTA', org: 'Ente Parco Regionale Veneto del Delta del Po', email: 'info@parcodeltapo.org', pec: 'parco.deltapo@regione.veneto.legalmail.it', site: 'https://www.parcodeltapo.org', reg: 'Veneto', prov: 'RO', sector: 'PUBBLICA_AMMINISTRAZIONE', desc: 'Riserva della Biosfera MAB UNESCO per la biodiversità e lo sviluppo sostenibile' },
  { id: 'PA-INAIL', org: 'INAIL — Istituto Nazionale Assicurazione Infortuni sul Lavoro', email: 'dccomunicazione@inail.it', pec: 'dccomunicazione@postacert.inail.it', site: 'https://www.inail.it', reg: 'NAZIONALE', prov: 'RM', sector: 'PUBBLICA_AMMINISTRAZIONE', desc: 'Prevenzione, sicurezza sul lavoro e ricerca applicata D.Lgs. 81/08' },
  { id: 'PA-INPS', org: 'INPS — Istituto Nazionale della Previdenza Sociale', email: 'urp@inps.it', pec: 'direzione.centrale.comunicazione@postacert.inps.gov.it', site: 'https://www.inps.it', reg: 'NAZIONALE', prov: 'RM', sector: 'PUBBLICA_AMMINISTRAZIONE', desc: 'Welfare, ammortizzatori sociali, previdenza e gestione contributiva' },
  { id: 'PA-ANCI', org: 'ANCI — Associazione Nazionale Comuni Italiani', email: 'anci@anci.it', pec: 'protocollo@postacert.anci.it', site: 'https://www.anci.it', reg: 'NAZIONALE', prov: 'RM', sector: 'PUBBLICA_AMMINISTRAZIONE', desc: 'Rappresentanza unitaria dei Comuni italiani e supporto formativo agli amministratori' },
  { id: 'PA-UPI', org: 'UPI — Unione Province d\'Italia', email: 'segreteria@upinet.it', pec: 'upi@postacert.it', site: 'https://www.provinceditalia.it', reg: 'NAZIONALE', prov: 'RM', sector: 'PUBBLICA_AMMINISTRAZIONE', desc: 'Coordinamento delle Province italiane e gestione delle deleghe territoriali' },
  { id: 'PA-FORMEZ', org: 'Formez PA — Centro servizi, assistenza, studi e formazione per l\'ammodernamento delle P.A.', email: 'urp@formez.it', pec: 'protocollo@pec.formez.it', site: 'https://www.formez.it', reg: 'NAZIONALE', prov: 'RM', sector: 'PUBBLICA_AMMINISTRAZIONE', desc: 'Attuazione delle riforme della PA, concorsi pubblici e capacity building' }
];

for (const p of publicEntities) {
  records.push({
    id: p.id,
    category_group: 'IMPRESE_PA',
    entity_type: 'PA',
    organization_name: p.org,
    acronym: p.id.replace('PA-', ''),
    sector: p.sector,
    contact_role: 'Direzione Generale e URP',
    email: p.email,
    pec: p.pec,
    phone: '',
    website: p.site,
    territory_level: p.reg === 'NAZIONALE' ? 'NAZIONALE' : 'REGIONALE',
    region: p.reg,
    province: p.prov,
    address: '',
    description: p.desc,
    status: 'ATTIVO'
  });
}

const outputPath = path.resolve('data/institutional-directory.json');
fs.mkdirSync('data', { recursive: true });
fs.writeFileSync(outputPath, JSON.stringify(records, null, 2), 'utf8');
console.log(`Esportati ${records.length} record in ${outputPath}`);
