<?php
/**
 * CAMPUS.CAMP — Verifica Stato Candidatura & Profilo Docente
 * Verifica pubblica tramite SIC-ID e Codice Fiscale / Email
 */


require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';

$pageTitle = 'Verifica Stato Candidatura — SIC-ID CAMPUS';
$pageDesc = 'Consulta lo stato di avanzamento della tua candidatura alla Faculty accademica CAMPUS tramite il tuo codice SIC-ID.';

$db = Database::getConnection();
$candidate = null;
$error = null;
$searched = false;

$querySic = trim($_GET['sic_id'] ?? $_POST['sic_id'] ?? '');
$queryCf = strtoupper(trim($_POST['fiscal_code'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] === 'POST' || !empty($querySic)) {
    $searched = true;
    if (!empty($querySic)) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($queryCf)) {
            $error = 'Inserisci il Codice Fiscale per motivi di riservatezza e sicurezza.';
        } else {
            $sql = "
                SELECT a.*, p.first_name, p.last_name, p.fiscal_code, p.nationality
                FROM faculty_applications a
                JOIN persons p ON a.person_id = p.id
                WHERE a.sic_id = ?
            ";
            $params = [$querySic];
            if (!empty($queryCf)) {
                $sql .= " AND p.fiscal_code = ?";
                $params[] = $queryCf;
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $candidate = $stmt->fetch();

            if (!$candidate) {
                $error = 'Nessuna candidatura trovata con le credenziali specificate. Verifica il SIC-ID e il Codice Fiscale.';
            } else {
                // Fetch preferences
                $stmtPref = $db->prepare("SELECT * FROM faculty_preferences WHERE application_id = ? ORDER BY priority_order ASC");
                $stmtPref->execute([$candidate['id']]);
                $candidate['courses'] = $stmtPref->fetchAll();
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top: 50px;">
  <div class="container" style="max-width: 850px;">

    <div style="text-align: center; margin-bottom: 35px;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2.5px; font-weight: 800;">
        Registro Accademico Trasparente
      </span>
      <h1 style="font-size: clamp(26px, 4vw, 36px); margin-top: 8px;">
        Verifica Stato <span class="gold-text">Candidatura Faculty</span>
      </h1>
      <p style="color: var(--text-muted); font-size: 15px;">
        Inserisci il Tuo codice univoco <strong>SIC-ID</strong> per consultare l'esito della valutazione.
      </p>
    </div>

    <!-- SEARCH CARD -->
    <div class="glass-card" style="margin-bottom: 35px; padding: 30px;">
      <form action="/status.php" method="POST" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 16px; align-items: flex-end;">
        <div>
          <label class="form-label">Codice SIC-ID *</label>
          <input type="text" name="sic_id" class="form-control" placeholder="SIC-ID-XXXXXXXXXXXX" value="<?= sanitize_output($querySic) ?>" required style="text-transform: uppercase; font-family: monospace;">
        </div>
        <div>
          <label class="form-label">Codice Fiscale Candidato *</label>
          <input type="text" name="fiscal_code" class="form-control" maxlength="16" placeholder="Codice Fiscale" value="<?= sanitize_output($queryCf) ?>" required style="text-transform: uppercase;">
        </div>
        <div>
          <button type="submit" class="btn-gold" style="height: 48px; padding: 0 24px; display: inline-flex; align-items: center; gap: 8px;">
            <?= icon_gold('search', 16) ?> Cerca
          </button>
        </div>
      </form>
    </div>

    <?php if ($error): ?>
      <div style="background: rgba(0, 0, 0, 0.7); border: 1px solid var(--gold-primary); color: #ffffff; padding: 16px 20px; border-radius: var(--radius-sm); margin-bottom: 30px; display: flex; align-items: center; gap: 10px;">
        <?= icon_gold('shield', 18) ?> <span><?= sanitize_output($error) ?></span>
      </div>
    <?php endif; ?>

    <!-- CANDIDATE DETAILS & TIMELINE -->
    <?php if ($candidate): ?>
      <div class="glass-card" style="border: 2px solid var(--border-glass); padding: 35px;">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: gap; border-bottom: 1px solid var(--border-subtle); padding-bottom: 20px; margin-bottom: 25px;">
          <div>
            <div style="font-size: 12px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700;">
              Docente Candidato
            </div>
            <h2 style="font-size: 26px; color: #ffffff; margin-top: 4px;">
              <?= sanitize_output($candidate['first_name'] . ' ' . $candidate['last_name']) ?>
            </h2>
            <div style="color: var(--text-muted); font-size: 14px;">
              <?= sanitize_output($candidate['profession']) ?> · <?= sanitize_output($candidate['address_city'] . ' (' . $candidate['address_province'] . ')') ?>
            </div>
          </div>

          <div style="text-align: right;">
            <span style="display: inline-block; padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase; background: rgba(212, 175, 55, 0.15); color: var(--gold-light); border: 1px solid var(--gold-primary);">
              <?= sanitize_output($candidate['status']) ?>
            </span>
            <div style="font-family: monospace; font-size: 13px; color: var(--text-dim); margin-top: 6px;">
              <?= sanitize_output($candidate['sic_id']) ?>
            </div>
          </div>
        </div>

        <!-- TIMELINE STEPS -->
        <h3 style="color: #ffffff; font-size: 16px; margin-bottom: 18px;">Avanzamento Istituzionale</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 30px; text-align: center;">
          
          <div style="background: rgba(212, 175, 55, 0.15); border: 1px solid var(--gold-primary); padding: 14px 8px; border-radius: var(--radius-sm);">
            <div><?= icon_gold('check', 16) ?></div>
            <div style="font-size: 11px; font-weight: 700; color: var(--gold-light); margin-top: 4px; text-transform: uppercase;">1. Ricevuto</div>
            <div style="font-size: 10px; color: var(--text-dim);"><?= substr($candidate['created_at'], 0, 10) ?></div>
          </div>

          <div style="background: <?= in_array($candidate['status'], ['UNDER_REVIEW', 'SHORTLISTED', 'INTERVIEW', 'APPROVED', 'FACULTY_ACTIVE']) ? 'rgba(212, 175, 55, 0.2); border: 1px solid var(--gold-light)' : 'rgba(255,255,255,0.02); border: 1px solid var(--border-subtle)' ?>; padding: 14px 8px; border-radius: var(--radius-sm);">
            <div><?= in_array($candidate['status'], ['UNDER_REVIEW', 'SHORTLISTED', 'INTERVIEW', 'APPROVED', 'FACULTY_ACTIVE']) ? icon_gold('check', 16) : '<span style="color: var(--text-dim);">—</span>' ?></div>
            <div style="font-size: 11px; font-weight: 700; color: <?= in_array($candidate['status'], ['UNDER_REVIEW', 'SHORTLISTED', 'INTERVIEW', 'APPROVED', 'FACULTY_ACTIVE']) ? 'var(--gold-light)' : 'var(--text-dim)' ?>; margin-top: 4px; text-transform: uppercase;">2. Esame Titoli</div>
          </div>

          <div style="background: <?= in_array($candidate['status'], ['SHORTLISTED', 'INTERVIEW', 'APPROVED', 'FACULTY_ACTIVE']) ? 'rgba(212, 175, 55, 0.2); border: 1px solid var(--gold-light)' : 'rgba(255,255,255,0.02); border: 1px solid var(--border-subtle)' ?>; padding: 14px 8px; border-radius: var(--radius-sm);">
            <div><?= in_array($candidate['status'], ['SHORTLISTED', 'INTERVIEW', 'APPROVED', 'FACULTY_ACTIVE']) ? icon_gold('check', 16) : '<span style="color: var(--text-dim);">—</span>' ?></div>
            <div style="font-size: 11px; font-weight: 700; color: <?= in_array($candidate['status'], ['SHORTLISTED', 'INTERVIEW', 'APPROVED', 'FACULTY_ACTIVE']) ? 'var(--gold-light)' : 'var(--text-dim)' ?>; margin-top: 4px; text-transform: uppercase;">3. Colloquio</div>
          </div>

          <div style="background: <?= in_array($candidate['status'], ['APPROVED', 'FACULTY_ACTIVE']) ? 'rgba(212, 175, 55, 0.25); border: 1px solid var(--gold-primary)' : 'rgba(255,255,255,0.02); border: 1px solid var(--border-subtle)' ?>; padding: 14px 8px; border-radius: var(--radius-sm);">
            <div><?= in_array($candidate['status'], ['APPROVED', 'FACULTY_ACTIVE']) ? icon_gold('crown', 16) : '<span style="color: var(--text-dim);">—</span>' ?></div>
            <div style="font-size: 11px; font-weight: 700; color: <?= in_array($candidate['status'], ['APPROVED', 'FACULTY_ACTIVE']) ? 'var(--gold-light)' : 'var(--text-dim)' ?>; margin-top: 4px; text-transform: uppercase;">4. Albo Faculty</div>
          </div>

        </div>

        <!-- INSEGNAMENTI SELEZIONATI -->
        <?php if (!empty($candidate['courses'])): ?>
          <h4 style="color: #ffffff; font-size: 15px; margin-bottom: 12px;">Insegnamenti e Corsi Opzionati:</h4>
          <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 25px;">
            <?php foreach ($candidate['courses'] as $pref): ?>
              <span style="background: rgba(255,255,255,0.04); border: 1px solid var(--border-subtle); padding: 6px 14px; border-radius: 4px; font-size: 13px; color: var(--gold-light);">
                <?= sanitize_output($pref['course_name']) ?>
              </span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($candidate['admin_notes'])): ?>
          <div style="background: rgba(212, 175, 55, 0.08); border-left: 4px solid var(--gold-primary); padding: 14px 18px; border-radius: 0 var(--radius-sm) var(--radius-sm) 0; font-size: 13.5px; color: #f8fafc;">
            <strong>Comunicazione della Commissione:</strong><br>
            <?= nl2br(sanitize_output($candidate['admin_notes'])) ?>
          </div>
        <?php endif; ?>

      </div>
    <?php endif; ?>

  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
