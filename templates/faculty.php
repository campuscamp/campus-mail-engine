<?php
/**
 * CAMPUS.CAMP — Landing Page Ufficiale Faculty
 * Conversione Magnetica per Docenti & Professionisti
 * Big Idea: IL SAPERE CHE PRATICHI PUÒ DIVENTARE IL SAPERE CHE TRASMETTI.
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/icons.php';

$pageTitle = 'Faculty Fondatrice CAMPUS — Bando Ufficiale Docenti & Cattedre';
$pageDesc = 'Il sapere che pratichi può diventare il sapere che trasmetti. Bando di reclutamento docenti ed esperti per l\'Istituzione Accademica CAMPUS.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- =========================================================================
     1. HERO SECTION: BIG IDEA & CONVERSION HOOK
     ========================================================================= -->
<section class="section" style="padding-top: 55px; padding-bottom: 60px; text-align: center; border-bottom: 1px solid var(--border-subtle); background: radial-gradient(circle at 50% 20%, rgba(212, 175, 55, 0.14) 0%, rgba(7, 9, 14, 0.96) 75%);">
  <div class="container" style="max-width: 960px;">
    
    <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(212, 175, 55, 0.1); border: 1px solid var(--gold-primary); padding: 7px 20px; border-radius: 30px; margin-bottom: 24px;">
      <?= icon_gold('institution', 16) ?>
      <span style="color: var(--gold-light); font-size: 11.5px; font-weight: 800; letter-spacing: 2.2px; text-transform: uppercase;">
        Bando Pubblico Permanente — Costituzione Faculty Fondatrice
      </span>
    </div>

    <h1 style="font-size: clamp(34px, 5.2vw, 58px); line-height: 1.12; margin-bottom: 20px; font-family: 'Cinzel', serif; color: #ffffff;">
      IL SAPERE CHE PRATICHI<br>
      <span class="gold-text">PUÒ DIVENTARE IL SAPERE CHE TRASMETTI.</span>
    </h1>

    <p style="font-size: clamp(16px, 2vw, 20px); color: var(--text-muted); max-width: 800px; margin: 0 auto 34px auto; line-height: 1.6; font-weight: 300;">
      Hai maturato anni di esperienza sul campo: cantieri complessi, sentenze, bilanci societari, casi clinici o innovazione industriale. CAMPUS ti offre lo spazio accademico per trasformarla in conoscenza d'eccellenza, conferendo <strong style="color: #ffffff;">dignità di cattedra</strong> alla tua pratica quotidiana.
    </p>

    <!-- CTA & MICROCOPY ANTI-ANSIA -->
    <div style="display: flex; gap: 16px; justify-content: center; align-items: center; flex-wrap: wrap; margin-bottom: 20px;">
      <a href="/apply.php" class="btn-gold" style="font-size: 15px; padding: 16px 38px; display: inline-flex; align-items: center; gap: 10px; font-weight: 700;">
        <?= icon_gold('document', 18) ?> CANDIDATI ALLA FACULTY
      </a>
      <a href="/manifesto-docenti-a4.html" target="_blank" class="btn-outline-gold" style="font-size: 15px; padding: 16px 36px; display: inline-flex; align-items: center; gap: 10px;">
        <?= icon_gold('print', 18) ?> Manifesto Ufficiale A4
      </a>
    </div>

    <!-- 4 RASSICURAZIONI PSICOLOGICHE IMMEDIATE (Objection Reversal) -->
    <div style="display: flex; justify-content: center; gap: 18px; flex-wrap: wrap; font-size: 12px; color: var(--text-dim); max-width: 820px; margin: 0 auto;">
      <span style="display: inline-flex; align-items: center; gap: 6px;">
        <?= icon_gold('check', 13) ?> Compilazione in 5 minuti
      </span>
      <span style="display: inline-flex; align-items: center; gap: 6px;">
        <?= icon_gold('check', 13) ?> Revisione dati prima dell'invio
      </span>
      <span style="display: inline-flex; align-items: center; gap: 6px;">
        <?= icon_gold('check', 13) ?> Emissione immediata del tuo SIC-ID
      </span>
      <span style="display: inline-flex; align-items: center; gap: 6px;">
        <?= icon_gold('check', 13) ?> Nessun vincolo economico o obbligo automatico
      </span>
    </div>

  </div>
</section>

<!-- =========================================================================
     2. PERCHÉ, COSA, PER CHI, COME (IL QUADRO STRUTTURALE)
     ========================================================================= -->
<section class="section">
  <div class="container">
    
    <div style="text-align: center; max-width: 820px; margin: 0 auto 45px auto;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        La Proposta di Valore
      </span>
      <h2 style="font-size: clamp(26px, 3.8vw, 38px); color: #ffffff; margin-top: 8px;">
        Cosa Significa Entrare nella Faculty <span class="gold-text">CAMPUS</span>
      </h2>
      <p style="color: var(--text-muted); font-size: 15px; margin-top: 8px;">
        Non un semplice registro nominativo, ma una comunità d'élite che valorizza il tuo profilo a livello nazionale.
      </p>
    </div>

    <div class="grid-3" style="margin-bottom: 55px;">
      
      <div class="glass-card" style="padding: 28px; border-top: 3px solid var(--gold-primary);">
        <div style="margin-bottom: 14px;"><?= icon_gold('crown', 32) ?></div>
        <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 10px;">Prestigio & Riconoscimento</h3>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6;">
          Consolida la tua autorevolezza professionale. Appartenere alla Faculty CAMPUS certifica formalmente il tuo ruolo di formatore qualificato agli occhi di colleghi, clienti, Ordini e istituzioni.
        </p>
      </div>

      <div class="glass-card" style="padding: 28px; border-top: 3px solid var(--gold-light);">
        <div style="margin-bottom: 14px;"><?= icon_gold('briefcase', 32) ?></div>
        <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 10px;">Incarichi di Docenza Retribuiti</h3>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6;">
          I docenti accreditati vengono designati per cattedre monografiche, master specialistici e corsi CFP retribuiti con tariffe e compensi orari prestabiliti in piena trasparenza contrattuale.
        </p>
      </div>

      <div class="glass-card" style="padding: 28px; border-top: 3px solid var(--gold-metallic);">
        <div style="margin-bottom: 14px;"><?= icon_gold('laptop', 32) ?></div>
        <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 10px;">Massima Flessibilità Ibrida</h3>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6;">
          Nessun vincolo orario che ostacoli la tua attività lavorativa. Corsi asincroni registrati, aule virtuali serali o nel weekend, oppure workshop residenziali intensivi a Porto Viro.
        </p>
      </div>

    </div>

    <!-- TIMELINE DI AMMISSIONE: DALLA CANDIDATURA ALLA CATTEDRA -->
    <div class="glass-card" style="border: 2px solid var(--border-gold); padding: 40px 30px; margin-bottom: 60px;">
      <div style="text-align: center; max-width: 700px; margin: 0 auto 35px auto;">
        <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
          Iter di Selezione Trasparente
        </span>
        <h3 style="font-size: 26px; color: #ffffff; margin-top: 6px;">
          Come Funziona l'Ammissione alla Faculty (5 Passi)
        </h3>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; text-align: center;">
        
        <div style="background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); padding: 20px 14px; border-radius: var(--radius-sm);">
          <div style="font-size: 20px; font-weight: 900; color: var(--gold-light); font-family: monospace;">01</div>
          <h4 style="color: #ffffff; font-size: 15px; margin: 8px 0 6px 0;">Candidatura Online</h4>
          <p style="color: var(--text-dim); font-size: 12px; line-height: 1.5;">Compili i dati anagrafici, professionali e selezioni gli insegnamenti di tuo interesse (5 min).</p>
        </div>

        <div style="background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); padding: 20px 14px; border-radius: var(--radius-sm);">
          <div style="font-size: 20px; font-weight: 900; color: var(--gold-light); font-family: monospace;">02</div>
          <h4 style="color: #ffffff; font-size: 15px; margin: 8px 0 6px 0;">Emissione SIC-ID</h4>
          <p style="color: var(--text-dim); font-size: 12px; line-height: 1.5;">Ricevi subito il tuo codice identificativo immutabile e la ricevuta di deposito protocollata.</p>
        </div>

        <div style="background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); padding: 20px 14px; border-radius: var(--radius-sm);">
          <div style="font-size: 20px; font-weight: 900; color: var(--gold-light); font-family: monospace;">03</div>
          <h4 style="color: #ffffff; font-size: 15px; margin: 8px 0 6px 0;">Esame dei Titoli</h4>
          <p style="color: var(--text-dim); font-size: 12px; line-height: 1.5;">La Commissione di Dipartimento esamina il CV e la pertinenza deontologica (entro 15 giorni).</p>
        </div>

        <div style="background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); padding: 20px 14px; border-radius: var(--radius-sm);">
          <div style="font-size: 20px; font-weight: 900; color: var(--gold-light); font-family: monospace;">04</div>
          <h4 style="color: #ffffff; font-size: 15px; margin: 8px 0 6px 0;">Colloquio & Allineamento</h4>
          <p style="color: var(--text-dim); font-size: 12px; line-height: 1.5;">I candidati idonei concordano con il Direttore di Dipartimento i moduli, orari e modalità.</p>
        </div>

        <div style="background: rgba(0,0,0,0.4); border: 1px solid var(--gold-primary); padding: 20px 14px; border-radius: var(--radius-sm);">
          <div style="font-size: 20px; font-weight: 900; color: var(--gold-primary); font-family: monospace;">05</div>
          <h4 style="color: #ffffff; font-size: 15px; margin: 8px 0 6px 0;">Albo & Cattedra</h4>
          <p style="color: var(--text-dim); font-size: 12px; line-height: 1.5;">Formalizzazione contrattuale e avvio dell'incarico didattico retribuito.</p>
        </div>

      </div>
    </div>

  </div>
</section>

<!-- =========================================================================
     3. REQUISITI DI AMMISSIBILITÀ & GLI 8 AMBITI PRIORITARI
     ========================================================================= -->
<section class="section" style="background: rgba(10, 14, 23, 0.5); border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle); padding: 65px 0;">
  <div class="container">
    
    <div style="text-align: center; max-width: 800px; margin: 0 auto 45px auto;">
      <h2 style="font-size: clamp(26px, 3.8vw, 38px); color: #ffffff;">Requisiti di Ammissibilità & Ambiti</h2>
      <p style="color: var(--text-muted); margin-top: 8px; font-size: 15px;">
        La serietà di un'istituzione accademica si misura dalla trasparenza dei criteri d'ingresso.
      </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px;">
      
      <!-- REQUISITI -->
      <div class="glass-card" style="border-left: 4px solid var(--gold-primary); padding: 32px 26px;">
        <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
          <?= icon_gold('shield', 18) ?> Chi Può Candidarsi
        </h3>
        <ul style="list-style: none; padding: 0; color: var(--text-muted); font-size: 14px; line-height: 1.75;">
          <li style="margin-bottom: 12px; display: flex; align-items: flex-start; gap: 10px;">
            <?= icon_gold('check', 14) ?>
            <span><strong>Titolo Abilitante:</strong> Laurea magistrale/specialistica o titolo equipollente nel settore di riferimento.</span>
          </li>
          <li style="margin-bottom: 12px; display: flex; align-items: flex-start; gap: 10px;">
            <?= icon_gold('check', 14) ?>
            <span><strong>Iscrizione all'Albo o Esperienza:</strong> Iscrizione ad Ordine, Collegio, Associazione L. 4/2013 o idonea esperienza aziendale.</span>
          </li>
          <li style="margin-bottom: 12px; display: flex; align-items: flex-start; gap: 10px;">
            <?= icon_gold('check', 14) ?>
            <span><strong>Esperienza Pratica Comprovata:</strong> Almeno 3-5 anni di effettivo esercizio della professione o direzione tecnica.</span>
          </li>
          <li style="margin-bottom: 12px; display: flex; align-items: flex-start; gap: 10px;">
            <?= icon_gold('check', 14) ?>
            <span><strong>Integrità Deontologica:</strong> Assenza di sanzioni disciplinari o penali ostative all'attività didattica.</span>
          </li>
          <li style="display: flex; align-items: flex-start; gap: 10px;">
            <?= icon_gold('check', 14) ?>
            <span><strong>Adesione al Codice Etico:</strong> Condivisione dei principi di lealtà, metodo scientifico e tutela dei discenti.</span>
          </li>
        </ul>
      </div>

      <!-- GLI 8 AMBITI PRIORITARI -->
      <div class="glass-card" style="border-left: 4px solid var(--gold-light); padding: 32px 26px;">
        <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
          <?= icon_gold('academic', 18) ?> Gli 8 Ambiti di Cattedra
        </h3>
        <ul style="list-style: none; padding: 0; color: var(--text-muted); font-size: 13.5px; line-height: 1.75;">
          <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
            <?= icon_gold('shield', 14) ?> <span><strong>Ingegneria, Edilizia & BIM:</strong> Strutture, sicurezza cantieri D.Lgs. 81/08.</span>
          </li>
          <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
            <?= icon_gold('scale', 14) ?> <span><strong>Economia, Diritto & Fisco:</strong> Revisione contabile, contenzioso, compliance 231.</span>
          </li>
          <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
            <?= icon_gold('heart', 14) ?> <span><strong>Sanità & Scienze della Cura:</strong> Telemedicina, risk management, nutrizione clinica.</span>
          </li>
          <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
            <?= icon_gold('leaf', 14) ?> <span><strong>Ambiente & Idraulica del Delta:</strong> Bonifiche, agritech, cuneo salino.</span>
          </li>
          <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
            <?= icon_gold('briefcase', 14) ?> <span><strong>Management & Organizzazione:</strong> Leadership, PMI, welfare aziendale.</span>
          </li>
          <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
            <?= icon_gold('laptop', 14) ?> <span><strong>Innovazione Digitale & AI:</strong> Machine learning, cyber security, industria 5.0.</span>
          </li>
          <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
            <?= icon_gold('hammer', 14) ?> <span><strong>Mestieri d'Eccellenza:</strong> Cantieristica nautica, restauro beni monumentali.</span>
          </li>
          <li style="display: flex; align-items: center; gap: 8px;">
            <?= icon_gold('institution', 14) ?> <span><strong>Pubblica Amministrazione:</strong> Appalti pubblici, PNRR, gestione bandi.</span>
          </li>
        </ul>
      </div>

    </div>

  </div>
</section>

<!-- =========================================================================
     4. OBJECTION HANDLING & FAQ PERSUASIVE
     ========================================================================= -->
<section class="section" style="padding-top: 60px; padding-bottom: 70px;">
  <div class="container" style="max-width: 860px;">
    
    <div style="text-align: center; margin-bottom: 40px;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Domande Frequenti & Chiarimenti
      </span>
      <h2 style="font-size: 32px; color: #ffffff; margin-top: 8px;">
        Tutto ciò che Devi Sapere Prima di Candidarti
      </h2>
    </div>

    <div class="glass-card" style="margin-bottom: 16px; padding: 24px;">
      <h4 style="color: var(--gold-light); font-size: 16px; margin-bottom: 8px;">La candidatura iniziale comporta costi o addebiti?</h4>
      <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6;">
        Assolutamente no. La presentazione della candidatura, il caricamento del CV, la verifica dei requisiti e l'eventuale colloquio sono al 100% gratuiti e non vincolanti.
      </p>
    </div>

    <div class="glass-card" style="margin-bottom: 16px; padding: 24px;">
      <h4 style="color: var(--gold-light); font-size: 16px; margin-bottom: 8px;">Posso insegnare mantenendo il mio lavoro a tempo pieno?</h4>
      <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6;">
        Sì, la didattica è studiata specificamente per i professionisti attivi: i moduli possono essere asincroni (registrati in comodità), svolti in streaming serale/weekend o in brevi sessioni intensive concordate con la Direzione.
      </p>
    </div>

    <div class="glass-card" style="margin-bottom: 16px; padding: 24px;">
      <h4 style="color: var(--gold-light); font-size: 16px; margin-bottom: 8px;">Come vengono retribuiti gli incarichi di insegnamento?</h4>
      <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6;">
        All'approvazione del modulo didattico, viene stipulato un regolare contratto di prestazione professionale o docenza retribuita con compenso orario o a forfait prefissato prima dell'avvio del corso.
      </p>
    </div>

    <div class="glass-card" style="margin-bottom: 16px; padding: 24px;">
      <h4 style="color: var(--gold-light); font-size: 16px; margin-bottom: 8px;">Cos'è il codice SIC-ID emesso al termine del modulo?</h4>
      <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6;">
        Il codice SIC-ID (es. <code>SIC-ID-XXXXXXXXXXXX</code>) è l'identificativo crittografico univoco che certifica il deposito della tua pratica negli archivi centrali CAMPUS. Ti garantisce tracciamento permanente e priorità di valutazione cronologica.
      </p>
    </div>

    <div class="glass-card" style="padding: 24px;">
      <h4 style="color: var(--gold-light); font-size: 16px; margin-bottom: 8px;">Quando e come si attiva la Membership Faculty annuale?</h4>
      <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6;">
        Solo a valle dell'esame positivo dei titoli e dell'accettazione esplicita del candidato, l'accreditamento definitivo all'Albo Docenti Ufficiale prevede una quota di associazione annuale di <strong>299,00 € (IVA compresa se dovuta)</strong> a copertura dei servizi accademici, convenzioni CFP e piattaforma. Nessun costo viene mai addebitato prima della formale ammissione.
      </p>
    </div>

    <!-- CTA BANNER IN FONDO -->
    <div style="text-align: center; margin-top: 50px; background: rgba(0,0,0,0.5); border: 2px solid var(--border-gold); padding: 40px 24px; border-radius: var(--radius-md);">
      <h3 style="color: #ffffff; font-size: 24px; margin-bottom: 12px; font-family: 'Cinzel', serif;">
        Sei Pronto a Portare la Tua Esperienza in Cattedra?
      </h3>
      <p style="color: var(--text-muted); font-size: 14.5px; margin-bottom: 24px; max-width: 600px; margin-left: auto; margin-right: auto;">
        La procedura richiede pochi minuti. Tieni a portata di mano i tuoi dati anagrafici, il Codice Fiscale e il tuo CV in formato PDF.
      </p>
      <a href="/apply.php" class="btn-gold" style="font-size: 16px; padding: 18px 45px; display: inline-flex; align-items: center; gap: 10px; font-weight: 700;">
        <?= icon_gold('document', 18) ?> COMPILA LA CANDIDATURA DOCENTE
      </a>
    </div>

  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
