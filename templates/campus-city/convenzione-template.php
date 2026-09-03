<?php
/**
 * CAMPUS.CAMP — Schema di Convenzione Quadro Istituzionale di Cooperazione (DRAFT)
 * Modello standard ad uso di Sindaci, Segretari Comunali e Dirigenti PA
 * Marcatura: DRAFT — LEGAL/ADMINISTRATIVE REVIEW REQUIRED
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/icons.php';

$pageTitle = 'Schema di Convenzione Quadro di Cooperazione Istituzionale (Bozza) — CAMPUS for Cities';
$pageDesc = 'Template di convenzione per Comuni ed Enti Pubblici per l insediamento e la valorizzazione degli spazi civici. Documento non vincolante pronto per gli uffici legali.';

require_once __DIR__ . '/../includes/header.php';
?>

<section class="section" style="padding-top: 50px; padding-bottom: 80px;">
  <div class="container" style="max-width: 960px;">

    <!-- BARRA AZIONI SUPERIORE -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
      <div>
        <a href="/campus-city/" style="color: var(--gold-light); font-size: 13px; text-decoration: none;">← Torna a CAMPUS for Cities</a>
        <h1 style="font-size: clamp(20px, 3vw, 30px); color: #ffffff; margin-top: 6px; font-family: 'Cinzel', serif;">
          Schema di Convenzione Quadro <span class="gold-text">di Cooperazione</span>
        </h1>
      </div>
      <div style="display: flex; gap: 10px;">
        <button onclick="window.print()" class="btn-gold" style="font-size: 13px; padding: 9px 20px; font-weight: 700;">
          <?= icon_gold('print', 14) ?> Stampa / Salva in PDF
        </button>
      </div>
    </div>

    <!-- AVVISO BOZZA LEGALE -->
    <div style="background: rgba(212, 175, 55, 0.1); border: 2px solid var(--gold-primary); padding: 16px 20px; border-radius: var(--radius-sm); margin-bottom: 30px; font-size: 13px; color: #ffffff;">
      <strong style="color: var(--gold-light); text-transform: uppercase;">DRAFT — LEGAL & ADMINISTRATIVE REVIEW REQUIRED:</strong><br>
      Il presente schema di convenzione costituisce una base di lavoro standard e non vincolante predisposta per agevolare l'istruttoria della Giunta Comunale e del Segretario Generale dell'Ente. Ciascun articolo può essere adattato in base ai regolamenti per l'uso del patrimonio e dei beni civici dell'amministrazione.
    </div>

    <!-- DOCUMENTO SCHEMA CONVENZIONE -->
    <div class="glass-card" style="padding: 50px 40px; border: 1px solid var(--border-gold); background: #07090e; font-size: 13.5px; line-height: 1.7; color: #cbd5e1;">
      
      <div style="text-align: center; margin-bottom: 35px; border-bottom: 2px solid var(--gold-primary); padding-bottom: 25px;">
        <img src="/assets/visual/Campus_Emblema_Transparent.png" alt="CAMPUS" style="width: 70px; margin-bottom: 12px;">
        <div style="font-size: 18px; font-family: 'Cinzel', serif; font-weight: 900; color: #ffffff;">
          CONVENZIONE QUADRO DI COOPERAZIONE ISTITUZIONALE
        </div>
        <div style="font-size: 13px; color: var(--gold-light); font-weight: 700; margin-top: 4px;">
          PER LA PROMOZIONE DELL'ALTA FORMAZIONE CONTINUA, DEL TALENTO E DELL'INNOVAZIONE
        </div>
        <div style="font-size: 11px; color: var(--text-dim); margin-top: 4px; font-family: monospace;">
          PROGRAMMA NAZIONALE: CAMPUS FOR CITIES · ATTO DI ADESIONE
        </div>
      </div>

      <h3 style="color: #ffffff; font-size: 16px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 6px;">TRA LE PARTI:</h3>
      <p>
        <strong>1. Il Comune di [NOME DEL COMUNE]</strong> (di seguito denominato "Ente"), con sede in [Indirizzo], C.F. [Codice Fiscale], in persona del Sindaco pro tempore [Nome Cognome] o del Dirigente autorizzato ai sensi dell'art. 107 del D.Lgs. 267/2000;<br>
        <strong>E</strong><br>
        <strong>2. <?= LEGAL_ENTITY_NAME ?></strong>, ente gestore dell'ateneo e della piattaforma di formazione <strong>CAMPUS</strong> (di seguito denominata "CAMPUS"), C.F./P.IVA [Dati Istituzionali], in persona del Legale Rappresentante pro tempore.
      </p>

      <h3 style="color: #ffffff; font-size: 16px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 6px; margin-top: 25px;">PREMESSO CHE:</h3>
      <p>
        a) L'Ente intende valorizzare il proprio patrimonio immobiliare destinato a scopi civici, culturali e di orientamento giovanile, stimolando opportunità di crescita professionale sul territorio;<br>
        b) CAMPUS costituisce una rete accademica e scientifica dotata di oltre 2.000 moduli didattici e di un corpo docente qualificato (Faculty) orientato al raccordo tra studio, professioni e distretti d'impresa;<br>
        c) Le Parti condividono l'opportunità di costituire un nodo della rete denominato <strong>"CAMPUS [NOME COMUNE]"</strong>, a beneficio dei cittadini residenti, dei giovani diplomati e dei professionisti locali.
      </p>

      <h3 style="color: #ffffff; font-size: 16px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 6px; margin-top: 25px;">SI CONVIENE E SI STIPULA QUANTO SEGUE:</h3>

      <p><strong>Articolo 1 — Oggetto e Finalità</strong><br>
      La presente Convenzione disciplina la cooperazione istituzionale tra l'Ente e CAMPUS per l'attivazione di percorsi formativi, master executive, workshop professionalizzanti e convegni con crediti formativi (CFP) presso le strutture comunali individuate all'art. 2.</p>

      <p><strong>Articolo 2 — Spazi Pubblici Assegnati</strong><br>
      L'Ente concede l'uso non esclusivo dei seguenti locali di proprietà comunale: <em>[Denominazione Struttura, es. Sala Civica / Biblioteca Comunale sita in Via ...]</em>, dotata di idoneità all'uso pubblico e agibilità. Gli orari e i giorni di svolgimento delle lezioni saranno concordati semestralmente tra le Parti in armonia con le altre esigenze civiche.</p>

      <p><strong>Articolo 3 — Oneri, Utenze e Assenza di Costi Strutturali</strong><br>
      L'adesione non comporta alcun onere fisso o investimento a carico del bilancio comunale. L'Ente assicura la normale erogazione delle utenze di base (energia elettrica, riscaldamento/climatizzazione, connettività Internet ove presente). CAMPUS si fa carico delle dotazioni didattiche supplementari e dell'assicurazione degli allievi.</p>

      <p><strong>Articolo 4 — Programmazione Didattica e Terzietà Accademica</strong><br>
      I programmi formativi e l'abilitazione della Faculty sono a cura esclusiva degli organi accademici di CAMPUS nel rispetto delle normative vigenti e dei regolamenti deontologici. L'Ente mantiene facoltà di proporre moduli su specifiche vocazioni economiche o ambientali del distretto.</p>

      <p><strong>Articolo 5 — Attività Gratuite per la Cittadinanza e Riduzioni</strong><br>
      CAMPUS si impegna ad erogare con cadenza periodica seminari aperti e gratuiti per la cittadinanza, nonché condizioni di agevolazione per i dipendenti dell'amministrazione comunale e borse di studio a supporto dei discenti meritevoli con ISEE certificato.</p>

      <p><strong>Articolo 6 — Durata, Recesso e Verifica dei KPI</strong><br>
      La Convenzione ha durata di anni 3 (tre) a decorrere dalla data di sottoscrizione. Ciascuna parte può recedere motivatamente in qualunque momento con preavviso scritto di giorni 90 (novanta), senza applicazione di indennità o penali, garantendo la conclusione dei corsi già avviati.</p>

      <p><strong>Articolo 7 — Tutela dei Dati e Trasparenza</strong><br>
      Il trattamento dei dati personali connessi alla gestione delle attività formative avviene in piena conformità al Regolamento UE 2016/679 (GDPR).</p>

      <div style="margin-top: 40px; display: flex; justify-content: space-between; border-top: 1px solid var(--border-subtle); padding-top: 30px;">
        <div style="text-align: center; width: 40%;">
          Per il Comune di [Nome Comune]<br>
          <em>Il Sindaco / Il Dirigente Incaricato</em><br><br>
          __________________________________
        </div>
        <div style="text-align: center; width: 40%;">
          Per <?= LEGAL_ENTITY_NAME ?> — CAMPUS<br>
          <em>La Direzione Istituzionale</em><br><br>
          __________________________________
        </div>
      </div>

    </div>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
