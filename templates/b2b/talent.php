<?php
/**
 * CAMPUS.CAMP — B2B Talent & Recruiting Qualificato
 * Hero: NON CERCARE SOLO CURRICULUM. TROVA COMPETENZE.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/icons.php';

$pageTitle = 'Talent & Recruiting di Competenze — CAMPUS B2B';
$pageDesc = 'Non cercare solo curriculum. Trova competenze reali. Servizi di talent matching, tirocini convenzionati, project work e recruiting specializzato per la tua impresa.';

$db = Database::getConnection();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero" style="min-height: 75vh; padding: 95px 20px 65px 20px;">
  <div class="hero-overlay"></div>
  <div class="hero-content" style="max-width: 950px;">
    
    <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 18px; border-radius: 20px; background: rgba(212,175,55,0.12); border: 1px solid var(--border-gold); margin-bottom: 20px;">
      <span style="font-size: 11px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Capitale Umano & Selezione
      </span>
    </div>

    <h1 style="font-size: clamp(30px, 4.8vw, 54px); line-height: 1.15; margin-bottom: 22px; font-family: 'Cinzel', serif;">
      NON CERCARE SOLO CURRICULUM.<br>
      <span class="gold-text">TROVA COMPETENZE.</span>
    </h1>

    <p style="font-size: 16.5px; color: var(--text-muted); max-width: 800px; margin: 0 auto 34px auto; line-height: 1.6;">
      L'assunzione tradizionale basata su autocertificazioni è inefficiente e rischiosa. In CAMPUS incontri talenti, professionisti abilitati e tecnici specializzati formati direttamente dai migliori docenti su casi reali.
    </p>

    <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
      <a href="/b2b/apply.php?goal=TALENT" class="btn-gold" style="font-size: 15px; padding: 16px 36px; font-weight: 800;">
        <?= icon_gold('briefcase', 18) ?> ATTIVA UN CANALE TALENT
      </a>
      <a href="/b2b/index.php" class="btn-outline-gold" style="font-size: 15px; padding: 16px 28px;">
        ← Panoramica B2B
      </a>
    </div>

  </div>
</section>

<!-- SERVIZI TALENT PER LE AZIENDE -->
<section class="section">
  <div class="container" style="max-width: 1240px;">

    <div style="text-align: center; max-width: 800px; margin: 0 auto 50px auto;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Dalla Formazione all'Inserimento
      </span>
      <h2 style="font-size: clamp(24px, 3.5vw, 38px); color: #ffffff; margin-top: 6px; font-family: 'Cinzel', serif;">
        GLI STRUMENTI DI TALENT MATCHING
      </h2>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">

      <div class="glass-card" style="padding: 30px;">
        <div style="margin-bottom: 14px;"><?= icon_gold('academic', 28) ?></div>
        <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 10px; font-family: 'Cinzel', serif;">
          Tirocini & Stage Convenzionati
        </h3>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin-bottom: 12px;">
          Convenzione quadro per l inserimento rapido di allievi in stage con tutor accademico dedicato, gestione burocratica semplificata e assicurazione a carico di legge.
        </p>
        <span style="color: var(--gold-light); font-size: 12px; font-weight: bold;">Valutazione diretta sul campo prima dell assunzione.</span>
      </div>

      <div class="glass-card" style="padding: 30px;">
        <div style="margin-bottom: 14px;"><?= icon_gold('briefcase', 28) ?></div>
        <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 10px; font-family: 'Cinzel', serif;">
          Recruiting & Talent Matching
        </h3>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin-bottom: 12px;">
          Screening guidato all interno della nostra banca dati di professionisti e allievi certificati: ricevi solo profili che hanno già superato verifiche di competenza pratiche.
        </p>
        <span style="color: var(--gold-light); font-size: 12px; font-weight: bold;">Riduzione dell 80% dei tempi di selezione.</span>
      </div>

      <div class="glass-card" style="padding: 30px;">
        <div style="margin-bottom: 14px;"><?= icon_gold('star', 28) ?></div>
        <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 10px; font-family: 'Cinzel', serif;">
          Project Work Aziendale
        </h3>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin-bottom: 12px;">
          Assegna una commessa o un problema reale della tua azienda a un gruppo di allievi supervisionato da un docente senior: ottieni soluzioni concrete osservando i candidati al lavoro.
        </p>
        <span style="color: var(--gold-light); font-size: 12px; font-weight: bold;">Innovazione immediata a costo zero per l azienda.</span>
      </div>

      <div class="glass-card" style="padding: 30px;">
        <div style="margin-bottom: 14px;"><?= icon_gold('crown', 28) ?></div>
        <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 10px; font-family: 'Cinzel', serif;">
          Career Day & Incontri Mirati
        </h3>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin-bottom: 12px;">
          Partecipa con stand istituzionale e presentazioni alle giornate di orientamento di CAMPUS: incontra di persona ingegneri, periti, manager e tecnici specializzati.
        </p>
        <span style="color: var(--gold-light); font-size: 12px; font-weight: bold;">Forte attrazione del marchio come datore di lavoro.</span>
      </div>

      <div class="glass-card" style="padding: 30px;">
        <div style="margin-bottom: 14px;"><?= icon_gold('institution', 28) ?></div>
        <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 10px; font-family: 'Cinzel', serif;">
          Faculty Corporate
        </h3>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin-bottom: 12px;">
          I tuoi migliori dirigenti e quadri tecnici possono salire in cattedra come docenti o relatori ospiti, accreditando l azienda come punto di riferimento formativo del settore.
        </p>
        <span style="color: var(--gold-light); font-size: 12px; font-weight: bold;">Employer branding ai massimi livelli accademici.</span>
      </div>

      <div class="glass-card" style="padding: 30px;">
        <div style="margin-bottom: 14px;"><?= icon_gold('shield', 28) ?></div>
        <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 10px; font-family: 'Cinzel', serif;">
          Innovation Challenge
        </h3>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin-bottom: 12px;">
          Lancia un hackathon o un bando a premi riservato agli allievi CAMPUS per sviluppare un prototipo, un software o una soluzione tecnica su misura per la tua impresa.
        </p>
        <span style="color: var(--gold-light); font-size: 12px; font-weight: bold;">Identifica i talenti più brillanti e intraprendenti.</span>
      </div>

    </div>

    <div style="text-align: center; margin-top: 50px;">
      <a href="/b2b/apply.php?goal=TALENT" class="btn-gold" style="font-size: 15px; padding: 16px 36px; font-weight: 700;">
        AVVIA LE RICERCHE DI TALENTO
      </a>
    </div>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
