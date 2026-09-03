<?php
/**
 * CAMPUS.CAMP — Portale Personale Docente / Candidato Faculty
 * Accesso riservato per monitorare cattedre, avanzamento, attestati e comunicazioni
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/icons.php';

$db = Database::getConnection();
$error = null;
$success = null;

// Gestione Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['user_sic_id']);
    unset($_SESSION['user_logged_in']);
    header('Location: /portal.php');
    exit;
}

// Auto-login tramite URL SIC-ID verificato o Login Form
$sicIdParam = trim($_GET['sic_id'] ?? '');
if (!empty($sicIdParam) && empty($_SESSION['user_sic_id'])) {
    // Verifica esistenza
    $stmtCheck = $db->prepare("SELECT a.*, p.first_name, p.last_name, p.fiscal_code FROM faculty_applications a JOIN persons p ON a.person_id = p.id WHERE a.sic_id = ?");
    $stmtCheck->execute([$sicIdParam]);
    $userApp = $stmtCheck->fetch();
    if ($userApp) {
        $_SESSION['user_sic_id'] = $userApp['sic_id'];
        $_SESSION['user_logged_in'] = true;
    }
}

// Login POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'user_login') {
    $sicOrCf = strtoupper(trim($_POST['sic_or_cf'] ?? ''));
    $email = strtolower(trim($_POST['email'] ?? ''));

    if (empty($sicOrCf) || empty($email)) {
        $error = 'Inserisci il tuo codice SIC-ID (o Codice Fiscale) e la tua Email.';
    } else {
        $stmtFind = $db->prepare("
            SELECT a.*, p.first_name, p.last_name, p.fiscal_code 
            FROM faculty_applications a 
            JOIN persons p ON a.person_id = p.id 
            WHERE (a.sic_id = ? OR p.fiscal_code = ?) AND LOWER(a.email) = ?
        ");
        $stmtFind->execute([$sicOrCf, $sicOrCf, $email]);
        $userApp = $stmtFind->fetch();

        if ($userApp) {
            $_SESSION['user_sic_id'] = $userApp['sic_id'];
            $_SESSION['user_logged_in'] = true;
            header('Location: /portal.php');
            exit;
        } else {
            $error = 'Nessuna candidatura trovata con le credenziali specificate. Verifica il SIC-ID e l\'email fornita.';
        }
    }
}

// Se l'utente è loggato, carichiamo tutti i dati
$currentUser = null;
$userCourses = [];

if (!empty($_SESSION['user_sic_id'])) {
    $stmtUser = $db->prepare("
        SELECT a.*, p.first_name, p.last_name, p.fiscal_code, p.nationality, p.birth_date, p.birth_place
        FROM faculty_applications a
        JOIN persons p ON a.person_id = p.id
        WHERE a.sic_id = ?
    ");
    $stmtUser->execute([$_SESSION['user_sic_id']]);
    $currentUser = $stmtUser->fetch();

    if ($currentUser) {
        // Gestione aggiornamento recapiti
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
            $newPhone = trim($_POST['phone'] ?? '');
            $newPec = trim($_POST['pec'] ?? '');
            $newBio = trim($_POST['bio'] ?? '');
            $newLinkedin = trim($_POST['linkedin_url'] ?? '');

            $stmtUp = $db->prepare("UPDATE faculty_applications SET phone = ?, pec = ?, bio = ?, linkedin_url = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmtUp->execute([$newPhone, $newPec, $newBio, $newLinkedin, $currentUser['id']]);
            $success = 'Recapiti e profilo aggiornati con successo.';

            // Ricarica
            $stmtUser->execute([$_SESSION['user_sic_id']]);
            $currentUser = $stmtUser->fetch();
        }

        // Gestione proposta nuova cattedra
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_custom_course') {
            $newCourseTitle = trim($_POST['new_course_title'] ?? '');
            $newCourseDesc = trim($_POST['new_course_desc'] ?? '');

            if (!empty($newCourseTitle)) {
                $maxOrder = (int)$db->query("SELECT MAX(priority_order) FROM faculty_preferences WHERE application_id = {$currentUser['id']}")->fetchColumn();
                $fullTitle = '[NUOVA CATTEDRA PROPOSTA] ' . $newCourseTitle . (!empty($newCourseDesc) ? ' (' . $newCourseDesc . ')' : '');

                $stmtAddPref = $db->prepare("INSERT INTO faculty_preferences (application_id, course_name, priority_order) VALUES (?, ?, ?)");
                $stmtAddPref->execute([$currentUser['id'], $fullTitle, $maxOrder + 1]);
                $success = 'Nuova proposta di cattedra aggiunta alla tua scheda con successo!';
            }
        }

        // Caricamento preferenze corsi
        $stmtCourses = $db->prepare("SELECT * FROM faculty_preferences WHERE application_id = ? ORDER BY priority_order ASC");
        $stmtCourses->execute([$currentUser['id']]);
        $userCourses = $stmtCourses->fetchAll();
    } else {
        unset($_SESSION['user_sic_id']);
        unset($_SESSION['user_logged_in']);
    }
}

$pageTitle = $currentUser ? 'Portale Docente — ' . sanitize_output($currentUser['first_name'] . ' ' . $currentUser['last_name']) : 'Accesso Portale Docenti & Candidati — CAMPUS';
$pageDesc = 'Area personale riservata per docenti e candidati della Faculty CAMPUS: monitoraggio cattedre, attestati SIC-ID e comunicazioni.';

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top: 40px; padding-bottom: 70px;">
  <div class="container" style="max-width: 1240px;">

    <?php if (!$currentUser): ?>
      <!-- LOGIN FORM DOCENTE / CANDIDATO -->
      <div style="max-width: 520px; margin: 0 auto;">
        
        <div style="text-align: center; margin-bottom: 30px;">
          <img src="/assets/visual/Campus_Emblema_Transparent.png" alt="CAMPUS" style="width: 85px; margin-bottom: 16px; filter: drop-shadow(0 0 20px rgba(212,175,55,0.45));">
          <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2.5px; font-weight: 800; display: block; margin-bottom: 6px;">
            Area Riservata Docenti & Candidati
          </span>
          <h1 style="font-size: clamp(24px, 3.5vw, 32px); color: #ffffff; font-family: 'Cinzel', serif;">
            Accedi al Tuo <span class="gold-text">Portale Faculty</span>
          </h1>
          <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">
            Inserisci il tuo codice <strong>SIC-ID</strong> (o Codice Fiscale) e l'indirizzo email indicato in fase di candidatura.
          </p>
        </div>

        <?php if ($error): ?>
          <div style="background: rgba(0, 0, 0, 0.85); border: 1px solid var(--gold-primary); color: #ffffff; padding: 14px 18px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 10px;">
            <?= icon_gold('shield', 18) ?> <span><?= sanitize_output($error) ?></span>
          </div>
        <?php endif; ?>

        <div class="glass-card" style="border: 2px solid var(--border-gold); padding: 35px 30px;">
          <form action="/portal.php" method="POST">
            <input type="hidden" name="action" value="user_login">

            <div style="margin-bottom: 18px;">
              <label class="form-label">Codice SIC-ID o Codice Fiscale *</label>
              <input type="text" name="sic_or_cf" class="form-control" placeholder="es. SIC-ID-XXXXXXXXXXXX o RSSMRA80..." required style="text-transform: uppercase; font-family: monospace; font-size: 14px;">
            </div>

            <div style="margin-bottom: 24px;">
              <label class="form-label">Indirizzo Email della Candidatura *</label>
              <input type="email" name="email" class="form-control" placeholder="mario.rossi@studio.it" required style="font-size: 14px;">
            </div>

            <button type="submit" class="btn-gold" style="width: 100%; padding: 14px; font-size: 14px; font-weight: 700; display: inline-flex; justify-content: center; align-items: center; gap: 8px;">
              <?= icon_gold('institution', 16) ?> ACCEDI AL PORTALE
            </button>
          </form>

          <div style="margin-top: 24px; text-align: center; border-top: 1px solid var(--border-subtle); padding-top: 16px; font-size: 12.5px; color: var(--text-dim);">
            Non hai ancora inviato la tua candidatura? <a href="/apply.php" style="color: var(--gold-light); text-decoration: underline;">Candidati ora alla Faculty</a>
          </div>
        </div>

      </div>

    <?php else: ?>
      <!-- DASHBOARD PERSONALE DOCENTE / CANDIDATO AUTENTICATO -->
      
      <!-- TOP BANNER DI BENVENUTO -->
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 16px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 20px;">
        <div>
          <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2.5px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
            <?= icon_gold('institution', 16) ?> Portale Personale Faculty
          </span>
          <h1 style="font-size: clamp(24px, 3.5vw, 36px); color: #ffffff; margin-top: 6px; font-family: 'Cinzel', serif;">
            Benvenuto, <span class="gold-text"><?= sanitize_output($currentUser['first_name'] . ' ' . $currentUser['last_name']) ?></span>
          </h1>
          <div style="color: var(--text-muted); font-size: 14px; margin-top: 4px;">
            <?= sanitize_output($currentUser['profession']) ?> · <?= sanitize_output($currentUser['address_city'] . ' (' . $currentUser['address_province'] . ')') ?>
          </div>
        </div>

        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
          <button onclick="window.print()" class="btn-outline-gold" style="font-size: 12px; padding: 9px 18px; display: inline-flex; align-items: center; gap: 6px;">
            <?= icon_gold('print', 14) ?> Stampa Scheda
          </button>
          <a href="/portal.php?action=logout" class="btn-outline-gold" style="font-size: 12px; padding: 9px 18px; border-color: rgba(255,255,255,0.2);">
            Esci
          </a>
        </div>
      </div>

      <?php if ($success): ?>
        <div style="background: rgba(212, 175, 55, 0.15); border: 1px solid var(--gold-primary); color: #ffffff; padding: 14px 20px; border-radius: var(--radius-sm); margin-bottom: 25px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
          <?= icon_gold('check', 18) ?> <span><?= sanitize_output($success) ?></span>
        </div>
      <?php endif; ?>

      <!-- GRIGLIA PRINCIPALE DASHBOARD -->
      <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px; margin-bottom: 35px;">
        
        <!-- COLONNA SINISTRA: TESSERA DIGITALE & STATUS -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
          
          <!-- TESSERA DIGITALE ACCADEMICA LUXURY -->
          <div class="glass-card" style="border: 2px solid var(--border-gold); padding: 26px; text-align: center; box-shadow: var(--gold-glow); position: relative; overflow: hidden;">
            <img src="/assets/visual/Campus_Emblema_Transparent.png" alt="CAMPUS" style="width: 70px; margin-bottom: 12px; filter: drop-shadow(0 0 15px rgba(212,175,55,0.4));">
            
            <div style="font-size: 10px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
              Identificativo Digitale Univoco
            </div>
            
            <div style="font-size: 20px; font-family: monospace; font-weight: 900; color: #ffffff; margin: 8px 0; letter-spacing: 1.5px;">
              <?= sanitize_output($currentUser['sic_id']) ?>
            </div>

            <div style="display: inline-block; padding: 4px 14px; border-radius: 14px; font-size: 11px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase; background: rgba(212, 175, 55, 0.2); color: var(--gold-light); border: 1px solid var(--gold-primary); margin-bottom: 16px;">
              STATO: <?= sanitize_output($currentUser['status']) ?>
            </div>

            <div style="text-align: left; background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); padding: 14px; border-radius: var(--radius-sm); font-size: 12px; line-height: 1.6; color: var(--text-muted);">
              <div>Codice Pratica: <strong style="color:#ffffff;"><?= sanitize_output($currentUser['application_code']) ?></strong></div>
              <div>Codice Fiscale: <strong style="color:#ffffff;"><?= sanitize_output($currentUser['fiscal_code']) ?></strong></div>
              <div>Data Registrazione: <strong style="color:#ffffff;"><?= substr($currentUser['created_at'], 0, 10) ?></strong></div>
              <div>Organismo/Albo: <strong style="color:#ffffff;"><?= sanitize_output($currentUser['professional_body_name'] ?: 'In verifica') ?></strong></div>
            </div>
          </div>

          <!-- RECAPITI & MODIFICA PROFILO -->
          <div class="glass-card" style="padding: 24px;">
            <h3 style="color: #ffffff; font-size: 16px; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
              <?= icon_gold('document', 16) ?> Dati di Contatto & Profilo
            </h3>

            <form action="/portal.php" method="POST">
              <input type="hidden" name="action" value="update_profile">

              <div style="margin-bottom: 12px;">
                <label class="form-label" style="font-size: 12px;">Email (Fissa)</label>
                <input type="text" class="form-control" value="<?= sanitize_output($currentUser['email']) ?>" disabled style="opacity: 0.7; font-size: 13px;">
              </div>

              <div style="margin-bottom: 12px;">
                <label class="form-label" style="font-size: 12px;">Telefono Cellulare</label>
                <input type="text" name="phone" class="form-control" value="<?= sanitize_output($currentUser['phone']) ?>" required style="font-size: 13px;">
              </div>

              <div style="margin-bottom: 12px;">
                <label class="form-label" style="font-size: 12px;">PEC Ufficiale</label>
                <input type="email" name="pec" class="form-control" value="<?= sanitize_output($currentUser['pec']) ?>" placeholder="nome@pec.it" style="font-size: 13px;">
              </div>

              <div style="margin-bottom: 12px;">
                <label class="form-label" style="font-size: 12px;">Profilo LinkedIn / Portfolio</label>
                <input type="url" name="linkedin_url" class="form-control" value="<?= sanitize_output($currentUser['linkedin_url']) ?>" placeholder="https://linkedin.com/in/..." style="font-size: 13px;">
              </div>

              <div style="margin-bottom: 14px;">
                <label class="form-label" style="font-size: 12px;">Bio Professionale</label>
                <textarea name="bio" class="form-control" rows="3" style="font-size: 13px;"><?= sanitize_output($currentUser['bio']) ?></textarea>
              </div>

              <button type="submit" class="btn-outline-gold" style="width: 100%; font-size: 12px; padding: 10px;">
                Aggiorna Informazioni
              </button>
            </form>
          </div>

        </div>

        <!-- COLONNA DESTRA: TIMELINE AVANZAMENTO & INSEGNAMENTI -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
          
          <!-- TIMELINE DI SELEZIONE E AVANZAMENTO -->
          <div class="glass-card" style="padding: 26px;">
            <h3 style="color: #ffffff; font-size: 17px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
              <?= icon_gold('star', 16) ?> Avanzamento della Tua Candidatura
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 10px; margin-bottom: 20px; text-align: center;">
              
              <div style="background: rgba(212, 175, 55, 0.15); border: 1px solid var(--gold-primary); padding: 12px 6px; border-radius: var(--radius-sm);">
                <div><?= icon_gold('check', 16) ?></div>
                <div style="font-size: 11px; font-weight: 700; color: var(--gold-light); margin-top: 4px; text-transform: uppercase;">1. Ricevuto</div>
                <div style="font-size: 10px; color: var(--text-dim);"><?= substr($currentUser['created_at'], 0, 10) ?></div>
              </div>

              <div style="background: <?= in_array($currentUser['status'], ['UNDER_REVIEW', 'SHORTLISTED', 'INTERVIEW', 'APPROVED', 'FACULTY_ACTIVE']) ? 'rgba(212, 175, 55, 0.2); border: 1px solid var(--gold-light)' : 'rgba(255,255,255,0.02); border: 1px solid var(--border-subtle)' ?>; padding: 12px 6px; border-radius: var(--radius-sm);">
                <div><?= in_array($currentUser['status'], ['UNDER_REVIEW', 'SHORTLISTED', 'INTERVIEW', 'APPROVED', 'FACULTY_ACTIVE']) ? icon_gold('check', 16) : '<span style="color: var(--text-dim);">—</span>' ?></div>
                <div style="font-size: 11px; font-weight: 700; color: <?= in_array($currentUser['status'], ['UNDER_REVIEW', 'SHORTLISTED', 'INTERVIEW', 'APPROVED', 'FACULTY_ACTIVE']) ? 'var(--gold-light)' : 'var(--text-dim)' ?>; margin-top: 4px; text-transform: uppercase;">2. Esame Titoli</div>
              </div>

              <div style="background: <?= in_array($currentUser['status'], ['SHORTLISTED', 'INTERVIEW', 'APPROVED', 'FACULTY_ACTIVE']) ? 'rgba(212, 175, 55, 0.2); border: 1px solid var(--gold-light)' : 'rgba(255,255,255,0.02); border: 1px solid var(--border-subtle)' ?>; padding: 12px 6px; border-radius: var(--radius-sm);">
                <div><?= in_array($currentUser['status'], ['SHORTLISTED', 'INTERVIEW', 'APPROVED', 'FACULTY_ACTIVE']) ? icon_gold('check', 16) : '<span style="color: var(--text-dim);">—</span>' ?></div>
                <div style="font-size: 11px; font-weight: 700; color: <?= in_array($currentUser['status'], ['SHORTLISTED', 'INTERVIEW', 'APPROVED', 'FACULTY_ACTIVE']) ? 'var(--gold-light)' : 'var(--text-dim)' ?>; margin-top: 4px; text-transform: uppercase;">3. Colloquio</div>
              </div>

              <div style="background: <?= in_array($currentUser['status'], ['APPROVED', 'FACULTY_ACTIVE']) ? 'rgba(212, 175, 55, 0.25); border: 1px solid var(--gold-primary)' : 'rgba(255,255,255,0.02); border: 1px solid var(--border-subtle)' ?>; padding: 12px 6px; border-radius: var(--radius-sm);">
                <div><?= in_array($currentUser['status'], ['APPROVED', 'FACULTY_ACTIVE']) ? icon_gold('crown', 16) : '<span style="color: var(--text-dim);">—</span>' ?></div>
                <div style="font-size: 11px; font-weight: 700; color: <?= in_array($currentUser['status'], ['APPROVED', 'FACULTY_ACTIVE']) ? 'var(--gold-light)' : 'var(--text-dim)' ?>; margin-top: 4px; text-transform: uppercase;">4. Albo Docenti</div>
              </div>

            </div>

            <?php if (!empty($currentUser['admin_notes'])): ?>
              <div style="background: rgba(212, 175, 55, 0.08); border-left: 4px solid var(--gold-primary); padding: 14px 18px; border-radius: 0 var(--radius-sm) var(--radius-sm) 0; font-size: 13.5px; color: #f8fafc; margin-top: 15px;">
                <strong>Comunicazione Ufficiale della Commissione:</strong><br>
                <?= nl2br(sanitize_output($currentUser['admin_notes'])) ?>
              </div>
            <?php endif; ?>
          </div>

          <!-- I TUOI INSEGNAMENTI & CATTEDRE CANDIDATE -->
          <div class="glass-card" style="padding: 26px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
              <h3 style="color: #ffffff; font-size: 17px; display: flex; align-items: center; gap: 8px; margin: 0;">
                <?= icon_gold('academic', 16) ?> Insegnamenti & Cattedre Richieste (<?= count($userCourses) ?>)
              </h3>
            </div>

            <?php if (empty($userCourses)): ?>
              <p style="color: var(--text-dim); font-size: 13.5px;">
                Nessun insegnamento associato. Utilizza il modulo sottostante per proporre una cattedra.
              </p>
            <?php else: ?>
              <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px;">
                <?php foreach ($userCourses as $idx => $uc): ?>
                  <div style="background: rgba(0,0,0,0.35); border: 1px solid var(--border-subtle); padding: 12px 16px; border-radius: var(--radius-sm); display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                      <span style="font-family: monospace; font-size: 12px; color: var(--gold-light); font-weight: bold;">#<?= $idx + 1 ?></span>
                      <strong style="color: #ffffff; font-size: 13.5px;"><?= sanitize_output($uc['course_name']) ?></strong>
                    </div>
                    <span class="badge" style="background: rgba(212,175,55,0.15); color: var(--gold-light); border: 1px solid var(--border-gold); font-size: 10px;">
                      In Valutazione
                    </span>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <!-- FORM PER AGGIUNGERE UN'ALTRA CATTEDRA / INSEGNAMENTO -->
            <div style="background: rgba(212, 175, 55, 0.05); border: 1px dashed var(--gold-primary); padding: 18px; border-radius: var(--radius-sm);">
              <h4 style="color: var(--gold-light); font-size: 14px; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                <?= icon_gold('star', 14) ?> Proponi un'Altra Materia o Cattedra
              </h4>
              <form action="/portal.php" method="POST" style="display: grid; grid-template-columns: 2fr 3fr auto; gap: 10px; align-items: flex-end;">
                <input type="hidden" name="action" value="add_custom_course">
                <div>
                  <label class="form-label" style="font-size: 11px;">Materia / Titolo Cattedra</label>
                  <input type="text" name="new_course_title" class="form-control" placeholder="es. Diritto Penale d'Impresa" required style="font-size: 12.5px; padding: 8px;">
                </div>
                <div>
                  <label class="form-label" style="font-size: 11px;">Sintesi Proposta Didattica</label>
                  <input type="text" name="new_course_desc" class="form-control" placeholder="es. Corso di 20 ore con crediti CFP" style="font-size: 12.5px; padding: 8px;">
                </div>
                <div>
                  <button type="submit" class="btn-gold" style="padding: 9px 18px; font-size: 12px; font-weight: 700; white-space: nowrap;">
                    + Aggiungi
                  </button>
                </div>
              </form>
            </div>

          </div>

          <!-- DOCUMENTI & ATTESTATI SCARICABILI -->
          <div class="glass-card" style="padding: 26px;">
            <h3 style="color: #ffffff; font-size: 17px; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
              <?= icon_gold('document', 16) ?> Atti Accademici & Documentazione Ufficiale
            </h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px;">
              
              <div style="background: rgba(0,0,0,0.3); border: 1px solid var(--border-subtle); padding: 14px; border-radius: var(--radius-sm); text-align: center;">
                <div style="margin-bottom: 8px;"><?= icon_gold('document', 28) ?></div>
                <strong style="color: #ffffff; font-size: 13px; display: block;">Ricevuta di Deposito</strong>
                <div style="font-size: 11px; color: var(--text-dim); margin: 4px 0 10px 0;">Protocollata con SIC-ID</div>
                <a href="/thank-you.php?sic_id=<?= urlencode($currentUser['sic_id']) ?>&code=<?= urlencode($currentUser['application_code']) ?>" class="btn-outline-gold" style="font-size: 11px; padding: 6px 14px; width: 100%;">
                  Visualizza Ricevuta
                </a>
              </div>

              <div style="background: rgba(0,0,0,0.3); border: 1px solid var(--border-subtle); padding: 14px; border-radius: var(--radius-sm); text-align: center;">
                <div style="margin-bottom: 8px;"><?= icon_gold('print', 28) ?></div>
                <strong style="color: #ffffff; font-size: 13px; display: block;">Manifesto Ufficiale A4</strong>
                <div style="font-size: 11px; color: var(--text-dim); margin: 4px 0 10px 0;">Regolamento Faculty 2026/2027</div>
                <a href="/manifesto-docenti-a4.html" target="_blank" class="btn-outline-gold" style="font-size: 11px; padding: 6px 14px; width: 100%;">
                  Scarica PDF A4
                </a>
              </div>

              <div style="background: rgba(0,0,0,0.3); border: 1px solid var(--border-subtle); padding: 14px; border-radius: var(--radius-sm); text-align: center;">
                <div style="margin-bottom: 8px;"><?= icon_gold('institution', 28) ?></div>
                <strong style="color: #ffffff; font-size: 13px; display: block;">Segreteria di Cattedra</strong>
                <div style="font-size: 11px; color: var(--text-dim); margin: 4px 0 10px 0;">Rettorato & Commissione</div>
                <a href="mailto:<?= CAMPUS_EMAIL_FACULTY ?>?subject=Quesito%20Candidatura%20<?= urlencode($currentUser['sic_id']) ?>" class="btn-gold" style="font-size: 11px; padding: 6px 14px; width: 100%;">
                  Contatta la Segreteria
                </a>
              </div>

            </div>
          </div>

        </div>

      </div>

    <?php endif; ?>

  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
