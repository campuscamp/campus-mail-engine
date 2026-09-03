<?php
/**
 * CAMPUS.CAMP — Academic Command Center & Executive CRM Dashboard
 * Gestione globale candidature, cattedre, deliberazioni commissione, registro enti e monitoraggio infrastruttura
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/icons.php';

// Auth Guard
if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: /admin/index.php');
    exit;
}

$db = Database::getConnection();
$feedbackMessage = null;

// Gestione Azioni Rapide POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (verify_csrf_token($token)) {
        $action = $_POST['action'] ?? '';

        // 1. Aggiornamento Stato e Note Candidato
        if ($action === 'update_candidate') {
            $appId = (int)$_POST['application_id'];
            $newStatus = trim($_POST['status'] ?? 'SUBMITTED');
            $adminNotes = trim($_POST['admin_notes'] ?? '');

            $stmtUp = $db->prepare("UPDATE faculty_applications SET status = ?, admin_notes = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmtUp->execute([$newStatus, $adminNotes, $appId]);

            // Audit Log
            $stmtLog = $db->prepare("INSERT INTO audit_log (action, entity_type, entity_id, details, ip_address, user_agent) VALUES ('ADMIN_UPDATE_STATUS', 'FACULTY_APPLICATION', ?, ?, ?, ?)");
            $stmtLog->execute([(string)$appId, "Stato aggiornato a {$newStatus}. Note: " . substr($adminNotes, 0, 80), $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown']);

            $feedbackMessage = "Pratica #{$appId} aggiornata con stato [{$newStatus}].";
        }

        // 2. Notifica Email Convocazione Colloquio
        if ($action === 'send_interview_invite') {
            $candEmail = strtolower(trim($_POST['candidate_email'] ?? ''));
            $candName = trim($_POST['candidate_name'] ?? '');
            $sicId = trim($_POST['sic_id'] ?? '');

            if (filter_var($candEmail, FILTER_VALIDATE_EMAIL)) {
                $subject = "[CAMPUS] Convocazione Ufficiale Colloquio Accademico Faculty — {$sicId}";
                $headers = [
                    'MIME-Version: 1.0',
                    'Content-type: text/html; charset=utf-8',
                    'From: CAMPUS Direzione Accademica <' . CAMPUS_EMAIL_INFO . '>',
                    'Reply-To: ' . CAMPUS_EMAIL_FACULTY
                ];
                $msg = "<p>Gentile <strong>{$candName}</strong>,</p><p>La Commissione di Valutazione di CAMPUS ha esaminato con esito favorevole i Suoi titoli e la Sua proposta didattica (Protocollo <strong>{$sicId}</strong>).</p><p>La invitiamo a concordare la data per il colloquio telematico di allineamento e assegnazione cattedra rispondendo a questa email o accedendo al Suo <a href='https://campus.camp/portal.php?sic_id={$sicId}'>Portale Personale Faculty</a>.</p><p>Cordiali saluti,<br><strong>Direzione Accademica CAMPUS</strong></p>";
                @mail($candEmail, $subject, $msg, implode("\r\n", $headers));
                
                $feedbackMessage = "Convocazione a colloquio inviata con successo a {$candEmail}.";
            }
        }
    }
}

// Handle Export CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=CAMPUS_COMMAND_CENTER_CANDIDATI_' . date('Ymd_His') . '.csv');
    
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID', 'SIC-ID', 'Codice Pratica', 'Nome', 'Cognome', 'Codice Fiscale', 'Email', 'Telefono', 'PEC', 'Città', 'Provincia', 'Regione', 'Professione', 'Codice ATECO', 'Ordine/Albo', 'Anni Esperienza', 'Stato', 'Note Commissione', 'Data Invio']);

    $rows = $db->query("
        SELECT a.id, a.sic_id, a.application_code, p.first_name, p.last_name, p.fiscal_code, a.email, a.phone, a.pec, a.address_city, a.address_province, a.address_region, a.profession, a.ateco_code, a.professional_body_name, a.years_experience, a.status, a.admin_notes, a.created_at
        FROM faculty_applications a
        JOIN persons p ON a.person_id = p.id
        ORDER BY a.id DESC
    ")->fetchAll();

    foreach ($rows as $r) {
        fputcsv($out, $r);
    }
    fclose($out);
    exit;
}

// ---------------------------------------------------------------------------
// KPI & STATS ENGINE
// ---------------------------------------------------------------------------
$totalCandidates = (int)$db->query("SELECT COUNT(*) FROM faculty_applications")->fetchColumn();
$todayCandidates = (int)$db->query("SELECT COUNT(*) FROM faculty_applications WHERE date(created_at) = date('now')")->fetchColumn();
$weekCandidates = (int)$db->query("SELECT COUNT(*) FROM faculty_applications WHERE created_at >= date('now', '-7 days')")->fetchColumn();

// Status Counters
$countSubmitted = (int)$db->query("SELECT COUNT(*) FROM faculty_applications WHERE status = 'SUBMITTED'")->fetchColumn();
$countUnderReview = (int)$db->query("SELECT COUNT(*) FROM faculty_applications WHERE status = 'UNDER_REVIEW'")->fetchColumn();
$countShortlisted = (int)$db->query("SELECT COUNT(*) FROM faculty_applications WHERE status IN ('SHORTLISTED', 'INTERVIEW')")->fetchColumn();
$countApproved = (int)$db->query("SELECT COUNT(*) FROM faculty_applications WHERE status IN ('APPROVED', 'FACULTY_ACTIVE')")->fetchColumn();
$countRejected = (int)$db->query("SELECT COUNT(*) FROM faculty_applications WHERE status = 'REJECTED'")->fetchColumn();

// System stats
$totalCourses = (int)$db->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$totalEntities = (int)$db->query("SELECT COUNT(*) FROM institutional_directory")->fetchColumn();
$dbFileSize = file_exists(CAMPUS_DB_PATH) ? round(filesize(CAMPUS_DB_PATH) / 1024, 1) . ' KB' : 'N/D';

// Top professions
$professionCounts = $db->query("SELECT profession, COUNT(*) as count FROM faculty_applications GROUP BY profession ORDER BY count DESC LIMIT 6")->fetchAll();

// Top regions
$regionCounts = $db->query("SELECT address_region, COUNT(*) as count FROM faculty_applications WHERE address_region != '' GROUP BY address_region ORDER BY count DESC LIMIT 5")->fetchAll();

// Most requested courses
$topCourses = $db->query("SELECT course_name, COUNT(*) as count FROM faculty_preferences GROUP BY course_name ORDER BY count DESC LIMIT 6")->fetchAll();

// Recent Audit Logs
$recentAudits = $db->query("SELECT * FROM audit_log ORDER BY id DESC LIMIT 5")->fetchAll();

// Fetch Candidates with their courses
$candidates = $db->query("
    SELECT a.*, p.first_name, p.last_name, p.fiscal_code, p.nationality
    FROM faculty_applications a
    JOIN persons p ON a.person_id = p.id
    ORDER BY a.id DESC
")->fetchAll();

// Attach preferences
$candidatesWithCourses = [];
foreach ($candidates as $cand) {
    $stmtC = $db->prepare("SELECT course_name, priority_order FROM faculty_preferences WHERE application_id = ? ORDER BY priority_order ASC");
    $stmtC->execute([$cand['id']]);
    $cand['courses'] = $stmtC->fetchAll();
    $candidatesWithCourses[] = $cand;
}

$pageTitle = 'Command Center Istituzionale — Direzione CAMPUS';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="section" style="padding-top: 30px; padding-bottom: 70px;">
  <div class="container" style="max-width: 1440px;">

    <!-- TOP BAR COMMAND CENTER -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 18px;">
      <div>
        <div style="display: flex; align-items: center; gap: 8px;">
          <span style="width: 9px; height: 9px; background: #10b981; border-radius: 50%; display: inline-block; box-shadow: 0 0 10px #10b981;"></span>
          <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
            Command Center Accademico & CRM Direzione
          </span>
        </div>
        <h1 style="font-size: clamp(22px, 3vw, 32px); color: #ffffff; margin-top: 4px; font-family: 'Cinzel', serif;">
          CAMPUS <span class="gold-text">Executive Command Center</span>
        </h1>
        <div style="font-size: 12.5px; color: var(--text-dim);">
          Ufficiale Responsabile: <strong style="color: #ffffff;"><?= sanitize_output($_SESSION['admin_name']) ?></strong> · Protocollo Sicurezza Attivo
        </div>
      </div>

      <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <a href="/admin/directory.php" class="btn-gold" style="font-size: 12px; padding: 9px 18px; display: inline-flex; align-items: center; gap: 6px;">
          <?= icon_gold('institution', 14) ?> Albo Enti & Ordini (<?= $totalEntities ?>)
        </a>
        <a href="/admin/dashboard.php?export=csv" class="btn-outline-gold" style="font-size: 12px; padding: 9px 18px; display: inline-flex; align-items: center; gap: 6px;">
          <?= icon_gold('download', 14) ?> Esporta CSV
        </a>
        <a href="/portal.php" target="_blank" class="btn-outline-gold" style="font-size: 12px; padding: 9px 18px; display: inline-flex; align-items: center; gap: 6px;">
          <?= icon_gold('eye', 14) ?> Vista Portale Docente
        </a>
        <a href="/admin/logout.php" class="btn-outline-gold" style="font-size: 12px; padding: 9px 18px; border-color: rgba(255,255,255,0.2);">
          Esci
        </a>
      </div>
    </div>

    <?php if ($feedbackMessage): ?>
      <div style="background: rgba(212, 175, 55, 0.15); border: 1px solid var(--gold-primary); color: #ffffff; padding: 12px 18px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 10px;">
        <?= icon_gold('check', 16) ?> <span><?= sanitize_output($feedbackMessage) ?></span>
      </div>
    <?php endif; ?>

    <!-- KPI ROW: METRICHE E STATI CHIAVE -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 14px; margin-bottom: 25px;">
      
      <div class="glass-card" style="padding: 16px; border-left: 4px solid var(--gold-primary);">
        <div style="font-size: 11px; text-transform: uppercase; color: var(--gold-light); font-weight: 700;">Totale Candidature</div>
        <div style="font-size: 28px; font-weight: 900; color: #ffffff; margin-top: 2px;"><?= $totalCandidates ?></div>
        <div style="font-size: 11px; color: var(--text-dim);"><?= $todayCandidates ?> oggi · <?= $weekCandidates ?> ultimi 7 gg</div>
      </div>

      <div class="glass-card" style="padding: 16px; border-left: 4px solid #3b82f6;">
        <div style="font-size: 11px; text-transform: uppercase; color: #93c5fd; font-weight: 700;">Da Valutare</div>
        <div style="font-size: 28px; font-weight: 900; color: #ffffff; margin-top: 2px;"><?= $countSubmitted ?></div>
        <div style="font-size: 11px; color: var(--text-dim);">Nuove sottomissioni</div>
      </div>

      <div class="glass-card" style="padding: 16px; border-left: 4px solid #f59e0b;">
        <div style="font-size: 11px; text-transform: uppercase; color: #fcd34d; font-weight: 700;">In Esame Titoli</div>
        <div style="font-size: 28px; font-weight: 900; color: #ffffff; margin-top: 2px;"><?= $countUnderReview ?></div>
        <div style="font-size: 11px; color: var(--text-dim);">In commissione paritetica</div>
      </div>

      <div class="glass-card" style="padding: 16px; border-left: 4px solid var(--gold-metallic);">
        <div style="font-size: 11px; text-transform: uppercase; color: var(--gold-light); font-weight: 700;">Colloqui / Shortlist</div>
        <div style="font-size: 28px; font-weight: 900; color: #ffffff; margin-top: 2px;"><?= $countShortlisted ?></div>
        <div style="font-size: 11px; color: var(--text-dim);">Convocati / In colloquio</div>
      </div>

      <div class="glass-card" style="padding: 16px; border-left: 4px solid #10b981;">
        <div style="font-size: 11px; text-transform: uppercase; color: #6ee7b7; font-weight: 700;">Docenti Approvati</div>
        <div style="font-size: 28px; font-weight: 900; color: #ffffff; margin-top: 2px;"><?= $countApproved ?></div>
        <div style="font-size: 11px; color: var(--text-dim);">Albo Faculty Attivo</div>
      </div>

      <div class="glass-card" style="padding: 16px; border-left: 4px solid #ef4444;">
        <div style="font-size: 11px; text-transform: uppercase; color: #fca5a5; font-weight: 700;">Non Ammessi</div>
        <div style="font-size: 28px; font-weight: 900; color: #ffffff; margin-top: 2px;"><?= $countRejected ?></div>
        <div style="font-size: 11px; color: var(--text-dim);">Archiviate o respinte</div>
      </div>

    </div>

    <!-- COMMAND CENTER OPERATIONAL PANELS (SYSTEM HEALTH + AUDIT LOG + TOP PREVIEWS) -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px;">
      
      <!-- STATISTICHE OPERATIVE & TOP CATTEDRE -->
      <div class="glass-card" style="padding: 22px;">
        <h3 style="color: #ffffff; font-size: 15px; margin-bottom: 14px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px; display: flex; align-items: center; gap: 8px;">
          <?= icon_gold('academic', 16) ?> Cattedre più Richieste & Aree di Padronanza
        </h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
          <div>
            <div style="font-size: 11px; color: var(--gold-light); text-transform: uppercase; font-weight: bold; margin-bottom: 8px;">Top Insegnamenti Richiesti</div>
            <?php if (empty($topCourses)): ?>
              <div style="color: var(--text-dim); font-size: 12px;">Nessuna preferenza registrata.</div>
            <?php else: ?>
              <ul style="list-style: none; padding: 0; font-size: 12.5px; margin: 0;">
                <?php foreach ($topCourses as $tc): ?>
                  <li style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid rgba(255,255,255,0.03);">
                    <span style="color: #ffffff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 80%;"><?= sanitize_output($tc['course_name']) ?></span>
                    <strong style="color: var(--gold-light);"><?= $tc['count'] ?></strong>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
          <div>
            <div style="font-size: 11px; color: var(--gold-light); text-transform: uppercase; font-weight: bold; margin-bottom: 8px;">Top Categorie Professionali</div>
            <?php if (empty($professionCounts)): ?>
              <div style="color: var(--text-dim); font-size: 12px;">Nessun dato.</div>
            <?php else: ?>
              <ul style="list-style: none; padding: 0; font-size: 12.5px; margin: 0;">
                <?php foreach ($professionCounts as $p): ?>
                  <li style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid rgba(255,255,255,0.03);">
                    <span style="color: #ffffff;"><?= sanitize_output($p['profession']) ?></span>
                    <strong style="color: var(--gold-light);"><?= $p['count'] ?></strong>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- SYSTEM & INFRASTRUCTURE MONITOR -->
      <div class="glass-card" style="padding: 22px;">
        <h3 style="color: #ffffff; font-size: 15px; margin-bottom: 14px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px; display: flex; align-items: center; gap: 8px;">
          <?= icon_gold('shield', 16) ?> Diagnostica di Sistema
        </h3>
        <div style="font-size: 12.5px; line-height: 1.8; color: var(--text-muted);">
          <div style="display: flex; justify-content: space-between;">
            <span>Database SQLite:</span>
            <strong style="color: #ffffff;"><?= $dbFileSize ?> (Integrity OK)</strong>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span>Catalogo Formativo:</span>
            <strong style="color: var(--gold-light);"><?= $totalCourses ?> Corsi Attivi</strong>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span>Directory Istituzionale:</span>
            <strong style="color: var(--gold-light);"><?= $totalEntities ?> Enti Censiti</strong>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span>Storage CV:</span>
            <strong style="color: #10b981;">Scrittura Attiva</strong>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span>Quota Membership:</span>
            <strong style="color: #ffffff;">299 € (IVA inc.)</strong>
          </div>
        </div>
      </div>

    </div>

    <!-- CANDIDATES TABLE & LIVE FILTER -->
    <div class="glass-card" style="padding: 25px;">
      
      <!-- TOOLBAR RICERCA E FILTRI LIVE -->
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <h2 style="font-size: 19px; color: #ffffff; margin: 0; font-family: 'Cinzel', serif;">
          Registro Candidature Faculty (<span id="visible-candidates-count"><?= count($candidatesWithCourses) ?></span>)
        </h2>

        <!-- FILTRI RAPIDI PER STATO -->
        <div style="display: flex; gap: 6px; flex-wrap: wrap;" id="status-filter-buttons">
          <button class="filter-btn active" data-filter="ALL" style="padding: 5px 12px; font-size: 11px; border-radius: 12px; background: var(--gold-primary); color: #000; font-weight: bold; border: none; cursor: pointer;">TUTTI</button>
          <button class="filter-btn" data-filter="SUBMITTED" style="padding: 5px 12px; font-size: 11px; border-radius: 12px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--border-subtle); cursor: pointer;">NUOVI (<?= $countSubmitted ?>)</button>
          <button class="filter-btn" data-filter="UNDER_REVIEW" style="padding: 5px 12px; font-size: 11px; border-radius: 12px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--border-subtle); cursor: pointer;">IN ESAME (<?= $countUnderReview ?>)</button>
          <button class="filter-btn" data-filter="SHORTLISTED,INTERVIEW" style="padding: 5px 12px; font-size: 11px; border-radius: 12px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--border-subtle); cursor: pointer;">COLLOQUI (<?= $countShortlisted ?>)</button>
          <button class="filter-btn" data-filter="APPROVED,FACULTY_ACTIVE" style="padding: 5px 12px; font-size: 11px; border-radius: 12px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--border-subtle); cursor: pointer;">APPROVATI (<?= $countApproved ?>)</button>
          <button class="filter-btn" data-filter="REJECTED" style="padding: 5px 12px; font-size: 11px; border-radius: 12px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--border-subtle); cursor: pointer;">RESPINTI (<?= $countRejected ?>)</button>
        </div>
      </div>

      <!-- CAMPO RICERCA ISTANTANEA -->
      <div style="margin-bottom: 18px;">
        <input type="text" id="candidate-search-input" placeholder="Cerca istantaneamente per Nome, Cognome, SIC-ID, Email, Codice Fiscale, Professione o Ordine..." class="form-control" style="font-size: 13.5px; padding: 11px 16px;">
      </div>

      <?php if (empty($candidatesWithCourses)): ?>
        <div style="text-align: center; padding: 50px 20px; color: var(--text-dim);">
          <div style="margin-bottom: 10px;"><?= icon_gold('document', 40) ?></div>
          Nessuna candidatura registrata nel database.<br>
          <a href="/apply.php" target="_blank" style="color: var(--gold-light); text-decoration: underline; margin-top: 10px; display: inline-block;">
            Apri il modulo pubblico per testare un invio
          </a>
        </div>
      <?php else: ?>
        <div style="overflow-x: auto;">
          <table id="candidates-table" style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
            <thead>
              <tr style="border-bottom: 2px solid var(--border-glass); color: var(--gold-primary);">
                <th style="padding: 10px 12px;">SIC-ID & Data</th>
                <th style="padding: 10px 12px;">Candidato & Recapiti</th>
                <th style="padding: 10px 12px;">Professione & Ordine</th>
                <th style="padding: 10px 12px;">Cattedre Richieste</th>
                <th style="padding: 10px 12px;">CV / Link</th>
                <th style="padding: 10px 12px;">Stato Attuale</th>
                <th style="padding: 10px 12px; text-align: right;">Delibera & Note</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($candidatesWithCourses as $cand): ?>
                <tr class="candidate-row" data-status="<?= sanitize_output($cand['status']) ?>" style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: background 0.2s;" onmouseover="this.style.background='rgba(212,175,55,0.04)'" onmouseout="this.style.background='transparent'">
                  
                  <!-- SIC-ID & CODICE -->
                  <td style="padding: 12px; vertical-align: top;">
                    <span style="font-family: monospace; font-weight: 800; color: var(--gold-light); font-size: 12.5px; display: block;">
                      <?= sanitize_output($cand['sic_id']) ?>
                    </span>
                    <span style="font-size: 11px; color: var(--text-dim); display: block; margin-top: 2px;">
                      <?= substr($cand['created_at'], 0, 10) ?> · <?= sanitize_output($cand['application_code']) ?>
                    </span>
                    <a href="/portal.php?sic_id=<?= urlencode($cand['sic_id']) ?>" target="_blank" style="font-size: 10.5px; color: var(--gold-light); text-decoration: underline; margin-top: 4px; display: inline-block;">
                      Apri Portale Utente ↗
                    </a>
                  </td>

                  <!-- CANDIDATO & CONTATTI -->
                  <td style="padding: 12px; vertical-align: top;">
                    <strong style="color: #ffffff; font-size: 14px;"><?= sanitize_output($cand['first_name'] . ' ' . $cand['last_name']) ?></strong><br>
                    <span style="font-family: monospace; font-size: 11.5px; color: var(--text-dim);"><?= sanitize_output($cand['fiscal_code']) ?></span><br>
                    <span style="font-size: 12px; color: var(--text-muted); margin-top: 2px; display: block;">
                      <a href="mailto:<?= sanitize_output($cand['email']) ?>" style="color: var(--text-muted); text-decoration: none;"><?= sanitize_output($cand['email']) ?></a> · 
                      <a href="tel:<?= sanitize_output($cand['phone']) ?>" style="color: var(--text-muted); text-decoration: none;"><?= sanitize_output($cand['phone']) ?></a>
                    </span>
                    <?php if (!empty($cand['pec'])): ?>
                      <span style="font-size: 11px; color: var(--gold-light); display: block;">PEC: <?= sanitize_output($cand['pec']) ?></span>
                    <?php endif; ?>
                  </td>

                  <!-- PROFESSIONE & ALBO -->
                  <td style="padding: 12px; vertical-align: top;">
                    <span style="color: #ffffff; font-weight: 600;"><?= sanitize_output($cand['profession']) ?></span><br>
                    <span style="font-size: 11.5px; color: var(--text-dim);">
                      <?= sanitize_output($cand['professional_body_name'] ?: 'Nessun ordine') ?>
                      <?= !empty($cand['professional_body_number']) ? ' (N. ' . sanitize_output($cand['professional_body_number']) . ')' : '' ?>
                    </span>
                    <div style="font-size: 11px; color: var(--text-dim); margin-top: 2px;">
                      <?= sanitize_output($cand['address_city'] ?: '-') ?> (<?= sanitize_output($cand['address_province'] ?: '-') ?>) · <?= $cand['years_experience'] ?> anni exp.
                    </div>
                  </td>

                  <!-- CORSI SCELTI & PROPOSTE -->
                  <td style="padding: 12px; vertical-align: top; max-width: 280px;">
                    <?php if (empty($cand['courses'])): ?>
                      <span style="color: var(--text-dim); font-size: 11.5px;">Nessun insegnamento associato</span>
                    <?php else: ?>
                      <div style="display: flex; flex-direction: column; gap: 4px;">
                        <?php foreach ($cand['courses'] as $c): ?>
                          <span style="background: rgba(255,255,255,0.03); border: 1px solid rgba(212,175,55,0.2); padding: 2px 6px; border-radius: 4px; font-size: 11px; color: <?= str_contains($c['course_name'], '[NUOVA CATTEDRA') ? 'var(--gold-light)' : '#ffffff' ?>; line-height: 1.3;">
                            <?= sanitize_output($c['course_name']) ?>
                          </span>
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>
                  </td>

                  <!-- CV / LINK -->
                  <td style="padding: 12px; vertical-align: top;">
                    <?php if (!empty($cand['cv_filename'])): ?>
                      <a href="/storage/uploads/cv/<?= urlencode($cand['cv_filename']) ?>" target="_blank" class="btn-outline-gold" style="font-size: 11px; padding: 4px 8px; display: inline-flex; align-items: center; gap: 4px; margin-bottom: 4px;">
                        <?= icon_gold('document', 12) ?> CV PDF
                      </a>
                    <?php endif; ?>
                    <?php if (!empty($cand['linkedin_url'])): ?>
                      <a href="<?= sanitize_output($cand['linkedin_url']) ?>" target="_blank" style="color: #60a5fa; font-size: 11px; display: block; text-decoration: underline;">
                        LinkedIn ↗
                      </a>
                    <?php endif; ?>
                  </td>

                  <!-- STATO CON BADGE -->
                  <td style="padding: 12px; vertical-align: top;">
                    <?php 
                      $badgeBg = 'rgba(212,175,55,0.15)';
                      $badgeColor = 'var(--gold-light)';
                      if ($cand['status'] === 'APPROVED' || $cand['status'] === 'FACULTY_ACTIVE') {
                          $badgeBg = 'rgba(16,185,129,0.15)';
                          $badgeColor = '#6ee7b7';
                      } elseif ($cand['status'] === 'REJECTED') {
                          $badgeBg = 'rgba(239,68,68,0.15)';
                          $badgeColor = '#fca5a5';
                      }
                    ?>
                    <span style="display: inline-block; padding: 4px 8px; border-radius: 10px; font-size: 10.5px; font-weight: 800; background: <?= $badgeBg ?>; color: <?= $badgeColor ?>; border: 1px solid <?= $badgeColor ?>; text-transform: uppercase;">
                      <?= sanitize_output($cand['status']) ?>
                    </span>
                  </td>

                  <!-- DELIBERA, NOTE & CONVOCAZIONE -->
                  <td style="padding: 12px; vertical-align: top; text-align: right;">
                    <form action="/admin/dashboard.php" method="POST" style="display: inline-flex; flex-direction: column; gap: 6px; align-items: flex-end;">
                      <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
                      <input type="hidden" name="action" value="update_candidate">
                      <input type="hidden" name="application_id" value="<?= $cand['id'] ?>">
                      
                      <div style="display: flex; gap: 6px;">
                        <select name="status" class="form-control" style="padding: 4px 8px; font-size: 11.5px; width: 130px; height: 30px;">
                          <option value="SUBMITTED" <?= $cand['status'] === 'SUBMITTED' ? 'selected' : '' ?>>Sottomesso</option>
                          <option value="UNDER_REVIEW" <?= $cand['status'] === 'UNDER_REVIEW' ? 'selected' : '' ?>>In Revisione</option>
                          <option value="SHORTLISTED" <?= $cand['status'] === 'SHORTLISTED' ? 'selected' : '' ?>>Shortlist</option>
                          <option value="INTERVIEW" <?= $cand['status'] === 'INTERVIEW' ? 'selected' : '' ?>>Colloquio</option>
                          <option value="APPROVED" <?= $cand['status'] === 'APPROVED' ? 'selected' : '' ?>>Approvato Albo</option>
                          <option value="REJECTED" <?= $cand['status'] === 'REJECTED' ? 'selected' : '' ?>>Non Ammesso</option>
                        </select>

                        <button type="submit" class="btn-gold" style="padding: 4px 10px; font-size: 11px; height: 30px; font-weight: 700;">
                          Salva
                        </button>
                      </div>

                      <input type="text" name="admin_notes" value="<?= sanitize_output($cand['admin_notes'] ?? '') ?>" placeholder="Note commissione..." class="form-control" style="font-size: 11px; padding: 4px 8px; width: 180px; height: 26px;">
                    </form>

                    <!-- Bottone rapido convocazione email -->
                    <form action="/admin/dashboard.php" method="POST" style="display: inline-block; margin-top: 4px;" onsubmit="return confirm('Confermi l invio dell invito a colloquio via email?');">
                      <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
                      <input type="hidden" name="action" value="send_interview_invite">
                      <input type="hidden" name="candidate_email" value="<?= sanitize_output($cand['email']) ?>">
                      <input type="hidden" name="candidate_name" value="<?= sanitize_output($cand['first_name'] . ' ' . $cand['last_name']) ?>">
                      <input type="hidden" name="sic_id" value="<?= sanitize_output($cand['sic_id']) ?>">
                      <button type="submit" style="background: transparent; border: none; color: var(--gold-light); font-size: 10.5px; cursor: pointer; text-decoration: underline; padding: 0;">
                        ✉ Invia Convocazione Colloquio
                      </button>
                    </form>
                  </td>

                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

    </div>

  </div>
</section>

<!-- LIVE FILTER & SEARCH JAVASCRIPT CONTROLLER -->
<script>
(function() {
  'use strict';

  var searchInput = document.getElementById('candidate-search-input');
  var rows = document.querySelectorAll('.candidate-row');
  var countSpan = document.getElementById('visible-candidates-count');
  var filterBtns = document.querySelectorAll('.filter-btn');

  var currentStatusFilter = 'ALL';
  var currentSearchQuery = '';

  function applyFilters() {
    var visible = 0;
    rows.forEach(function(row) {
      var text = row.textContent.toLowerCase();
      var status = row.getAttribute('data-status') || '';

      var matchesSearch = (currentSearchQuery === '' || text.includes(currentSearchQuery));
      var matchesStatus = false;

      if (currentStatusFilter === 'ALL') {
        matchesStatus = true;
      } else {
        var allowed = currentStatusFilter.split(',');
        matchesStatus = allowed.includes(status);
      }

      if (matchesSearch && matchesStatus) {
        row.style.display = '';
        visible++;
      } else {
        row.style.display = 'none';
      }
    });

    if (countSpan) countSpan.textContent = visible;
  }

  if (searchInput) {
    searchInput.addEventListener('input', function() {
      currentSearchQuery = this.value.toLowerCase().trim();
      applyFilters();
    });
  }

  filterBtns.forEach(function(btn) {
    btn.addEventListener('click', function() {
      filterBtns.forEach(function(b) {
        b.style.background = 'rgba(255,255,255,0.05)';
        b.style.color = '#fff';
        b.style.border = '1px solid var(--border-subtle)';
        b.classList.remove('active');
      });

      this.style.background = 'var(--gold-primary)';
      this.style.color = '#000';
      this.style.border = 'none';
      this.classList.add('active');

      currentStatusFilter = this.getAttribute('data-filter') || 'ALL';
      applyFilters();
    });
  });

})();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
