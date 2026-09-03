<?php
/**
 * CAMPUS.CAMP — Candidatura Comune / PA (CAMPUS for Cities)
 * Wizard Istituzionale in 10 Step con calcolo City Readiness Score e protocollo SIC-ID Municipality
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/icons.php';

$pageTitle = 'Candidatura Comune & Territorio — CAMPUS for Cities';
$pageDesc = 'Procedura telematica ufficiale per candidare un Comune o Ente Territoriale nella rete nazionale CAMPUS. Rilascio del protocollo digitale SIC-ID Municipality.';

$db = Database::getConnection();
$error = null;
$submittedCity = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $error = 'Sessione di sicurezza scaduta. Ricarica la pagina e riprova.';
    } else {
        // Step 1: Ente
        $entityType = trim($_POST['entity_type'] ?? 'COMUNE');
        $entityName = trim($_POST['entity_name'] ?? '');
        $fiscalCode = strtoupper(trim($_POST['fiscal_code'] ?? ''));
        $ipaCode = strtoupper(trim($_POST['ipa_code'] ?? ''));
        $pec = strtolower(trim($_POST['pec'] ?? ''));
        $website = trim($_POST['website'] ?? '');

        // Step 2: Referente
        $contactRole = trim($_POST['contact_role'] ?? 'SINDACO');
        $contactName = trim($_POST['contact_name'] ?? '');
        $contactEmail = strtolower(trim($_POST['contact_email'] ?? ''));
        $contactPhone = trim($_POST['contact_phone'] ?? '');

        // Step 3: Territorio
        $population = (int)($_POST['population'] ?? 0);
        $surfaceSqkm = (float)($_POST['surface_sqkm'] ?? 0);
        $estimatedCatchment = (int)($_POST['estimated_catchment'] ?? 0);
        if ($estimatedCatchment === 0 && $population > 0) {
            $estimatedCatchment = (int)($population * 2.5); // Stima bacino distretto
        }

        // Step 4: Struttura / Immobile
        $facilityType = trim($_POST['facility_type'] ?? 'SALA_CIVICA');
        $facilityName = trim($_POST['facility_name'] ?? '');
        $facilityAddress = trim($_POST['facility_address'] ?? '');
        $facilitySqMeters = (int)($_POST['facility_sqm'] ?? 0);
        $facilityRooms = (int)($_POST['facility_rooms'] ?? 1);
        $facilityCapacity = (int)($_POST['facility_capacity'] ?? 0);
        $hasAccessibility = isset($_POST['has_accessibility']) ? 1 : 0;
        $hasParking = isset($_POST['has_parking']) ? 1 : 0;
        $hasBroadband = isset($_POST['has_broadband']) ? 1 : 0;
        $availableHours = trim($_POST['available_hours'] ?? '');

        // Step 5: Ecosistema
        $ecosystem = isset($_POST['ecosystem']) ? json_encode((array)$_POST['ecosystem'], JSON_UNESCAPED_UNICODE) : '[]';

        // Step 6: Obiettivi
        $goals = isset($_POST['goals']) ? json_encode((array)$_POST['goals'], JSON_UNESCAPED_UNICODE) : '[]';

        // Step 7: Disponibilità
        $availabilityType = trim($_POST['availability_type'] ?? 'CONVENZIONE');

        // Step 8: Note e Motivazioni
        $notes = trim($_POST['notes'] ?? '');

        // Step 9: Consensi
        $consentPrivacy = isset($_POST['consent_privacy']) ? 1 : 0;

        // Validazione minima essenziale
        if (empty($entityName) || empty($contactName) || empty($contactEmail) || empty($contactPhone)) {
            $error = 'Compilare tutti i campi obbligatori dell Ente e del Referente.';
        } elseif (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
            $error = 'Indirizzo email referente non valido.';
        } elseif (!$consentPrivacy) {
            $error = 'La presa visione dell informativa e l autorizzazione al trattamento sono obbligatorie.';
        }

        if (!$error) {
            try {
                $db->beginTransaction();

                // 1. Genera SIC-ID Canonico per Municipality
                $sicId = generate_canonical_sic_id();
                // Assicuriamo prefisso leggibile per le città
                $sicIdMuni = str_replace('SIC-ID-', 'SIC-ID-MUNI-', $sicId);
                $appCode = 'CAMPUS-CITY-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(4)));

                // 2. Calcolo City Readiness Score (0-100)
                $score = 50; // base istituzionale
                if ($hasBroadband) $score += 10;
                if ($hasAccessibility) $score += 10;
                if ($hasParking) $score += 5;
                if ($facilitySqMeters >= 100) $score += 5;
                if ($facilitySqMeters >= 250) $score += 5;
                if ($population >= 5000) $score += 5;
                if ($population >= 15000) $score += 5;
                $ecoArr = json_decode($ecosystem, true) ?: [];
                if (count($ecoArr) >= 3) $score += 5;
                $score = min(100, $score);

                // Struttura JSON sintetica
                $facilitySummary = [
                    'type' => $facilityType,
                    'name' => $facilityName,
                    'address' => $facilityAddress,
                    'sqm' => $facilitySqMeters,
                    'rooms' => $facilityRooms,
                    'capacity' => $facilityCapacity,
                    'accessibility' => $hasAccessibility,
                    'parking' => $hasParking,
                    'broadband' => $hasBroadband,
                    'hours' => $availableHours
                ];
                $facilitiesJson = json_encode([$facilitySummary], JSON_UNESCAPED_UNICODE);

                // 3. Inserimento in campus_cities
                $stmt = $db->prepare("
                    INSERT INTO campus_cities (
                        application_code, sic_id, entity_type, entity_name, fiscal_code, ipa_code,
                        pec, website, contact_name, contact_role, contact_email, contact_phone,
                        population, surface_sqkm, estimated_catchment,
                        facilities_json, ecosystem_json, goals_json, availability_type, notes,
                        readiness_score, pipeline_stage, status
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?,
                        ?, ?, ?, ?, ?, ?,
                        ?, ?, ?,
                        ?, ?, ?, ?, ?,
                        ?, 'APPLICATION_RECEIVED', 'SUBMITTED'
                    )
                ");

                $stmt->execute([
                    $appCode, $sicIdMuni, $entityType, $entityName, $fiscalCode, $ipaCode,
                    $pec, $website, $contactName, $contactRole, $contactEmail, $contactPhone,
                    $population, $surfaceSqkm, $estimatedCatchment,
                    $facilitiesJson, $ecosystem, $goals, $availabilityType, $notes,
                    $score
                ]);
                $cityAppId = (int)$db->lastInsertId();

                // 4. Inserimento in city_facilities
                if (!empty($facilityName)) {
                    $stmtF = $db->prepare("
                        INSERT INTO city_facilities (
                            city_application_id, facility_name, facility_type, address,
                            square_meters, rooms_count, capacity, has_accessibility, has_parking, has_broadband, available_hours
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmtF->execute([
                        $cityAppId, $facilityName, $facilityType, $facilityAddress,
                        $facilitySqMeters, $facilityRooms, $facilityCapacity, $hasAccessibility, $hasParking, $hasBroadband, $availableHours
                    ]);
                }

                // 5. Registrazione in sic_ids
                $stmtSic = $db->prepare("
                    INSERT OR IGNORE INTO sic_ids (sic_id, person_id, entity_type, status)
                    VALUES (?, ?, 'MUNICIPALITY', 'ACTIVE')
                ");
                $stmtSic->execute([$sicIdMuni, $cityAppId]);

                // 6. Audit Log
                $stmtAudit = $db->prepare("
                    INSERT INTO audit_log (action, entity_type, entity_id, details, ip_address, user_agent)
                    VALUES ('CITY_APPLICATION_SUBMITTED', 'MUNICIPALITY', ?, ?, ?, ?)
                ");
                $stmtAudit->execute([
                    $sicIdMuni,
                    "Candidatura Comune depositata da {$entityName} (Ref: {$contactName} - {$contactRole}) - Readiness Score: {$score}",
                    $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                    $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
                ]);

                $db->commit();

                // Invio notifica email alla PA
                $headers = [
                    'MIME-Version: 1.0',
                    'Content-type: text/html; charset=utf-8',
                    'From: CAMPUS Direzione Territoriale <' . CAMPUS_EMAIL_INFO . '>',
                    'Reply-To: ' . CAMPUS_EMAIL_INFO
                ];
                $mailMsg = "<p>Gentile <strong>{$contactName}</strong> ({$contactRole} del {$entityName}),</p><p>La candidatura del Suo Ente per l accreditamento nella rete nazionale <strong>CAMPUS for Cities</strong> è stata protocollata con successo.</p><p>Protocollo Istituzionale: <strong>{$sicIdMuni}</strong><br>Codice Pratica: <strong>{$appCode}</strong><br>Readiness Score Preliminare: <strong>{$score}/100</strong></p><p>Il Comitato di Indirizzo Territoriale contatterà il Suo ufficio entro 5 giorni lavorativi per trasmettere lo Studio Preliminare di Prefattibilità da condividere con la Giunta.</p><p>Cordiali saluti,<br><strong>Direzione Generale CAMPUS</strong></p>";
                @mail($contactEmail, "[CAMPUS for Cities] Ricevuta Deposito Candidatura — Protocollo {$sicIdMuni}", $mailMsg, implode("\r\n", $headers));

                $submittedCity = [
                    'entity_name' => $entityName,
                    'sic_id' => $sicIdMuni,
                    'app_code' => $appCode,
                    'score' => $score,
                    'contact_email' => $contactEmail
                ];

            } catch (Exception $e) {
                if ($db->inTransaction()) $db->rollBack();
                $error = 'Errore durante la registrazione telematica: ' . $e->getMessage();
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="section" style="padding-top: 40px; padding-bottom: 70px;">
  <div class="container" style="max-width: 920px;">

    <?php if ($submittedCity): ?>
      <!-- SCHEDA DI SUCCESSO E GRATIFICAZIONE ISTITUZIONALE -->
      <div class="glass-card" style="padding: 45px 35px; text-align: center; border: 2px solid var(--border-gold); box-shadow: var(--gold-glow);">
        <img src="/assets/visual/Campus_Emblema_Transparent.png" alt="CAMPUS" style="width: 85px; margin-bottom: 20px; filter: drop-shadow(0 0 20px rgba(212,175,55,0.45));">
        
        <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2.5px; font-weight: 800; display: block; margin-bottom: 6px;">
          Procedura Istituzionale Conclusa con Successo
        </span>

        <h1 style="font-size: clamp(24px, 3.5vw, 36px); color: #ffffff; font-family: 'Cinzel', serif; margin-bottom: 14px;">
          CANDIDATURA DEPOSITATA: <span class="gold-text"><?= sanitize_output($submittedCity['entity_name']) ?></span>
        </h1>

        <p style="color: var(--text-muted); font-size: 15px; max-width: 650px; margin: 0 auto 28px auto; line-height: 1.6;">
          La candidatura del vostro territorio è stata formalmente acquisita nei registri di ateneo. Abbiamo rilasciato il protocollo crittografico istituzionale di municipalità.
        </p>

        <!-- BOX SIC-ID & SCORE -->
        <div style="background: rgba(0,0,0,0.6); border: 2px solid var(--gold-primary); border-radius: var(--radius-sm); padding: 22px; max-width: 520px; margin: 0 auto 30px auto;">
          <div style="font-size: 11px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 2px; font-weight: bold;">
            Protocollo Immutabile SIC-ID Municipality
          </div>
          <div style="font-size: 22px; font-family: monospace; font-weight: 900; color: #ffffff; margin: 8px 0; letter-spacing: 2px;">
            <?= sanitize_output($submittedCity['sic_id']) ?>
          </div>
          <div style="display: flex; justify-content: space-around; margin-top: 12px; font-size: 12px; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 10px;">
            <span>Codice Pratica: <strong style="color: #ffffff;"><?= sanitize_output($submittedCity['app_code']) ?></strong></span>
            <span>Readiness Score: <strong style="color: var(--gold-light);"><?= $submittedCity['score'] ?>/100</strong></span>
          </div>
        </div>

        <p style="font-size: 14px; color: #cbd5e1; max-width: 620px; margin: 0 auto 30px auto; line-height: 1.6;">
          Una ricevuta è stata inviata a <strong><?= sanitize_output($submittedCity['contact_email']) ?></strong>. La Direzione predisporrà lo Studio Preliminare di Prefattibilità da sottoporre agli organi deliberanti dell'Ente.
        </p>

        <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
          <a href="/campus-city/studio-fattibilita.php" class="btn-gold" style="font-size: 14px; padding: 14px 28px; font-weight: 700;">
            <?= icon_gold('document', 14) ?> GENERA IL DOSSIER PER LA GIUNTA
          </a>
          <button onclick="window.print()" class="btn-outline-gold" style="font-size: 14px; padding: 14px 24px;">
            <?= icon_gold('print', 14) ?> Stampa Ricevuta Protocollo
          </button>
        </div>
      </div>

    <?php else: ?>
      <!-- WIZARD CANDIDATURA PA IN 10 STEP -->

      <div style="text-align: center; margin-bottom: 30px;">
        <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2.5px; font-weight: 800; display: inline-flex; align-items: center; gap: 8px;">
          <?= icon_gold('institution', 15) ?> Procedura Telematica Istituzionale
        </span>
        <h1 style="font-size: clamp(26px, 4vw, 38px); margin-top: 8px; font-family: 'Cinzel', serif;">
          Candidatura <span class="gold-text">Città CAMPUS</span>
        </h1>
        <p style="color: var(--text-muted); font-size: 15px; margin-top: 6px;">
          Riservata a Comuni, Unioni di Comuni, Province ed Enti di Governo Locale. Assegnazione immediata del protocollo <strong>SIC-ID Municipality</strong>.
        </p>
      </div>

      <?php if ($error): ?>
        <div style="background: rgba(0,0,0,0.85); border: 2px solid var(--gold-primary); color: #ffffff; padding: 14px 20px; border-radius: var(--radius-sm); margin-bottom: 25px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
          <?= icon_gold('shield', 20) ?> <span><strong>Attenzione:</strong> <?= sanitize_output($error) ?></span>
        </div>
      <?php endif; ?>

      <div class="glass-card" style="padding: 35px 30px; border: 2px solid var(--border-gold);">
        
        <!-- Progress Bar -->
        <div style="margin-bottom: 30px;">
          <div style="display: flex; justify-content: space-between; font-size: 12px; color: var(--gold-light); margin-bottom: 8px; font-weight: 600;">
            <span id="city-step-indicator">Passaggio 1 di 10: L'Ente Pubblico</span>
            <span id="city-step-pct">10%</span>
          </div>
          <div style="width: 100%; height: 6px; background: rgba(255,255,255,0.08); border-radius: 3px; overflow: hidden;">
            <div id="city-progress-fill" style="width: 10%; height: 100%; background: var(--gold-gradient); transition: width 0.3s ease;"></div>
          </div>
        </div>

        <form id="city-wizard-form" action="/campus-city/apply.php" method="POST">
          <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">

          <!-- STEP 1: ENTE -->
          <div class="city-step active" data-step="1" data-title="L'Ente Pubblico">
            <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
              <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 4px;">1. Dati Istituzionali dell'Ente</h3>
              <p style="color: var(--text-dim); font-size: 13px; margin: 0;">Tipologia, denominazione e riferimenti amministrativi.</p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px; margin-bottom: 16px;">
              <div>
                <label class="form-label">Tipologia Ente *</label>
                <select name="entity_type" class="form-control" required>
                  <option value="COMUNE" selected>Comune</option>
                  <option value="UNIONE_COMUNI">Unione di Comuni</option>
                  <option value="PROVINCIA">Provincia / Città Metropolitana</option>
                  <option value="COMUNITA_MONTANA">Comunità Montana / Consorzio</option>
                  <option value="ALTRO">Altro Ente Territoriale</option>
                </select>
              </div>
              <div>
                <label class="form-label">Denominazione Ufficiale *</label>
                <input type="text" name="entity_name" class="form-control" placeholder="es. Comune di Porto Viro / Comune di Adria" required>
              </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 16px;">
              <div>
                <label class="form-label">Codice Fiscale Ente</label>
                <input type="text" name="fiscal_code" class="form-control" placeholder="01234567890">
              </div>
              <div>
                <label class="form-label">Codice Univoco IPA</label>
                <input type="text" name="ipa_code" class="form-control" placeholder="c_h726" style="text-transform: uppercase;">
              </div>
              <div>
                <label class="form-label">Indirizzo PEC Istituzionale</label>
                <input type="email" name="pec" class="form-control" placeholder="protocollo@pec.comune.it">
              </div>
            </div>

            <div>
              <label class="form-label">Portale Istituzionale Web</label>
              <input type="url" name="website" class="form-control" placeholder="https://www.comune.portoviro.ro.it">
            </div>
          </div>

          <!-- STEP 2: REFERENTE -->
          <div class="city-step" data-step="2" data-title="Referente Istituzionale">
            <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
              <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 4px;">2. Referente Politico o Amministrativo</h3>
              <p style="color: var(--text-dim); font-size: 13px; margin: 0;">Sindaco, Assessore delegato, Dirigente o Responsabile del servizio.</p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px; margin-bottom: 16px;">
              <div>
                <label class="form-label">Ruolo / Incarico *</label>
                <select name="contact_role" class="form-control" required>
                  <option value="SINDACO" selected>Sindaco / Presidente</option>
                  <option value="ASSESSORE">Assessore Delegato</option>
                  <option value="SEGRETARIO">Segretario Generale</option>
                  <option value="DIRIGENTE">Dirigente di Settore</option>
                  <option value="RESPONSABILE">Responsabile Servizi</option>
                  <option value="ALTRO">Altro Referente Incaricato</option>
                </select>
              </div>
              <div>
                <label class="form-label">Nome e Cognome Referente *</label>
                <input type="text" name="contact_name" class="form-control" placeholder="es. Dott. Mario Rossi" required>
              </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
              <div>
                <label class="form-label">Email Istituzionale Diretta *</label>
                <input type="email" name="contact_email" class="form-control" placeholder="sindaco@comune.it" required>
              </div>
              <div>
                <label class="form-label">Telefono / Recapito Diretto *</label>
                <input type="tel" name="contact_phone" class="form-control" placeholder="0426 32571 / 335 1234567" required>
              </div>
            </div>
          </div>

          <!-- STEP 3: TERRITORIO -->
          <div class="city-step" data-step="3" data-title="Territorio">
            <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
              <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 4px;">3. Dimensioni del Territorio & Bacino</h3>
              <p style="color: var(--text-dim); font-size: 13px; margin: 0;">Parametri demografici per calcolare il potenziale di iscritti e corsisti.</p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
              <div>
                <label class="form-label">Popolazione Residente</label>
                <input type="number" name="population" class="form-control" placeholder="es. 14000" min="500">
              </div>
              <div>
                <label class="form-label">Superficie Territoriale (kmq)</label>
                <input type="number" step="0.1" name="surface_sqkm" class="form-control" placeholder="es. 133.3">
              </div>
              <div>
                <label class="form-label">Bacino Stimato di Distretto</label>
                <input type="number" name="estimated_catchment" class="form-control" placeholder="es. 85000">
              </div>
            </div>
          </div>

          <!-- STEP 4: STRUTTURA / IMMOBILE -->
          <div class="city-step" data-step="4" data-title="Struttura Candidata">
            <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
              <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 4px;">4. Lo Spazio Pubblico Candidato</h3>
              <p style="color: var(--text-dim); font-size: 13px; margin: 0;">L'immobile che l'amministrazione intende valorizzare come nodo CAMPUS.</p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px; margin-bottom: 16px;">
              <div>
                <label class="form-label">Tipologia Immobile *</label>
                <select name="facility_type" class="form-control">
                  <option value="SALA_CIVICA" selected>Sala Civica / Consiliare</option>
                  <option value="BIBLIOTECA">Biblioteca Comunale</option>
                  <option value="EX_SCUOLA">Ex Plesso Scolastico</option>
                  <option value="AUDITORIUM">Auditorium / Teatro</option>
                  <option value="PALAZZO_STORICO">Palazzo Storico / Rappresentanza</option>
                  <option value="CENTRO_GIOVANI">Centro Giovani / Polivalente</option>
                  <option value="COWORKING">Coworking Pubblico</option>
                  <option value="LABORATORIO">Laboratorio Tecnico</option>
                  <option value="ALTRO">Altro Immobile Comunale</option>
                </select>
              </div>
              <div>
                <label class="form-label">Denominazione della Struttura *</label>
                <input type="text" name="facility_name" class="form-control" placeholder="es. Centro Civico San Rocco / Biblioteca Civica" required>
              </div>
            </div>

            <div style="margin-bottom: 16px;">
              <label class="form-label">Indirizzo Completo della Struttura</label>
              <input type="text" name="facility_address" class="form-control" placeholder="Via Mantovana, 78 - 45014 Porto Viro (RO)">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 16px;">
              <div>
                <label class="form-label">Superficie Disponibile (mq)</label>
                <input type="number" name="facility_sqm" class="form-control" placeholder="es. 250">
              </div>
              <div>
                <label class="form-label">Numero Sale / Aule</label>
                <input type="number" name="facility_rooms" class="form-control" value="2" min="1">
              </div>
              <div>
                <label class="form-label">Capienza Totale Stimata (posti)</label>
                <input type="number" name="facility_capacity" class="form-control" placeholder="es. 80">
              </div>
            </div>

            <div style="display: flex; gap: 20px; font-size: 13px; color: #ffffff; flex-wrap: wrap;">
              <label style="display:flex; align-items:center; gap:6px; cursor:pointer;"><input type="checkbox" name="has_accessibility" value="1" checked style="accent-color: var(--gold-primary);"> Accessibilità disabili (D.P.R. 503/96)</label>
              <label style="display:flex; align-items:center; gap:6px; cursor:pointer;"><input type="checkbox" name="has_parking" value="1" checked style="accent-color: var(--gold-primary);"> Parcheggio pubblico adiacente</label>
              <label style="display:flex; align-items:center; gap:6px; cursor:pointer;"><input type="checkbox" name="has_broadband" value="1" checked style="accent-color: var(--gold-primary);"> Connessione Internet / Wi-Fi</label>
            </div>
          </div>

          <!-- STEP 5: ECOSISTEMA -->
          <div class="city-step" data-step="5" data-title="Ecosistema Locale">
            <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
              <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 4px;">5. Ecosistema & Presidi sul Territorio</h3>
              <p style="color: var(--text-dim); font-size: 13px; margin: 0;">Seleziona le realtà attive nel vostro bacino con cui integrare le attività.</p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px; color: #ffffff;">
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="ecosystem[]" value="Istituti Superiori" checked style="accent-color: var(--gold-primary);"> Istituti Superiori / Licei / Istituti Tecnici</label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="ecosystem[]" value="Enti di Formazione VET" checked style="accent-color: var(--gold-primary);"> Centri di Formazione Professionale (VET/ENAIP)</label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="ecosystem[]" value="PMI e Imprese" checked style="accent-color: var(--gold-primary);"> Distretto di Piccole e Medie Imprese</label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="ecosystem[]" value="Ordini Professionali" checked style="accent-color: var(--gold-primary);"> Ordini e Collegi Professionali (Ingegneri, Periti, ecc.)</label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="ecosystem[]" value="Associazioni di Categoria" checked style="accent-color: var(--gold-primary);"> Associazioni Datoriali (Confindustria, Confartigianato, ecc.)</label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="ecosystem[]" value="Terzo Settore" checked style="accent-color: var(--gold-primary);"> Enti del Terzo Settore e Fondazioni Locali</label>
            </div>
          </div>

          <!-- STEP 6: OBIETTIVI -->
          <div class="city-step" data-step="6" data-title="Obiettivi Prioritari">
            <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
              <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 4px;">6. Traguardi Strategici dell'Amministrazione</h3>
              <p style="color: var(--text-dim); font-size: 13px; margin: 0;">Cosa intende realizzare prioritariamente la Giunta attraverso CAMPUS?</p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px; color: #ffffff;">
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="goals[]" value="Trattenere i Giovani" checked style="accent-color: var(--gold-primary);"> Trattenere i Giovani sul Territorio</label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="goals[]" value="Formazione Continua Lavoratori" checked style="accent-color: var(--gold-primary);"> Formazione Continua per Lavoratori e Imprese</label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="goals[]" value="Rigenerazione Urbana" checked style="accent-color: var(--gold-primary);"> Rigenerazione di un Edificio Pubblico Dismesso</label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="goals[]" value="Convegni con CFP" checked style="accent-color: var(--gold-primary);"> Attrazione di Convegni e Crediti Formativi</label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="goals[]" value="Ricerca Applicata Territoriale" style="accent-color: var(--gold-primary);"> Ricerca Applicata su Ambiente e Territorio</label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer;"><input type="checkbox" name="goals[]" value="Formazione Personale Comunale" style="accent-color: var(--gold-primary);"> Riqualificazione Personale Dipendente del Comune</label>
            </div>
          </div>

          <!-- STEP 7: DISPONIBILITÀ -->
          <div class="city-step" data-step="7" data-title="Regime Giuridico">
            <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
              <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 4px;">7. Regime Giuridico di Utilizzo Ipotizzato</h3>
              <p style="color: var(--text-dim); font-size: 13px; margin: 0;">Orientamento iniziale non vincolante da approfondire nello studio di fattibilità.</p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
              <label style="background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); padding: 14px; border-radius: var(--radius-sm); cursor: pointer; display: flex; align-items: flex-start; gap: 10px;">
                <input type="radio" name="availability_type" value="CONVENZIONE" checked style="margin-top: 3px; accent-color: var(--gold-primary);">
                <div>
                  <strong style="color: #ffffff; font-size: 13.5px;">Convenzione d'Uso Temporaneo / Condiviso</strong>
                  <p style="color: var(--text-muted); font-size: 12px; margin-top: 4px;">Utilizzo della sala o biblioteca per giornate/orari concordati senza oneri fissi.</p>
                </div>
              </label>

              <label style="background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); padding: 14px; border-radius: var(--radius-sm); cursor: pointer; display: flex; align-items: flex-start; gap: 10px;">
                <input type="radio" name="availability_type" value="COMODATO" style="margin-top: 3px; accent-color: var(--gold-primary);">
                <div>
                  <strong style="color: #ffffff; font-size: 13.5px;">Comodato d'Uso Gratuito per Finalità Pubbliche</strong>
                  <p style="color: var(--text-muted); font-size: 12px; margin-top: 4px;">Concessione d'uso istituzionale per la gestione continua del Learning Hub.</p>
                </div>
              </label>

              <label style="background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); padding: 14px; border-radius: var(--radius-sm); cursor: pointer; display: flex; align-items: flex-start; gap: 10px;">
                <input type="radio" name="availability_type" value="CONCESSIONE_SERVIZI" style="margin-top: 3px; accent-color: var(--gold-primary);">
                <div>
                  <strong style="color: #ffffff; font-size: 13.5px;">Concessione di Servizi / Polo Polifunzionale</strong>
                  <p style="color: var(--text-muted); font-size: 12px; margin-top: 4px;">Affidamento congiunto dello spazio con modello di gestione mista.</p>
                </div>
              </label>

              <label style="background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); padding: 14px; border-radius: var(--radius-sm); cursor: pointer; display: flex; align-items: flex-start; gap: 10px;">
                <input type="radio" name="availability_type" value="DA_DEFINIRE" style="margin-top: 3px; accent-color: var(--gold-primary);">
                <div>
                  <strong style="color: #ffffff; font-size: 13.5px;">Formula Aperta da Concordare con la Giunta</strong>
                  <p style="color: var(--text-muted); font-size: 12px; margin-top: 4px;">Da valutare congiuntamente con il Segretario Comunale e l'Ufficio Tecnico.</p>
                </div>
              </label>
            </div>
          </div>

          <!-- STEP 8: NOTE E MOTIVAZIONE -->
          <div class="city-step" data-step="8" data-title="Motivazione">
            <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
              <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 4px;">8. Perché il Vostro Comune Vuole Diventare Città CAMPUS?</h3>
              <p style="color: var(--text-dim); font-size: 13px; margin: 0;">Sintesi delle motivazioni e delle aspettative dell'amministrazione locale.</p>
            </div>

            <div>
              <textarea name="notes" class="form-control" rows="4" placeholder="es. Il nostro Comune dispone di una sala polifunzionale di recente restauro nel centro storico. Desideriamo riempirla di contenuti accademici per offrire corsi qualificati ai diplomati del distretto e creare un punto di raccordo con le aziende locali..."></textarea>
            </div>
          </div>

          <!-- STEP 9: CONSENSI E CONFORMITÀ -->
          <div class="city-step" data-step="9" data-title="Consensi Istituzionali">
            <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
              <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 4px;">9. Trattamento Dati & Dichiarazione Istituzionale</h3>
              <p style="color: var(--text-dim); font-size: 13px; margin: 0;">Conformità GDPR e autorizzazione alle comunicazioni ufficiali con l'Ente.</p>
            </div>

            <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-subtle); padding: 18px; border-radius: var(--radius-sm); margin-bottom: 18px;">
              <label style="display: flex; align-items: flex-start; gap: 12px; font-size: 13px; color: #ffffff; cursor: pointer;">
                <input type="checkbox" name="consent_privacy" value="1" required checked style="margin-top: 3px; accent-color: var(--gold-primary);">
                <div>
                  <strong>Autorizzazione al Trattamento Dati per la Candidatura Istituzionale (Obbligatorio) *</strong>
                  <p style="color: var(--text-dim); font-size: 12px; margin-top: 4px;">
                    Dichiaro di trasmettere i dati dell'Ente per le finalità di valutazione della candidatura alla rete CAMPUS for Cities a cura di <?= LEGAL_ENTITY_NAME ?> e autorizzo le comunicazioni preliminari con il referente designato.
                  </p>
                </div>
              </label>
            </div>
          </div>

          <!-- STEP 10: RIEPILOGO & SUBMIT -->
          <div class="city-step" data-step="10" data-title="Deposito Protocollo">
            <div style="text-align: center; padding: 20px 0;">
              <img src="/assets/visual/Campus_Emblema_Transparent.png" alt="CAMPUS" style="width: 80px; margin-bottom: 14px; filter: drop-shadow(0 0 20px rgba(212,175,55,0.45));">
              <h3 style="color: #ffffff; font-size: 22px; font-family: 'Cinzel', serif; margin-bottom: 10px;">
                Deposito Telematico Candidatura Comune
              </h3>
              <p style="color: var(--text-muted); font-size: 14.5px; max-width: 640px; margin: 0 auto 26px auto; line-height: 1.6;">
                Confermando il deposito, la candidatura verrà registrata negli archivi centrali di CAMPUS con emissione immediata del protocollo digitale <strong>SIC-ID Municipality</strong>. L'invio non vincola economicamente l'amministrazione.
              </p>
              <button type="submit" class="btn-gold" style="font-size: 16px; padding: 18px 45px; font-weight: 800; display: inline-flex; align-items: center; gap: 10px; cursor: pointer;">
                <?= icon_gold('institution', 18) ?> DEPOSITA CANDIDATURA & RICEVI IL SIC-ID
              </button>
            </div>
          </div>

          <!-- BOTTONI WIZARD -->
          <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 35px; padding-top: 20px; border-top: 1px solid var(--border-subtle);">
            <button type="button" id="city-btn-prev" class="btn-outline-gold" style="display: none; padding: 10px 24px;">
              ← Indietro
            </button>
            <div style="margin-left: auto;">
              <button type="button" id="city-btn-next" class="btn-gold" style="padding: 10px 30px; font-weight: 700;">
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
  var totalSteps = 10;

  var steps = document.querySelectorAll('.city-step');
  var btnPrev = document.getElementById('city-btn-prev');
  var btnNext = document.getElementById('city-btn-next');
  var indicator = document.getElementById('city-step-indicator');
  var pctSpan = document.getElementById('city-step-pct');
  var fillBar = document.getElementById('city-progress-fill');

  function updateStep(step) {
    steps.forEach(function(s) { s.classList.remove('active'); });
    var target = document.querySelector('.city-step[data-step="' + step + '"]');
    if (target) {
      target.classList.add('active');
      var title = target.getAttribute('data-title') || '';
      if (indicator) indicator.textContent = 'Passaggio ' + step + ' di ' + totalSteps + ': ' + title;
      var pct = Math.round((step / totalSteps) * 100);
      if (pctSpan) pctSpan.textContent = pct + '%';
      if (fillBar) fillBar.style.width = pct + '%';
    }

    if (btnPrev) btnPrev.style.display = (step > 1 && step < 10) ? 'inline-flex' : 'none';
    if (btnNext) {
      if (step >= 10) {
        btnNext.style.display = 'none';
      } else {
        btnNext.style.display = 'inline-flex';
        btnNext.textContent = (step === 9) ? 'Procedi al Deposito →' : 'Avanti →';
      }
    }
  }

  function validateStep(step) {
    var target = document.querySelector('.city-step[data-step="' + step + '"]');
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
