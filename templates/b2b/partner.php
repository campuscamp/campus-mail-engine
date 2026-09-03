<?php
/**
 * CAMPUS.CAMP — B2B Diventa Partner
 * Hero: NON ESSERE UNO SPONSOR. DIVENTA PARTE DELL'ECOSISTEMA.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/icons.php';

$pageTitle = 'Diventa Partner Ufficiale — CAMPUS B2B';
$pageDesc = 'Non essere un semplice sponsor: diventa parte dell ecosistema CAMPUS. Partnership territoriali, formative, tecniche, di ricerca e corporate.';

$db = Database::getConnection();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero" style="min-height: 75vh; padding: 95px 20px 65px 20px;">
  <div class="hero-overlay"></div>
  <div class="hero-content" style="max-width: 950px;">
    
    <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 18px; border-radius: 20px; background: rgba(212,175,55,0.12); border: 1px solid var(--border-gold); margin-bottom: 20px;">
      <span style="font-size: 11px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Alleanze Istituzionali
      </span>
    </div>

    <h1 style="font-size: clamp(30px, 4.8vw, 54px); line-height: 1.15; margin-bottom: 22px; font-family: 'Cinzel', serif;">
      NON ESSERE UNO SPONSOR.<br>
      <span class="gold-text">DIVENTA PARTE DELL'ECOSISTEMA.</span>
    </h1>

    <p style="font-size: 16.5px; color: var(--text-muted); max-width: 800px; margin: 0 auto 34px auto; line-height: 1.6;">
      Una partnership con CAMPUS non è una transazione pubblicitaria: è un'alleanza continuativa che integra il tuo know-how aziendale nei percorsi di studio, nella ricerca e nello sviluppo del tessuto economico.
    </p>

    <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
      <a href="/b2b/apply.php?goal=PARTNER" class="btn-gold" style="font-size: 15px; padding: 16px 36px; font-weight: 800;">
        <?= icon_gold('institution', 18) ?> CANDIDA LA TUA AZIENDA
      </a>
      <a href="/b2b/index.php" class="btn-outline-gold" style="font-size: 15px; padding: 16px 28px;">
        Esplora la Value Ladder
      </a>
    </div>

  </div>
</section>

<!-- LE 7 FORME DI PARTNERSHIP IN CAMPUS -->
<section class="section">
  <div class="container" style="max-width: 1240px;">

    <div style="text-align: center; max-width: 800px; margin: 0 auto 50px auto;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Integrazione su Misura
      </span>
      <h2 style="font-size: clamp(24px, 3.5vw, 38px); color: #ffffff; margin-top: 6px; font-family: 'Cinzel', serif;">
        LE 7 TIPOLOGIE DI PARTNERSHIP
      </h2>
      <p style="color: var(--text-muted); font-size: 15px;">
        Individua il livello di raccordo più coerente con le tue competenze e la tua presenza geografica.
      </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 20px;">

      <div class="glass-card" style="padding: 28px; border-left: 3px solid var(--gold-primary);">
        <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 8px; font-family: 'Cinzel', serif;">
          01. Partner Territoriale & CAMPUS Point
        </h3>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Radica la presenza di CAMPUS nella tua provincia o comune: agisci come presidio di orientamento, informazione e punto di riferimento per ordini e professionisti locali.
        </p>
      </div>

      <div class="glass-card" style="padding: 28px; border-left: 3px solid var(--gold-primary);">
        <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 8px; font-family: 'Cinzel', serif;">
          02. Partner Formativo & Didattico
        </h3>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Co-progetta corsi di specializzazione e master executive: apporta casi pratici, docenti del tuo settore e ottieni percorsi formativi di eccellenza per i tuoi quadri.
        </p>
      </div>

      <div class="glass-card" style="padding: 28px; border-left: 3px solid var(--gold-primary);">
        <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 8px; font-family: 'Cinzel', serif;">
          03. Partner Tecnico & Strumentale
        </h3>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Fornisci tecnologie, software professionali o strumentazioni per i laboratori didattici: forma gli allievi all uso delle tue soluzioni prima del loro ingresso sul mercato.
        </p>
      </div>

      <div class="glass-card" style="padding: 28px; border-left: 3px solid var(--gold-primary);">
        <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 8px; font-family: 'Cinzel', serif;">
          04. Experience Partner
        </h3>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Ospita visite tecniche aziendali, study tour e workshop immersivi: apri le porte delle tue linee produttive o cantieri ai discenti più meritevoli.
        </p>
      </div>

      <div class="glass-card" style="padding: 28px; border-left: 3px solid var(--gold-primary);">
        <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 8px; font-family: 'Cinzel', serif;">
          05. Research & Lab Partner
        </h3>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Attiva congiuntamente progetti di ricerca applicata, paper scientifici e collaudi sul campo, valorizzando il credito d imposta in Ricerca & Sviluppo.
        </p>
      </div>

      <div class="glass-card" style="padding: 28px; border-left: 3px solid var(--gold-primary);">
        <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 8px; font-family: 'Cinzel', serif;">
          06. Faculty Corporate Partner
        </h3>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Inserisci i tuoi manager, ingegneri e professionisti apicali all interno del corpo docente accademico con moduli didattici e cattedre intitolate.
        </p>
      </div>

      <div class="glass-card" style="padding: 28px; border-left: 3px solid var(--gold-primary); grid-column: span 1 / -1;">
        <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 8px; font-family: 'Cinzel', serif;">
          07. Corporate Strategic Partner & Socio Fondatore
        </h3>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Il massimo livello di integrazione: partecipazione al Comitato di Indirizzo Strategico di CAMPUS, co-intestazione di aule e laboratori, governance congiunta dei programmi formativi di distretto.
        </p>
      </div>

    </div>

    <div style="text-align: center; margin-top: 45px;">
      <a href="/b2b/apply.php?goal=PARTNER" class="btn-gold" style="font-size: 15px; padding: 16px 36px; font-weight: 700;">
        CANDIDA LA TUA AZIENDA COME PARTNER
      </a>
    </div>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
