<?php
/**
 * CAMPUS.CAMP — B2B Convenzioni & Benefit Community
 * Hero: UN VANTAGGIO PER LA COMMUNITY. UN NUOVO CLIENTE PER L'IMPRESA.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/icons.php';

$pageTitle = 'Convenzioni Aziendali & Benefit Community — CAMPUS B2B';
$pageDesc = 'Attiva una convenzione con la community di CAMPUS: professionisti, allievi e docenti. Un vantaggio concreto per gli iscritti, nuovi clienti qualificati per l impresa.';

$db = Database::getConnection();

// Carica convenzioni attive
$conventions = $db->query("
    SELECT * FROM b2b_conventions 
    WHERE status = 'ACTIVE' 
    ORDER BY id DESC LIMIT 12
")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero" style="min-height: 70vh; padding: 90px 20px 60px 20px;">
  <div class="hero-overlay"></div>
  <div class="hero-content" style="max-width: 950px;">
    
    <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 18px; border-radius: 20px; background: rgba(212,175,55,0.12); border: 1px solid var(--border-gold); margin-bottom: 20px;">
      <span style="font-size: 11px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Programma Convenzioni Istituzionali
      </span>
    </div>

    <h1 style="font-size: clamp(28px, 4.5vw, 52px); line-height: 1.2; margin-bottom: 20px; font-family: 'Cinzel', serif;">
      UN VANTAGGIO PER LA COMMUNITY.<br>
      <span class="gold-text">UN NUOVO CLIENTE PER L'IMPRESA.</span>
    </h1>

    <p style="font-size: 16px; color: var(--text-muted); max-width: 780px; margin: 0 auto 32px auto; line-height: 1.6;">
      Proponi sconti, servizi dedicati, voucher ed esperienze esclusive alla rete accademica di CAMPUS: oltre 2.000 corsisti, la Faculty dei docenti, ordini professionali e aziende partner.
    </p>

    <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
      <a href="/b2b/apply.php?goal=CONVENZIONE" class="btn-gold" style="font-size: 14px; padding: 15px 32px; font-weight: 700;">
        <?= icon_gold('star', 16) ?> PROPONI UNA CONVENZIONE
      </a>
      <a href="/b2b/index.php" class="btn-outline-gold" style="font-size: 14px; padding: 15px 28px;">
        ← Torna all'Ecosistema B2B
      </a>
    </div>

  </div>
</section>

<!-- COSA PUÒ PROPORRE L'IMPRESA -->
<section class="section">
  <div class="container" style="max-width: 1200px;">

    <div style="text-align: center; max-width: 800px; margin: 0 auto 45px auto;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Tipologie di Accordo
      </span>
      <h2 style="font-size: clamp(24px, 3.5vw, 36px); color: #ffffff; margin-top: 6px; font-family: 'Cinzel', serif;">
        COSA PUÒ PROPORRE LA TUA AZIENDA
      </h2>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
      
      <div class="glass-card" style="padding: 26px;">
        <div style="margin-bottom: 12px;"><?= icon_gold('briefcase', 28) ?></div>
        <h3 style="color: #ffffff; font-size: 17px; margin-bottom: 8px;">Sconti & Tariffe Agevolate</h3>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Percentuale di sconto fissa su beni, software professionali, strumentazioni tecniche o servizi di consulenza.
        </p>
      </div>

      <div class="glass-card" style="padding: 26px;">
        <div style="margin-bottom: 12px;"><?= icon_gold('academic', 28) ?></div>
        <h3 style="color: #ffffff; font-size: 17px; margin-bottom: 8px;">Convenzione Dipendenti</h3>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Accordo bidirezionale: i tuoi dipendenti accedono ai corsi CAMPUS con quote agevolate, la community accede ai tuoi servizi.
        </p>
      </div>

      <div class="glass-card" style="padding: 26px;">
        <div style="margin-bottom: 12px;"><?= icon_gold('star', 28) ?></div>
        <h3 style="color: #ffffff; font-size: 17px; margin-bottom: 8px;">Accesso Prioritario / VIP</h3>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Canale di supporto riservato, ingressi prioritari a workshop o condizioni privilegiate di fornitura.
        </p>
      </div>

      <div class="glass-card" style="padding: 26px;">
        <div style="margin-bottom: 12px;"><?= icon_gold('crown', 28) ?></div>
        <h3 style="color: #ffffff; font-size: 17px; margin-bottom: 8px;">Esperienze & Territorio</h3>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Attività nel distretto del Delta del Po, ospitalità, visite aziendali guidate per i corsisti dei master.
        </p>
      </div>

    </div>

    <!-- CARATTERISTICHE DI OGNI CONVENZIONE -->
    <div class="glass-card" style="margin-top: 40px; padding: 35px; border: 1px solid var(--border-gold);">
      <h3 style="color: #ffffff; font-size: 20px; font-family: 'Cinzel', serif; margin-bottom: 16px;">
        Tracciamento Digitale & Redemption con QR Code
      </h3>
      <p style="color: var(--text-muted); font-size: 14.5px; line-height: 1.7; margin-bottom: 20px;">
        Ogni convenzione approvata dalla Direzione viene dotata di: identificativo univoco, scheda aziendale con target verificato, condizioni di validità temporale e <strong>QR Code di redenzione tracciato</strong> per monitorare accessi e conversioni reali.
      </p>
      <div style="display: flex; gap: 20px; font-size: 13px; color: var(--gold-light); flex-wrap: wrap;">
        <span>✓ Attribuzione certa dell'ordine</span>
        <span>✓ Zero commissioni di intermediazione</span>
        <span>✓ Pubblicazione nel registro convenzioni</span>
        <span>✓ Tutela del decoro e della correttezza commerciale</span>
      </div>
    </div>

  </div>
</section>

<!-- CTA -->
<section class="section" style="text-align: center; padding: 60px 20px 80px 20px;">
  <div class="container" style="max-width: 800px;">
    <h3 style="color: #ffffff; font-size: 24px; font-family: 'Cinzel', serif; margin-bottom: 14px;">
      Vuoi attivare una convenzione per i tuoi prodotti o servizi?
    </h3>
    <a href="/b2b/apply.php?goal=CONVENZIONE" class="btn-gold" style="font-size: 14px; padding: 15px 36px; font-weight: 700;">
      CANDIDA ORA LA TUA CONVENZIONE
    </a>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
