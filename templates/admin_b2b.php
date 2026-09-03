<?php
/**
 * CAMPUS.CAMP — Command Center CRM B2B: Aziende, Partner, Sponsor & Convenzioni
 * Pipeline: NEW -> QUALIFYING -> DISCOVERY -> OPPORTUNITY -> PROPOSAL -> NEGOTIATION -> AGREEMENT -> ACTIVE
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

// Gestione Azioni POST (cambio stage pipeline, note, etc.)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (verify_csrf_token($token)) {
        $action = $_POST['action'] ?? '';

        // Aggiornamento stage pipeline e note
        if ($action === 'update_pipeline') {
            $b2bId = (int)$_POST['application_id'];
            $newStage = trim($_POST['pipeline_stage'] ?? 'NEW');
            $newStatus = trim($_POST['status'] ?? 'SUBMITTED');
            $adminNotes = trim($_POST['admin_notes'] ?? '');

            $stmtUp = $db->prepare("UPDATE b2b_applications SET pipeline_stage = ?, status = ?, admin_notes = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmtUp->execute([$newStage, $newStatus, $adminNotes, $b2bId]);

            // Audit Log
            $stmtLog = $db->prepare("INSERT INTO audit_log (action, entity_type, entity_id, details, ip_address, user_agent) VALUES ('B2B_PIPELINE_UPDATE', 'ORGANIZATION', ?, ?, ?, ?)");
            $stmtLog->execute([(string)$b2bId, "Azienda aggiornata: stage [{$newStage}], status [{$newStatus}]", $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown']);

            $feedbackMessage = "Pratica B2B #{$b2bId} aggiornata con successo allo stage [{$newStage}].";
        }

        // Attivazione Convenzione
        if ($action === 'create_convention') {
            $appId = (int)$_POST['application_id'];
            $companyName = trim($_POST['company_name'] ?? '');
            $sicId = trim($_POST['sic_id'] ?? '');
            $title = trim($_POST['benefit_title'] ?? '');
            $category = trim($_POST['category'] ?? 'SERVIZI');
            $territory = trim($_POST['territory'] ?? 'Nazionale');
            $desc = trim($_POST['benefit_description'] ?? '');

            if (!empty($title)) {
                $qrToken = 'QR-CONV-' . strtoupper(bin2hex(random_bytes(5)));
                $stmtConv = $db->prepare("INSERT INTO b2b_conventions (application_id, sic_id, company_name, category, territory, benefit_title, benefit_description, qr_code_token, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'ACTIVE')");
                $stmtConv->execute([$appId, $sicId, $companyName, $category, $territory, $title, $desc, $qrToken]);
                $feedbackMessage = "Convenzione ufficiale attivata con successo con token [{$qrToken}].";
            }
        }
    }
}

// Export CSV B2B
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=CAMPUS_B2B_PIPELINE_' . date('Ymd_His') . '.csv');
    
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID', 'SIC-ID', 'Codice Pratica', 'Ragione Sociale', 'P.IVA', 'ATECO', 'Referente', 'Email', 'Telefono', 'Città', 'Regione', 'Interessi', 'Fit Score', 'Pipeline Stage', 'Stato', 'Data']);

    $rows = $db->query("
        SELECT id, sic_id, application_code, company_name, vat_number, ateco_code, contact_name, contact_email, contact_phone, address_city, address_region, looking_for, fit_score, pipeline_stage, status, created_at
        FROM b2b_applications
        ORDER BY id DESC
    ")->fetchAll();

    foreach ($rows as $r) {
        fputcsv($out, $r);
    }
    fclose($out);
    exit;
}

// ---------------------------------------------------------------------------
// KPI PIPELINE B2B
// ---------------------------------------------------------------------------
$totalB2B = (int)$db->query("SELECT COUNT(*) FROM b2b_applications")->fetchColumn();
$countNew = (int)$db->query("SELECT COUNT(*) FROM b2b_applications WHERE pipeline_stage = 'NEW'")->fetchColumn();
$countQualifying = (int)$db->query("SELECT COUNT(*) FROM b2b_applications WHERE pipeline_stage = 'QUALIFYING'")->fetchColumn();
$countOpportunity = (int)$db->query("SELECT COUNT(*) FROM b2b_applications WHERE pipeline_stage IN ('DISCOVERY', 'OPPORTUNITY', 'PROPOSAL')")->fetchColumn();
$countNegotiation = (int)$db->query("SELECT COUNT(*) FROM b2b_applications WHERE pipeline_stage = 'NEGOTIATION'")->fetchColumn();
$countActive = (int)$db->query("SELECT COUNT(*) FROM b2b_applications WHERE pipeline_stage = 'ACTIVE'")->fetchColumn();

// Convenzioni attive
$totalConventions = (int)$db->query("SELECT COUNT(*) FROM b2b_conventions WHERE status = 'ACTIVE'")->fetchColumn();

// Lista Aziende B2B
$companies = $db->query("SELECT * FROM b2b_applications ORDER BY id DESC")->fetchAll();

$pageTitle = 'CRM B2B Command Center — Aziende & Partner CAMPUS';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="section" style="padding-top: 30px; padding-bottom: 70px;">
  <div class="container" style="max-width: 1440px;">

    <!-- TOP BAR B2B COMMAND CENTER -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 18px;">
      <div>
        <div style="display: flex; align-items: center; gap: 8px;">
          <span style="width: 9px; height: 9px; background: var(--gold-light); border-radius: 50%; display: inline-block; box-shadow: 0 0 10px var(--gold-light);"></span>
          <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
            CRM B2B · Imprese, Partner & Sponsor
          </span>
        </div>
        <h1 style="font-size: clamp(22px, 3vw, 32px); color: #ffffff; margin-top: 4px; font-family: 'Cinzel', serif;">
          CAMPUS <span class="gold-text">B2B Command Center</span>
        </h1>
        <div style="font-size: 12.5px; color: var(--text-dim);">
          Gestione Pipeline Relazioni Istituzionali, Corporate Academy e Convenzioni Territoriali
        </div>
      </div>

      <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <a href="/admin/dashboard.php" class="btn-outline-gold" style="font-size: 12px; padding: 9px 18px; display: inline-flex; align-items: center; gap: 6px;">
          ← Faculty CRM
        </a>
        <a href="/admin/directory.php" class="btn-outline-gold" style="font-size: 12px; padding: 9px 18px; display: inline-flex; align-items: center; gap: 6px;">
          <?= icon_gold('institution', 14) ?> Albo Enti (110)
        </a>
        <a href="/admin/b2b.php?export=csv" class="btn-gold" style="font-size: 12px; padding: 9px 18px; display: inline-flex; align-items: center; gap: 6px;">
          <?= icon_gold('download', 14) ?> Esporta CSV B2B
        </a>
        <a href="/b2b/index.php" target="_blank" class="btn-outline-gold" style="font-size: 12px; padding: 9px 18px; display: inline-flex; align-items: center; gap: 6px;">
          <?= icon_gold('eye', 14) ?> Vista Sito B2B
        </a>
      </div>
    </div>

    <?php if ($feedbackMessage): ?>
      <div style="background: rgba(212, 175, 55, 0.15); border: 1px solid var(--gold-primary); color: #ffffff; padding: 12px 18px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 10px;">
        <?= icon_gold('check', 16) ?> <span><?= sanitize_output($feedbackMessage) ?></span>
      </div>
    <?php endif; ?>

    <!-- PIPELINE FUNNEL KPI -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin-bottom: 25px;">
      
      <div class="glass-card" style="padding: 16px; border-left: 4px solid var(--gold-primary);">
        <div style="font-size: 10.5px; text-transform: uppercase; color: var(--gold-light); font-weight: 700;">Totale Aziende</div>
        <div style="font-size: 26px; font-weight: 900; color: #ffffff; margin-top: 2px;"><?= $totalB2B ?></div>
        <div style="font-size: 11px; color: var(--text-dim);">Registrate nel CRM</div>
      </div>

      <div class="glass-card" style="padding: 16px; border-left: 4px solid #3b82f6;">
        <div style="font-size: 10.5px; text-transform: uppercase; color: #93c5fd; font-weight: 700;">1. New</div>
        <div style="font-size: 26px; font-weight: 900; color: #ffffff; margin-top: 2px;"><?= $countNew ?></div>
        <div style="font-size: 11px; color: var(--text-dim);">Appena ricevute</div>
      </div>

      <div class="glass-card" style="padding: 16px; border-left: 4px solid #f59e0b;">
        <div style="font-size: 10.5px; text-transform: uppercase; color: #fcd34d; font-weight: 700;">2. Qualifying</div>
        <div style="font-size: 26px; font-weight: 900; color: #ffffff; margin-top: 2px;"><?= $countQualifying ?></div>
        <div style="font-size: 11px; color: var(--text-dim);">Analisi ATECO / Fit</div>
      </div>

      <div class="glass-card" style="padding: 16px; border-left: 4px solid var(--gold-metallic);">
        <div style="font-size: 10.5px; text-transform: uppercase; color: var(--gold-light); font-weight: 700;">3. Opportunity</div>
        <div style="font-size: 26px; font-weight: 900; color: #ffffff; margin-top: 2px;"><?= $countOpportunity ?></div>
        <div style="font-size: 11px; color: var(--text-dim);">Proposta elaborata</div>
      </div>

      <div class="glass-card" style="padding: 16px; border-left: 4px solid #a855f7;">
        <div style="font-size: 10.5px; text-transform: uppercase; color: #d8b4fe; font-weight: 700;">4. Negotiation</div>
        <div style="font-size: 26px; font-weight: 900; color: #ffffff; margin-top: 2px;"><?= $countNegotiation ?></div>
        <div style="font-size: 11px; color: var(--text-dim);">Stesura convenzione</div>
      </div>

      <div class="glass-card" style="padding: 16px; border-left: 4px solid #10b981;">
        <div style="font-size: 10.5px; text-transform: uppercase; color: #6ee7b7; font-weight: 700;">5. Active Partner</div>
        <div style="font-size: 26px; font-weight: 900; color: #ffffff; margin-top: 2px;"><?= $countActive ?></div>
        <div style="font-size: 11px; color: var(--text-dim);"><?= $totalConventions ?> convenzioni attive</div>
      </div>

    </div>

    <!-- TABELLA PIPELINE B2B -->
    <div class="glass-card" style="padding: 25px;">
      
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <h2 style="font-size: 19px; color: #ffffff; margin: 0; font-family: 'Cinzel', serif;">
          Aziende Candidate & Partner (<span id="b2b-visible-count"><?= count($companies) ?></span>)
        </h2>

        <div style="display: flex; gap: 6px; flex-wrap: wrap;" id="b2b-stage-filter-btns">
          <button class="b2b-filter-btn active" data-stage="ALL" style="padding: 4px 10px; font-size: 11px; border-radius: 10px; background: var(--gold-primary); color: #000; font-weight: bold; border: none; cursor: pointer;">TUTTI</button>
          <button class="b2b-filter-btn" data-stage="NEW" style="padding: 4px 10px; font-size: 11px; border-radius: 10px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--border-subtle); cursor: pointer;">NEW</button>
          <button class="b2b-filter-btn" data-stage="QUALIFYING" style="padding: 4px 10px; font-size: 11px; border-radius: 10px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--border-subtle); cursor: pointer;">QUALIFYING</button>
          <button class="b2b-filter-btn" data-stage="OPPORTUNITY,PROPOSAL" style="padding: 4px 10px; font-size: 11px; border-radius: 10px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--border-subtle); cursor: pointer;">OPPORTUNITY</button>
          <button class="b2b-filter-btn" data-stage="NEGOTIATION" style="padding: 4px 10px; font-size: 11px; border-radius: 10px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--border-subtle); cursor: pointer;">NEGOTIATION</button>
          <button class="b2b-filter-btn" data-stage="ACTIVE" style="padding: 4px 10px; font-size: 11px; border-radius: 10px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--border-subtle); cursor: pointer;">ACTIVE</button>
        </div>
      </div>

      <div style="margin-bottom: 18px;">
        <input type="text" id="b2b-search-input" placeholder="Cerca istantaneamente per Ragione Sociale, SIC-ID, Referente, ATECO, Email, Città..." class="form-control" style="font-size: 13.5px; padding: 10px 14px;">
      </div>

      <?php if (empty($companies)): ?>
        <div style="text-align: center; padding: 50px 20px; color: var(--text-dim);">
          <div style="margin-bottom: 10px;"><?= icon_gold('institution', 40) ?></div>
          Nessuna azienda candidata ancora presente.<br>
          <a href="/b2b/apply.php" target="_blank" style="color: var(--gold-light); text-decoration: underline; margin-top: 10px; display: inline-block;">
            Apri il modulo B2B pubblico per testare un invio
          </a>
        </div>
      <?php else: ?>
        <div style="overflow-x: auto;">
          <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
            <thead>
              <tr style="border-bottom: 2px solid var(--border-glass); color: var(--gold-primary);">
                <th style="padding: 10px 12px;">SIC-ID & Data</th>
                <th style="padding: 10px 12px;">Azienda & ATECO</th>
                <th style="padding: 10px 12px;">Referente & Recapiti</th>
                <th style="padding: 10px 12px;">Interessi / Fabbisogno</th>
                <th style="padding: 10px 12px;">Fit Score</th>
                <th style="padding: 10px 12px;">Pipeline Stage</th>
                <th style="padding: 10px 12px; text-align: right;">Gestione & Note</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($companies as $comp): ?>
                <?php 
                  $looking = json_decode($comp['looking_for'], true) ?: [];
                  $stageColor = '#94a3b8';
                  if ($comp['pipeline_stage'] === 'NEW') $stageColor = '#3b82f6';
                  elseif ($comp['pipeline_stage'] === 'QUALIFYING') $stageColor = '#f59e0b';
                  elseif ($comp['pipeline_stage'] === 'ACTIVE') $stageColor = '#10b981';
                ?>
                <tr class="b2b-row" data-stage="<?= sanitize_output($comp['pipeline_stage']) ?>" style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: background 0.2s;" onmouseover="this.style.background='rgba(212,175,55,0.04)'" onmouseout="this.style.background='transparent'">
                  
                  <!-- SIC-ID -->
                  <td style="padding: 12px; vertical-align: top;">
                    <span style="font-family: monospace; font-weight: 800; color: var(--gold-light); font-size: 12.5px; display: block;">
                      <?= sanitize_output($comp['sic_id']) ?>
                    </span>
                    <span style="font-size: 11px; color: var(--text-dim); display: block; margin-top: 2px;">
                      <?= substr($comp['created_at'], 0, 10) ?> · <?= sanitize_output($comp['application_code']) ?>
                    </span>
                  </td>

                  <!-- AZIENDA -->
                  <td style="padding: 12px; vertical-align: top;">
                    <strong style="color: #ffffff; font-size: 14px;"><?= sanitize_output($comp['company_name']) ?></strong><br>
                    <?php if (!empty($comp['brand_name'])): ?>
                      <span style="font-size: 11.5px; color: var(--gold-light);">Brand: <?= sanitize_output($comp['brand_name']) ?></span><br>
                    <?php endif; ?>
                    <span style="font-size: 11px; color: var(--text-dim);">
                      P.IVA: <?= sanitize_output($comp['vat_number'] ?: 'N/D') ?> · <?= sanitize_output($comp['company_size']) ?> (<?= sanitize_output($comp['employees_range']) ?> addetti)
                    </span>
                    <?php if (!empty($comp['ateco_code'])): ?>
                      <div style="font-size: 11px; color: var(--gold-primary); margin-top: 2px;">
                        ATECO: <?= sanitize_output($comp['ateco_code']) ?>
                      </div>
                    <?php endif; ?>
                  </td>

                  <!-- REFERENTE -->
                  <td style="padding: 12px; vertical-align: top;">
                    <strong style="color: #ffffff;"><?= sanitize_output($comp['contact_name']) ?></strong><br>
                    <span style="font-size: 11.5px; color: var(--text-dim);"><?= sanitize_output($comp['contact_role'] ?: 'Referente') ?></span><br>
                    <span style="font-size: 12px; color: var(--text-muted);">
                      <a href="mailto:<?= sanitize_output($comp['contact_email']) ?>" style="color: var(--text-muted); text-decoration: none;"><?= sanitize_output($comp['contact_email']) ?></a><br>
                      <a href="tel:<?= sanitize_output($comp['contact_phone']) ?>" style="color: var(--text-muted); text-decoration: none;"><?= sanitize_output($comp['contact_phone']) ?></a>
                    </span>
                  </td>

                  <!-- INTERESSI -->
                  <td style="padding: 12px; vertical-align: top; max-width: 240px;">
                    <?php if (empty($looking)): ?>
                      <span style="color: var(--text-dim); font-size: 11px;">Generale B2B</span>
                    <?php else: ?>
                      <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                        <?php foreach ($looking as $item): ?>
                          <span style="background: rgba(212,175,55,0.12); border: 1px solid var(--border-gold); padding: 1px 6px; border-radius: 4px; font-size: 10.5px; color: #ffffff;">
                            <?= sanitize_output($item) ?>
                          </span>
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>
                    <?php if (!empty($comp['goal_description'])): ?>
                      <div style="font-size: 11px; color: var(--text-dim); margin-top: 6px; font-style: italic; line-height: 1.3;">
                        "<?= sanitize_output(substr($comp['goal_description'], 0, 70)) ?>..."
                      </div>
                    <?php endif; ?>
                  </td>

                  <!-- FIT SCORE -->
                  <td style="padding: 12px; vertical-align: top;">
                    <div style="font-size: 18px; font-weight: 900; color: <?= $comp['fit_score'] >= 75 ? '#10b981' : ($comp['fit_score'] >= 50 ? 'var(--gold-light)' : '#94a3b8') ?>;">
                      <?= $comp['fit_score'] ?>/100
                    </div>
                    <div style="font-size: 10px; color: var(--text-dim);">AI Fit Score</div>
                  </td>

                  <!-- STAGE BADGE -->
                  <td style="padding: 12px; vertical-align: top;">
                    <span style="display: inline-block; padding: 3px 8px; border-radius: 10px; font-size: 10.5px; font-weight: 800; background: rgba(0,0,0,0.5); color: <?= $stageColor ?>; border: 1px solid <?= $stageColor ?>; text-transform: uppercase;">
                      <?= sanitize_output($comp['pipeline_stage']) ?>
                    </span>
                  </td>

                  <!-- AZIONI PIPELINE -->
                  <td style="padding: 12px; vertical-align: top; text-align: right;">
                    <form action="/admin/b2b.php" method="POST" style="display: inline-flex; flex-direction: column; gap: 6px; align-items: flex-end;">
                      <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
                      <input type="hidden" name="action" value="update_pipeline">
                      <input type="hidden" name="application_id" value="<?= $comp['id'] ?>">
                      
                      <div style="display: flex; gap: 4px;">
                        <select name="pipeline_stage" class="form-control" style="padding: 4px 6px; font-size: 11px; width: 120px; height: 28px;">
                          <option value="NEW" <?= $comp['pipeline_stage'] === 'NEW' ? 'selected' : '' ?>>1. New</option>
                          <option value="QUALIFYING" <?= $comp['pipeline_stage'] === 'QUALIFYING' ? 'selected' : '' ?>>2. Qualifying</option>
                          <option value="DISCOVERY" <?= $comp['pipeline_stage'] === 'DISCOVERY' ? 'selected' : '' ?>>3. Discovery</option>
                          <option value="OPPORTUNITY" <?= $comp['pipeline_stage'] === 'OPPORTUNITY' ? 'selected' : '' ?>>4. Opportunity</option>
                          <option value="PROPOSAL" <?= $comp['pipeline_stage'] === 'PROPOSAL' ? 'selected' : '' ?>>5. Proposal</option>
                          <option value="NEGOTIATION" <?= $comp['pipeline_stage'] === 'NEGOTIATION' ? 'selected' : '' ?>>6. Negotiation</option>
                          <option value="AGREEMENT" <?= $comp['pipeline_stage'] === 'AGREEMENT' ? 'selected' : '' ?>>7. Agreement</option>
                          <option value="ACTIVE" <?= $comp['pipeline_stage'] === 'ACTIVE' ? 'selected' : '' ?>>8. Active</option>
                          <option value="LOST" <?= $comp['pipeline_stage'] === 'LOST' ? 'selected' : '' ?>>Lost / Archiviato</option>
                        </select>

                        <button type="submit" class="btn-gold" style="padding: 4px 8px; font-size: 11px; height: 28px; font-weight: 700;">
                          Salva
                        </button>
                      </div>

                      <input type="text" name="admin_notes" value="<?= sanitize_output($comp['admin_notes'] ?? '') ?>" placeholder="Note trattativa..." class="form-control" style="font-size: 10.5px; padding: 4px 6px; width: 170px; height: 24px;">
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

<!-- LIVE FILTER SCRIPT B2B -->
<script>
(function() {
  'use strict';

  var searchInput = document.getElementById('b2b-search-input');
  var rows = document.querySelectorAll('.b2b-row');
  var countSpan = document.getElementById('b2b-visible-count');
  var filterBtns = document.querySelectorAll('.b2b-filter-btn');

  var currentStageFilter = 'ALL';
  var currentSearch = '';

  function filterTable() {
    var visible = 0;
    rows.forEach(function(row) {
      var text = row.textContent.toLowerCase();
      var stage = row.getAttribute('data-stage') || '';

      var matchQuery = (currentSearch === '' || text.includes(currentSearch));
      var matchStage = false;

      if (currentStageFilter === 'ALL') {
        matchStage = true;
      } else {
        var allowed = currentStageFilter.split(',');
        matchStage = allowed.includes(stage);
      }

      if (matchQuery && matchStage) {
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
      currentSearch = this.value.toLowerCase().trim();
      filterTable();
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

      currentStageFilter = this.getAttribute('data-stage') || 'ALL';
      filterTable();
    });
  });

})();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
