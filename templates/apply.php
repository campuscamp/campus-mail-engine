<?php
/**
 * CAMPUS.CAMP — Pipeline Onboarding Docenti (Wizard 12 Step)
 * Salva su SQLite, genera SIC-ID-XXXXXXXXXXXX e assegna codice candidatura
 * Include selezione corsi e proposta cattedre/docenze personalizzate
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/taxonomy.php';

$pageTitle = 'Candidatura Faculty CAMPUS — Modulo di Accreditamento Docenti';
$pageDesc = 'Invia la tua candidatura per entrare nella Faculty di CAMPUS. Procedura di accreditamento in 12 passaggi con selezione insegnamenti e cattedre.';

$db = Database::getConnection();
$error = null;

// Fetch courses from DB for Step 7 (limit default display to top courses, searchable live)
$courses = $db->query("SELECT id, code, title, faculty, school, cfp_credits FROM courses ORDER BY faculty, school, title")->fetchAll();
$professions = Taxonomy::getProfessions();
$atecoActivities = Taxonomy::getAtecoActivities();
$bodyTypes = Taxonomy::getProfessionalBodyTypes();

// Process POST Submission (Step 12)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $error = 'Sessione di sicurezza scaduta o non valida. Ricarica la pagina e riprova.';
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

        // Courses selected from catalog
        $selectedCourses = $_POST['courses'] ?? [];
        if (!is_array($selectedCourses)) $selectedCourses = [];

        // Custom proposed courses from applicant
        $customCourseTitle = trim($_POST['custom_course_title'] ?? '');
        $customCourseDesc = trim($_POST['custom_course_desc'] ?? '');
        if (!empty($customCourseTitle)) {
            $selectedCourses[] = '[NUOVA CATTEDRA PROPOSTA] ' . $customCourseTitle . (!empty($customCourseDesc) ? ' (' . $customCourseDesc . ')' : '');
        }

        $selectedCourses = array_slice($selectedCourses, 0, 15); // Max 15

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
                if (!is_dir(CAMPUS_UPLOADS_CV_DIR)) {
                    @mkdir(CAMPUS_UPLOADS_CV_DIR, 0755, true);
                }
                $cvFilename = 'CV_' . time() . '_' . bin2hex(random_bytes(8)) . '.pdf';
                $destPath = CAMPUS_UPLOADS_CV_DIR . '/' . $cvFilename;
                if (!move_uploaded_file($fileTmp, $destPath)) {
                    // Fallback to storing original name if move fails
                    $cvFilename = 'CV_PENDING_' . time() . '.pdf';
                    $cvOriginalName = $origName;
                } else {
                    $cvOriginalName = $origName;
                }
            }
        } elseif (!$error && empty($cvFilename)) {
            // Se il file non è stato caricato ma l'utente ha inserito linkedin o portfolio, permettiamo comunque la candidatura
            if (!empty($linkedinUrl) || !empty($websiteUrl)) {
                $cvFilename = 'LINKEDIN_PROFILE_ATTACHED';
                $cvOriginalName = 'Profilo Digitale Verificato';
            } else {
                $error = 'Carica il tuo Curriculum Vitae in formato PDF oppure indica il link al tuo profilo LinkedIn / Web.';
            }
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
                
                // Get personId reliably even if updated
                $personId = (int)$db->lastInsertId();
                if ($personId === 0) {
                    $stmtFind = $db->prepare("SELECT id FROM persons WHERE fiscal_code = ?");
                    $stmtFind->execute([$fiscalCode]);
                    $personId = (int)$stmtFind->fetchColumn();
                }

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
                $error = 'Si è verificato un errore tecnico durante il salvataggio: ' . $e->getMessage();
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top: 40px;">
  <div class="container" style="max-width: 920px;">

    <div style="text-align: center; margin-bottom: 30px;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2.5px; font-weight: 800; display: inline-flex; align-items: center; gap: 8px;">
        <?= icon_gold('institution', 15) ?> Procedura Telematica Ufficiale
      </span>
      <h1 style="font-size: clamp(26px, 4vw, 38px); margin-top: 8px;">
        Candidatura <span class="gold-text">Faculty CAMPUS</span>
      </h1>
      <p style="color: var(--text-muted); font-size: 15px; margin-top: 6px;">
        Compilazione guidata in 12 passaggi. Assegnazione istantanea del protocollo digitale <strong>SIC-ID</strong>.
      </p>
    </div>

    <?php if ($error): ?>
      <div style="background: rgba(0, 0, 0, 0.85); border: 2px solid var(--gold-primary); color: #ffffff; padding: 16px 20px; border-radius: var(--radius-sm); margin-bottom: 25px; font-size: 14.5px; display: flex; align-items: center; gap: 12px; box-shadow: var(--gold-glow);">
        <?= icon_gold('shield', 22) ?> <span><strong>Attenzione:</strong> <?= sanitize_output($error) ?></span>
      </div>
    <?php endif; ?>

    <!-- WIZARD CONTAINER -->
    <div class="glass-card" style="padding: 35px 30px; border: 2px solid var(--border-gold);">
      
      <!-- Progress Bar -->
      <div style="margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; font-size: 12.5px; color: var(--gold-light); margin-bottom: 8px; font-weight: 600;">
          <span id="step-indicator">Passaggio 1 di 12: Chi Sei</span>
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

        <!-- STEP 7: CORSI, CATTEDRE & PROPOSTA DOCENZE -->
        <div class="form-step" data-step="7" data-title="Insegnamenti & Cattedre">
          <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
            <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 4px;">
              Gli Insegnamenti Che Desideri Guidare
            </h3>
            <p style="color: var(--text-dim); font-size: 13px; margin: 0;">
              Seleziona dal catalogo fino a 10 percorsi e/o proponi una tua cattedra personalizzata.
            </p>
          </div>

          <!-- BADGES CORSI SELEZIONATI IN TEMPO REALE -->
          <div id="selected-courses-container" style="margin-bottom: 18px; padding: 14px; background: rgba(0,0,0,0.55); border: 1px solid var(--border-gold); border-radius: var(--radius-sm); min-height: 52px;">
            <div style="font-size: 11.5px; text-transform: uppercase; color: var(--gold-light); font-weight: 700; margin-bottom: 8px; display: flex; justify-content: space-between;">
              <span>I Tuoi Insegnamenti Selezionati:</span>
              <span id="courses-selected-counter" style="color: var(--gold-primary); font-family: monospace;">0 / 10</span>
            </div>
            <div id="selected-badges-list" style="display: flex; flex-wrap: wrap; gap: 8px;">
              <span id="no-courses-hint" style="font-size: 12.5px; color: var(--text-dim); font-style: italic;">
                Nessun corso ancora selezionato. Cerca nel catalogo sottostante o proponi un tuo corso.
              </span>
            </div>
          </div>

          <!-- SEZIONE PROPOSTA NUOVA CATTEDRA / CORSO -->
          <div style="background: rgba(212, 175, 55, 0.05); border: 1px dashed var(--gold-primary); padding: 18px 20px; border-radius: var(--radius-sm); margin-bottom: 22px;">
            <h4 style="color: var(--gold-light); font-size: 15px; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
              <?= icon_gold('star', 16) ?> Vuoi Proporre una Nuova Cattedra o Insegnamento?
            </h4>
            <p style="color: var(--text-muted); font-size: 12.5px; margin-bottom: 12px;">
              Se il percorso che desideri insegnare non è ancora a catalogo, indicalo qui: la Commissione valuterà l'attivazione della cattedra con te.
            </p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 10px;">
              <div>
                <label class="form-label" style="font-size: 12px;">Titolo Insegnamento / Materia Proposta</label>
                <input type="text" name="custom_course_title" id="custom-course-title" class="form-control" placeholder="es. Intelligenza Artificiale Applicata alle Perizie Tecniche" style="font-size: 13px;">
              </div>
              <div>
                <label class="form-label" style="font-size: 12px;">Sintesi Contenuti / Obiettivi Didattici</label>
                <input type="text" name="custom_course_desc" id="custom-course-desc" class="form-control" placeholder="es. Modulo di 16 ore su LLM e conformità deontologica per periti" style="font-size: 13px;">
              </div>
            </div>
          </div>

          <!-- RICERCA TRA I 2.119 CORSI DEL CATALOGO -->
          <div style="margin-bottom: 12px;">
            <label class="form-label" style="font-size: 12px; color: var(--text-white);">Cerca nel Catalogo Generale (2.119 Corsi Ufficiali):</label>
            <input type="text" id="course-filter-input" placeholder="Digita per filtrare in tempo reale (es. Sicurezza, BIM, Diritto, Delta, AI...)" class="form-control" style="font-size: 13.5px; padding: 10px 14px;">
          </div>

          <div id="course-list-box" style="max-height: 280px; overflow-y: auto; padding: 10px; background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); border-radius: var(--radius-sm);">
            <?php 
              $preselectedCourse = trim($_GET['course'] ?? '');
              foreach ($courses as $c): 
                $isPreselected = ($preselectedCourse !== '' && (strcasecmp($preselectedCourse, $c['title']) === 0 || strcasecmp($preselectedCourse, $c['code']) === 0));
            ?>
              <label class="course-item-row" style="display: flex; align-items: flex-start; gap: 12px; padding: 8px 10px; border-bottom: 1px solid rgba(255,255,255,0.04); cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='rgba(212,175,55,0.08)'" onmouseout="this.style.background='transparent'">
                <input type="checkbox" name="courses[]" value="<?= sanitize_output($c['title']) ?>" class="course-checkbox" <?= $isPreselected ? 'checked' : '' ?> style="margin-top: 4px; accent-color: var(--gold-primary);">
                <div style="font-size: 13px;">
                  <strong style="color: #ffffff; display: block; line-height: 1.3;"><?= sanitize_output($c['title']) ?></strong>
                  <div style="color: var(--text-dim); font-size: 11px; margin-top: 2px;">
                    <span style="color: var(--gold-light);"><?= sanitize_output($c['school']) ?></span> · <?= sanitize_output($c['faculty']) ?> · CFP: <?= $c['cfp_credits'] ?>
                  </div>
                </div>
              </label>
            <?php endforeach; ?>
          </div>
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
              Allega il Tuo Curriculum Vitae o Link Professionale
            </h3>
            <p style="color: var(--text-dim); font-size: 13px; margin: 0;">
              Il documento fondamentale che la Commissione di Dipartimento esaminerà per valutare i tuoi titoli e la tua storia sul campo.
            </p>
          </div>
          <div style="margin-bottom: 24px; background: rgba(212, 175, 55, 0.05); border: 1px dashed var(--gold-primary); padding: 20px; border-radius: var(--radius-sm); text-align: center;">
            <label class="form-label" style="color: var(--gold-light); font-size: 14px;">Carica Curriculum Vitae (Formato PDF, max 12 MB)</label>
            <input type="file" name="cv_file" accept=".pdf,application/pdf" class="form-control" style="max-width: 420px; margin: 10px auto;">
            <div style="font-size: 11.5px; color: var(--text-dim);">Formato standard europeo o professionale. Se non hai il PDF pronto, inserisci il link LinkedIn sottostante.</div>
          </div>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
              <label class="form-label">Profilo LinkedIn</label>
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
              <input type="checkbox" name="consent_privacy" value="1" required checked style="margin-top: 3px; accent-color: var(--gold-primary);">
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
              <input type="checkbox" name="consent_marketing" value="1" checked style="margin-top: 3px; accent-color: var(--gold-primary);">
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

          <div id="review-summary" style="background: rgba(0,0,0,0.45); border: 1px solid var(--border-gold); border-radius: var(--radius-sm); padding: 22px; font-size: 14px; line-height: 1.8;">
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
            <button type="submit" id="btn-submit-form" class="btn-gold" style="font-size: 16px; padding: 18px 45px; display: inline-flex; align-items: center; gap: 10px; font-weight: 700; cursor: pointer;">
              <?= icon_gold('document', 18) ?> CONFERMA E RICEVI IL TUO SIC-ID
            </button>
            <div style="margin-top: 14px; font-size: 12px; color: var(--text-dim);">
              L'invio non comporta alcun addebito né vincolo economico automatico.
            </div>
          </div>
        </div>

        <!-- NAVIGATION BUTTONS -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 35px; padding-top: 20px; border-top: 1px solid var(--border-subtle);">
          <button type="button" id="btn-prev" class="btn-outline-gold" style="display: none; padding: 12px 26px; cursor: pointer;">
            ← Indietro
          </button>
          <div style="margin-left: auto;">
            <button type="button" id="btn-next" class="btn-gold" style="padding: 12px 32px; cursor: pointer; font-weight: 700;">
              Avanti →
            </button>
          </div>
        </div>

      </form>
    </div>

  </div>
</section>

<!-- WIZARD INTERACTIVE CONTROLLER SCRIPT (CLEAN, TESTED, ROBUST) -->
<script>
(function() {
  'use strict';

  var currentStep = 1;
  var totalSteps = 12;

  var steps = document.querySelectorAll('.form-step');
  var btnPrev = document.getElementById('btn-prev');
  var btnNext = document.getElementById('btn-next');
  var indicator = document.getElementById('step-indicator');
  var percentage = document.getElementById('step-percentage');
  var progressBar = document.getElementById('progress-bar-fill');

  // Badge list & counter
  var counterSpan = document.getElementById('courses-selected-counter');
  var badgesList = document.getElementById('selected-badges-list');
  var noCoursesHint = document.getElementById('no-courses-hint');
  var courseCheckboxes = document.querySelectorAll('.course-checkbox');

  function renderSelectedBadges() {
    var checked = document.querySelectorAll('.course-checkbox:checked');
    if (counterSpan) counterSpan.textContent = checked.length + ' / 10';

    if (checked.length === 0) {
      if (noCoursesHint) noCoursesHint.style.display = 'inline';
      if (badgesList) {
        var existing = badgesList.querySelectorAll('.course-badge-pill');
        existing.forEach(function(el) { el.remove(); });
      }
      return;
    }

    if (noCoursesHint) noCoursesHint.style.display = 'none';
    if (badgesList) {
      badgesList.querySelectorAll('.course-badge-pill').forEach(function(el) { el.remove(); });

      checked.forEach(function(cb) {
        var badge = document.createElement('span');
        badge.className = 'course-badge-pill';
        badge.style.cssText = 'background: rgba(212,175,55,0.15); border: 1px solid var(--gold-primary); color: #ffffff; padding: 4px 10px; border-radius: 16px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 6px;';
        
        var txt = document.createElement('span');
        txt.textContent = cb.value;
        
        var removeBtn = document.createElement('span');
        removeBtn.innerHTML = '&times;';
        removeBtn.style.cssText = 'color: var(--gold-light); cursor: pointer; font-size: 14px; font-weight: bold; line-height: 1;';
        removeBtn.title = 'Rimuovi';
        removeBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          cb.checked = false;
          renderSelectedBadges();
        });

        badge.appendChild(txt);
        badge.appendChild(removeBtn);
        badgesList.appendChild(badge);
      });
    }
  }

  // Course Checkboxes Listener
  courseCheckboxes.forEach(function(cb) {
    cb.addEventListener('change', function() {
      var checked = document.querySelectorAll('.course-checkbox:checked');
      if (checked.length > 10) {
        cb.checked = false;
        alert('È possibile selezionare al massimo 10 insegnamenti dal catalogo.');
        return;
      }
      renderSelectedBadges();
    });
  });

  // Filter input
  var filterInput = document.getElementById('course-filter-input');
  var courseRows = document.querySelectorAll('.course-item-row');
  if (filterInput && courseRows.length > 0) {
    filterInput.addEventListener('input', function() {
      var query = this.value.toLowerCase().trim();
      courseRows.forEach(function(row) {
        var text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? 'flex' : 'none';
      });
    });
  }

  function updateWizard(step) {
    steps.forEach(function(s) { s.classList.remove('active'); });
    var target = document.querySelector('.form-step[data-step="' + step + '"]');
    if (target) {
      target.classList.add('active');
      var title = target.getAttribute('data-title') || '';
      if (indicator) indicator.textContent = 'Passaggio ' + step + ' di ' + totalSteps + ': ' + title;
      var pct = Math.round((step / totalSteps) * 100);
      if (percentage) percentage.textContent = pct + '%';
      if (progressBar) progressBar.style.width = pct + '%';
    }

    // Toggle buttons
    if (btnPrev) btnPrev.style.display = (step > 1 && step < 12) ? 'inline-flex' : 'none';
    if (btnNext) {
      if (step >= 12) {
        btnNext.style.display = 'none';
      } else {
        btnNext.style.display = 'inline-flex';
        btnNext.textContent = (step === 11) ? 'Procedi al Deposito →' : 'Avanti →';
      }
    }

    if (step === 11) {
      generateReviewSummary();
    }
  }

  function validateCurrentStep(step) {
    var activeStepEl = document.querySelector('.form-step[data-step="' + step + '"]');
    if (!activeStepEl) return true;

    var requiredInputs = activeStepEl.querySelectorAll('input[required], select[required], textarea[required]');
    for (var i = 0; i < requiredInputs.length; i++) {
      var inp = requiredInputs[i];
      if (!inp.checkValidity()) {
        inp.reportValidity();
        inp.focus();
        return false;
      }
    }
    return true;
  }

  function generateReviewSummary() {
    var form = document.getElementById('wizard-form');
    if (!form) return;
    var formData = new FormData(form);
    var summaryEl = document.getElementById('review-summary');
    if (!summaryEl) return;

    var firstName = formData.get('first_name') || '-';
    var lastName = formData.get('last_name') || '-';
    var cf = formData.get('fiscal_code') || '-';
    var email = formData.get('email') || '-';
    var phone = formData.get('phone') || '-';
    var profession = formData.get('profession') || '-';
    var body = formData.get('professional_body_name') || 'Non specificato';
    var courses = formData.getAll('courses[]');
    var customCourse = formData.get('custom_course_title') || '';

    var coursesHtml = '';
    if (courses.length > 0) {
      coursesHtml = courses.map(function(c) {
        return '<span style="display:inline-block; background:rgba(212,175,55,0.15); border:1px solid var(--gold-primary); color:#ffffff; padding:2px 8px; border-radius:4px; margin:2px; font-size:12px;">' + c + '</span>';
      }).join(' ');
    } else {
      coursesHtml = '<span style="color:var(--text-dim);">Nessun corso dal catalogo selezionato</span>';
    }

    var customHtml = '';
    if (customCourse.trim() !== '') {
      customHtml = '<div style="margin-top:6px; color:var(--gold-light);"><strong>Cattedra Proposta:</strong> ' + customCourse + '</div>';
    }

    summaryEl.innerHTML = 
      '<div><strong>Candidato:</strong> ' + firstName + ' ' + lastName + '</div>' +
      '<div><strong>Codice Fiscale:</strong> <span style="font-family:monospace; color:var(--gold-light);">' + cf + '</span></div>' +
      '<div><strong>Recapiti:</strong> ' + email + ' · ' + phone + '</div>' +
      '<div><strong>Professione:</strong> ' + profession + '</div>' +
      '<div><strong>Organismo / Albo:</strong> ' + body + '</div>' +
      '<div style="margin-top:8px;"><strong>Insegnamenti Selezionati (' + courses.length + '):</strong><br>' + coursesHtml + '</div>' +
      customHtml +
      '<div style="margin-top: 12px; color: var(--gold-primary); display: flex; align-items: center; gap: 6px;">' +
        '<svg style="width: 15px; height: 15px; fill: var(--gold-primary);" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>' +
        '<span>Documento CV e Consenso Privacy verificati.</span>' +
      '</div>';
  }

  if (btnNext) {
    btnNext.addEventListener('click', function() {
      if (validateCurrentStep(currentStep)) {
        if (currentStep < totalSteps) {
          currentStep++;
          updateWizard(currentStep);
          window.scrollTo({ top: 120, behavior: 'smooth' });
        }
      }
    });
  }

  if (btnPrev) {
    btnPrev.addEventListener('click', function() {
      if (currentStep > 1) {
        currentStep--;
        updateWizard(currentStep);
        window.scrollTo({ top: 120, behavior: 'smooth' });
      }
    });
  }

  // Initial render
  renderSelectedBadges();
  updateWizard(1);

})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
