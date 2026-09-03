<?php
/**
 * CAMPUS.CAMP — Rete Nazionale dei Comuni & Territori Aderenti
 * Visualizzazione dei nodi attivi e di quelli in fase di istruttoria tecnica/amministrativa
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/icons.php';

$pageTitle = 'Rete dei Comuni & Territori Aderenti — CAMPUS for Cities';
$pageDesc = 'Mappa istituzionale dei poli territoriali attivi e dei Comuni candidati nella rete accademica nazionale CAMPUS.';

$db = Database::getConnection();

// Query nodi attivi
$activeNodalCities = $db->query("
    SELECT c.*, f.facility_name, f.facility_type, f.address as facility_address, f.square_meters
    FROM campus_cities c
    LEFT JOIN city_facilities f ON f.city_application_id = c.id
    WHERE c.status = 'ACTIVE' OR c.pipeline_stage = 'CAMPUS_CITY_ACTIVE'
    ORDER BY c.id ASC
")->fetchAll();

// Query comuni in valutazione / istruttoria
$candidateCities = $db->query("
    SELECT entity_name, entity_type, population, pipeline_stage, readiness_score, created_at
    FROM campus_cities
    WHERE status != 'ACTIVE' AND pipeline_stage != 'CAMPUS_CITY_ACTIVE'
    ORDER BY id DESC
")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero" style="min-height: 60vh; padding: 95px 20px 55px 20px;">
  <div class="hero-overlay"></div>
  <div class="hero-content" style="max-width: 980px;">
    
    <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 18px; border-radius: 20px; background: rgba(212,175,55,0.12); border: 1px solid var(--border-gold); margin-bottom: 20px;">
      <span style="font-size: 11px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 2.5px; font-weight: 800;">
        Presidi Territoriali della Conoscenza
      </span>
    </div>

    <h1 style="font-size: clamp(28px, 4.5vw, 50px); line-height: 1.15; margin-bottom: 20px; font-family: 'Cinzel', serif;">
      LA RETE NAZIONALE<br>
      <span class="gold-text">DELLE CITTÀ CAMPUS</span>
    </h1>

    <p style="font-size: 16px; color: var(--text-muted); max-width: 820px; margin: 0 auto 30px auto; line-height: 1.65;">
      Dalla pianura del Delta del Po alle città metropolitane: l'infrastruttura della formazione continua cresce collegando spazi civici e comunità locali in una federazione accademica aperta.
    </p>

    <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
      <a href="/campus-city/apply.php" class="btn-gold" style="font-size: 14px; padding: 14px 30px; font-weight: 700;">
        <?= icon_gold('institution', 16) ?> CANDIDA IL TUO COMUNE
      </a>
      <a href="/campus-city/index.php" class="btn-outline-gold" style="font-size: 14px; padding: 14px 26px;">
        ← Modello CAMPUS City
      </a>
    </div>

  </div>
</section>

<!-- SEZIONE POLI ATTIVI -->
<section class="section" style="padding-top: 30px; padding-bottom: 60px;">
  <div class="container" style="max-width: 1200px;">

    <div style="text-align: center; max-width: 800px; margin: 0 auto 40px auto;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Poli Operativi Ufficiali
      </span>
      <h2 style="font-size: clamp(22px, 3.2vw, 34px); color: #ffffff; margin-top: 6px; font-family: 'Cinzel', serif;">
        I NODI ACCREDITATI ED ATTIVI
      </h2>
      <p style="color: var(--text-muted); font-size: 14px;">
        Nodi territoriali convenzionati dove sono attive aule didattiche, laboratori di ricerca e presidi d'ateneo.
      </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 24px;">
      <?php foreach ($activeNodalCities as $node): ?>
        <div class="glass-card" style="padding: 32px; border: 2px solid var(--border-gold); box-shadow: var(--gold-glow);">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
            <span style="font-size: 11px; color: #10b981; font-weight: bold; background: rgba(16,185,129,0.1); border: 1px solid #10b981; padding: 3px 8px; border-radius: 10px; text-transform: uppercase;">
              ● POLO ATTIVO
            </span>
            <span style="font-family: monospace; font-size: 11px; color: var(--gold-light);">
              <?= sanitize_output($node['sic_id']) ?>
            </span>
          </div>

          <h3 style="color: #ffffff; font-size: 22px; font-family: 'Cinzel', serif; margin-bottom: 6px;">
            <?= sanitize_output($node['entity_name']) ?>
          </h3>
          <div style="font-size: 13px; color: var(--gold-primary); margin-bottom: 14px; font-weight: 600;">
            <?= sanitize_output($node['facility_name'] ?: 'Polo Territoriale Accademico') ?>
          </div>

          <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6; margin-bottom: 16px;">
            <?= sanitize_output($node['notes']) ?>
          </p>

          <div style="background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); padding: 14px; border-radius: var(--radius-sm); font-size: 12px; color: #cbd5e1; display: flex; flex-direction: column; gap: 6px; margin-bottom: 18px;">
            <div>📍 <strong>Sede:</strong> <?= sanitize_output($node['facility_address'] ?: 'Via Mantovana 78, Porto Viro (RO)') ?></div>
            <div>🏛️ <strong>Superficie:</strong> <?= $node['square_meters'] ?: 450 ?> mq operativi</div>
            <div>👥 <strong>Bacino Distretto:</strong> <?= number_format($node['estimated_catchment'] ?: 85000, 0, ',', '.') ?> abitanti</div>
          </div>

          <a href="/porto-viro.php" class="btn-gold" style="width: 100%; text-align: center; display: block; font-size: 13px; font-weight: 700; box-sizing: border-box; padding: 10px;">
            SCHEDA POLO & ATTIVITÀ →
          </a>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- COMUNI IN ISTRUTTORIA -->
    <div style="margin-top: 60px;">
      <div style="text-align: center; margin-bottom: 25px;">
        <span style="font-size: 11px; color: var(--text-dim); text-transform: uppercase; letter-spacing: 2px; font-weight: 700;">
          Trasparenza Amministrativa
        </span>
        <h3 style="color: #ffffff; font-size: 20px; font-family: 'Cinzel', serif; margin-top: 4px;">
          Candidature in Fase di Istruttoria Preliminare
        </h3>
        <p style="color: var(--text-dim); font-size: 13px;">
          Le amministrazioni comunali che hanno depositato la manifestazione di interesse per la verifica di prefattibilità.
        </p>
      </div>

      <?php if (empty($candidateCities)): ?>
        <div style="text-align: center; padding: 30px; background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); font-size: 13px; color: var(--text-dim);">
          Nuove candidature in fase di acquisizione telematica.<br>
          <a href="/campus-city/apply.php" style="color: var(--gold-light); text-decoration: underline; margin-top: 6px; display: inline-block;">
            Candidare il proprio Comune nella prima sessione 2026
          </a>
        </div>
      <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 14px;">
          <?php foreach ($candidateCities as $cand): ?>
            <div class="glass-card" style="padding: 18px;">
              <div style="font-size: 11px; color: #f59e0b; text-transform: uppercase; font-weight: bold; margin-bottom: 4px;">
                ● In Istruttoria (Readiness: <?= $cand['readiness_score'] ?>/100)
              </div>
              <strong style="color: #ffffff; font-size: 15px;"><?= sanitize_output($cand['entity_name']) ?></strong>
              <div style="font-size: 12px; color: var(--text-dim); margin-top: 4px;">
                Ente: <?= sanitize_output($cand['entity_type']) ?> · Abitanti: <?= number_format((int)$cand['population'], 0, ',', '.') ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
