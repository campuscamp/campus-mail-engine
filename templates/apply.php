<?php
/**
 * CAMPUS.CAMP — Pipeline Onboarding Docenti (Wizard 12 Step)
 * Salva su SQLite, genera SIC-ID-XXXXXXXXXXXX e assegna codice candidatura
 */


require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/taxonomy.php';

$pageTitle = 'Candidatura Faculty CAMPUS — Modulo di Accreditamento Docenti';
$pageDesc = 'Invia la tua candidatura per entrare nella Faculty di CAMPUS. Procedura di accreditamento in 12 passaggi.';

$db = Database::getConnection();
$error = null;

// Fetch courses from DB for Step 7
$courses = $db->query("SELECT * FROM courses ORDER BY faculty, school, title")->fetchAll();
$professions = Taxonomy::getProfessions();
$atecoActivities = Taxonomy::getAtecoActivities();
$bodyTypes = Taxonomy::getProfessionalBodyTypes();

// Process POST Submission (Step 12)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $error = 'Sessione di sicurezza scaduta o non valida. Riprova.';
    } else {
        // Collect inputs
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $fiscalCode = strtoupper(trim($_POST['fiscal_code'] ?? ''));
        $birthDate = trim($_POST['birth_date'] ?? '');
        $birthPlace = trim($_POST['birth_place'] ?? '');
        $nationality = trim($_POST['nationality'] ?? 'Italiana');

        $email = strtolower(trim($_POST['email'] ?? ''));
        $phone = trim($_POST['phone'] ?? '');
        $pec = trim($_POST['pec'] ?? '');

        $street = trim($_POST['address_street'] ?? '');
        $number = trim($_POST['address_number'] ?? '');
        $zip = trim($_POST['address_zip'] ?? '');
        $city = trim($_POST['address_city'] ?? '');
        $province = strtoupper(trim($_POST['address_province'] ?? ''));
        $region = trim($_POST['address_region'] ?? '');
        $country = trim($_POST['address_country'] ?? 'Italia');

        $profession = trim($_POST['profession'] ?? '');
        $atecoCode = trim($_POST['ateco_code'] ?? '');
        $atecoDesc = '';
        foreach ($atecoActivities as $act) {
            if ($act['code'] === $atecoCode) {
                $atecoDesc = $act['desc'];
                break;
            }
        }

        $bodyType = trim($_POST['professional_body_type'] ?? '');
        $bodyName = trim($_POST['professional_body_name'] ?? '');
        $bodyNumber = trim($_POST['professional_body_number'] ?? '');
        $bodyTerritory = trim($_POST['professional_body_territory'] ?? '');

        $yearsExp = (int)($_POST['years_experience'] ?? 0);
        $disciplines = trim($_POST['disciplines'] ?? '');
        $skills = trim($_POST['skills'] ?? '');
        $bio = trim($_POST['bio'] ?? '');

        $selectedCourses = $_POST['courses'] ?? [];
        if (!is_array($selectedCourses)) $selectedCourses = [];
        $selectedCourses = array_slice($selectedCourses, 0, 10); // Max 10

        $teachingYears = (int)($_POST['teaching_years'] ?? 0);
        $teachingModalities = isset($_POST['teaching_modalities']) ? implode(', ', (array)$_POST['teaching_modalities']) : '';

        $portfolioUrl = trim($_POST['portfolio_url'] ?? '');
        $linkedinUrl = trim($_POST['linkedin_url'] ?? '');
        $orcidUrl = trim($_POST['orcid_url'] ?? '');
        $websiteUrl = trim($_POST['website_url'] ?? '');

        $consentPrivacy = isset($_POST['consent_privacy']) ? 1 : 0;
        $consentMarketing = isset($_POST['consent_marketing']) ? 1 : 0;

        // Validation
        if (empty($firstName) || empty($lastName) || empty($fiscalCode) || empty($email) || empty($phone)) {
            $error = 'Compilare tutti i campi anagrafici e di contatto obbligatori.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Indirizzo email non valido.';
        } elseif (!$consentPrivacy) {
            $error = 'Il consenso al trattamento dei dati per la candidatura è obbligatorio.';
        }

        // CV Upload Handling
        $cvFilename = null;
        $cvOriginalName = null;
        if (!$error && isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmp = $_FILES['cv_file']['tmp_name'];
            $fileSize = $_FILES['cv_file']['size'];
            $origName = basename($_FILES['cv_file']['name']);
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

            if ($ext !== 'pdf') {
                $error = 'Il Curriculum Vitae deve essere in formato PDF.';
            } elseif ($fileSize > 12 * 1024 * 1024) { // 12MB limit
                $error = 'Il file PDF non può superare 12 MB.';
            } else {
                $cvFilename = 'CV_' . time() . '_' . bin2hex(random_bytes(8)) . '.pdf';
                $destPath = CAMPUS_UPLOADS_CV_DIR . '/' . $cvFilename;
                if (!move_uploaded_file($fileTmp, $destPath)) {
                    $error = 'Errore durante il caricamento del file CV.';
                } else {
                    $cvOriginalName = $origName;
                }
            }
        } elseif (!$error && empty($cvFilename)) {
            $error = 'Il caricamento del Curriculum Vitae in formato PDF è obbligatorio.';
        }

        // Database Persistence & SIC-ID Generation
        if (!$error) {
            try {
                $db->beginTransaction();

                // 1. Generate Canonical SIC-ID
                $sicId = generate_canonical_sic_id();
                $appCode = 'APP-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

                // 2. Insert or update Person
                $stmtPerson = $db->prepare("
                    INSERT INTO persons (sic_id, first_name, last_name, fiscal_code, birth_date, birth_place, nationality)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                    ON CONFLICT(fiscal_code) DO UPDATE SET
                        first_name = excluded.first_name,
                        last_name = excluded.last_name,
                        updated_at = CURRENT_TIMESTAMP
                ");
                $stmtPerson->execute([$sicId, $firstName, $lastName, $fiscalCode, $birthDate, $birthPlace, $nationality]);
                $personId = (int)$db->lastInsertId();

                // 3. Register SIC-ID
                $stmtSic = $db->prepare("
                    INSERT OR IGNORE INTO sic_ids (sic_id, person_id, entity_type, status)
                    VALUES (?, ?, 'FACULTY_CANDIDATE', 'ACTIVE')
                ");
                $stmtSic->execute([$sicId, $personId]);

                // 4. Create Faculty Application
                $stmtApp = $db->prepare("
                    INSERT INTO faculty_applications (
                        application_code, person_id, sic_id, email, phone, pec,
                        address_street, address_number, address_zip, address_city, address_province, address_region, address_country,
                        profession, ateco_code, ateco_description,
                        professional_body_type, professional_body_name, professional_body_number, professional_body_territory,
                        years_experience, disciplines, skills, bio,
                        teaching_years, teaching_modalities,
                        cv_filename, cv_original_name, portfolio_url, linkedin_url, orcid_url, website_url,
                        consent_privacy, consent_marketing, status
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?,
                        ?, ?, ?, ?, ?, ?, ?,
                        ?, ?, ?,
                        ?, ?, ?, ?,
                        ?, ?, ?, ?,
                        ?, ?,
                        ?, ?, ?, ?, ?, ?,
                        ?, ?, 'SUBMITTED'
                    )
                ");
                $stmtApp->execute([
                    $appCode, $personId, $sicId, $email, $phone, $pec,
                    $street, $number, $zip, $city, $province, $region, $country,
                    $profession, $atecoCode, $atecoDesc,
                    $bodyType, $bodyName, $bodyNumber, $bodyTerritory,
                    $yearsExp, $disciplines, $skills, $bio,
                    $teachingYears, $teachingModalities,
                    $cvFilename, $cvOriginalName, $portfolioUrl, $linkedinUrl, $orcidUrl, $websiteUrl,
                    $consentPrivacy, $consentMarketing
                ]);
                $appId = (int)$db->lastInsertId();

                // 5. Save Course Preferences
                if (!empty($selectedCourses)) {
                    $stmtPref = $db->prepare("
                        INSERT INTO faculty_preferences (application_id, course_name, priority_order)
                        VALUES (?, ?, ?)
                    ");
                    $order = 1;
                    foreach ($selectedCourses as $cName) {
                        $stmtPref->execute([$appId, trim($cName), $order++]);
                    }
                }

                // 6. Audit Log
                $stmtAudit = $db->prepare("
                    INSERT INTO audit_log (action, entity_type, entity_id, details, ip_address, user_agent)
                    VALUES ('FACULTY_APPLICATION_SUBMITTED', 'FACULTY_CANDIDATE', ?, ?, ?, ?)
                ");
                $stmtAudit->execute([
                    $sicId,
                    "Candidatura presentata da {$firstName} {$lastName} ({$profession})",
                    $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                    $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
                ]);

                $db->commit();

                // 7. Send Email Confirmation & Admin Alert
                require_once __DIR__ . '/includes/mailer-helper.php';
                @send_application_confirmation_email($email, "{$firstName} {$lastName}", $sicId, $appCode, $profession);

                // Redirect to Thank-You Page with SIC-ID
                header("Location: /thank-you.php?sic_id=" . urlencode($sicId) . "&code=" . urlencode($appCode));
                exit;

            } catch (Exception $e) {
                $db->rollBack();
                error_log('Application Submit Error: ' . $e->getMessage());
                $error = 'Si è verificato un errore tecnico durante il salvataggio. Riprova.';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top: 40px;">
  <div class="container" style="max-width: 900px;">

    <div style="text-align: center; margin-bottom: 30px;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2.5px; font-weight: 800;">
        Procedura Telematica Ufficiale
      </span>
      <h1 style="font-size: clamp(26px, 4vw, 38px); margin-top: 8px;">
        Candidatura <span class="gold-text">Faculty CAMPUS</span>
      </h1>
      <p style="color: var(--text-muted); font-size: 15px; margin-top: 6px;">
        Compilazione guidata in 12 passaggi. Assegnazione istantanea del protocollo digitale <strong>SIC-ID</strong>.
      </p>
    </div>

    <?php if ($error): ?>
      <div style="background: rgba(0, 0, 0, 0.7); border: 1px solid var(--gold-primary); color: #ffffff; padding: 14px 20px; border-radius: var(--radius-sm); margin-bottom: 25px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
        <?= icon_gold('shield', 18) ?> <span><strong>Attenzione:</strong> <?= sanitize_output($error) ?></span>
      </div>
    <?php endif; ?>

    <!-- WIZARD CONTAINER -->
    <div class="glass-card" style="padding: 35px 30px;">
      
      <!-- Progress Bar -->
      <div style="margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; font-size: 12px; color: var(--gold-light); margin-bottom: 8px; font-weight: 600;">
          <span id="step-indicator">Passaggio 1 di 12: Identità</span>
          <span id="step-percentage">8%</span>
        </div>
        <div style="width: 100%; height: 6px; background: rgba(255,255,255,0.08); border-radius: 3px; overflow: hidden;">
          <div id="progress-bar-fill" style="width: 8.33%; height: 100%; background: var(--gold-gradient); transition: width 0.3s ease;"></div>
        </div>
      </div>

      <form id="wizard-form" action="/apply.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">

        <!-- STEP 1: IDENTITÀ -->
        <div class="form-step active" data-step="1" data-title="Chi Sei">
          <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
            <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 4px;">
              Chi Sei: I Tuoi Dati Identificativi
            </h3>
            <p style="color: var(--text-dim); font-size: 13px; margin: 0;">
              Ci permettono di aprire la tua pratica ufficiale ed emettere il tuo protocollo crittografico immutabile SIC-ID.
            </p>
          </div>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
              <label class="form-label">Nome *</label>
              <input type="text" name="first_name" class="form-control" placeholder="es. Mario" required>
            </div>
            <div>
              <label class="form-label">Cognome *</label>
              <input type="text" name="last_name" class="form-control" placeholder="es. Rossi" required>
            </div>
          </div>
          <div style="margin-top: 16px;">
            <label class="form-label">Codice Fiscale * (16 caratteri)</label>
            <input type="text" name="fiscal_code" class="form-control" maxlength="16" placeholder="RSSMRA80A01H501U" style="text-transform: uppercase;" required>
          </div>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 16px;">
            <div>
              <label class="form-label">Data di Nascita</label>
              <input type="date" name="birth_date" class="form-control">
            </div>
            <div>
              <label class="form-label">Luogo di Nascita</label>
              <input type="text" name="birth_place" class="form-control" placeholder="Comune / Stato">
            </div>
          </div>
          <div style="margin-top: 16px;">
            <label class="form-label">Nazionalità</label>
            <input type="text" name="nationality" class="form-control" value="Italiana">
          </div>
        </div>

        <!-- STEP 2: CONTATTI -->
        <div class="form-step" data-step="2" data-title="Come Contattarti">
          <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
            <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 4px;">
              Come Possiamo Contattarti
            </h3>
            <p style="color: var(--text-dim); font-size: 13px; margin: 0;">
              Utilizzeremo questi recapiti esclusivamente per comunicarti l'esito della Commissione e concordare il colloquio conoscitivo.
            </p>
          </div>
          <div style="margin-bottom: 16px;">
            <label class="form-label">Email Professionale / Personale *</label>
            <input type="email" name="email" class="form-control" placeholder="mario.rossi@studio.it" required>
            <small style="color: var(--text-dim); font-size: 12px;">Riceverai qui la ricevuta protocollata e il codice SIC-ID.</small>
          </div>
          <div style="margin-bottom: 16px;">
            <label class="form-label">Telefono Cellulare / WhatsApp *</label>
            <input type="tel" name="phone" class="form-control" placeholder="+39 333 1234567" required>
          </div>
          <div>
            <label class="form-label">Indirizzo PEC (Posta Elettronica Certificata - Opzionale)</label>
            <input type="email" name="pec" class="form-control" placeholder="mario.rossi@pec.ordine.it">
          </div>
        </div>

        <!-- STEP 3: RESIDENZA / DOMICILIO -->
        <div class="form-step" data-step="3" data-title="Dove Operi">
          <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
            <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 4px;">
              Il Tuo Radicamento Territoriale
            </h3>
            <p style="color: var(--text-dim); font-size: 13px; margin: 0;">
              Ci aiuta a proporti incarichi nei poli territoriali più vicini e sessioni di laboratorio compatibili con la tua sede.
            </p>
          </div>
          <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 16px;">
            <div>
              <label class="form-label">Indirizzo (Via/Piazza)</label>
              <input type="text" name="address_street" class="form-control" placeholder="Via Mantovana">
            </div>
            <div>
              <label class="form-label">N. Civico</label>
              <input type="text" name="address_number" class="form-control" placeholder="78">
            </div>
          </div>
          <div style="display: grid; grid-template-columns: 1fr 2fr 1fr; gap: 16px; margin-top: 16px;">
            <div>
              <label class="form-label">CAP</label>
              <input type="text" name="address_zip" class="form-control" placeholder="45014">
            </div>
            <div>
              <label class="form-label">Comune</label>
              <input type="text" name="address_city" class="form-control" placeholder="Porto Viro">
            </div>
            <div>
              <label class="form-label">Provincia</label>
              <input type="text" name="address_province" class="form-control" maxlength="2" placeholder="RO" style="text-transform: uppercase;">
            </div>
          </div>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 16px;">
            <div>
              <label class="form-label">Regione</label>
              <input type="text" name="address_region" class="form-control" placeholder="Veneto">
            </div>
            <div>
              <label class="form-label">Stato</label>
              <input type="text" name="address_country" class="form-control" value="Italia">
            </div>
          </div>
        </div>

        <!-- STEP 4: PROFESSIONE & ATECO -->
        <div class="form-step" data-step="4" data-title="Professione">
          <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
            <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 4px;">
              La Tua Attività Prevalente
            </h3>
            <p style="color: var(--text-dim); font-size: 13px; margin: 0;">
              Inquadra il tuo ambito professionale per connetterti alla Scuola e al dipartimento accademico di riferimento.
            </p>
          </div>
          <div style="margin-bottom: 20px;">
            <label class="form-label">Professione Esercitata *</label>
            <select name="profession" class="form-control" required>
              <option value="">-- Seleziona la Tua Professione --</option>
              <?php foreach ($professions as $p): ?>
                <option value="<?= sanitize_output($p) ?>"><?= sanitize_output($p) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="form-label">Attività Prevalente / Codice ATECO</label>
            <select name="ateco_code" class="form-control">
              <option value="">-- Seleziona Codice ATECO (se applicabile) --</option>
              <?php foreach ($atecoActivities as $act): ?>
                <option value="<?= sanitize_output($act['code']) ?>"><?= sanitize_output($act['code']) ?> — <?= sanitize_output($act['desc']) ?></option>
              <?php endforeach; ?>
            </select>
            <small style="color: var(--text-dim); font-size: 12px; margin-top: 4px; display: block;">
              Dato utilizzato per la classificazione statistica dell'Albo Docenti e per i crediti formativi.
            </small>
          </div>
        </div>

        <!-- STEP 5: ORDINE, COLLEGIO O ALBO -->
        <div class="form-step" data-step="5" data-title="Albo o Ordine">
          <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
            <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 4px;">
              La Tua Abilitazione & Albo Professionale
            </h3>
            <p style="color: var(--text-dim); font-size: 13px; margin: 0;">
              Garantisce la conformità deontologica e permette l'attribuzione diretta dei crediti formativi (CFP) ai discenti.
            </p>
          </div>
          <div style="margin-bottom: 16px;">
            <label class="form-label">Tipologia di Organismo</label>
            <select name="professional_body_type" class="form-control">
              <option value="">-- Seleziona Tipologia --</option>
              <?php foreach ($bodyTypes as $k => $v): ?>
                <option value="<?= sanitize_output($k) ?>"><?= sanitize_output($v) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div style="margin-bottom: 16px;">
            <label class="form-label">Denominazione Esatta Organismo</label>
            <input type="text" name="professional_body_name" class="form-control" placeholder="es. Ordine degli Ingegneri della Provincia di Rovigo">
          </div>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
              <label class="form-label">Numero di Iscrizione / Matricola</label>
              <input type="text" name="professional_body_number" class="form-control" placeholder="es. 1234 Sez. A">
            </div>
            <div>
              <label class="form-label">Sede Territoriale / Provincia</label>
              <input type="text" name="professional_body_territory" class="form-control" placeholder="es. Rovigo (RO)">
            </div>
          </div>
        </div>

        <!-- STEP 6: COMPETENZE & BIO -->
        <div class="form-step" data-step="6" data-title="Cosa Sai Fare">
          <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
            <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 4px;">
              Raccontaci Cosa Sai Fare
            </h3>
            <p style="color: var(--text-dim); font-size: 13px; margin: 0;">
              Ci aiuterà a collegare la tua esperienza pratica alle Faculty, ai master e ai moduli didattici più pertinenti.
            </p>
          </div>
          <div style="margin-bottom: 16px;">
            <label class="form-label">Anni di Esperienza Professionale Comprovata</label>
            <input type="number" name="years_experience" class="form-control" min="0" max="60" value="5">
          </div>
          <div style="margin-bottom: 16px;">
            <label class="form-label">Discipline Principali di Padronanza (separate da virgola)</label>
            <input type="text" name="disciplines" class="form-control" placeholder="es. Diritto del Lavoro, Contrattazione Collettiva, Welfare Aziendale">
          </div>
          <div style="margin-bottom: 16px;">
            <label class="form-label">Parole Chiave / Hard Skills</label>
            <input type="text" name="skills" class="form-control" placeholder="es. BIM Management, Project Management, Revisione Contabile">
          </div>
          <div>
            <label class="form-label">Breve Bio Professionale (Sintesi per l'Albo Docenti)</label>
            <textarea name="bio" class="form-control" rows="3" placeholder="Sintetizza il tuo percorso, ruoli ricoperti, progetti significativi o pubblicazioni..."></textarea>
          </div>
        </div>

        <!-- STEP 7: CORSI & CATTEDRE PRESCELTE (MAX 10) -->
        <div class="form-step" data-step="7" data-title="Insegnamenti Desiderati">
          <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
            <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 4px;">
              Gli Insegnamenti Che Desideri Guidare
            </h3>
            <p style="color: var(--text-dim); font-size: 13px; margin: 0;">
              Seleziona fino a 10 percorsi dal catalogo accademico di 2.119 corsi per i quali intendi proporti come Docente o Coordinatore.
              (<span id="courses-selected-counter" style="color: var(--gold-light); font-weight: 700;">0</span>/10 selezionati)
            </p>
          </div>

          <div style="margin-bottom: 12px;">
            <input type="text" id="course-filter-input" placeholder="Digita per filtrare tra i 2.119 percorsi didattici (es. Sicurezza, Diritto, BIM, Management...)" class="form-control" style="font-size: 13.5px; padding: 10px 14px;">
          </div>

          <div id="course-list-box" style="max-height: 340px; overflow-y: auto; padding: 12px; background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); border-radius: var(--radius-sm);">
            <?php 
              $preselectedCourse = trim($_GET['course'] ?? '');
              foreach ($courses as $c): 
                $isPreselected = ($preselectedCourse !== '' && strcasecmp($preselectedCourse, $c['title']) === 0);
            ?>
              <label class="course-item-row" style="display: flex; align-items: flex-start; gap: 12px; padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.05); cursor: pointer;">
                <input type="checkbox" name="courses[]" value="<?= sanitize_output($c['title']) ?>" class="course-checkbox" <?= $isPreselected ? 'checked' : '' ?> style="margin-top: 4px; accent-color: var(--gold-primary);">
                <div style="font-size: 13.5px;">
                  <strong style="color: #ffffff;"><?= sanitize_output($c['title']) ?></strong>
                  <div style="color: var(--text-dim); font-size: 11.5px;">
                    <?= sanitize_output($c['faculty']) ?> · <?= sanitize_output($c['school']) ?> · CFP: <?= $c['cfp_credits'] ?>
                  </div>
                </div>
              </label>
            <?php endforeach; ?>
          </div>

          <script>
            // Live instant filter for the 2.117 courses
            document.addEventListener('DOMContentLoaded', function() {
              const filterInput = document.getElementById('course-filter-input');
              const rows = document.querySelectorAll('.course-item-row');
              if (filterInput && rows.length > 0) {
                filterInput.addEventListener('input', function() {
                  const query = this.value.toLowerCase().trim();
                  rows.forEach(function(row) {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(query) ? 'flex' : 'none';
                  });
                });
              }
            });
          </script>
        </div>

        <!-- STEP 8: ESPERIENZA DIDATTICA -->
        <div class="form-step" data-step="8" data-title="Esperienza Formativa">
          <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
            <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 4px;">
              La Tua Esperienza Didattica & Formativa
            </h3>
            <p style="color: var(--text-dim); font-size: 13px; margin: 0;">
              Se hai già insegnato valorizziamo il tuo percorso; se è la prima volta, CAMPUS ti affiancherà con supporto pedagogico e metodologico.
            </p>
          </div>
          <div style="margin-bottom: 20px;">
            <label class="form-label">Anni di Insegnamento / Formazione già svolti</label>
            <input type="number" name="teaching_years" class="form-control" min="0" max="50" value="0">
            <small style="color: var(--text-dim); font-size: 12px;">Se è la tua prima esperienza didattica istituzionale, indica 0 (sono previsti moduli di affiancamento didattico CAMPUS).</small>
          </div>
          <div>
            <label class="form-label">Modalità di Erogazione Preferite (Seleziona quelle applicabili)</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 8px;">
              <label style="display: flex; align-items: center; gap: 10px; font-size: 13.5px; color: var(--text-muted); cursor: pointer;">
                <input type="checkbox" name="teaching_modalities[]" value="ONLINE_LIVE" checked style="accent-color: var(--gold-primary);">
                Aule Virtuali Online (Live sincrono)
              </label>
              <label style="display: flex; align-items: center; gap: 10px; font-size: 13.5px; color: var(--text-muted); cursor: pointer;">
                <input type="checkbox" name="teaching_modalities[]" value="ASINCRONO" checked style="accent-color: var(--gold-primary);">
                Videolezioni Registrate (Asincrono)
              </label>
              <label style="display: flex; align-items: center; gap: 10px; font-size: 13.5px; color: var(--text-muted); cursor: pointer;">
                <input type="checkbox" name="teaching_modalities[]" value="PRESENZA" style="accent-color: var(--gold-primary);">
                Lezioni in Presenza / Campus
              </label>
              <label style="display: flex; align-items: center; gap: 10px; font-size: 13.5px; color: var(--text-muted); cursor: pointer;">
                <input type="checkbox" name="teaching_modalities[]" value="LABORATORI" style="accent-color: var(--gold-primary);">
                Workshop & Laboratori Territoriali
              </label>
            </div>
          </div>
        </div>

        <!-- STEP 9: DOCUMENTI & CV PDF -->
        <div class="form-step" data-step="9" data-title="Allega il Tuo CV">
          <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
            <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 4px;">
              Allega il Tuo Curriculum Vitae (PDF)
            </h3>
            <p style="color: var(--text-dim); font-size: 13px; margin: 0;">
              Il documento fondamentale che la Commissione di Dipartimento esaminerà per valutare i tuoi titoli e la tua storia sul campo.
            </p>
          </div>
          <div style="margin-bottom: 24px; background: rgba(212, 175, 55, 0.05); border: 1px dashed var(--gold-primary); padding: 20px; border-radius: var(--radius-sm); text-align: center;">
            <label class="form-label" style="color: var(--gold-light); font-size: 14px;">Carica Curriculum Vitae Aggiornato (Obbligatorio - Solo PDF, max 12 MB) *</label>
            <input type="file" name="cv_file" accept=".pdf,application/pdf" class="form-control" style="max-width: 400px; margin: 10px auto;" required>
            <div style="font-size: 11.5px; color: var(--text-dim);">Formato standard europeo o professionale accademico.</div>
          </div>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
              <label class="form-label">Profilo LinkedIn (Opzionale)</label>
              <input type="url" name="linkedin_url" class="form-control" placeholder="https://linkedin.com/in/nomeprofilo">
            </div>
            <div>
              <label class="form-label">Sito Web / Portfolio (Opzionale)</label>
              <input type="url" name="website_url" class="form-control" placeholder="https://studio.it">
            </div>
          </div>
        </div>

        <!-- STEP 10: CONSENSI GDPR -->
        <div class="form-step" data-step="10" data-title="Tutela e Privacy">
          <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
            <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 4px;">
              Tutela dei Tuoi Dati & Privacy (GDPR)
            </h3>
            <p style="color: var(--text-dim); font-size: 13px; margin: 0;">
              I tuoi dati sono protetti e trattati esclusivamente per i fini della selezione accademica da CAMPUS, senza alcuna cessione a terzi.
            </p>
          </div>
          <div style="margin-bottom: 20px; background: rgba(255,255,255,0.02); border: 1px solid var(--border-subtle); padding: 18px; border-radius: var(--radius-sm);">
            <label style="display: flex; align-items: flex-start; gap: 14px; font-size: 13.5px; color: #ffffff; cursor: pointer;">
              <input type="checkbox" name="consent_privacy" value="1" required style="margin-top: 3px; accent-color: var(--gold-primary);">
              <div>
                <strong>Informativa sul Trattamento dei Dati per la Candidatura (OBBLIGATORIA) *</strong>
                <p style="color: var(--text-dim); font-size: 12.5px; margin-top: 4px;">
                  Dichiaro di aver preso visione dell'<a href="/legal/privacy.php" target="_blank" style="color: var(--gold-light); text-decoration: underline;">Informativa Privacy</a> e acconsento al trattamento dei dati personali forniti e del CV per le finalità di valutazione, selezione e gestione della candidatura accademica a cura di <?= LEGAL_ENTITY_NAME ?> (Titolare del trattamento).
                </p>
              </div>
            </label>
          </div>

          <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-subtle); padding: 18px; border-radius: var(--radius-sm);">
            <label style="display: flex; align-items: flex-start; gap: 14px; font-size: 13.5px; color: var(--text-muted); cursor: pointer;">
              <input type="checkbox" name="consent_marketing" value="1" style="margin-top: 3px; accent-color: var(--gold-primary);">
              <div>
                <strong>Comunicazioni Istituzionali e Notifiche di Nuovi Bandi (FACOLTATIVO)</strong>
                <p style="color: var(--text-dim); font-size: 12.5px; margin-top: 4px;">
                  Desidero ricevere la newsletter istituzionale, gli inviti a convegni e le comunicazioni su future opportunità didattiche di CAMPUS. (Consenso separato, revocabile in ogni momento).
                </p>
              </div>
            </label>
          </div>
        </div>

        <!-- STEP 11: REVIEW & RIEPILOGO -->
        <div class="form-step" data-step="11" data-title="Verifica Candidatura">
          <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
            <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 4px;">
              Rivedi la Tua Candidatura Prima del Deposito
            </h3>
            <p style="color: var(--text-dim); font-size: 13px; margin: 0;">
              Prenditi un momento per verificare la correttezza dei dati inseriti prima del deposito ufficiale.
            </p>
          </div>

          <div id="review-summary" style="background: rgba(0,0,0,0.3); border: 1px solid var(--border-glass); border-radius: var(--radius-sm); padding: 20px; font-size: 13.5px; line-height: 1.8;">
            <!-- Populated via JavaScript -->
          </div>
        </div>

        <!-- STEP 12: SUBMIT & PROTOCOLLO -->
        <div class="form-step" data-step="12" data-title="Ricevi il Tuo SIC-ID">
          <div style="text-align: center; padding: 20px 0;">
            <img src="/assets/visual/Campus_Emblema_Transparent.png" alt="CAMPUS" style="width: 85px; margin-bottom: 16px; filter: drop-shadow(0 0 20px rgba(212,175,55,0.45));">
            <h3 style="color: #ffffff; font-size: 24px; margin-bottom: 10px; font-family: 'Cinzel', serif;">
              Deposito Ufficiale & Protocollo SIC-ID
            </h3>
            <p style="color: var(--text-muted); font-size: 15px; max-width: 620px; margin: 0 auto 30px auto; line-height: 1.6;">
              La tua candidatura sta per entrare ufficialmente negli archivi centrali di CAMPUS. Confermando, verrà emesso il tuo protocollo crittografico immutabile <strong>SIC-ID</strong> e riceverai la ricevuta di deposito all'indirizzo email indicato.
            </p>
            <button type="submit" class="btn-gold" style="font-size: 16px; padding: 18px 45px; display: inline-flex; align-items: center; gap: 10px; font-weight: 700;">
              <?= icon_gold('document', 18) ?> CONFERMA E RICEVI IL TUO SIC-ID
            </button>
            <div style="margin-top: 14px; font-size: 12px; color: var(--text-dim);">
              L'invio non comporta alcun addebito né vincolo economico automatico.
            </div>
          </div>
        </div>

        <!-- NAVIGATION BUTTONS -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 35px; padding-top: 20px; border-top: 1px solid var(--border-subtle);">
          <button type="button" id="btn-prev" class="btn-outline-gold" style="display: none;">
            ← Indietro
          </button>
          <div style="margin-left: auto;">
            <button type="button" id="btn-next" class="btn-gold">
              Avanti →
            </button>
          </div>
        </div>

      </form>
    </div>

  </div>
</section>

<!-- WIZARD INTERACTIVE CONTROLLER SCRIPT -->
<script>
document dangerouslySetInnerHTML = (function() {
  let currentStep = 1;
  const totalSteps = 12;

  const steps = document.querySelectorAll('.form-step');
  const btnPrev = document.getElementById('btn-prev');
  const btnNext = document.getElementById('btn-next');
  const indicator = document.getElementById('step-indicator');
  const percentage = document.getElementById('step-percentage');
  const progressBar = document.getElementById('progress-bar-fill');

  // Courses counter (Max 10)
  const courseCheckboxes = document.querySelectorAll('.course-checkbox');
  const counterSpan = document.getElementById('courses-selected-counter');

  courseCheckboxes.forEach(cb => {
    cb.addEventListener('change', () => {
      const selected = document.querySelectorAll('.course-checkbox:checked');
      if (selected.length > 10) {
        cb.checked = false;
        alert('È possibile selezionare al massimo 10 insegnamenti preferiti.');
      }
      const count = document.querySelectorAll('.course-checkbox:checked').length;
      if (counterSpan) counterSpan.textContent = count;
    });
  });

  function updateWizard(step) {
    steps.forEach(s => s.classList.remove('active'));
    const target = document.querySelector(`.form-step[data-step="${step}"]`);
    if (target) {
      target.classList.add('active');
      const title = target.getAttribute('data-title') || '';
      indicator.textContent = `Passaggio ${step} di ${totalSteps}: ${title}`;
      const pct = Math.round((step / totalSteps) * 100);
      percentage.textContent = `${pct}%`;
      progressBar.style.width = `${pct}%`;
    }

    // Toggle Back button
    btnPrev.style.display = (step > 1 && step < 12) ? 'inline-flex' : 'none';

    // Toggle Next button
    if (step >= 12) {
      btnNext.style.display = 'none';
    } else {
      btnNext.style.display = 'inline-flex';
      btnNext.textContent = (step === 11) ? 'Procedi al Deposito →' : 'Avanti →';
    }

    // If step 11, generate review summary
    if (step === 11) {
      generateReviewSummary();
    }
  }

  function validateCurrentStep(step) {
    const activeStepEl = document.querySelector(`.form-step[data-step="${step}"]`);
    if (!activeStepEl) return true;

    const requiredInputs = activeStepEl.querySelectorAll('input[required], select[required], textarea[required]');
    for (const input of requiredInputs) {
      if (!input.checkValidity()) {
        input.reportValidity();
        return false;
      }
    }
    return true;
  }

  function generateReviewSummary() {
    const form = document.getElementById('wizard-form');
    const formData = new FormData(form);
    const summaryEl = document.getElementById('review-summary');

    const firstName = formData.get('first_name') || '-';
    const lastName = formData.get('last_name') || '-';
    const cf = formData.get('fiscal_code') || '-';
    const email = formData.get('email') || '-';
    const phone = formData.get('phone') || '-';
    const profession = formData.get('profession') || '-';
    const body = formData.get('professional_body_name') || 'Non specificato';
    const courses = formData.getAll('courses[]');

    summaryEl.innerHTML = `
      <div><strong>Candidato:</strong> ${firstName} ${lastName}</div>
      <div><strong>Codice Fiscale:</strong> <span style="font-family:monospace; color:var(--gold-light);">${cf}</span></div>
      <div><strong>Contatti:</strong> ${email} · ${phone}</div>
      <div><strong>Professione:</strong> ${profession}</div>
      <div><strong>Organismo / Albo:</strong> ${body}</div>
      <div><strong>Insegnamenti Selezionati (${courses.length}):</strong> ${courses.join(', ') || 'Nessuno selezionato'}</div>
      <div style="margin-top: 10px; color: var(--gold-primary); display: flex; align-items: center; gap: 6px;">
        <svg style="width: 15px; height: 15px; fill: var(--gold-primary);" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
        <span>Documento CV PDF e Consenso Privacy allegati.</span>
      </div>
    `;
  }

  btnNext.addEventListener('click', () => {
    if (validateCurrentStep(currentStep)) {
      if (currentStep < totalSteps) {
        currentStep++;
        updateWizard(currentStep);
        window.scrollTo({ top: 150, behavior: 'smooth' });
      }
    }
  });

  btnPrev.addEventListener('click', () => {
    if (currentStep > 1) {
      currentStep--;
      updateWizard(currentStep);
      window.scrollTo({ top: 150, behavior: 'smooth' });
    }
  });

  updateWizard(1);
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
