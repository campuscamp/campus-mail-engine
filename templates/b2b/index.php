<?php
/**
 * CAMPUS.CAMP — Motore B2B: Aziende · Partner · Sponsor
 * Landing Master con architettura persuasiva, 6 assi del valore, configuratore e value ladder
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../includes/taxonomy.php';

$pageTitle = 'B2B Aziende, Partner & Sponsor — CAMPUS';
$pageDesc = 'La tua azienda può fare più che sponsorizzare: può diventare parte di CAMPUS. Corporate Academy, Talent matching, Ricerca e Alleanze territoriali.';

$db = Database::getConnection();

// Carica la Value Ladder e i Pacchetti dal Database
$packages = $db->query("
    SELECT p.*, pv.price_amount, pv.price_status, pv.currency
    FROM b2b_packages p
    LEFT JOIN b2b_price_versions pv ON p.id = pv.package_id
    WHERE p.is_active = 1
    ORDER BY p.display_order ASC
")->fetchAll();

// Allega componenti
$packagesWithComponents = [];
foreach ($packages as $pkg) {
    $stmtC = $db->prepare("SELECT component_text, is_highlighted FROM b2b_package_components WHERE package_id = ? ORDER BY display_order ASC");
    $stmtC->execute([$pkg['id']]);
    $pkg['components'] = $stmtC->fetchAll();
    $packagesWithComponents[] = $pkg;
}

// Carica elenco ATECO per il configuratore micro-commitment
$atecoActivities = Taxonomy::getAtecoActivities();

require_once __DIR__ . '/../includes/header.php';
?>

<!-- HERO SECTION: BIG IDEA -->
<section class="hero" style="min-height: 85vh; padding: 100px 20px 80px 20px;">
  <div class="hero-overlay"></div>
  <div class="hero-content" style="max-width: 1050px;">
    
    <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 18px; border-radius: 20px; background: rgba(212,175,55,0.12); border: 1px solid var(--border-gold); margin-bottom: 24px;">
      <span style="width: 8px; height: 8px; background: var(--gold-light); border-radius: 50%;"></span>
      <span style="font-size: 11.5px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 2.5px; font-weight: 800;">
        Ecosistema B2B · Imprese & Istituzioni
      </span>
    </div>

    <h1 style="font-size: clamp(32px, 5.5vw, 62px); line-height: 1.15; margin-bottom: 24px; font-family: 'Cinzel', serif; letter-spacing: 1.5px;">
      LA TUA AZIENDA PUÒ FARE PIÙ CHE SPONSORIZZARE.<br>
      <span class="gold-text">PUÒ DIVENTARE PARTE DI CAMPUS.</span>
    </h1>

    <p style="font-size: clamp(16px, 2vw, 20px); color: var(--text-muted); max-width: 850px; margin: 0 auto 38px auto; line-height: 1.6;">
      Forma le tue persone. Trova competenze qualificate. Condividi la tua esperienza sul campo. Sostieni lo sviluppo del territorio. Entra in una rete organica che collega formazione continua, professioni, imprese e innovazione applicata.
    </p>

    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
      <a href="/b2b/apply.php" class="btn-gold" style="font-size: 15px; padding: 16px 36px; font-weight: 800;" data-analytics="b2b_hero_apply">
        <?= icon_gold('institution', 18) ?> ENTRA NEL NETWORK CAMPUS
      </a>
      <a href="#configuratore" class="btn-outline-gold" style="font-size: 15px; padding: 16px 32px;" data-analytics="b2b_hero_configurator">
        SCOPRI LE OPPORTUNITÀ ↓
      </a>
    </div>

    <div style="margin-top: 36px; display: flex; justify-content: center; gap: 30px; font-size: 12.5px; color: var(--text-dim); flex-wrap: wrap;">
      <span><?= icon_gold('shield', 14) ?> Riconoscimento Istituzionale</span>
      <span><?= icon_gold('crown', 14) ?> Protocollo SIC-ID Organization</span>
      <span><?= icon_gold('academic', 14) ?> 2.119 Percorsi & Corpo Docente</span>
      <span><?= icon_gold('star', 14) ?> Zero Spazi Pubblicitari Passivi</span>
    </div>

  </div>
</section>


<!-- IL PERCHÉ: IL TERRITORIO CRESCE QUANDO CRESCONO LE SUE IMPRESE -->
<section class="section" style="background: rgba(10,10,10,0.6); border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);">
  <div class="container" style="max-width: 1100px;">
    
    <div style="text-align: center; max-width: 800px; margin: 0 auto 50px auto;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        La Filosofia dell'Ecosistema
      </span>
      <h2 style="font-size: clamp(26px, 3.8vw, 42px); color: #ffffff; margin-top: 8px; font-family: 'Cinzel', serif;">
        IL TERRITORIO CRESCE QUANDO <span class="gold-text">CRESCONO LE SUE IMPRESE</span>
      </h2>
      <p style="color: var(--text-muted); font-size: 16px; line-height: 1.7; margin-top: 14px;">
        CAMPUS non chiede alle aziende semplicemente di finanziare iniziative o acquistare loghi su uno striscione.<br>
        <strong>Le invita a costruire l'ateneo insieme.</strong>
      </p>
    </div>

    <div class="glass-card" style="padding: 40px; border: 1px solid var(--border-gold);">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 36px; align-items: center;">
        <div>
          <h3 style="color: #ffffff; font-size: 20px; font-family: 'Cinzel', serif; margin-bottom: 14px;">
            Un Unico Ecosistema Convergente
          </h3>
          <p style="color: var(--text-muted); font-size: 14.5px; line-height: 1.7; margin-bottom: 16px;">
            Mettiamo nello stesso circuito integrato: <strong>aziende, professionisti ordinistici, Faculty accademica, discenti, ricercatori, istituzioni e associazioni datoriali</strong>.
          </p>
          <p style="color: var(--text-muted); font-size: 14.5px; line-height: 1.7;">
            Quando un'impresa entra in CAMPUS, non sostiene una spesa promozionale: attiva un polo di formazione continua per i propri quadri, acquisisce un canale privilegiato sui talenti emergenti e porta le proprie sfide reali all'interno dei nostri laboratori applicati.
          </p>
        </div>

        <div style="background: rgba(0,0,0,0.5); border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); padding: 26px;">
          <div style="font-size: 12px; color: var(--gold-light); text-transform: uppercase; font-weight: 700; letter-spacing: 1.5px; margin-bottom: 14px;">
            I Pilastri della Convergenza:
          </div>
          <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13.5px; color: #ffffff;">
            <div style="display: flex; align-items: center; gap: 10px;">
              <?= icon_gold('check', 16) ?> <span><strong>Competenze Reali:</strong> colmiamo lo skill gap con didattica sul campo</span>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
              <?= icon_gold('check', 16) ?> <span><strong>Capitale Umano:</strong> accesso prioritario ai profili formati</span>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
              <?= icon_gold('check', 16) ?> <span><strong>Laboratori Territoriali:</strong> prototipi e progetti reali (Polo Delta del Po)</span>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
              <?= icon_gold('check', 16) ?> <span><strong>Reputazione Istituzionale:</strong> credibilità scientifica e deontologica</span>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>


<!-- I 6 ASSI DEL VALORE B2B -->
<section class="section">
  <div class="container" style="max-width: 1280px;">

    <div style="text-align: center; max-width: 800px; margin: 0 auto 55px auto;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Generazione di Valore Tangibile
      </span>
      <h2 style="font-size: clamp(26px, 3.8vw, 40px); color: #ffffff; margin-top: 8px; font-family: 'Cinzel', serif;">
        I 6 ASSI DEL VALORE <span class="gold-text">PER LA TUA IMPRESA</span>
      </h2>
      <p style="color: var(--text-muted); font-size: 15.5px;">
        Sei aree operative per tradurre la collaborazione in risultati misurabili di crescita, personale e mercato.
      </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 24px;">

      <!-- 01 FORMAZIONE -->
      <div class="glass-card" style="padding: 32px; border-top: 3px solid var(--gold-primary);">
        <div style="font-size: 12px; color: var(--gold-light); font-weight: 800; letter-spacing: 2px;">01 — ASSE FORMATIVO</div>
        <h3 style="color: #ffffff; font-size: 20px; margin: 8px 0 12px 0; font-family: 'Cinzel', serif;">
          Formazione & Corporate Academy
        </h3>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin-bottom: 16px;">
          Forma dipendenti, collaboratori e quadri aziendali con percorsi accademici certificati e conformi alle normative.
        </p>
        <div style="font-size: 12.5px; color: var(--text-dim); display: flex; flex-direction: column; gap: 6px;">
          <span>• <strong>Corporate Academy:</strong> accademia interna su misura</span>
          <span>• <strong>Upskilling & Reskilling:</strong> competenze operative emergenti</span>
          <span>• <strong>Executive Education:</strong> alta direzione per C-Level</span>
          <span>• <strong>Employee Learning Pass:</strong> crediti prepagati per il team</span>
        </div>
        <div style="margin-top: 20px;">
          <a href="/b2b/corporate-academy.php" style="color: var(--gold-light); font-size: 13px; text-decoration: underline; font-weight: 700;">
            Approfondisci Corporate Academy →
          </a>
        </div>
      </div>

      <!-- 02 TALENTO -->
      <div class="glass-card" style="padding: 32px; border-top: 3px solid var(--gold-primary);">
        <div style="font-size: 12px; color: var(--gold-light); font-weight: 800; letter-spacing: 2px;">02 — ASSE TALENTO</div>
        <h3 style="color: #ffffff; font-size: 20px; margin: 8px 0 12px 0; font-family: 'Cinzel', serif;">
          Talento & Recruiting Qualificato
        </h3>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin-bottom: 16px;">
          Entra in relazione diretta con professionisti abilitati, allievi selezionati e figure specialistiche già formate sul campo.
        </p>
        <div style="font-size: 12.5px; color: var(--text-dim); display: flex; flex-direction: column; gap: 6px;">
          <span>• <strong>Talent Matching:</strong> screening mirato per le tue selezioni</span>
          <span>• <strong>Tirocini & Stage:</strong> convenzioni quadro con tutor accademico</span>
          <span>• <strong>Project Work:</strong> commissiona brief reali a classi di allievi</span>
          <span>• <strong>Career Days:</strong> incontri mirati e stand istituzionale</span>
        </div>
        <div style="margin-top: 20px;">
          <a href="/b2b/talent.php" style="color: var(--gold-light); font-size: 13px; text-decoration: underline; font-weight: 700;">
            Scopri i servizi Talent & Recruiting →
          </a>
        </div>
      </div>

      <!-- 03 INNOVAZIONE -->
      <div class="glass-card" style="padding: 32px; border-top: 3px solid var(--gold-primary);">
        <div style="font-size: 12px; color: var(--gold-light); font-weight: 800; letter-spacing: 2px;">03 — ASSE INNOVAZIONE</div>
        <h3 style="color: #ffffff; font-size: 20px; margin: 8px 0 12px 0; font-family: 'Cinzel', serif;">
          Ricerca Applicata & Laboratori
        </h3>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin-bottom: 16px;">
          Porta problemi industriali o gestionali reali dentro CAMPUS per risolverli con ricercatori e comitati scientifici.
        </p>
        <div style="font-size: 12.5px; color: var(--text-dim); display: flex; flex-direction: column; gap: 6px;">
          <span>• <strong>Research Challenge:</strong> bandi e sfide di innovazione</span>
          <span>• <strong>Laboratori Territoriali:</strong> sperimentazioni sul campo (Porto Viro)</span>
          <span>• <strong>Trasferimento Tecnologico:</strong> prototipi e perizie scientifiche</span>
          <span>• <strong>Borse di Studio Intitolate:</strong> supporto a tesi applicate</span>
        </div>
        <div style="margin-top: 20px;">
          <a href="/research.php" style="color: var(--gold-light); font-size: 13px; text-decoration: underline; font-weight: 700;">
            Esplora i Laboratori di Ricerca →
          </a>
        </div>
      </div>

      <!-- 04 VISIBILITÀ -->
      <div class="glass-card" style="padding: 32px; border-top: 3px solid var(--gold-primary);">
        <div style="font-size: 12px; color: var(--gold-light); font-weight: 800; letter-spacing: 2px;">04 — ASSE VISIBILITÀ</div>
        <h3 style="color: #ffffff; font-size: 20px; margin: 8px 0 12px 0; font-family: 'Cinzel', serif;">
          Visibilità & Reputazione Autorevole
        </h3>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin-bottom: 16px;">
          Fatti conoscere da una platea qualificata di professionisti iscritti a ordini, dirigenti e imprese committenti.
        </p>
        <div style="font-size: 12.5px; color: var(--text-dim); display: flex; flex-direction: column; gap: 6px;">
          <span>• <strong>Pagina Partner Ufficiale:</strong> scheda permanente con backlink</span>
          <span>• <strong>Partner Wall Istituzionale:</strong> presenza fisica e digitale</span>
          <span>• <strong>Presenza nei Programmi:</strong> menzione su master e corsi</span>
          <span>• <strong>QR Attribution Tracciato:</strong> misurazione contatti e conversioni</span>
        </div>
        <div style="margin-top: 20px;">
          <a href="/b2b/partner.php" style="color: var(--gold-light); font-size: 13px; text-decoration: underline; font-weight: 700;">
            Diventa Partner Ufficiale →
          </a>
        </div>
      </div>

      <!-- 05 COMUNICAZIONE -->
      <div class="glass-card" style="padding: 32px; border-top: 3px solid var(--gold-primary);">
        <div style="font-size: 12px; color: var(--gold-light); font-weight: 800; letter-spacing: 2px;">05 — ASSE COMUNICAZIONE</div>
        <h3 style="color: #ffffff; font-size: 20px; margin: 8px 0 12px 0; font-family: 'Cinzel', serif;">
          Storytelling Istituzionale & Media
        </h3>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin-bottom: 16px;">
          Aiutiamo l'impresa ad avere qualcosa di autentico e prestigioso da raccontare a clienti, banche e stakeholder.
        </p>
        <div style="font-size: 12.5px; color: var(--text-dim); display: flex; flex-direction: column; gap: 6px;">
          <span>• <strong>Partner Story:</strong> intervista e case study redatto dalla redazione</span>
          <span>• <strong>Press & Social Kit:</strong> grafiche e comunicati certificati</span>
          <span>• <strong>Video Story:</strong> riprese professionali al management aziendale</span>
          <span>• <strong>Report Territoriale:</strong> citazione come co-autore scientifico</span>
        </div>
        <div style="margin-top: 20px;">
          <a href="/b2b/sponsor.php" style="color: var(--gold-light); font-size: 13px; text-decoration: underline; font-weight: 700;">
            Sostieni un'Iniziativa di Valore →
          </a>
        </div>
      </div>

      <!-- 06 APPARTENENZA -->
      <div class="glass-card" style="padding: 32px; border-top: 3px solid var(--gold-primary);">
        <div style="font-size: 12px; color: var(--gold-light); font-weight: 800; letter-spacing: 2px;">06 — ASSE APPARTENENZA</div>
        <h3 style="color: #ffffff; font-size: 20px; margin: 8px 0 12px 0; font-family: 'Cinzel', serif;">
          Rete, Convenzioni & CAMPUS Point
        </h3>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin-bottom: 16px;">
          Non comprare uno spazio effimero: entra in un network vitale con vantaggi reciproci per la community.
        </p>
        <div style="font-size: 12.5px; color: var(--text-dim); display: flex; flex-direction: column; gap: 6px;">
          <span>• <strong>CAMPUS Point:</strong> presidio territoriale con targa ufficiale</span>
          <span>• <strong>Convenzioni & Benefit:</strong> proponi sconti a studenti e docenti</span>
          <span>• <strong>Club Partner:</strong> cene direzionali e tavoli riservati di business</span>
          <span>• <strong>Faculty Corporate:</strong> inserisci i tuoi dirigenti come docenti</span>
        </div>
        <div style="margin-top: 20px;">
          <a href="/b2b/convenzioni.php" style="color: var(--gold-light); font-size: 13px; text-decoration: underline; font-weight: 700;">
            Attiva una Convenzione Aziendale →
          </a>
        </div>
      </div>

    </div>

  </div>
</section>


<!-- MINI CONFIGURATORE MICRO-COMMITMENT: COSA VUOI FAR CRESCERE? -->
<section id="configuratore" class="section" style="background: rgba(15,15,15,0.7); border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);">
  <div class="container" style="max-width: 950px;">
    
    <div style="text-align: center; margin-bottom: 35px;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2.5px; font-weight: 800;">
        Configuratore Interattivo B2B
      </span>
      <h2 style="font-size: clamp(24px, 3.5vw, 36px); color: #ffffff; margin-top: 6px; font-family: 'Cinzel', serif;">
        COSA VUOI FAR CRESCERE <span class="gold-text">NELLA TUA AZIENDA?</span>
      </h2>
      <p style="color: var(--text-muted); font-size: 15px; margin-top: 6px;">
        Rispondi a 4 semplici domande per individuare la combinazione di opportunità più adatta al tuo settore.
      </p>
    </div>

    <div class="glass-card" style="border: 2px solid var(--border-gold); padding: 35px;">
      
      <!-- DOMANDA 1: OBIETTIVO PRINCIPALE -->
      <div style="margin-bottom: 25px;">
        <label class="form-label" style="font-size: 14px; color: #ffffff; font-weight: 700; margin-bottom: 12px; display: block;">
          1. Qual è l'obiettivo prioritario della tua organizzazione?
        </label>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;" id="goal-selector">
          
          <label style="background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); padding: 12px; border-radius: var(--radius-sm); cursor: pointer; display: flex; align-items: center; gap: 10px; transition: border-color 0.2s;">
            <input type="radio" name="cfg_goal" value="PERSONE" checked style="accent-color: var(--gold-primary);">
            <span style="font-size: 13px; color: #ffffff;">Le Mie Persone (Formazione)</span>
          </label>

          <label style="background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); padding: 12px; border-radius: var(--radius-sm); cursor: pointer; display: flex; align-items: center; gap: 10px; transition: border-color 0.2s;">
            <input type="radio" name="cfg_goal" value="TALENTI" style="accent-color: var(--gold-primary);">
            <span style="font-size: 13px; color: #ffffff;">Nuovi Talenti (Recruiting)</span>
          </label>

          <label style="background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); padding: 12px; border-radius: var(--radius-sm); cursor: pointer; display: flex; align-items: center; gap: 10px; transition: border-color 0.2s;">
            <input type="radio" name="cfg_goal" value="BRAND" style="accent-color: var(--gold-primary);">
            <span style="font-size: 13px; color: #ffffff;">Il Mio Brand & Reputazione</span>
          </label>

          <label style="background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); padding: 12px; border-radius: var(--radius-sm); cursor: pointer; display: flex; align-items: center; gap: 10px; transition: border-color 0.2s;">
            <input type="radio" name="cfg_goal" value="INNOVAZIONE" style="accent-color: var(--gold-primary);">
            <span style="font-size: 13px; color: #ffffff;">Capacità di Innovare (R&D)</span>
          </label>

          <label style="background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); padding: 12px; border-radius: var(--radius-sm); cursor: pointer; display: flex; align-items: center; gap: 10px; transition: border-color 0.2s;">
            <input type="radio" name="cfg_goal" value="TERRITORIO" style="accent-color: var(--gold-primary);">
            <span style="font-size: 13px; color: #ffffff;">Il Mio Territorio & Presidio</span>
          </label>

          <label style="background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); padding: 12px; border-radius: var(--radius-sm); cursor: pointer; display: flex; align-items: center; gap: 10px; transition: border-color 0.2s;">
            <input type="radio" name="cfg_goal" value="NETWORK" style="accent-color: var(--gold-primary);">
            <span style="font-size: 13px; color: #ffffff;">Il Mio Network di Relazioni</span>
          </label>

        </div>
      </div>

      <!-- DOMANDE 2, 3, 4 IN GRIGLIA -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 25px;">
        
        <div>
          <label class="form-label" style="font-size: 13px;">2. Settore / Ambito Attività (ATECO)</label>
          <select id="cfg-ateco" class="form-control" style="font-size: 13px;">
            <option value="GENERALE">-- Tutti i Settori Produttivi --</option>
            <?php foreach (array_slice($atecoActivities, 0, 15) as $act): ?>
              <option value="<?= sanitize_output($act['code']) ?>"><?= sanitize_output($act['code']) ?> - <?= sanitize_output(substr($act['desc'], 0, 45)) ?>...</option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="form-label" style="font-size: 13px;">3. Dimensione Aziendale</label>
          <select id="cfg-size" class="form-control" style="font-size: 13px;">
            <option value="STUDIO">Studio Professionale / Micro-Impresa (1-9 addetti)</option>
            <option value="PMI" selected>Piccola/Media Impresa (10-49 addetti)</option>
            <option value="MID_CORP">Media Impresa Consolidata (50-249 addetti)</option>
            <option value="LARGE">Grande Impresa / Gruppo (>250 addetti)</option>
            <option value="ENTE">Ente, Associazione o Fondazione</option>
          </select>
        </div>

        <div>
          <label class="form-label" style="font-size: 13px;">4. Territorio di Interesse Prevalente</label>
          <input type="text" id="cfg-territory" class="form-control" placeholder="es. Rovigo, Delta del Po, Veneto, Nazionale..." style="font-size: 13px;">
        </div>

      </div>

      <!-- OUTPUT DEL CONFIGURATORE -->
      <div id="config-result" style="background: rgba(212,175,55,0.08); border: 1px solid var(--gold-primary); border-radius: var(--radius-sm); padding: 22px; margin-top: 15px;">
        <div style="font-size: 11px; color: var(--gold-light); text-transform: uppercase; font-weight: 800; letter-spacing: 1.5px; margin-bottom: 6px;">
          Raccomandazione Strategica Personalizzata
        </div>
        <h4 id="config-title" style="color: #ffffff; font-size: 18px; margin-bottom: 10px; font-family: 'Cinzel', serif;">
          Percorso Consigliato: Corporate Partner & Corporate Academy
        </h4>
        <p id="config-desc" style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin-bottom: 16px;">
          Per le tue esigenze di crescita delle persone, CAMPUS propone l'analisi dei fabbisogni formativi, l'accesso prepagato ai 2.119 corsi per il tuo team e l'attivazione di tirocini con tutor accademico dedicato.
        </p>
        <div style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap;">
          <a href="/b2b/apply.php" id="config-cta-apply" class="btn-gold" style="font-size: 13px; padding: 10px 22px; font-weight: 700;">
            <?= icon_gold('document', 14) ?> PROCEDI CON QUESTA CONFIGURAZIONE
          </a>
          <a href="/b2b/corporate-academy.php" id="config-cta-detail" class="btn-outline-gold" style="font-size: 13px; padding: 10px 20px;">
            Dettagli Approfonditi →
          </a>
        </div>
      </div>

    </div>

  </div>
</section>


<!-- VALUE LADDER: LIVELLI DI PARTNERSHIP (DA DB, PREZZI NON HARDCODED) -->
<section class="section">
  <div class="container" style="max-width: 1320px;">
    
    <div style="text-align: center; max-width: 800px; margin: 0 auto 50px auto;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Progressione Graduale a Basso Rischio
      </span>
      <h2 style="font-size: clamp(26px, 3.8vw, 42px); color: #ffffff; margin-top: 8px; font-family: 'Cinzel', serif;">
        CAMPUS <span class="gold-text">VALUE LADDER</span>
      </h2>
      <p style="color: var(--text-muted); font-size: 15.5px;">
        Dal primo presidio territoriale fino alla partecipazione al collegio dei soci fondatori: una partnership commisurata alla maturità e agli obiettivi dell'impresa.
      </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
      <?php foreach ($packagesWithComponents as $idx => $pkg): ?>
        <div class="glass-card" style="padding: 28px; display: flex; flex-direction: column; justify-content: space-between; position: relative; <?= $pkg['tier_level'] >= 4 ? 'border: 2px solid var(--border-gold);' : '' ?>">
          
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
              <span style="font-family: monospace; font-size: 11px; color: var(--gold-light); font-weight: bold; text-transform: uppercase;">
                LIVELLO 0<?= $pkg['tier_level'] ?> · <?= sanitize_output($pkg['tier_name']) ?>
              </span>
              <?php if (!empty($pkg['badge'])): ?>
                <span style="background: rgba(212,175,55,0.2); border: 1px solid var(--gold-primary); color: var(--gold-light); font-size: 10px; padding: 3px 8px; border-radius: 10px; font-weight: 700;">
                  <?= sanitize_output($pkg['badge']) ?>
                </span>
              <?php endif; ?>
            </div>

            <h3 style="color: #ffffff; font-size: 22px; font-family: 'Cinzel', serif; margin-bottom: 6px;">
              <?= sanitize_output($pkg['name']) ?>
            </h3>

            <p style="color: var(--text-muted); font-size: 13px; line-height: 1.5; margin-bottom: 18px;">
              <?= sanitize_output($pkg['headline']) ?>
            </p>

            <!-- QUOTA / STATUS (DA DB, MAI HARDCODED) -->
            <div style="background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); padding: 10px 14px; border-radius: var(--radius-sm); margin-bottom: 18px;">
              <div style="font-size: 10.5px; color: var(--text-dim); text-transform: uppercase; letter-spacing: 1px;">Quota & Contribuzione</div>
              <div style="font-size: 14px; font-weight: 700; color: #ffffff; margin-top: 2px;">
                <?php if ($pkg['price_status'] === 'ACTIVE' && !empty($pkg['price_amount'])): ?>
                  <?= number_format($pkg['price_amount'], 2, ',', '.') ?> <?= $pkg['currency'] ?> / anno
                <?php else: ?>
                  <span style="color: var(--gold-light); font-size: 13px;">Definita su Misura con la Direzione</span>
                <?php endif; ?>
              </div>
            </div>

            <!-- COMPONENTI INCLUSI -->
            <div style="font-size: 12.5px; color: #e2e8f0; display: flex; flex-direction: column; gap: 8px; margin-bottom: 24px;">
              <?php foreach ($pkg['components'] as $comp): ?>
                <div style="display: flex; align-items: flex-start; gap: 8px; line-height: 1.4;">
                  <span style="color: var(--gold-primary); line-height: 1;">✓</span>
                  <span><?= sanitize_output($comp['component_text']) ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <div>
            <a href="/b2b/apply.php?package=<?= urlencode($pkg['code']) ?>" class="<?= $pkg['tier_level'] >= 4 ? 'btn-gold' : 'btn-outline-gold' ?>" style="width: 100%; font-size: 12.5px; padding: 12px; font-weight: 700; text-align: center; display: block; box-sizing: border-box;">
              CANDIDA PER <?= strtoupper(sanitize_output($pkg['name'])) ?>
            </a>
          </div>

        </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>


<!-- FAQ DI COMPRENSIONE & OBIEZIONI B2B -->
<section class="section" style="background: rgba(10,10,10,0.6); border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);">
  <div class="container" style="max-width: 900px;">
    
    <div style="text-align: center; margin-bottom: 40px;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Chiarezza & Deontologia
      </span>
      <h2 style="font-size: clamp(24px, 3.5vw, 36px); color: #ffffff; margin-top: 6px; font-family: 'Cinzel', serif;">
        DOMANDE FREQUENTI <span class="gold-text">SUL B2B CAMPUS</span>
      </h2>
    </div>

    <div style="display: flex; flex-direction: column; gap: 14px;">
      
      <div class="glass-card" style="padding: 22px;">
        <h4 style="color: #ffffff; font-size: 16px; margin-bottom: 8px;">
          Qual è la differenza sostanziale tra una sponsorizzazione classica e l'ingresso in CAMPUS?
        </h4>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin: 0;">
          Una sponsorizzazione tradizionale si limita all'acquisto di visibilità passiva (loghi, banner). In CAMPUS, la tua impresa entra in un circuito organico: riceve formazione certificata per i propri collaboratori, accede a un canale preferenziale per il recruiting di figure qualificate e può proporre casi industriali concreti da sviluppare nei laboratori di ricerca applicata.
        </p>
      </div>

      <div class="glass-card" style="padding: 22px;">
        <h4 style="color: #ffffff; font-size: 16px; margin-bottom: 8px;">
          Un'azienda partner o sponsor può influenzare i contenuti accademici o le valutazioni?
        </h4>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin: 0;">
          <strong>Assolutamente no.</strong> A tutela del valore delle certificazioni e dell'autorevolezza istituzionale, le partnership economiche e le convenzioni non attribuiscono alcun controllo sui contenuti accademici, sui criteri d'esame, sulle deliberazioni del Senato Accademico o sul rilascio delle credenziali. La terzietà scientifica è totale e intangibile.
        </p>
      </div>

      <div class="glass-card" style="padding: 22px;">
        <h4 style="color: #ffffff; font-size: 16px; margin-bottom: 8px;">
          Come viene gestita la parte contrattuale e fiscale del contributo?
        </h4>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin: 0;">
          Ogni accordo viene formalizzato tramite apposita convenzione o contratto quadro con l'Ente Gestore (LABO TECNIC STUDIO). A seconda della tipologia (formazione aziendale accreditata, ricerca applicata o sponsorizzazione tecnica), la spesa può beneficiare delle deducibilità fiscali di legge e del credito d'imposta per formazione continua e R&D.
        </p>
      </div>

      <div class="glass-card" style="padding: 22px;">
        <h4 style="color: #ffffff; font-size: 16px; margin-bottom: 8px;">
          Quanto tempo richiede l'esame della candidatura aziendale?
        </h4>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin: 0;">
          All'invio della candidatura tramite il modulo telematico viene rilasciato immediatamente il protocollo crittografico <strong>SIC-ID Organization</strong>. Il comitato di indirizzo esamina la congruenza dell'attività (codice ATECO, etica, coerenza) entro 5 giorni lavorativi, concordando un incontro conoscitivo per calibrare il pacchetto.
        </p>
      </div>

    </div>

  </div>
</section>


<!-- CTA FINALE AD ALTA CONVERSIONE -->
<section class="section" style="text-align: center; padding: 90px 20px;">
  <div class="container" style="max-width: 850px;">
    
    <img src="/assets/visual/Campus_Emblema_Transparent.png" alt="CAMPUS" style="width: 90px; margin-bottom: 20px; filter: drop-shadow(0 0 25px rgba(212,175,55,0.45));">

    <h2 style="font-size: clamp(28px, 4.5vw, 44px); color: #ffffff; font-family: 'Cinzel', serif; margin-bottom: 16px;">
      PORTA LA TUA AZIENDA <span class="gold-text">DENTRO CAMPUS</span>
    </h2>

    <p style="font-size: 16px; color: var(--text-muted); line-height: 1.7; margin-bottom: 35px;">
      Un colloquio esplorativo con la Direzione per capire come integrare formazione, talenti e territorio nella strategia di crescita della tua impresa. Nessun vincolo economico iniziale.
    </p>

    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
      <a href="/b2b/apply.php" class="btn-gold" style="font-size: 16px; padding: 18px 42px; font-weight: 800;" data-analytics="b2b_footer_cta">
        <?= icon_gold('institution', 18) ?> CANDIDA LA TUA AZIENDA ORA
      </a>
      <a href="mailto:<?= CAMPUS_EMAIL_INFO ?>?subject=Richiesta%20Informazioni%20B2B%20CAMPUS" class="btn-outline-gold" style="font-size: 16px; padding: 18px 32px;">
        Scrivi alla Direzione B2B
      </a>
    </div>

  </div>
</section>


<!-- LOGICA JS DEL CONFIGURATORE MICRO-COMMITMENT -->
<script>
(function() {
  'use strict';

  var goals = {
    'PERSONE': {
      title: 'Obiettivo Formazione: Corporate Academy & Learning Pass',
      desc: 'Ideale per qualificare dipendenti e collaboratori. Include analisi dei fabbisogni, percorsi su misura tra i 2.119 corsi a catalogo e rilascio di attestati ufficiali.',
      link: '/b2b/corporate-academy.php'
    },
    'TALENTI': {
      title: 'Obiettivo Capitale Umano: Talent Matching & Internship',
      desc: 'Progettato per intercettare i migliori corsisti e professionisti iscritti ad albi. Include screening mirato, convenzione tirocini e partecipazione ai Career Days.',
      link: '/b2b/talent.php'
    },
    'BRAND': {
      title: 'Obiettivo Reputazione: Partner Ufficiale & Storytelling',
      desc: 'Posiziona il tuo marchio a fianco di un ateneo autorevole. Include Partner Page permanente, presenza sul Partner Wall e redazione di una Partner Story certificata.',
      link: '/b2b/partner.php'
    },
    'INNOVAZIONE': {
      title: 'Obiettivo R&D: Research Partner & Innovation Challenge',
      desc: 'Porta una sfida industriale all interno dei nostri laboratori applicati (Polo Delta del Po) e avvia progetti di ricerca congiunta con il comitato scientifico.',
      link: '/research.php'
    },
    'TERRITORIO': {
      title: 'Obiettivo Territorio: CAMPUS Point & Presidio di Distretto',
      desc: 'Attiva la tua sede o studio come punto di riferimento geografico con targa ufficiale, QR Code crittografico e inserimento nella mappa ufficiale dei presidi.',
      link: '/b2b/apply.php?goal=CAMPUS_POINT'
    },
    'NETWORK': {
      title: 'Obiettivo Relazioni: Club Partner & Convenzioni Community',
      desc: 'Partecipa alla rete ristretta degli imprenditori partner con accesso a tavoli direzionali e attiva convenzioni vantaggiose per gli iscritti di CAMPUS.',
      link: '/b2b/convenzioni.php'
    }
  };

  var radioGoals = document.querySelectorAll('input[name="cfg_goal"]');
  var titleEl = document.getElementById('config-title');
  var descEl = document.getElementById('config-desc');
  var ctaDetail = document.getElementById('config-cta-detail');
  var ctaApply = document.getElementById('config-cta-apply');

  function updateConfig() {
    var selected = document.querySelector('input[name="cfg_goal"]:checked');
    if (!selected) return;
    var val = selected.value;
    var data = goals[val] || goals['PERSONE'];

    if (titleEl) titleEl.textContent = data.title;
    if (descEl) descEl.textContent = data.desc;
    if (ctaDetail) ctaDetail.href = data.link;
    if (ctaApply) ctaApply.href = '/b2b/apply.php?goal=' + encodeURIComponent(val);

    if (window.campusAnalytics && window.campusAnalytics.track) {
      window.campusAnalytics.track('b2b_goal_selected', { goal: val });
    }
  }

  radioGoals.forEach(function(r) {
    r.addEventListener('change', updateConfig);
  });

  updateConfig();

})();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
