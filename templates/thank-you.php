<?php
/**
 * CAMPUS.CAMP — Pagina di Ricevuta e Conferma Candidatura
 * Momento di Gratificazione Accademica:
 * "LA TUA CANDIDATURA È ENTRATA IN CAMPUS."
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/icons.php';

$sicId = trim($_GET['sic_id'] ?? '');
$appCode = trim($_GET['code'] ?? '');

$pageTitle = 'La Tua Candidatura è Entrata in CAMPUS — SIC-ID Ufficiale';
$pageDesc = 'Ricevuta ufficiale di deposito della candidatura accademica CAMPUS con emissione del codice SIC-ID.';

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top: 55px; padding-bottom: 80px;">
  <div class="container" style="max-width: 840px; text-align: center;">

    <div class="glass-card" style="border: 2px solid var(--border-gold); padding: 50px 35px; box-shadow: var(--gold-glow);">
      
      <img src="/assets/visual/Campus_Emblema_Transparent.png" alt="CAMPUS" style="width: 95px; margin-bottom: 20px; filter: drop-shadow(0 0 25px rgba(212,175,55,0.45));">

      <div style="font-size: 11.5px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 2.8px; font-weight: 800; margin-bottom: 12px; display: inline-flex; align-items: center; gap: 8px;">
        <?= icon_gold('institution', 16) ?> ATTO ACCADEMICO REGISTRATO
      </div>

      <h1 style="font-size: clamp(28px, 4.5vw, 42px); line-height: 1.15; margin: 0 0 16px 0; font-family: 'Cinzel', serif; color: #ffffff;">
        LA TUA CANDIDATURA<br>
        <span class="gold-text">È ENTRATA IN CAMPUS.</span>
      </h1>

      <p style="color: var(--text-muted); font-size: 16px; max-width: 680px; margin: 0 auto 32px auto; line-height: 1.65;">
        Abbiamo acquisito i tuoi dati e il tuo curriculum negli archivi centrali dell'Ateneo. Da questo istante la tua pratica è tutelata e associata al tuo identificativo crittografico permanente.
      </p>

      <!-- BOX SIC-ID CANONICO DI PREGIO -->
      <div style="background: rgba(0, 0, 0, 0.55); border: 2px solid var(--gold-primary); border-radius: var(--radius-md); padding: 26px 20px; margin-bottom: 35px; box-shadow: 0 0 30px rgba(212,175,55,0.25);">
        <div style="font-size: 11.5px; text-transform: uppercase; letter-spacing: 2px; color: var(--gold-light); margin-bottom: 8px; font-weight: 700;">
          Il Tuo Identificativo Digitale Univoco e Immutabile
        </div>
        <div style="font-size: clamp(22px, 4vw, 34px); font-family: monospace; font-weight: 900; letter-spacing: 2px; color: #ffffff;">
          <?= !empty($sicId) ? sanitize_output($sicId) : 'SIC-ID-EMESSO' ?>
        </div>
        <div style="margin-top: 10px; font-size: 12px; color: var(--text-dim); display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
          <span>Stato: <strong style="color: var(--gold-light);">IN VALUTAZIONE</strong></span>
          <span>Codice Deposito: <strong style="color: #ffffff;"><?= !empty($appCode) ? sanitize_output($appCode) : 'APP-' . date('Ymd') ?></strong></span>
          <span>Data Deposito: <strong style="color: #ffffff;"><?= date('d/m/Y H:i') ?></strong></span>
        </div>
      </div>

      <!-- TIMELINE DEI PROSSIMI PASSI (CERTEZZA DEI TEMPI) -->
      <div style="text-align: left; background: rgba(255,255,255,0.02); border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); padding: 26px; margin-bottom: 35px;">
        <h3 style="color: #ffffff; font-size: 16px; margin-bottom: 16px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px; display: flex; align-items: center; gap: 8px; font-family: 'Cinzel', serif;">
          <?= icon_gold('document', 16) ?> Cosa Succede Adesso: I Prossimi Passi
        </h3>
        
        <div style="display: flex; gap: 14px; margin-bottom: 16px;">
          <span style="color: var(--gold-primary); font-size: 16px; font-weight: 800; font-family: monospace;">01.</span>
          <div style="font-size: 13.5px; color: var(--text-muted); line-height: 1.5;">
            <strong style="color: #ffffff;">Esame dei Titoli (entro 15 giorni lavorativi):</strong> La Commissione Scientifica di Dipartimento esamina il tuo CV, le cattedre richieste e i requisiti deontologici.
          </div>
        </div>

        <div style="display: flex; gap: 14px; margin-bottom: 16px;">
          <span style="color: var(--gold-primary); font-size: 16px; font-weight: 800; font-family: monospace;">02.</span>
          <div style="font-size: 13.5px; color: var(--text-muted); line-height: 1.5;">
            <strong style="color: #ffffff;">Colloquio Conoscitivo:</strong> I candidati idonei ricevono un invito telefonico o via email per concordare i moduli didattici, la pianificazione e i compensi orari.
          </div>
        </div>

        <div style="display: flex; gap: 14px;">
          <span style="color: var(--gold-primary); font-size: 16px; font-weight: 800; font-family: monospace;">03.</span>
          <div style="font-size: 13.5px; color: var(--text-muted); line-height: 1.5;">
            <strong style="color: #ffffff;">Iscrizione all'Albo Docenti Ufficiale:</strong> Formalizzazione contrattuale dell'incarico retribuito e attivazione delle credenziali di docenza.
          </div>
        </div>
      </div>

      <!-- AZIONI PRIMARIE & SECONDARIE -->
      <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
        <a href="/status.php?sic_id=<?= urlencode($sicId) ?>" class="btn-gold" style="font-size: 14px; padding: 13px 26px; display: inline-flex; align-items: center; gap: 8px;">
          <?= icon_gold('search', 16) ?> VERIFICA LA TUA CANDIDATURA
        </a>
        <a href="/courses.php" class="btn-outline-gold" style="font-size: 14px; padding: 13px 26px; display: inline-flex; align-items: center; gap: 8px;">
          <?= icon_gold('academic', 16) ?> ESPLORA I PERCORSI (2.119)
        </a>
        <a href="/" class="btn-outline-gold" style="font-size: 14px; padding: 13px 26px;">
          SCOPRI CAMPUS
        </a>
        <button onclick="window.print()" class="btn-outline-gold" style="font-size: 14px; padding: 13px 22px; display: inline-flex; align-items: center; gap: 8px;">
          <?= icon_gold('print', 15) ?> Salva Ricevuta
        </button>
      </div>

    </div>

  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
