<?php
/**
 * CAMPUS.CAMP — Command Center Territori & Comuni (CAMPUS for Cities CRM)
 * Pipeline: APPLICATION_RECEIVED -> PRELIMINARY_REVIEW -> TERRITORY_ANALYSIS -> FACILITY_REVIEW -> FEASIBILITY -> INSTITUTIONAL_MEETING -> PROPOSAL -> CO_DESIGN -> CONVENTION -> CAMPUS_SETUP -> CAMPUS_CITY_ACTIVE
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

// Gestione Azioni POST (cambio stage, note)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (verify_csrf_token($token)) {
        $action = $_POST['action'] ?? '';

        if ($action === 'update_city_stage') {
            $cityId = (int)$_POST['city_id'];
            $newStage = trim($_POST['pipeline_stage'] ?? 'APPLICATION_RECEIVED');
            $newStatus = ($newStage === 'CAMPUS_CITY_ACTIVE') ? 'ACTIVE' : 'SUBMITTED';
            $adminNotes = trim($_POST['admin_notes'] ?? '');

            $stmtUp = $db->prepare("UPDATE campus_cities SET pipeline_stage = ?, status = ?, admin_notes = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmtUp->execute([$newStage, $newStatus, $adminNotes, $cityId]);

            // Audit Log
            $stmtLog = $db->prepare("INSERT INTO audit_log (action, entity_type, entity_id, details, ip_address, user_agent) VALUES ('CITY_PIPELINE_UPDATE', 'MUNICIPALITY', ?, ?, ?, ?)");
            $stmtLog->execute([(string)$cityId, "Comune aggiornato: stage [{$newStage}]", $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown']);

            $feedbackMessage = "Pratica Territoriale #{$cityId} aggiornata allo stage [{$newStage}].";
        }
    }
}

// Export CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=CAMPUS_CITIES_PIPELINE_' . date('Ymd_His') . '.csv');
    
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID', 'SIC-ID', 'Codice Pratica', 'Tipologia Ente', 'Denominazione', 'Codice Fiscale', 'IPA', 'PEC', 'Referente', 'Ruolo', 'Email', 'Telefono', 'Abitanti', 'Readiness Score', 'Stage Pipeline', 'Stato', 'Data']);

    $rows = $db->query("
        SELECT id, sic_id, application_code, entity_type, entity_name, fiscal_code, ipa_code, pec, contact_name, contact_role, contact_email, contact_phone, population, readiness_score, pipeline_stage, status, created_at
        FROM campus_cities
        ORDER BY id DESC
    ")->fetchAll();

    foreach ($rows as $r) {
        fputcsv($out, $r);
    }
    fclose($out);
    exit;
}

// Statistiche Pipeline Cities
$totalCities = (int)$db->query("SELECT COUNT(*) FROM campus_cities")->fetchColumn();
$countReceived = (int)$db->query("SELECT COUNT(*) FROM campus_cities WHERE pipeline_stage = 'APPLICATION_RECEIVED'")->fetchColumn();
$countReview = (int)$db->query("SELECT COUNT(*) FROM campus_cities WHERE pipeline_stage IN ('PRELIMINARY_REVIEW', 'TERRITORY_ANALYSIS', 'FACILITY_REVIEW', 'FEASIBILITY')")->fetchColumn();
$countNegotiation = (int)$db->query("SELECT COUNT(*) FROM campus_cities WHERE pipeline_stage IN ('INSTITUTIONAL_MEETING', 'PROPOSAL', 'CO_DESIGN', 'CONVENTION')")->fetchColumn();
$countActive = (int)$db->query("SELECT COUNT(*) FROM campus_cities WHERE pipeline_stage = 'CAMPUS_CITY_ACTIVE'")->fetchColumn();

// Elenco Completo Comuni con Immobile
$cities = $db->query("
    SELECT c.*, f.facility_name, f.facility_type, f.square_meters, f.capacity
    FROM campus_cities c
    LEFT JOIN city_facilities f ON f.city_application_id = c.id
    ORDER BY c.id DESC
")->fetchAll();

$pageTitle = 'CRM Territori & Comuni — CAMPUS Command Center';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="section" style="padding-top: 30px; padding-bottom: 70px;">
  <div class="container" style="max-width: 1440px;">

    <!-- TOP BAR -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 18px;">
      <div>
        <div style="display: flex; align-items: center; gap: 8px;">
          <span style="width: 9px; height: 9px; background: var(--gold-light); border-radius: 50%; display: inline-block; box-shadow: 0 0 10px var(--gold-light);"></span>
          <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
            CRM Territori & Pubbliche Amministrazioni
          </span>
        </div>
        <h1 style="font-size: clamp(22px, 3vw, 32px); color: #ffffff; margin-top: 4px; font-family: 'Cinzel', serif;">
          CAMPUS for Cities <span class="gold-text">Command Center</span>
        </h1>
        <div style="font-size: 12.5px; color: var(--text-dim);">
          Gestione Istruttoria Candidature Enti Locali, Prefattibilità e Nodi Accreditati
        </div>
      </div>

      <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <a href="/admin/dashboard.php" class="btn-outline-gold" style="font-size: 12px; padding: 9px 18px; display: inline-flex; align-items: center; gap: 6px;">
          ← Faculty CRM
        </a>
        <a href="/admin/b2b.php" class="btn-outline-gold" style="font-size: 12px; padding: 9px 18px; display: inline-flex; align-items: center; gap: 6px;">
          <?= icon_gold('briefcase', 14) ?> B2B Aziende
        </a>
        <a href="/admin/cities.php?export=csv" class="btn-gold" style="font-size: 12px; padding: 9px 18px; display: inline-flex; align-items: center; gap: 6px;">
          <?= icon_gold('download', 14) ?> Esporta CSV Comuni
        </a>
        <a href="/campus-city/network.php" target="_blank" class="btn-outline-gold" style="font-size: 12px; padding: 9px 18px; display: inline-flex; align-items: center; gap: 6px;">
          <?= icon_gold('eye', 14) ?> Vista Rete Pubblica
        </a>
      </div>
    </div>

    <?php if ($feedbackMessage): ?>
      <div style="background: rgba(212, 175, 55, 0.15); border: 1px solid var(--gold-primary); color: #ffffff; padding: 12px 18px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 10px;">
        <?= icon_gold('check', 16) ?> <span><?= sanitize_output($feedbackMessage) ?></span>
      </div>
    <?php endif; ?>

    <!-- FUNNEL KPI COMUNI -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 12px; margin-bottom: 25px;">
      
      <div class="glass-card" style="padding: 16px; border-left: 4px solid var(--gold-primary);">
        <div style="font-size: 10.5px; text-transform: uppercase; color: var(--gold-light); font-weight: 700;">Totale Enti Candidati</div>
        <div style="font-size: 26px; font-weight: 900; color: #ffffff; margin-top: 2px;"><?= $totalCities ?></div>
        <div style="font-size: 11px; color: var(--text-dim);">Nel database centrale</div>
      </div>

      <div class="glass-card" style="padding: 16px; border-left: 4px solid #3b82f6;">
        <div style="font-size: 10.5px; text-transform: uppercase; color: #93c5fd; font-weight: 700;">1. Ricevuta</div>
        <div style="font-size: 26px; font-weight: 900; color: #ffffff; margin-top: 2px;"><?= $countReceived ?></div>
        <div style="font-size: 11px; color: var(--text-dim);">Nuove candidature PA</div>
      </div>

      <div class="glass-card" style="padding: 16px; border-left: 4px solid #f59e0b;">
        <div style="font-size: 10.5px; text-transform: uppercase; color: #fcd34d; font-weight: 700;">2. Istruttoria</div>
        <div style="font-size: 26px; font-weight: 900; color: #ffffff; margin-top: 2px;"><?= $countReview ?></div>
        <div style="font-size: 11px; color: var(--text-dim);">Studio di Prefattibilità</div>
      </div>

      <div class="glass-card" style="padding: 16px; border-left: 4px solid #a855f7;">
        <div style="font-size: 10.5px; text-transform: uppercase; color: #d8b4fe; font-weight: 700;">3. Incontro & Bozza</div>
        <div style="font-size: 26px; font-weight: 900; color: #ffffff; margin-top: 2px;"><?= $countNegotiation ?></div>
        <div style="font-size: 11px; color: var(--text-dim);">Confronto con la Giunta</div>
      </div>

      <div class="glass-card" style="padding: 16px; border-left: 4px solid #10b981;">
        <div style="font-size: 10.5px; text-transform: uppercase; color: #6ee7b7; font-weight: 700;">4. Nodi Attivi</div>
        <div style="font-size: 26px; font-weight: 900; color: #ffffff; margin-top: 2px;"><?= $countActive ?></div>
        <div style="font-size: 11px; color: var(--text-dim);">Convenzioni deliberate</div>
      </div>

    </div>

    <!-- TABELLA CANDIDATURE COMUNI -->
    <div class="glass-card" style="padding: 25px;">
      
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <h2 style="font-size: 19px; color: #ffffff; margin: 0; font-family: 'Cinzel', serif;">
          Registro Candidature Territoriali (<span id="city-visible-count"><?= count($cities) ?></span>)
        </h2>
      </div>

      <div style="margin-bottom: 18px;">
        <input type="text" id="city-search-input" placeholder="Cerca istantaneamente per Ente, SIC-ID, Referente, Abitanti, PEC..." class="form-control" style="font-size: 13.5px; padding: 10px 14px;">
      </div>

      <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
          <thead>
            <tr style="border-bottom: 2px solid var(--border-glass); color: var(--gold-primary);">
              <th style="padding: 10px 12px;">SIC-ID & Data</th>
              <th style="padding: 10px 12px;">Ente Pubblico & Abitanti</th>
              <th style="padding: 10px 12px;">Struttura Candidata</th>
              <th style="padding: 10px 12px;">Referente Istituzionale</th>
              <th style="padding: 10px 12px;">Readiness</th>
              <th style="padding: 10px 12px;">Pipeline Stage</th>
              <th style="padding: 10px 12px; text-align: right;">Gestione Istruttoria</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cities as $c): ?>
              <?php 
                $stageColor = '#94a3b8';
                if ($c['pipeline_stage'] === 'APPLICATION_RECEIVED') $stageColor = '#3b82f6';
                elseif (in_array($c['pipeline_stage'], ['PRELIMINARY_REVIEW', 'TERRITORY_ANALYSIS', 'FEASIBILITY'])) $stageColor = '#f59e0b';
                elseif ($c['pipeline_stage'] === 'CAMPUS_CITY_ACTIVE') $stageColor = '#10b981';
              ?>
              <tr class="city-row" style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: background 0.2s;" onmouseover="this.style.background='rgba(212,175,55,0.04)'" onmouseout="this.style.background='transparent'">
                
                <!-- SIC-ID -->
                <td style="padding: 12px; vertical-align: top;">
                  <span style="font-family: monospace; font-weight: 800; color: var(--gold-light); font-size: 12px; display: block;">
                    <?= sanitize_output($c['sic_id']) ?>
                  </span>
                  <span style="font-size: 11px; color: var(--text-dim); display: block; margin-top: 2px;">
                    <?= substr($c['created_at'], 0, 10) ?> · <?= sanitize_output($c['application_code']) ?>
                  </span>
                </td>

                <!-- ENTE -->
                <td style="padding: 12px; vertical-align: top;">
                  <strong style="color: #ffffff; font-size: 14px;"><?= sanitize_output($c['entity_name']) ?></strong><br>
                  <span style="font-size: 11.5px; color: var(--text-dim);">
                    <?= sanitize_output($c['entity_type']) ?> · <?= number_format((int)$c['population'], 0, ',', '.') ?> ab. (Bacino: <?= number_format((int)$c['estimated_catchment'], 0, ',', '.') ?>)
                  </span>
                  <?php if (!empty($c['ipa_code'])): ?>
                    <div style="font-size: 10.5px; color: var(--gold-primary); font-family: monospace; margin-top: 2px;">
                      IPA: <?= sanitize_output($c['ipa_code']) ?>
                    </div>
                  <?php endif; ?>
                </td>

                <!-- STRUTTURA -->
                <td style="padding: 12px; vertical-align: top;">
                  <strong style="color: #cbd5e1;"><?= sanitize_output($c['facility_name'] ?: 'Struttura Civica') ?></strong><br>
                  <span style="font-size: 11px; color: var(--text-dim);">
                    <?= sanitize_output($c['facility_type'] ?: 'Non specificata') ?> · <?= $c['square_meters'] ?: 'N/D' ?> mq
                  </span>
                </td>

                <!-- REFERENTE -->
                <td style="padding: 12px; vertical-align: top;">
                  <strong style="color: #ffffff;"><?= sanitize_output($c['contact_name']) ?></strong><br>
                  <span style="font-size: 11.5px; color: var(--gold-light);"><?= sanitize_output($c['contact_role']) ?></span><br>
                  <span style="font-size: 12px; color: var(--text-dim);">
                    <a href="mailto:<?= sanitize_output($c['contact_email']) ?>" style="color: var(--text-muted); text-decoration: none;"><?= sanitize_output($c['contact_email']) ?></a><br>
                    <a href="tel:<?= sanitize_output($c['contact_phone']) ?>" style="color: var(--text-muted); text-decoration: none;"><?= sanitize_output($c['contact_phone']) ?></a>
                  </span>
                </td>

                <!-- READINESS SCORE -->
                <td style="padding: 12px; vertical-align: top;">
                  <div style="font-size: 18px; font-weight: 900; color: <?= $c['readiness_score'] >= 75 ? '#10b981' : ($c['readiness_score'] >= 50 ? 'var(--gold-light)' : '#94a3b8') ?>;">
                    <?= $c['readiness_score'] ?>/100
                  </div>
                  <div style="font-size: 10px; color: var(--text-dim);">Readiness Score</div>
                </td>

                <!-- STAGE -->
                <td style="padding: 12px; vertical-align: top;">
                  <span style="display: inline-block; padding: 3px 8px; border-radius: 10px; font-size: 10px; font-weight: 800; background: rgba(0,0,0,0.5); color: <?= $stageColor ?>; border: 1px solid <?= $stageColor ?>; text-transform: uppercase;">
                    <?= sanitize_output($c['pipeline_stage']) ?>
                  </span>
                </td>

                <!-- AZIONI -->
                <td style="padding: 12px; vertical-align: top; text-align: right;">
                  <form action="/admin/cities.php" method="POST" style="display: inline-flex; flex-direction: column; gap: 6px; align-items: flex-end;">
                    <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
                    <input type="hidden" name="action" value="update_city_stage">
                    <input type="hidden" name="city_id" value="<?= $c['id'] ?>">
                    
                    <div style="display: flex; gap: 4px;">
                      <select name="pipeline_stage" class="form-control" style="padding: 4px 6px; font-size: 11px; width: 140px; height: 28px;">
                        <option value="APPLICATION_RECEIVED" <?= $c['pipeline_stage'] === 'APPLICATION_RECEIVED' ? 'selected' : '' ?>>1. Ricevuta</option>
                        <option value="PRELIMINARY_REVIEW" <?= $c['pipeline_stage'] === 'PRELIMINARY_REVIEW' ? 'selected' : '' ?>>2. Esame Preliminare</option>
                        <option value="FEASIBILITY" <?= $c['pipeline_stage'] === 'FEASIBILITY' ? 'selected' : '' ?>>3. Prefattibilità</option>
                        <option value="INSTITUTIONAL_MEETING" <?= $c['pipeline_stage'] === 'INSTITUTIONAL_MEETING' ? 'selected' : '' ?>>4. Incontro Giunta</option>
                        <option value="CONVENTION" <?= $c['pipeline_stage'] === 'CONVENTION' ? 'selected' : '' ?>>5. Bozza Convenzione</option>
                        <option value="CAMPUS_CITY_ACTIVE" <?= $c['pipeline_stage'] === 'CAMPUS_CITY_ACTIVE' ? 'selected' : '' ?>>6. Attivo / Convenzionato</option>
                        <option value="PAUSED" <?= $c['pipeline_stage'] === 'PAUSED' ? 'selected' : '' ?>>In Sospeso</option>
                        <option value="NOT_FEASIBLE" <?= $c['pipeline_stage'] === 'NOT_FEASIBLE' ? 'selected' : '' ?>>Non Idoneo</option>
                      </select>

                      <button type="submit" class="btn-gold" style="padding: 4px 8px; font-size: 11px; height: 28px; font-weight: 700;">
                        Salva
                      </button>
                    </div>

                    <input type="text" name="admin_notes" value="<?= sanitize_output($c['admin_notes'] ?? '') ?>" placeholder="Note istruttoria Ente..." class="form-control" style="font-size: 10.5px; padding: 4px 6px; width: 190px; height: 24px;">
                  </form>
                </td>

              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>

  </div>
</section>

<!-- LIVE FILTER SCRIPT -->
<script>
(function() {
  'use strict';

  var searchInput = document.getElementById('city-search-input');
  var rows = document.querySelectorAll('.city-row');
  var countSpan = document.getElementById('city-visible-count');

  if (searchInput) {
    searchInput.addEventListener('input', function() {
      var q = this.value.toLowerCase().trim();
      var visible = 0;
      rows.forEach(function(row) {
        var text = row.textContent.toLowerCase();
        if (q === '' || text.includes(q)) {
          row.style.display = '';
          visible++;
        } else {
          row.style.display = 'none';
        }
      });
      if (countSpan) countSpan.textContent = visible;
    });
  }

})();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
