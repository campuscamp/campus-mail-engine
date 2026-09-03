<?php
/**
 * CAMPUS.CAMP — B2B Candidatura Azienda / Partner / Sponsor
 * Wizard Conversion-First in 7 Step con calcolo Fit Score e rilascio SIC-ID Organization
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/taxonomy.php';
require_once __DIR__ . '/../includes/icons.php';

$pageTitle = 'Candidatura Azienda & Partner — CAMPUS B2B';
$pageDesc = 'Modulo di accreditamento B2B per imprese, studi e organizzazioni. Procedura telematica con assegnazione del protocollo digitale SIC-ID Organization.';

$db = Database::getConnection();
$error = null;
$submittedData = null;

$atecoActivities = Taxonomy::getAtecoActivities();

// Gestione invio form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $error = 'Sessione di sicurezza scaduta. Ricarica la pagina e riprova.';
    } else {
        // Step 1: Azienda
        $companyName = trim($_POST['company_name'] ?? '');
        $brandName = trim($_POST['brand_name'] ?? '');
        $vatNumber = trim($_POST['vat_number'] ?? '');
        $fiscalCode = strtoupper(trim($_POST['fiscal_code'] ?? ''));
        $atecoCode = trim($_POST['ateco_code'] ?? '');
        $atecoDesc = '';
        foreach ($atecoActivities as $act) {
            if ($act['code'] === $atecoCode) {
                $atecoDesc = $act['desc'];
                break;
            }
        }
        $website = trim($_POST['website'] ?? '');
        $companySize = trim($_POST['company_size'] ?? '');
        $employeesRange = trim($_POST['employees_range'] ?? '');
        $revenueRange = trim($_POST['revenue_range'] ?? '');

        // Step 2: Referente
        $contactName = trim($_POST['contact_name'] ?? '');
        $contactRole = trim($_POST['contact_role'] ?? '');
        $contactEmail = strtolower(trim($_POST['contact_email'] ?? ''));
        $contactPhone = trim($_POST['contact_phone'] ?? '');

        // Step 3: Territorio
        $addressStreet = trim($_POST['address_street'] ?? '');
        $addressCity = trim($_POST['address_city'] ?? '');
        $addressProvince = strtoupper(trim($_POST['address_province'] ?? ''));
        $addressRegion = trim($_POST['address_region'] ?? '');
        $addressCountry = trim($_POST['address_country'] ?? 'Italia');

        // Step 4: Cosa cerchi
        $lookingFor = isset($_POST['looking_for']) ? json_encode((array)$_POST['looking_for'], JSON_UNESCAPED_UNICODE) : '[]';

        // Step 5: Cosa puoi portare
        $bringing = isset($_POST['bringing']) ? json_encode((array)$_POST['bringing'], JSON_UNESCAPED_UNICODE) : '[]';

        // Step 6: Obiettivo
        $goalDescription = trim($_POST['goal_description'] ?? '');

        // Step 7: Consensi
        $consentPrivacy = isset($_POST['consent_privacy']) ? 1 : 0;
        $consentMarketing = isset($_POST['consent_marketing']) ? 1 : 0;

        // Validazione minima essenziale
        if (empty($companyName) || empty($contactName) || empty($contactEmail) || empty($contactPhone)) {
            $error = 'Compilare tutti i campi obbligatori contrassegnati da asterisco.';
        } elseif (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
            $error = 'Indirizzo email referente non valido.';
        } elseif (!$consentPrivacy) {
            $error = 'Il consenso al trattamento dati per la candidatura aziendale è obbligatorio.';
        }

        if (!$error) {
            try {
                $db->beginTransaction();

                // 1. Genera SIC-ID canonico per Organization
                $sicId = generate_canonical_sic_id();
                $appCode = 'CAMPUS-B2B-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(4)));

                // 2. Calcolo AI Lead Fit Score (0-100) basato su parametri
                $score = 50; // base
                if (!empty($atecoCode)) $score += 10;
                if (!empty($website)) $score += 5;
                if (!empty($vatNumber)) $score += 5;
                if ($companySize === 'PMI' || $companySize === 'MID_CORP') $score += 10;
                if ($companySize === 'LARGE') $score += 15;
                if (in_array(strtoupper($addressRegion), ['VENETO', 'EMILIA-ROMAGNA', 'LOMBARDIA'])) $score += 10; // Territorio di prossimità
                $lookingArr = json_decode($lookingFor, true) ?: [];
                if (count($lookingArr) >= 2) $score += 5;
                $score = min(100, $score);

                // 3. Inserimento in b2b_applications
                $stmt = $db->prepare("
                    INSERT INTO b2b_applications (
                        application_code, sic_id, company_name, brand_name, vat_number, fiscal_code,
                        ateco_code, ateco_description, website, company_size, employees_range, revenue_range,
                        contact_name, contact_role, contact_email, contact_phone,
                        address_street, address_city, address_province, address_region, address_country,
                        looking_for, bringing, goal_description, fit_score, pipeline_stage, status
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?,
                        ?, ?, ?, ?, ?, ?,
                        ?, ?, ?, ?,
                        ?, ?, ?, ?, ?,
                        ?, ?, ?, ?, 'NEW', 'SUBMITTED'
                    )
                ");

                $stmt->execute([
                    $appCode, $sicId, $companyName, $brandName, $vatNumber, $fiscalCode,
                    $atecoCode, $atecoDesc, $website, $companySize, $employeesRange, $revenueRange,
                    $contactName, $contactRole, $contactEmail, $contactPhone,
                    $addressStreet, $addressCity, $addressProvince, $addressRegion, $addressCountry,
                    $lookingFor, $bringing, $goalDescription, $score
                ]);
                $b2bAppId = (int)$db->lastInsertId();

                // 4. Registrazione in sic_ids
                $stmtSic = $db->prepare("
                    INSERT OR IGNORE INTO sic_ids (sic_id, person_id, entity_type, status)
                    VALUES (?, ?, 'ORGANIZATION', 'ACTIVE')
                ");
                $stmtSic->execute([$sicId, $b2bAppId]);

                // 5. Audit Log
                $stmtAudit = $db->prepare("
                    INSERT INTO audit_log (action, entity_type, entity_id, details, ip_address, user_agent)
                    VALUES ('B2B_APPLICATION_SUBMITTED', 'ORGANIZATION', ?, ?, ?, ?)
                ");
                $stmtAudit->execute([
                    $sicId,
                    "Candidatura B2B depositata da {$companyName} (Ref: {$contactName}) - Score: {$score}",
                    $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                    $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
                ]);

                $db->commit();

                // Invio email notifica
                $headers = [
                    'MIME-Version: 1.0',
                    'Content-type: text/html; charset=utf-8',
                    'From: CAMPUS Direzione B2B <' . CAMPUS_EMAIL_INFO . '>',
                    'Reply-To: ' . CAMPUS_EMAIL_INFO
                ];
                $mailMsg = "<p>Gentile <strong>{$contactName}</strong> ({$companyName}),</p><p>La candidatura della Sua azienda per l ingresso nell Ecosistema B2B di CAMPUS è stata registrata con successo.</p><p>Protocollo Ufficiale: <strong>{$sicId}</strong><br>Codice Pratica: <strong>{$appCode}</strong></p><p>La Direzione esaminerà la proposta entro 5 giorni lavorativi per organizzare un incontro esplorativo.</p><p>Cordiali saluti,<br><strong>Direzione B2B & Partnership CAMPUS</strong></p>";
                @mail($contactEmail, "[CAMPUS] Ricevuta Candidatura B2B — Protocollo {$sicId}", $mailMsg, implode("\r\n", $headers));

                $submittedData = [
                    'company_name' => $companyName,
                    'sic_id' => $sicId,
                    'app_code' => $appCode,
                    'contact_email' => $contactEmail
                ];

            } catch (Exception $e) {
                if ($db->inTransaction()) $db->rollBack();
                $error = 'Si è verificato un errore tecnico durante il salvataggio: ' . $e->getMessage();
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="section" style="padding-top: 40px; padding-bottom: 70px;">
  <div class="container" style="max-width: 920px;">

    <?php if ($submittedData): ?>
      <!-- SCHEDA DI SUCCESSO E GRATIFICAZIONE -->
      <div class="glass-card" style="padding: 45px 35px; text-align: center; border: 2px solid var(--border-gold); box-shadow: var(--gold-glow);">
        <img src="/assets/visual/Campus_Emblema_Transparent.png" alt="CAMPUS" style="width: 85px; margin-bottom: 20px; filter: drop-shadow(0 0 20px rgba(212,175,55,0.45));">
        
        <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2.5px; font-weight: 800; display: block; margin-bottom: 6px;">
          Procedura Telematica Ufficiale Completata
        </span>

        <h1 style="font-size: clamp(24px, 3.5vw, 36px); color: #ffffff; font-family: 'Cinzel', serif; margin-bottom: 14px;">
          LA TUA AZIENDA È ENTRATA <span class="gold-text">IN CAMPUS</span>
        </h1>

        <p style="color: var(--text-muted); font-size: 15px; max-width: 650px; margin: 0 auto 28px auto; line-height: 1.6;">
          La pratica per <strong><?= sanitize_output($submittedData['company_name']) ?></strong> è stata protocollata negli archivi centrali. Abbiamo emesso l'identificativo digitale immutabile di organizzazione.
        </p>

        <!-- BOX SIC-ID -->
        <div style="background: rgba(0,0,0,0.6); border: 2px solid var(--gold-primary); border-radius: var(--radius-sm); padding: 22px; max-width: 500px; margin: 0 auto 30px auto;">
          <div style="font-size: 11px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 2px; font-weight: bold;">
            Protocollo Crittografico SIC-ID Organization
          </div>
          <div style="font-size: 24px; font-family: monospace; font-weight: 900; color: #ffffff; margin: 8px 0; letter-spacing: 2px;">
            <?= sanitize_output($submittedData['sic_id']) ?>
          </div>
          <div style="font-size: 12px; color: var(--text-dim);">
            Codice Pratica: <strong><?= sanitize_output($submittedData['app_code']) ?></strong>
          </div>
        </div>

        <p style="font-size: 14px; color: #cbd5e1; max-width: 600px; margin: 0 auto 30px auto; line-height: 1.6;">
          Una ricevuta è stata inviata a <strong><?= sanitize_output($submittedData['contact_email']) ?></strong>. La Direzione di CAMPUS ti contatterà entro 5 giorni lavorativi per programmare l'incontro conoscitivo di allineamento strategico.
        </p>

        <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
          <a href="/b2b/index.php" class="btn-gold" style="font-size: 14px; padding: 14px 28px; font-weight: 700;">
            TORNA ALL'ECOSISTEMA B2B
          </a>
          <button onclick="window.print()" class="btn-outline-gold" style="font-size: 14px; padding: 14px 24px;">
            <?= icon_gold('print', 14) ?> Stampa Ricevuta
          </button>
        </div>
      </div>

    <?php else: ?>
      <!-- WIZARD FORM IN 7 PASSAGGI -->

      <div style="text-align: center; margin-bottom: 30px;">
        <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2.5px; font-weight: 800; display: inline-flex; align-items: center; gap: 8px;">
          <?= icon_gold('institution', 15) ?> Protocollo Candidatura Aziendale
        </span>
        <h1 style="font-size: clamp(26px, 4vw, 38px); margin-top: 8px; font-family: 'Cinzel', serif;">
          Candidatura Azienda <span class="gold-text">& Partner CAMPUS</span>
        </h1>
        <p style="color: var(--text-muted); font-size: 15px; margin-top: 6px;">
          Compilazione guidata in 7 passaggi. Assegnazione del protocollo digitale <strong>SIC-ID Organization</strong>.
        </p>
      </div>

      <?php if ($error): ?>
        <div style="background: rgba(0,0,0,0.85); border: 2px solid var(--gold-primary); color: #ffffff; padding: 14px 20px; border-radius: var(--radius-sm); margin-bottom: 25px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
          <?= icon_gold('shield', 20) ?> <span><strong>Attenzione:</strong> <?= sanitize_output($error) ?></span>
        </div>
      <?php endif; ?>

      <div class="glass-card" style="padding: 35px 30px; border: 2px solid var(--border-gold);">
        
        <!-- Progress bar -->
        <div style="margin-bottom: 30px;">
          <div style="display: flex; justify-content: space-between; font-size: 12.5px; color: var(--gold-light); margin-bottom: 8px; font-weight: 600;">
            <span id="b2b-step-indicator">Passaggio 1 di 7: L'Azienda</span>
            <span id="b2b-step-pct">14%</span>
          </div>
          <div style="width: 100%; height: 6px; background: rgba(255,255,255,0.08); border-radius: 3px; overflow: hidden;">
            <div id="b2b-progress-fill" style="width: 14.28%; height: 100%; background: var(--gold-gradient); transition: width 0.3s ease;"></div>
          </div>
        </div>

        <form id="b2b-wizard-form" action="/b2b/apply.php" method="POST">
          <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">

          <!-- STEP 1: AZIENDA -->
          <div class="b2b-step active" data-step="1" data-title="L'Azienda">
            <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
              <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 4px;">1. Dati della Tua Organizzazione</h3>
              <p style="color: var(--text-dim); font-size: 13px; margin: 0;">Ragione sociale, identità fiscale e inquadramento settoriale.</p>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 16px;">
              <div>
                <label class="form-label">Ragione Sociale Esatta *</label>
                <input type="text" name="company_name" class="form-control" placeholder="es. Studio Tecnico Associato Delta / Acme SpA" required>
              </div>
              <div>
                <label class="form-label">Nome Brand / Marchio</label>
                <input type="text" name="brand_name" class="form-control" placeholder="es. Acme Tech">
              </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
              <div>
                <label class="form-label">Partita IVA</label>
                <input type="text" name="vat_number" class="form-control" placeholder="01234567890">
              </div>
              <div>
                <label class="form-label">Codice Fiscale Aziendale</label>
                <input type="text" name="fiscal_code" class="form-control" placeholder="01234567890">
              </div>
            </div>

            <div style="margin-bottom: 16px;">
              <label class="form-label">Codice ATECO Prevalente</label>
              <select name="ateco_code" class="form-control">
                <option value="">-- Seleziona Settore ATECO --</option>
                <?php foreach ($atecoActivities as $act): ?>
                  <option value="<?= sanitize_output($act['code']) ?>"><?= sanitize_output($act['code']) ?> — <?= sanitize_output($act['desc']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 16px;">
              <div>
                <label class="form-label">Sito Web Aziendale</label>
                <input type="url" name="website" class="form-control" placeholder="https://www.azienda.it">
              </div>
              <div>
                <label class="form-label">Dimensione</label>
                <select name="company_size" class="form-control">
                  <option value="STUDIO">Studio / Micro</option>
                  <option value="PMI" selected>PMI</option>
                  <option value="MID_CORP">Media Impresa</option>
                  <option value="LARGE">Grande Impresa</option>
                  <option value="ENTE">Ente / Ordine</option>
                </select>
              </div>
              <div>
                <label class="form-label">Dipendenti</label>
                <select name="employees_range" class="form-control">
                  <option value="1-5">1-5</option>
                  <option value="6-19" selected>6-19</option>
                  <option value="20-49">20-49</option>
                  <option value="50-249">50-249</option>
                  <option value="250+">> 250</option>
                </select>
              </div>
            </div>
          </div>

          <!-- STEP 2: REFERENTE -->
          <div class="b2b-step" data-step="2" data-title="Il Referente">
            <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
              <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 4px;">2. Referente Aziendale & Contatti</h3>
              <p style="color: var(--text-dim); font-size: 13px; margin: 0;">La persona con cui concorderemo l incontro preliminare riservato.</p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
              <div>
                <label class="form-label">Nome e Cognome Referente *</label>
                <input type="text" name="contact_name" class="form-control" placeholder="es. Dott.ssa Laura Bianchi" required>
              </div>
              <div>
                <label class="form-label">Ruolo / Funzione Aziendale</label>
                <input type="text" name="contact_role" class="form-control" placeholder="es. Titolare / HR Director / Amministratore">
              </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
              <div>
                <label class="form-label">Email Professionale *</label>
                <input type="email" name="contact_email" class="form-control" placeholder="laura.bianchi@azienda.it" required>
              </div>
              <div>
                <label class="form-label">Telefono / Cellulare Diretto *</label>
                <input type="tel" name="contact_phone" class="form-control" placeholder="+39 335 1234567" required>
              </div>
            </div>
          </div>

          <!-- STEP 3: TERRITORIO -->
          <div class="b2b-step" data-step="3" data-title="Territorio">
            <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
              <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 4px;">3. Radicamento Geografico</h3>
              <p style="color: var(--text-dim); font-size: 13px; margin: 0;">Ci permette di valutare iniziative di prossimità o presidi territoriali.</p>
            </div>

            <div style="margin-bottom: 16px;">
              <label class="form-label">Indirizzo Sede Principale</label>
              <input type="text" name="address_street" class="form-control" placeholder="Via Roma, 10">
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 16px;">
              <div>
                <label class="form-label">Comune Sede</label>
                <input type="text" name="address_city" class="form-control" placeholder="es. Rovigo">
              </div>
              <div>
                <label class="form-label">Provincia</label>
                <input type="text" name="address_province" class="form-control" maxlength="2" placeholder="RO" style="text-transform: uppercase;">
              </div>
              <div>
                <label class="form-label">Regione</label>
                <input type="text" name="address_region" class="form-control" placeholder="Veneto">
              </div>
            </div>
          </div>

          <!-- STEP 4: COSA CERCHI? -->
          <div class="b2b-step" data-step="4" data-title="Cosa Cerchi">
            <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
              <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 4px;">4. Cosa Cerca la Tua Impresa da CAMPUS?</h3>
              <p style="color: var(--text-dim); font-size: 13px; margin: 0;">Seleziona tutte le aree di interesse per cui desideri attivare collaborazioni.</p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13.5px; color: #ffffff;">
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="looking_for[]" value="Corporate Academy" style="accent-color: var(--gold-primary);"> Corporate Academy per Dipendenti</label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="looking_for[]" value="Talent & Recruiting" style="accent-color: var(--gold-primary);"> Talent Matching & Recruiting</label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="looking_for[]" value="Stage e Tirocini" style="accent-color: var(--gold-primary);"> Attivazione Stage e Tirocini</label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="looking_for[]" value="Ricerca e Laboratori" style="accent-color: var(--gold-primary);"> Ricerca Applicata & Laboratori</label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="looking_for[]" value="Convenzioni Community" style="accent-color: var(--gold-primary);"> Convenzioni & Sconti Community</label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="looking_for[]" value="CAMPUS Point" style="accent-color: var(--gold-primary);"> Diventare CAMPUS Point Territoriale</label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="looking_for[]" value="Sponsorship & Borse" style="accent-color: var(--gold-primary);"> Sponsorizzare Borse di Studio / Aule</label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="looking_for[]" value="Eventi e Convegni" style="accent-color: var(--gold-primary);"> Co-Organizzare Convegni e CFP</label>
            </div>
          </div>

          <!-- STEP 5: COSA PUOI PORTARE? -->
          <div class="b2b-step" data-step="5" data-title="Cosa Porti">
            <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
              <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 4px;">5. Cosa Può Portare la Tua Azienda in CAMPUS?</h3>
              <p style="color: var(--text-dim); font-size: 13px; margin: 0;">L ecosistema cresce per reciprocità di competenze e risorse.</p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13.5px; color: #ffffff;">
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="bringing[]" value="Docenti ed Esperti" style="accent-color: var(--gold-primary);"> Esperti / Tecnici per la Faculty</label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="bringing[]" value="Casi Reali e Project Work" style="accent-color: var(--gold-primary);"> Casi Studio Reali per Project Work</label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="bringing[]" value="Opportunita di Stage" style="accent-color: var(--gold-primary);"> Posti di Tirocinio e Assunzione</label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="bringing[]" value="Strumenti e Tecnologie" style="accent-color: var(--gold-primary);"> Fornitura di Software o Attrezzature</label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="bringing[]" value="Spazi e Sedi" style="accent-color: var(--gold-primary);"> Spazi Operativi per Presidi Territoriali</label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="bringing[]" value="Budget di Sostegno" style="accent-color: var(--gold-primary);"> Contributo Economico / Sponsorship</label>
            </div>
          </div>

          <!-- STEP 6: OBIETTIVO -->
          <div class="b2b-step" data-step="6" data-title="Obiettivo">
            <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
              <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 4px;">6. Qual è l'Obiettivo Primario?</h3>
              <p style="color: var(--text-dim); font-size: 13px; margin: 0;">Descrivi in sintesi cosa vorresti ottenere entrando nell Ecosistema CAMPUS.</p>
            </div>

            <div>
              <textarea name="goal_description" class="form-control" rows="4" placeholder="es. Vorremmo qualificare 20 tecnici sul BIM e sulle nuove normative ambientali, attivare una convenzione per i tirocini estivi e valutare l apertura di un CAMPUS Point presso la nostra sede..."></textarea>
            </div>
          </div>

          <!-- STEP 7: CONSENSI E CONFERMA -->
          <div class="b2b-step" data-step="7" data-title="Conferma">
            <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
              <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 4px;">7. Tutela dei Dati & Deposito Ufficiale</h3>
              <p style="color: var(--text-dim); font-size: 13px; margin: 0;">Verifica finale ed emissione del protocollo SIC-ID Organization.</p>
            </div>

            <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-subtle); padding: 18px; border-radius: var(--radius-sm); margin-bottom: 18px;">
              <label style="display: flex; align-items: flex-start; gap: 12px; font-size: 13px; color: #ffffff; cursor: pointer;">
                <input type="checkbox" name="consent_privacy" value="1" required checked style="margin-top: 3px; accent-color: var(--gold-primary);">
                <div>
                  <strong>Trattamento Dati Personali e Aziendali (Obbligatorio) *</strong>
                  <p style="color: var(--text-dim); font-size: 12px; margin-top: 4px;">
                    Dichiaro di aver preso visione dell Informativa Privacy e acconsento al trattamento dei dati per la valutazione e gestione della candidatura B2B a cura di <?= LEGAL_ENTITY_NAME ?>.
                  </p>
                </div>
              </label>
            </div>

            <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-subtle); padding: 18px; border-radius: var(--radius-sm); margin-bottom: 24px;">
              <label style="display: flex; align-items: flex-start; gap: 12px; font-size: 13px; color: var(--text-muted); cursor: pointer;">
                <input type="checkbox" name="consent_marketing" value="1" checked style="margin-top: 3px; accent-color: var(--gold-primary);">
                <div>
                  <strong>Comunicazioni Istituzionali B2B (Facoltativo)</strong>
                  <p style="color: var(--text-dim); font-size: 12px; margin-top: 4px;">
                    Desidero ricevere aggiornamenti su bandi di finanziamento alla formazione, convegni scientifici e opportunità di distretto.
                  </p>
                </div>
              </label>
            </div>

            <div style="text-align: center; padding-top: 10px;">
              <button type="submit" class="btn-gold" style="font-size: 16px; padding: 16px 42px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px;">
                <?= icon_gold('institution', 18) ?> DEPOSITA CANDIDATURA & RICEVI IL SIC-ID
              </button>
              <div style="font-size: 12px; color: var(--text-dim); margin-top: 10px;">
                Nessun costo iniziale né impegno vincolante fino alla firma della convenzione.
              </div>
            </div>
          </div>

          <!-- BOTTONI DI NAVIGAZIONE WIZARD -->
          <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 35px; padding-top: 20px; border-top: 1px solid var(--border-subtle);">
            <button type="button" id="b2b-btn-prev" class="btn-outline-gold" style="display: none; padding: 10px 24px;">
              ← Indietro
            </button>
            <div style="margin-left: auto;">
              <button type="button" id="b2b-btn-next" class="btn-gold" style="padding: 10px 30px; font-weight: 700;">
                Avanti →
              </button>
            </div>
          </div>

        </form>
      </div>

    <?php endif; ?>

  </div>
</section>

<!-- WIZARD CONTROLLER JS -->
<script>
(function() {
  'use strict';

  var currentStep = 1;
  var totalSteps = 7;

  var steps = document.querySelectorAll('.b2b-step');
  var btnPrev = document.getElementById('b2b-btn-prev');
  var btnNext = document.getElementById('b2b-btn-next');
  var indicator = document.getElementById('b2b-step-indicator');
  var pctSpan = document.getElementById('b2b-step-pct');
  var fillBar = document.getElementById('b2b-progress-fill');

  function updateStep(step) {
    steps.forEach(function(s) { s.classList.remove('active'); });
    var target = document.querySelector('.b2b-step[data-step="' + step + '"]');
    if (target) {
      target.classList.add('active');
      var title = target.getAttribute('data-title') || '';
      if (indicator) indicator.textContent = 'Passaggio ' + step + ' di ' + totalSteps + ': ' + title;
      var pct = Math.round((step / totalSteps) * 100);
      if (pctSpan) pctSpan.textContent = pct + '%';
      if (fillBar) fillBar.style.width = pct + '%';
    }

    if (btnPrev) btnPrev.style.display = (step > 1 && step < 7) ? 'inline-flex' : 'none';
    if (btnNext) {
      if (step >= 7) {
        btnNext.style.display = 'none';
      } else {
        btnNext.style.display = 'inline-flex';
        btnNext.textContent = (step === 6) ? 'Rivedi e Deposita →' : 'Avanti →';
      }
    }
  }

  function validateStep(step) {
    var target = document.querySelector('.b2b-step[data-step="' + step + '"]');
    if (!target) return true;
    var inputs = target.querySelectorAll('input[required], select[required], textarea[required]');
    for (var i = 0; i < inputs.length; i++) {
      if (!inputs[i].checkValidity()) {
        inputs[i].reportValidity();
        inputs[i].focus();
        return false;
      }
    }
    return true;
  }

  if (btnNext) {
    btnNext.addEventListener('click', function() {
      if (validateStep(currentStep)) {
        if (currentStep < totalSteps) {
          currentStep++;
          updateStep(currentStep);
          window.scrollTo({ top: 120, behavior: 'smooth' });
        }
      }
    });
  }

  if (btnPrev) {
    btnPrev.addEventListener('click', function() {
      if (currentStep > 1) {
        currentStep--;
        updateStep(currentStep);
        window.scrollTo({ top: 120, behavior: 'smooth' });
      }
    });
  }

  updateStep(1);

})();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
