<?php
/**
 * CAMPUS.CAMP — B2B Corporate Academy
 * Hero: LE COMPETENZE CHE SERVIRANNO DOMANI POSSONO INIZIARE OGGI.
 * Pipeline: AZIENDA -> FABBISOGNI -> SKILL GAP -> PERSONE -> PERCORSI -> FORMAZIONE -> ASSESSMENT -> OUTCOME
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/icons.php';

$pageTitle = 'Corporate Academy Aziendale su Misura — CAMPUS B2B';
$pageDesc = 'Progetta la Corporate Academy della tua impresa con l ateneo CAMPUS. Pipeline scientifica: fabbisogni, skill gap, percorsi, formazione certificata e outcome misurabili.';

$db = Database::getConnection();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero" style="min-height: 75vh; padding: 95px 20px 65px 20px;">
  <div class="hero-overlay"></div>
  <div class="hero-content" style="max-width: 1000px;">
    
    <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 18px; border-radius: 20px; background: rgba(212,175,55,0.12); border: 1px solid var(--border-gold); margin-bottom: 20px;">
      <span style="font-size: 11px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Alta Formazione per l'Impresa
      </span>
    </div>

    <h1 style="font-size: clamp(30px, 4.8vw, 54px); line-height: 1.15; margin-bottom: 22px; font-family: 'Cinzel', serif;">
      LE COMPETENZE CHE SERVIRANNO DOMANI<br>
      <span class="gold-text">POSSONO INIZIARE OGGI.</span>
    </h1>

    <p style="font-size: 16.5px; color: var(--text-muted); max-width: 820px; margin: 0 auto 34px auto; line-height: 1.6;">
      Trasforma la formazione aziendale da mero obbligo burocratico in leva primaria di competitività e retention. Progettiamo l'accademia interna della tua impresa attingendo a 2.119 insegnamenti e al corpo docente di CAMPUS.
    </p>

    <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
      <a href="/b2b/apply.php?goal=CORPORATE_ACADEMY" class="btn-gold" style="font-size: 15px; padding: 16px 36px; font-weight: 800;">
        <?= icon_gold('academic', 18) ?> PROGETTA LA TUA CORPORATE ACADEMY
      </a>
      <a href="/courses.php" class="btn-outline-gold" style="font-size: 15px; padding: 16px 28px;">
        Esplora il Catalogo Corsi (2.119)
      </a>
    </div>

  </div>
</section>

<!-- LA PIPELINE DELLA CORPORATE ACADEMY -->
<section class="section" style="background: rgba(10,10,10,0.6); border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);">
  <div class="container" style="max-width: 1240px;">

    <div style="text-align: center; max-width: 800px; margin: 0 auto 50px auto;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Metodologia Accademica Rigorosa
      </span>
      <h2 style="font-size: clamp(24px, 3.5vw, 38px); color: #ffffff; margin-top: 6px; font-family: 'Cinzel', serif;">
        LA NOSTRA PIPELINE IN <span class="gold-text">8 FASI STRATEGICHE</span>
      </h2>
      <p style="color: var(--text-muted); font-size: 15px;">
        Un metodo collaudato che garantisce l'allineamento tra obiettivi di bilancio e crescita delle persone.
      </p>
    </div>

    <!-- PIPELINE GRIGLIA -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
      
      <div class="glass-card" style="padding: 22px; border-left: 3px solid var(--gold-primary);">
        <div style="font-family: monospace; color: var(--gold-light); font-size: 12px; font-weight: bold;">FASE 01</div>
        <h4 style="color: #ffffff; font-size: 16px; margin: 6px 0 8px 0;">Azienda & Visione</h4>
        <p style="color: var(--text-muted); font-size: 13px; line-height: 1.5; margin: 0;">
          Incontro preliminare per comprendere modello di business, mercati serviti e piani di sviluppo a 3-5 anni.
        </p>
      </div>

      <div class="glass-card" style="padding: 22px; border-left: 3px solid var(--gold-primary);">
        <div style="font-family: monospace; color: var(--gold-light); font-size: 12px; font-weight: bold;">FASE 02</div>
        <h4 style="color: #ffffff; font-size: 16px; margin: 6px 0 8px 0;">Fabbisogni Formativi</h4>
        <p style="color: var(--text-muted); font-size: 13px; line-height: 1.5; margin: 0;">
          Rilevazione puntuale delle carenze operative, conformità normative (es. Sicurezza, ESG) e nuove mansioni.
        </p>
      </div>

      <div class="glass-card" style="padding: 22px; border-left: 3px solid var(--gold-primary);">
        <div style="font-family: monospace; color: var(--gold-light); font-size: 12px; font-weight: bold;">FASE 03</div>
        <h4 style="color: #ffffff; font-size: 16px; margin: 6px 0 8px 0;">Skill Gap Analysis</h4>
        <p style="color: var(--text-muted); font-size: 13px; line-height: 1.5; margin: 0;">
          Mappatura oggettiva tra le competenze attualmente presenti in organico e quelle richieste dal settore.
        </p>
      </div>

      <div class="glass-card" style="padding: 22px; border-left: 3px solid var(--gold-primary);">
        <div style="font-family: monospace; color: var(--gold-light); font-size: 12px; font-weight: bold;">FASE 04</div>
        <h4 style="color: #ffffff; font-size: 16px; margin: 6px 0 8px 0;">Target & Ruoli</h4>
        <p style="color: var(--text-muted); font-size: 13px; line-height: 1.5; margin: 0;">
          Segmentazione dei destinatari: operai specializzati, quadri, responsabili di reparto, neo-assunti o dirigenti.
        </p>
      </div>

      <div class="glass-card" style="padding: 22px; border-left: 3px solid var(--gold-primary);">
        <div style="font-family: monospace; color: var(--gold-light); font-size: 12px; font-weight: bold;">FASE 05</div>
        <h4 style="color: #ffffff; font-size: 16px; margin: 6px 0 8px 0;">Percorsi Didattici</h4>
        <p style="color: var(--text-muted); font-size: 13px; line-height: 1.5; margin: 0;">
          Composizione del syllabus su misura combinando moduli dei 2.119 corsi CAMPUS ed esercitazioni su casi propri.
        </p>
      </div>

      <div class="glass-card" style="padding: 22px; border-left: 3px solid var(--gold-primary);">
        <div style="font-family: monospace; color: var(--gold-light); font-size: 12px; font-weight: bold;">FASE 06</div>
        <h4 style="color: #ffffff; font-size: 16px; margin: 6px 0 8px 0;">Erogazione Flessibile</h4>
        <p style="color: var(--text-muted); font-size: 13px; line-height: 1.5; margin: 0;">
          Aule virtuali sincrone, lezioni registrate, workshop in azienda o nei laboratori del Polo Delta del Po.
        </p>
      </div>

      <div class="glass-card" style="padding: 22px; border-left: 3px solid var(--gold-primary);">
        <div style="font-family: monospace; color: var(--gold-light); font-size: 12px; font-weight: bold;">FASE 07</div>
        <h4 style="color: #ffffff; font-size: 16px; margin: 6px 0 8px 0;">Assessment & Certificazione</h4>
        <p style="color: var(--text-muted); font-size: 13px; line-height: 1.5; margin: 0;">
          Verifiche scientifiche dell apprendimento e rilascio di attestati con protocollo immutabile SIC-ID.
        </p>
      </div>

      <div class="glass-card" style="padding: 22px; border-left: 3px solid var(--gold-primary);">
        <div style="font-family: monospace; color: var(--gold-light); font-size: 12px; font-weight: bold;">FASE 08</div>
        <h4 style="color: #ffffff; font-size: 16px; margin: 6px 0 8px 0;">Outcome & ROI</h4>
        <p style="color: var(--text-muted); font-size: 13px; line-height: 1.5; margin: 0;">
          Misurazione dell impatto sul lavoro effettivo, riduzione errori operativi e rendicontazione dei fondi interprofessionali.
        </p>
      </div>

    </div>

  </div>
</section>

<!-- BENEFICI CORPORATE -->
<section class="section">
  <div class="container" style="max-width: 1100px;">
    <div class="glass-card" style="padding: 40px; border: 2px solid var(--border-gold);">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 35px; align-items: center;">
        <div>
          <span style="font-size: 11px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
            Vantaggi Istituzionali
          </span>
          <h3 style="color: #ffffff; font-size: 24px; font-family: 'Cinzel', serif; margin: 8px 0 14px 0;">
            Perché Affidare l'Academy a CAMPUS
          </h3>
          <p style="color: var(--text-muted); font-size: 14.5px; line-height: 1.7; margin-bottom: 16px;">
            Costruire un'accademia aziendale interna da zero comporta costi enormi di accreditamento, segreteria e reperimento docenti qualificati. 
          </p>
          <p style="color: var(--text-muted); font-size: 14.5px; line-height: 1.7;">
            Con CAMPUS la tua impresa ottiene subito: una piattaforma LMS conforme agli standard più elevati, oltre 2.000 moduli didattici già strutturati, docenti abilitati e la possibilità di finanziare la formazione tramite Fondi Paritetici Interprofessionali (Fondimpresa, For.Te, Fondo Professioni, ecc.).
          </p>
        </div>

        <div style="background: rgba(0,0,0,0.5); border: 1px solid var(--border-subtle); padding: 26px; border-radius: var(--radius-sm);">
          <div style="font-size: 13px; color: var(--gold-primary); font-weight: 700; margin-bottom: 14px; text-transform: uppercase;">
            Cosa Comprende l'Accordo:
          </div>
          <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13.5px; color: #ffffff;">
            <div>✓ Piattaforma didattica personalizzata con logo aziendale</div>
            <div>✓ Docenti Faculty dedicati e tutoraggio continuo</div>
            <div>✓ Dashboard HR per monitorare frequenza ed esiti</div>
            <div>✓ Attestati con protocollo crittografico SIC-ID</div>
            <div>✓ Piena deducibilità fiscale dei costi di formazione</div>
          </div>
          <div style="margin-top: 24px;">
            <a href="/b2b/apply.php?goal=CORPORATE_ACADEMY" class="btn-gold" style="width: 100%; text-align: center; display: block; font-size: 13px; font-weight: 700; box-sizing: border-box; padding: 12px;">
              RICHIEDI UN PREVENTIVO DI PROGETTO
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
