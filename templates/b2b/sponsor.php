<?php
/**
 * CAMPUS.CAMP — B2B Diventa Sponsor & Mecenate Accademico
 * Hero: SOSTIENI QUELLO CHE VUOI VEDERE CRESCERE.
 * Include disclaimer etico di indipendenza accademica
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/icons.php';

$pageTitle = 'Sponsorizzazioni & Mecenatismo Accademico — CAMPUS B2B';
$pageDesc = 'Sostieni la crescita dei talenti, le borse di studio e la ricerca applicata in CAMPUS. Sponsorship ad alto impatto etico, scientifico e reputazionale.';

$db = Database::getConnection();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero" style="min-height: 75vh; padding: 95px 20px 65px 20px;">
  <div class="hero-overlay"></div>
  <div class="hero-content" style="max-width: 950px;">
    
    <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 18px; border-radius: 20px; background: rgba(212,175,55,0.12); border: 1px solid var(--border-gold); margin-bottom: 20px;">
      <span style="font-size: 11px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Sponsorship & Mecenatismo
      </span>
    </div>

    <h1 style="font-size: clamp(30px, 4.8vw, 54px); line-height: 1.15; margin-bottom: 22px; font-family: 'Cinzel', serif;">
      SOSTIENI QUELLO CHE<br>
      <span class="gold-text">VUOI VEDERE CRESCERE.</span>
    </h1>

    <p style="font-size: 16.5px; color: var(--text-muted); max-width: 800px; margin: 0 auto 34px auto; line-height: 1.6;">
      Associa il prestigio della tua impresa a progetti formativi di eccellenza, borse di studio per giovani meritevoli e laboratori territoriali. Un investimento a beneficio della collettività e della competitività d'impresa.
    </p>

    <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
      <a href="/b2b/apply.php?goal=SPONSOR" class="btn-gold" style="font-size: 15px; padding: 16px 36px; font-weight: 800;">
        <?= icon_gold('crown', 18) ?> SOSTIENI UN PROGETTO
      </a>
      <a href="/b2b/index.php" class="btn-outline-gold" style="font-size: 15px; padding: 16px 28px;">
        ← Panoramica B2B
      </a>
    </div>

  </div>
</section>

<!-- COSA PUOI SOSTENERE IN CAMPUS -->
<section class="section">
  <div class="container" style="max-width: 1240px;">

    <div style="text-align: center; max-width: 800px; margin: 0 auto 50px auto;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Opportunità di Sostegno
      </span>
      <h2 style="font-size: clamp(24px, 3.5vw, 38px); color: #ffffff; margin-top: 6px; font-family: 'Cinzel', serif;">
        LE AREE DI INTERVENTO SPONSOR
      </h2>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
      
      <div class="glass-card" style="padding: 26px;">
        <div style="margin-bottom: 12px;"><?= icon_gold('crown', 28) ?></div>
        <h3 style="color: #ffffff; font-size: 17px; margin-bottom: 8px;">Borse di Studio Nominative</h3>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Finanzia l accesso ai master per studenti meritevoli con ISEE certificato: cerimonia di consegna ufficiale, rassegna stampa e visibilità etica d impresa.
        </p>
      </div>

      <div class="glass-card" style="padding: 26px;">
        <div style="margin-bottom: 12px;"><?= icon_gold('academic', 28) ?></div>
        <h3 style="color: #ffffff; font-size: 17px; margin-bottom: 8px;">Cattedre & Scuole Specialistiche</h3>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Intesta una cattedra o un intero modulo formativo al brand o alla memoria di un fondatore: presenza nei titoli di testa e nei diplomi rilasciati.
        </p>
      </div>

      <div class="glass-card" style="padding: 26px;">
        <div style="margin-bottom: 12px;"><?= icon_gold('institution', 28) ?></div>
        <h3 style="color: #ffffff; font-size: 17px; margin-bottom: 8px;">Laboratori & Tecnologia</h3>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Sostieni l allestimento di aule tecnologiche, strumentazioni di misura e banchi prova nel Polo Territoriale di Porto Viro e nei centri di ricerca.
        </p>
      </div>

      <div class="glass-card" style="padding: 26px;">
        <div style="margin-bottom: 12px;"><?= icon_gold('star', 28) ?></div>
        <h3 style="color: #ffffff; font-size: 17px; margin-bottom: 8px;">Eventi & Convegni Nazionali</h3>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Title sponsorship delle conferenze annuali con crediti formativi professionali (CFP) per ordini, collegi e associazioni di categoria.
        </p>
      </div>

    </div>

    <!-- CLAUSOLA INDIPENDENZA ACCADEMICA (RIGOROSA) -->
    <div class="glass-card" style="margin-top: 40px; padding: 32px; border: 2px solid var(--border-gold); background: rgba(0,0,0,0.65);">
      <div style="display: flex; align-items: flex-start; gap: 18px;">
        <div><?= icon_gold('shield', 36) ?></div>
        <div>
          <h4 style="color: var(--gold-light); font-size: 17px; margin-bottom: 8px; font-family: 'Cinzel', serif;">
            Tutela Rigorosa dell'Indipendenza Scientifica
          </h4>
          <p style="color: #ffffff; font-size: 14.5px; line-height: 1.7; margin: 0; font-style: italic;">
            “Le partnership economiche e le sponsorizzazioni non attribuiscono alcun controllo o condizionamento sui contenuti accademici, sulle valutazioni dei docenti, sull esame dei titoli o sulle credenziali rilasciate dall Ateneo.”
          </p>
          <div style="margin-top: 10px; font-size: 12px; color: var(--text-dim);">
            Principio cardine approvato dal Senato Accademico di CAMPUS a garanzia dell autenticità e della terzietà di ogni titolo emesso.
          </div>
        </div>
      </div>
    </div>

    <div style="text-align: center; margin-top: 45px;">
      <a href="/b2b/apply.php?goal=SPONSOR" class="btn-gold" style="font-size: 15px; padding: 16px 36px; font-weight: 700;">
        PROSPETTO SPONSORSHIP CON LA DIREZIONE
      </a>
    </div>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
