<?php
/**
 * CAMPUS.CAMP — Studio di Prefattibilità Economica & Cost/Benefit per Comuni
 * Configuratore Interattivo a 3 Scenari (MINIMAL, BASE, HUB) con distinzione rigorosa tra FACT, ASSUMPTION, SCENARIO e TARGET
 * Generatore di Dossier stampabile/scaricabile per la Giunta Comunale
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/icons.php';

$pageTitle = 'Studio di Prefattibilità per la Giunta Comunale — CAMPUS for Cities';
$pageDesc = 'Configuratore interattivo Cost/Benefit per amministrazioni comunali. Analisi di sostenibilità economica e generazione del dossier preliminare per il Sindaco e la Giunta.';

$db = Database::getConnection();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="hero" style="min-height: 55vh; padding: 90px 20px 50px 20px;">
  <div class="hero-overlay"></div>
  <div class="hero-content" style="max-width: 980px;">
    
    <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 18px; border-radius: 20px; background: rgba(212,175,55,0.12); border: 1px solid var(--border-gold); margin-bottom: 20px;">
      <span style="font-size: 11px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 2.5px; font-weight: 800;">
        Strumento di Analisi Economico-Finanziaria per la PA
      </span>
    </div>

    <h1 style="font-size: clamp(28px, 4.5vw, 50px); line-height: 1.15; margin-bottom: 20px; font-family: 'Cinzel', serif;">
      QUANTO POTREBBE COSTARE ATTIVARE CAMPUS<br>
      <span class="gold-text">NEL TUO COMUNE?</span>
    </h1>

    <p style="font-size: 16px; color: var(--text-muted); max-width: 820px; margin: 0 auto 28px auto; line-height: 1.65;">
      Simula i parametri del tuo territorio. Calcola i costi di attivazione, i risparmi derivanti dall'uso di spazi esistenti e le stime di impatto formativo a 3 scenari (Minimal, Base, Hub).
    </p>

    <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
      <a href="#calcolatore" class="btn-gold" style="font-size: 14px; padding: 14px 30px; font-weight: 700;">
        <?= icon_gold('document', 16) ?> CONFIGURA LO STUDIO
      </a>
      <a href="/campus-city/apply.php" class="btn-outline-gold" style="font-size: 14px; padding: 14px 26px;">
        Candidatura Ufficiale Ente
      </a>
    </div>

  </div>
</section>

<!-- CONFIGURATORE COST/BENEFIT -->
<section id="calcolatore" class="section" style="padding-top: 40px; padding-bottom: 80px;">
  <div class="container" style="max-width: 1200px;">

    <!-- RIGORE E METODOLOGIA: DISTINZIONE DEI TERMINI -->
    <div class="glass-card" style="padding: 20px; margin-bottom: 30px; border-left: 4px solid var(--gold-primary); background: rgba(0,0,0,0.5);">
      <div style="font-size: 11.5px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 1.5px; font-weight: 800; margin-bottom: 6px;">
        Principio di Trasparenza Finanziaria per Amministratori Pubblici:
      </div>
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; font-size: 12.5px; color: var(--text-muted);">
        <div><strong style="color: #60a5fa;">[FACT]</strong> Dati storici certi (superficie immobile, abitanti ISTAT, tariffa oraria utenze).</div>
        <div><strong style="color: #f59e0b;">[ASSUMPTION]</strong> Ipotesi tecniche di utilizzo (giorni di apertura, frequenza lezioni).</div>
        <div><strong style="color: #a855f7;">[SCENARIO]</strong> Simulazione comparativa (Minimal, Base, Hub) a parametri variabili.</div>
        <div><strong style="color: #10b981;">[TARGET]</strong> Obiettivo auspicato di discenti, CFP e partner territoriali coinvolti.</div>
      </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1.3fr; gap: 30px; align-items: start;">
      
      <!-- COLONNA INPUT COMUNE -->
      <div class="glass-card" style="padding: 30px; border: 1px solid var(--border-gold);">
        <h3 style="color: #ffffff; font-size: 18px; font-family: 'Cinzel', serif; margin-bottom: 18px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 10px;">
          Parametri dell'Amministrazione
        </h3>

        <div style="margin-bottom: 16px;">
          <label class="form-label">Nome del Comune / Ente</label>
          <input type="text" id="cfg-comune-name" value="Comune di Porto Viro" class="form-control" placeholder="es. Comune di Rovigo">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
          <div>
            <label class="form-label">Popolazione Residente</label>
            <input type="number" id="cfg-popolazione" value="14000" class="form-control" step="500">
          </div>
          <div>
            <label class="form-label">Bacino Distretto Stimato</label>
            <input type="number" id="cfg-bacino" value="65000" class="form-control" step="1000">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
          <div>
            <label class="form-label">Numero Strutture</label>
            <input type="number" id="cfg-strutture" value="1" min="1" max="5" class="form-control">
          </div>
          <div>
            <label class="form-label">Numero Sale / Aule</label>
            <input type="number" id="cfg-sale" value="2" min="1" max="10" class="form-control">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
          <div>
            <label class="form-label">Capienza Complessiva Posti</label>
            <input type="number" id="cfg-capienza" value="75" step="5" class="form-control">
          </div>
          <div>
            <label class="form-label">Giorni Apertura / Mese</label>
            <input type="number" id="cfg-giorni" value="12" min="2" max="26" class="form-control">
          </div>
        </div>

        <div style="margin-bottom: 20px;">
          <label class="form-label">Partner Territoriali Disponibili</label>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 12.5px; color: #ffffff;">
            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;"><input type="checkbox" id="cfg-scuole" checked style="accent-color: var(--gold-primary);"> Scuole Superiori / ITS</label>
            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;"><input type="checkbox" id="cfg-imprese" checked style="accent-color: var(--gold-primary);"> Rete Imprese / PMI</label>
            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;"><input type="checkbox" id="cfg-ordini" checked style="accent-color: var(--gold-primary);"> Ordini Professionali</label>
            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;"><input type="checkbox" id="cfg-associazioni" checked style="accent-color: var(--gold-primary);"> Terzo Settore Locale</label>
          </div>
        </div>

        <div style="border-top: 1px solid var(--border-subtle); padding-top: 16px;">
          <button type="button" id="btn-recalc" class="btn-gold" style="width: 100%; font-size: 13.5px; padding: 12px; font-weight: 700;">
            AGGIORNA SIMULAZIONE SCENARI
          </button>
        </div>

      </div>

      <!-- COLONNA RISULTATI SCENARI COMPARATI -->
      <div>
        
        <!-- SELETTORE SCENARIO -->
        <div style="display: flex; gap: 10px; margin-bottom: 16px;">
          <button type="button" class="scenario-btn active" data-scenario="minimal" style="flex: 1; padding: 10px; border-radius: var(--radius-sm); background: var(--gold-primary); color: #000; font-weight: 800; border: none; font-size: 12px; cursor: pointer;">
            1. SCENARIO MINIMAL
          </button>
          <button type="button" class="scenario-btn" data-scenario="base" style="flex: 1; padding: 10px; border-radius: var(--radius-sm); background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--border-subtle); font-size: 12px; cursor: pointer;">
            2. SCENARIO BASE
          </button>
          <button type="button" class="scenario-btn" data-scenario="hub" style="flex: 1; padding: 10px; border-radius: var(--radius-sm); background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--border-subtle); font-size: 12px; cursor: pointer;">
            3. SCENARIO HUB
          </button>
        </div>

        <!-- SCHEDA RISULTATI SCENARIO -->
        <div class="glass-card" style="padding: 30px; border: 2px solid var(--border-gold);">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 12px;">
            <div>
              <span id="sc-tag" style="font-size: 11px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 1.5px; font-weight: 800;">Presidio Civico Leggero</span>
              <h3 id="sc-title" style="color: #ffffff; font-size: 22px; margin: 4px 0 0 0; font-family: 'Cinzel', serif;">CAMPUS Point (1 Sala)</h3>
            </div>
            <div style="text-align: right;">
              <span style="font-size: 11px; color: var(--text-dim); text-transform: uppercase;">Costo Fisso Comune</span>
              <div style="font-size: 24px; font-weight: 900; color: #10b981;">0 € / anno</div>
            </div>
          </div>

          <!-- GRIGLIA INDICATORI ECONOMICI -->
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 20px;">
            
            <div style="background: rgba(0,0,0,0.4); padding: 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
              <div style="font-size: 11px; color: var(--text-dim); text-transform: uppercase;">Spazio & Struttura [FACT]</div>
              <div id="sc-spazio" style="font-size: 14px; color: #ffffff; font-weight: 700; margin-top: 4px;">Patrimonio già esistente (Zero Cemento)</div>
              <div style="font-size: 11px; color: #10b981; margin-top: 2px;">Risparmio stimato: oltre 350.000 €</div>
            </div>

            <div style="background: rgba(0,0,0,0.4); padding: 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
              <div style="font-size: 11px; color: var(--text-dim); text-transform: uppercase;">Utenze & Riscaldamento [ASSUMPTION]</div>
              <div id="sc-utenze" style="font-size: 14px; color: #ffffff; font-weight: 700; margin-top: 4px;">~80 - 150 € / mese (orari condivisi)</div>
              <div style="font-size: 11px; color: var(--text-dim); margin-top: 2px;">A valere sulle spese ordinarie dell'immobile</div>
            </div>

            <div style="background: rgba(0,0,0,0.4); padding: 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
              <div style="font-size: 11px; color: var(--text-dim); text-transform: uppercase;">Discenti & Corsisti / Anno [TARGET]</div>
              <div id="sc-corsisti" style="font-size: 18px; color: var(--gold-light); font-weight: 900; margin-top: 2px;">120 - 250 persone</div>
              <div style="font-size: 11px; color: var(--text-dim);">Tra giovani, professionisti e dipendenti</div>
            </div>

            <div style="background: rgba(0,0,0,0.4); padding: 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
              <div style="font-size: 11px; color: var(--text-dim); text-transform: uppercase;">Eventi & Convegni con CFP [TARGET]</div>
              <div id="sc-eventi" style="font-size: 18px; color: var(--gold-light); font-weight: 900; margin-top: 2px;">6 - 12 iniziative / anno</div>
              <div style="font-size: 11px; color: var(--text-dim);">Con il patrocinio degli Ordini del distretto</div>
            </div>

          </div>

          <!-- GENERAZIONE DOSSIER PER LA GIUNTA -->
          <div style="background: rgba(212,175,55,0.08); border: 1px solid var(--border-gold); padding: 20px; border-radius: var(--radius-sm); text-align: center;">
            <div style="font-size: 13px; color: #ffffff; font-weight: 700; margin-bottom: 6px;">
              Dossier di Prefattibilità Istituzionale
            </div>
            <p style="font-size: 12.5px; color: var(--text-muted); margin: 0 0 16px 0;">
              Genera il documento A4 ufficiale impaginato con delibera tipo, cronoprogramma, analisi SWOT e quadro economico da sottoporre a Giunta e Consiglio.
            </p>
            <button type="button" onclick="window.print()" class="btn-gold" style="font-size: 13px; padding: 12px 28px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px;">
              <?= icon_gold('print', 14) ?> SCARICA / STAMPA DOSSIER PER LA GIUNTA
            </button>
          </div>

        </div>

      </div>

    </div>

  </div>
</section>

<!-- SEZIONE STAMPABILE: DOSSIER A4 UFFICIALE PER LA GIUNTA (VISIBILE ANCHE A SCHERMO IN ANTEPRIMA) -->
<section class="section" style="background: rgba(0,0,0,0.85); border-top: 1px solid var(--border-subtle); padding: 50px 20px;">
  <div class="container" style="max-width: 900px; background: #07090e; border: 2px solid var(--border-gold); padding: 50px 40px; border-radius: var(--radius-sm);">
    
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--gold-primary); padding-bottom: 20px; margin-bottom: 25px;">
      <div style="display: flex; align-items: center; gap: 15px;">
        <img src="/assets/visual/Campus_Emblema_Transparent.png" alt="CAMPUS" style="width: 55px;">
        <div>
          <div style="font-size: 18px; font-family: 'Cinzel', serif; font-weight: 900; color: #ffffff; letter-spacing: 2px;">CAMPUS FOR CITIES</div>
          <div style="font-size: 10px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 1.5px;">Studio Preliminare di Prefattibilità Territoriale</div>
        </div>
      </div>
      <div style="text-align: right; font-size: 11px; color: var(--text-dim); font-family: monospace;">
        DOC: CAMPUS_PRE_FEASIBILITY_2026<br>
        STATO: PROPOSTA NON VINCOLANTE
      </div>
    </div>

    <div style="margin-bottom: 25px;">
      <h2 style="font-size: 22px; color: #ffffff; font-family: 'Cinzel', serif; margin-bottom: 10px;">
        Dossier Istituzionale per la Giunta Comunale di <span id="print-comune-name" class="gold-text">Porto Viro</span>
      </h2>
      <p style="font-size: 13.5px; color: var(--text-muted); line-height: 1.6;">
        Oggetto: Attivazione di un nodo territoriale di alta formazione continua e raccordo con il mondo delle imprese attraverso la valorizzazione degli immobili pubblici comunali a costo strutturale zero.
      </p>
    </div>

    <!-- SINTESI ESECUTIVA DOSSIER -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 25px; font-size: 13px;">
      <div style="background: rgba(255,255,255,0.03); padding: 16px; border: 1px solid var(--border-subtle); border-radius: var(--radius-sm);">
        <strong style="color: var(--gold-light); display: block; margin-bottom: 6px;">1. Obiettivo Amministrativo:</strong>
        Offrire alla cittadinanza, ai giovani diplomati e ai lavoratori un punto fisico di accesso a oltre 2.000 corsi certificati e convegni con crediti formativi (CFP) senza dover costruire nuovi edifici.
      </div>
      <div style="background: rgba(255,255,255,0.03); padding: 16px; border: 1px solid var(--border-subtle); border-radius: var(--radius-sm);">
        <strong style="color: var(--gold-light); display: block; margin-bottom: 6px;">2. Strumento Giuridico:</strong>
        Schema di Convenzione Quadro di cooperazione istituzionale (durata 24 o 36 mesi) con facoltà di recesso motivato senza penali e verifica annuale dei risultati.
      </div>
    </div>

    <!-- TABELLA SWOT AMMINISTRATIVA -->
    <div style="margin-bottom: 25px;">
      <div style="font-size: 12px; color: var(--gold-primary); text-transform: uppercase; font-weight: bold; margin-bottom: 8px;">
        Analisi di Rischio e Fattibilità (SWOT PA):
      </div>
      <table style="width: 100%; border-collapse: collapse; font-size: 12px; text-align: left; border: 1px solid var(--border-subtle);">
        <tr style="background: rgba(255,255,255,0.05); color: #ffffff;">
          <th style="padding: 8px 10px; border: 1px solid var(--border-subtle);">Punti di Forza (Strengths)</th>
          <th style="padding: 8px 10px; border: 1px solid var(--border-subtle);">Mitigazione Rischi (Risks)</th>
        </tr>
        <tr>
          <td style="padding: 10px; border: 1px solid var(--border-subtle); color: var(--text-muted);">
            • Valorizzazione immediata di spazi sottoutilizzati<br>
            • Nessun indebitamento o mutuo per l'Ente<br>
            • Autonomia didattica e scientifica garantita da CAMPUS
          </td>
          <td style="padding: 10px; border: 1px solid var(--border-subtle); color: var(--text-muted);">
            • Utenze coperte dalle quote di partecipazione<br>
            • Verifica semestrale del rispetto delle agibilità<br>
            • Zero vincoli proprietari sull'immobile
          </td>
        </tr>
      </table>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-subtle); padding-top: 20px;">
      <div style="font-size: 12px; color: var(--text-dim);">
        Preparato per l'Ufficio di Gabinetto del Sindaco e Segreteria Generale
      </div>
      <a href="/campus-city/apply.php" class="btn-gold" style="font-size: 13px; padding: 10px 24px;">
        AVVIA CANDIDATURA UFFICIALE ENTE
      </a>
    </div>

  </div>
</section>

<!-- CONTROLLER JS CONFIGURATORE -->
<script>
(function() {
  'use strict';

  var comuneInput = document.getElementById('cfg-comune-name');
  var popInput = document.getElementById('cfg-popolazione');
  var bacinoInput = document.getElementById('cfg-bacino');
  var btnRecalc = document.getElementById('btn-recalc');

  var scenarioBtns = document.querySelectorAll('.scenario-btn');
  var scTag = document.getElementById('sc-tag');
  var scTitle = document.getElementById('sc-title');
  var scCorsisti = document.getElementById('sc-corsisti');
  var scEventi = document.getElementById('sc-eventi');
  var printComune = document.getElementById('print-comune-name');

  var currentScenario = 'minimal';

  function updateDisplay() {
    var comune = comuneInput.value.trim() || 'Comune';
    var pop = parseInt(popInput.value, 10) || 10000;
    var bacino = parseInt(bacinoInput.value, 10) || (pop * 2.5);

    if (printComune) printComune.textContent = comune;

    if (currentScenario === 'minimal') {
      scTag.textContent = 'Presidio Civico Leggero';
      scTitle.textContent = 'CAMPUS Point (1 Sala)';
      var minC = Math.round(pop * 0.015);
      var maxC = Math.round(pop * 0.03);
      scCorsisti.textContent = minC + ' - ' + maxC + ' persone';
      scEventi.textContent = '6 - 10 iniziative / anno';
    } else if (currentScenario === 'base') {
      scTag.textContent = 'Polo Formativo di Distretto';
      scTitle.textContent = 'CAMPUS City Base (2-3 Aule)';
      var minC = Math.round(pop * 0.035);
      var maxC = Math.round(pop * 0.07);
      scCorsisti.textContent = minC + ' - ' + maxC + ' persone';
      scEventi.textContent = '12 - 20 iniziative / anno';
    } else if (currentScenario === 'hub') {
      scTag.textContent = 'Polo Territoriale Completo';
      scTitle.textContent = 'CAMPUS Hub (Polifunzionale + Lab)';
      var minC = Math.round(pop * 0.08);
      var maxC = Math.round(pop * 0.15);
      scCorsisti.textContent = minC + ' - ' + maxC + ' persone';
      scEventi.textContent = '24 - 40 iniziative / anno';
    }
  }

  if (btnRecalc) {
    btnRecalc.addEventListener('click', updateDisplay);
  }

  scenarioBtns.forEach(function(btn) {
    btn.addEventListener('click', function() {
      scenarioBtns.forEach(function(b) {
        b.style.background = 'rgba(255,255,255,0.05)';
        b.style.color = '#fff';
        b.style.border = '1px solid var(--border-subtle)';
        b.classList.remove('active');
      });

      this.style.background = 'var(--gold-primary)';
      this.style.color = '#000';
      this.style.border = 'none';
      this.classList.add('active');

      currentScenario = this.getAttribute('data-scenario');
      updateDisplay();
    });
  });

  updateDisplay();

})();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
