<?php
/**
 * CAMPUS.CAMP — Master Home Page
 * Viaggio di Conversione Istituzionale:
 * Visione → Possibilità → Personas → Percorsi → Professioni → Imprese → Territorio → Prova → Appartenenza → Azione
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/icons.php';

$pageTitle = 'CAMPUS — La Conoscenza Che Trasforma · Discere · Docere · Crescere';
$pageDesc = 'Istituzione Accademica di Alta Formazione. Il luogo dove professionisti, docenti, ordini e imprese convergono per imparare, insegnare e crescere.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- =========================================================================
     1. CINEMATIC HERO: IDENTITÀ & PROMESSA IMMEDIATA (Above The Fold)
     ========================================================================= -->
<section class="hero" style="position: relative; min-height: 92vh; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 60px 20px;">
  <!-- Video background -->
  <video class="hero-video-bg" autoplay muted loop playsinline style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 1; opacity: 0.28;">
    <source src="/assets/video/Professionals_walking_in_modern_._202609031317.mp4" type="video/mp4">
  </video>
  <div class="hero-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at center, rgba(7, 9, 14, 0.65) 0%, rgba(7, 9, 14, 0.96) 80%); z-index: 2;"></div>

  <div class="hero-content" style="position: relative; z-index: 3; text-align: center; max-width: 960px; margin: 0 auto;">
    
    <img src="/assets/visual/Campus_Emblema_Transparent.png" alt="CAMPUS Stemma Ufficiale" class="hero-crest" style="width: 110px; height: 110px; margin-bottom: 24px; filter: drop-shadow(0 0 25px rgba(212,175,55,0.45));">
    
    <div style="font-size: 12px; color: var(--gold-light); letter-spacing: 3.5px; margin-bottom: 12px; text-transform: uppercase; font-weight: 700; display: inline-flex; align-items: center; gap: 8px;">
      <?= icon_gold('academic', 15) ?> ISTITUZIONE ACCADEMICA SUPERIORE
    </div>

    <h1 style="font-size: clamp(34px, 5.5vw, 62px); line-height: 1.1; margin: 0 0 16px 0; font-family: 'Cinzel', serif; font-weight: 700; color: #ffffff; letter-spacing: 1px;">
      LA CONOSCENZA<br><span class="gold-text">CHE TRASFORMA</span>.
    </h1>

    <div style="font-size: 13.5px; color: var(--gold-primary); letter-spacing: 4px; margin-bottom: 20px; text-transform: uppercase; font-weight: 600;">
      DISCERE · DOCERE · CRESCERE
    </div>

    <!-- Subheadline orientata al risultato concreto e significato personale -->
    <p style="color: var(--text-muted); font-size: clamp(16px, 2.2vw, 19px); line-height: 1.6; max-width: 760px; margin: 0 auto 35px auto;">
      Un'architettura accademica viva dove la sapienza pratica dei migliori professionisti incontra la cattedra universitaria. Qui puoi <strong style="color: #ffffff;">imparare</strong> da chi opera sul campo, <strong style="color: #ffffff;">insegnare</strong> ciò che pratichi e <strong style="color: #ffffff;">crescere</strong> all'interno di una comunità che condivide la tua stessa ambizione.
    </p>

    <!-- CTA Architecture Primaria & Secondaria -->
    <div style="display: flex; gap: 16px; justify-content: center; align-items: center; flex-wrap: wrap;">
      <a href="/faculty.php" class="btn-gold" data-analytics="hero_cta_primary" style="font-size: 15px; padding: 16px 36px; display: inline-flex; align-items: center; gap: 10px; font-weight: 700;">
        <?= icon_gold('institution', 18) ?> ENTRA IN CAMPUS
      </a>
      <a href="/courses.php" class="btn-outline-gold" data-analytics="hero_cta_courses" style="font-size: 15px; padding: 16px 36px; display: inline-flex; align-items: center; gap: 8px;">
        <?= icon_gold('search', 16) ?> SCOPRI I PERCORSI (2.119)
      </a>
    </div>

    <!-- Microcopy di rassicurazione e fiducia -->
    <div style="margin-top: 24px; font-size: 12.5px; color: var(--text-dim); display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
      <span style="display: inline-flex; align-items: center; gap: 6px;">
        <?= icon_gold('check', 13) ?> Protocollo Digitale Immutabile SIC-ID
      </span>
      <span style="display: inline-flex; align-items: center; gap: 6px;">
        <?= icon_gold('check', 13) ?> Crediti CFP ai sensi dei Regolamenti Nazionali
      </span>
      <span style="display: inline-flex; align-items: center; gap: 6px;">
        <?= icon_gold('check', 13) ?> Nessun costo occulto o rinnovo automatico
      </span>
    </div>

  </div>
</section>

<!-- =========================================================================
     2. PORTE D'INGRESSO MULTI-PERSONA (Choice Architecture: "Trova il Tuo Ruolo")
     ========================================================================= -->
<section class="section" style="padding-top: 50px; padding-bottom: 60px; background: rgba(10, 14, 23, 0.6); border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);">
  <div class="container">
    
    <div style="text-align: center; max-width: 800px; margin: 0 auto 40px auto;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2.5px; font-weight: 800;">
        Una Casa Accademica, Molteplici Destini
      </span>
      <h2 style="font-size: clamp(26px, 3.5vw, 38px); color: #ffffff; margin-top: 8px;">
        Qual è il Tuo Posto in <span class="gold-text">CAMPUS</span>?
      </h2>
      <p style="color: var(--text-muted); font-size: 15px; margin-top: 10px;">
        Non un'offerta generalista per tutti, ma un percorso rigoroso disegnato sull'identità e sul valore che porti.
      </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
      
      <!-- PERSONA 1: PROFESSIONISTA & DOCENTE -->
      <div class="glass-card" style="padding: 28px 24px; display: flex; flex-direction: column; justify-content: space-between; border-top: 3px solid var(--gold-primary);">
        <div>
          <div style="margin-bottom: 14px;"><?= icon_gold('academic', 32) ?></div>
          <span style="font-size: 10.5px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700;">Per Te che Pratichi</span>
          <h3 style="color: #ffffff; font-size: 18px; margin: 6px 0 10px 0;">Professionista & Futuro Docente</h3>
          <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
            Hai accumulato anni di perizie, cantieri, bilanci o contenziosi. CAMPUS trasforma la tua esperienza pratica in cattedra retribuita, pubblicazioni e prestigio istituzionale.
          </p>
        </div>
        <div style="margin-top: 20px; border-top: 1px solid rgba(212,175,55,0.15); padding-top: 14px;">
          <a href="/apply.php" class="btn-gold" style="font-size: 12px; padding: 10px 18px; width: 100%; text-align: center; display: inline-flex; justify-content: center; align-items: center; gap: 6px;">
            <?= icon_gold('document', 14) ?> Candidati alla Faculty
          </a>
        </div>
      </div>

      <!-- PERSONA 2: LEARNER / PROFESSIONISTA IN AGGIORNAMENTO -->
      <div class="glass-card" style="padding: 28px 24px; display: flex; flex-direction: column; justify-content: space-between; border-top: 3px solid var(--gold-light);">
        <div>
          <div style="margin-bottom: 14px;"><?= icon_gold('search', 32) ?></div>
          <span style="font-size: 10.5px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700;">Per Te che Cresci</span>
          <h3 style="color: #ffffff; font-size: 18px; margin: 6px 0 10px 0;">Professionista & Discente</h3>
          <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
            Non cercare crediti formativi formali privi di valore. Iscriviti a corsi e master che aggiornano la tua operatività reale, con attestati SIC-ID riconosciuti e crediti CFP ufficiali.
          </p>
        </div>
        <div style="margin-top: 20px; border-top: 1px solid rgba(212,175,55,0.15); padding-top: 14px;">
          <a href="/courses.php" class="btn-outline-gold" style="font-size: 12px; padding: 10px 18px; width: 100%; text-align: center; display: inline-flex; justify-content: center; align-items: center; gap: 6px;">
            <?= icon_gold('search', 14) ?> Esplora il Catalogo Corsi
          </a>
        </div>
      </div>

      <!-- PERSONA 3: IMPRESA & CORPORATE ACADEMY -->
      <div class="glass-card" style="padding: 28px 24px; display: flex; flex-direction: column; justify-content: space-between; border-top: 3px solid var(--gold-metallic);">
        <div>
          <div style="margin-bottom: 14px;"><?= icon_gold('briefcase', 32) ?></div>
          <span style="font-size: 10.5px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700;">Per la Tua Azienda</span>
          <h3 style="color: #ffffff; font-size: 18px; margin: 6px 0 10px 0;">Impresa & Corporate Academy</h3>
          <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
            Forma i tuoi quadri e dipendenti con piani su misura conformi a D.Lgs. 81/08, ESG e transizione 5.0, azzerando i costi tramite fondi paritetici interprofessionali (Fondimpresa, Fon.Ter, Fondirigenti).
          </p>
        </div>
        <div style="margin-top: 20px; border-top: 1px solid rgba(212,175,55,0.15); padding-top: 14px;">
          <a href="/partners.php" class="btn-outline-gold" style="font-size: 12px; padding: 10px 18px; width: 100%; text-align: center; display: inline-flex; justify-content: center; align-items: center; gap: 6px;">
            <?= icon_gold('briefcase', 14) ?> Piani Aziendali & PA
          </a>
        </div>
      </div>

      <!-- PERSONA 4: ORDINE, COLLEGIO & ENTE LOCALE -->
      <div class="glass-card" style="padding: 28px 24px; display: flex; flex-direction: column; justify-content: space-between; border-top: 3px solid var(--gold-primary);">
        <div>
          <div style="margin-bottom: 14px;"><?= icon_gold('shield', 32) ?></div>
          <span style="font-size: 10.5px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700;">Per gli Organismi</span>
          <h3 style="color: #ffffff; font-size: 18px; margin: 6px 0 10px 0;">Ordini, Collegi & Enti PA</h3>
          <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
            Stipula una Convenzione Quadro bilaterale per valorizzare gli iscritti al tuo albo, erogare crediti formativi certificati e co-organizzare convegni di rilievo scientifico nazionale.
          </p>
        </div>
        <div style="margin-top: 20px; border-top: 1px solid rgba(212,175,55,0.15); padding-top: 14px;">
          <a href="/organizations.php" class="btn-outline-gold" style="font-size: 12px; padding: 10px 18px; width: 100%; text-align: center; display: inline-flex; justify-content: center; align-items: center; gap: 6px;">
            <?= icon_gold('institution', 14) ?> Albo Convenzioni
          </a>
        </div>
      </div>

    </div>

  </div>
</section>

<!-- =========================================================================
     3. IL PERCORSO DIDATTICO: LE 3 SCUOLE DI ALTA FORMAZIONE
     ========================================================================= -->
<section class="section" style="padding-top: 60px; padding-bottom: 70px;">
  <div class="container">
    
    <div style="text-align: center; max-width: 840px; margin: 0 auto 45px auto;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2.5px; font-weight: 800;">
        Architettura Didattica
      </span>
      <h2 style="font-size: clamp(26px, 3.8vw, 40px); color: #ffffff; margin-top: 8px;">
        Tre Scuole Integrate, Unico Standard Accademico
      </h2>
      <p style="color: var(--text-muted); font-size: 15.5px; margin-top: 10px; line-height: 1.6;">
        Un ecosistema di 2.119 percorsi verificati, divisi per livello di complessità, abilitazione ed expertise professionale.
      </p>
    </div>

    <div class="grid-3" style="margin-bottom: 40px;">
      
      <div class="glass-card" style="padding: 32px 26px; border: 1px solid var(--border-gold);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
          <span class="badge" style="background: rgba(212,175,55,0.15); color: var(--gold-primary); border: 1px solid var(--gold-primary); font-size: 11px;">
            TECNICA & CANTIERE
          </span>
          <span style="font-family: monospace; color: var(--gold-light); font-size: 14px; font-weight: 700;">816 Corsi</span>
        </div>
        <h3 style="color: #ffffff; font-size: 20px; margin-bottom: 10px; font-family: 'Cinzel', serif;">CAMPUS SCHOOL</h3>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6; margin-bottom: 20px;">
          La Scuola dei Mestieri e delle abilitazioni tecniche per geometri, periti, impiantisti e coordinatori di sicurezza. Pratica viva, rilievi, cantieristica navale e adempimenti D.Lgs. 81/08.
        </p>
        <a href="/courses.php?school=CAMPUS+SCHOOL" class="btn-outline-gold" style="width: 100%; text-align: center; font-size: 12px; padding: 10px;">
          Accedi a CAMPUS SCHOOL →
        </a>
      </div>

      <div class="glass-card" style="padding: 32px 26px; border: 1px solid var(--border-gold);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
          <span class="badge" style="background: rgba(212,175,55,0.15); color: var(--gold-primary); border: 1px solid var(--gold-primary); font-size: 11px;">
            POST-LAUREA & ALBI
          </span>
          <span style="font-family: monospace; color: var(--gold-light); font-size: 14px; font-weight: 700;">1.200 Corsi</span>
        </div>
        <h3 style="color: #ffffff; font-size: 20px; margin-bottom: 10px; font-family: 'Cinzel', serif;">CAMPUS MASTER</h3>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6; margin-bottom: 20px;">
          Master specialistici ed executive training per ingegneri, architetti, commercialisti, avvocati e consulenti del lavoro. Ingegneria idraulica, conformità 231, audit societari e diritto del lavoro.
        </p>
        <a href="/courses.php?school=CAMPUS+MASTER" class="btn-outline-gold" style="width: 100%; text-align: center; font-size: 12px; padding: 10px;">
          Accedi a CAMPUS MASTER →
        </a>
      </div>

      <div class="glass-card" style="padding: 32px 26px; border: 1px solid var(--border-gold);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
          <span class="badge" style="background: rgba(212,175,55,0.15); color: var(--gold-primary); border: 1px solid var(--gold-primary); font-size: 11px;">
            ÉLITE & FRONTIERA
          </span>
          <span style="font-family: monospace; color: var(--gold-light); font-size: 14px; font-weight: 700;">103 Corsi</span>
        </div>
        <h3 style="color: #ffffff; font-size: 20px; margin-bottom: 10px; font-family: 'Cinzel', serif;">CAMPUS ACADEMY</h3>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6; margin-bottom: 20px;">
          Percorsi d'avanguardia per l'industria 5.0: intelligenza artificiale generativa per gli ordini protetti, cybersecurity difensiva, bio-economia del Delta, finanza agevolata e sostenibilità ESG.
        </p>
        <a href="/courses.php?school=CAMPUS+ACADEMY" class="btn-outline-gold" style="width: 100%; text-align: center; font-size: 12px; padding: 10px;">
          Accedi a CAMPUS ACADEMY →
        </a>
      </div>

    </div>

  </div>
</section>

<!-- =========================================================================
     4. IL POLO TERRITORIALE: PORTO VIRO & IL DELTA DEL PO
     ========================================================================= -->
<section class="section" style="background: rgba(10, 14, 23, 0.4); border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle); padding: 70px 0;">
  <div class="container">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 40px; align-items: center;">
      <div>
        <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
          Radicamento Fisico & Bellezza Ambientale
        </span>
        <h2 style="font-size: clamp(26px, 3.8vw, 38px); color: #ffffff; margin: 10px 0 16px 0;">
          Il Polo Sperimentale di <span class="gold-text">Porto Viro</span>
        </h2>
        <p style="color: var(--text-muted); font-size: 15px; line-height: 1.7; margin-bottom: 16px;">
          CAMPUS non è un'entità astratta nel cloud. Ha radici salde in uno dei territori più complessi e affascinanti d'Europa: il <strong>Parco Regionale Veneto del Delta del Po</strong>, Riserva della Biosfera MAB UNESCO.
        </p>
        <p style="color: var(--text-muted); font-size: 15px; line-height: 1.7; margin-bottom: 24px;">
          Qui risiede il nostro laboratorio a cielo aperto: dall'idraulica delle bonifiche e il contrasto al cuneo salino, fino alla cantieristica navale d'eccellenza, alle summer school residenziali e ai workshop tecnici in presenza.
        </p>
        <div style="display: flex; gap: 14px; flex-wrap: wrap;">
          <a href="/porto-viro.php" class="btn-gold" style="font-size: 13px; padding: 12px 24px;">
            <?= icon_gold('institution', 15) ?> Scopri Campus Porto Viro
          </a>
          <a href="/research.php" class="btn-outline-gold" style="font-size: 13px; padding: 12px 24px;">
            <?= icon_gold('academic', 15) ?> I Centri di Ricerca
          </a>
        </div>
      </div>

      <div class="glass-card" style="border: 2px solid var(--border-gold); padding: 35px; text-align: center;">
        <img src="/assets/visual/Campus%20Camp%20Logo%20Porto%20Viro.png" alt="Polo Porto Viro Delta Po" style="max-width: 220px; margin-bottom: 18px; filter: drop-shadow(0 0 20px rgba(212,175,55,0.35));">
        <h3 style="color: #ffffff; font-size: 20px; margin-bottom: 8px;">Polo Idraulico & Ambientale</h3>
        <p style="color: var(--text-dim); font-size: 13px; line-height: 1.6; margin-bottom: 20px;">
          Via Mantovana, 78 — Porto Viro (RO)<br>
          Aule didattiche, laboratori di rilievo tecnico e punto di incontro con gli Ordini territoriali del Veneto e dell'Emilia-Romagna.
        </p>
        <span class="badge" style="background: rgba(212,175,55,0.15); color: var(--gold-primary); border: 1px solid var(--gold-primary); font-size: 11px;">
          Presidio Operativo Attivo
        </span>
      </div>
    </div>
  </div>
</section>

<!-- =========================================================================
     5. EVIDENCE & TRUST SIGNALS: PERCHÉ FIDARSI
     ========================================================================= -->
<section class="section" style="padding-top: 60px; padding-bottom: 70px;">
  <div class="container">
    
    <div style="text-align: center; max-width: 850px; margin: 0 auto 45px auto;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2.5px; font-weight: 800;">
        Garanzie Istituzionali
      </span>
      <h2 style="font-size: clamp(26px, 3.8vw, 38px); color: #ffffff; margin-top: 8px;">
        Perché Dovresti Fidarti di <span class="gold-text">CAMPUS</span>
      </h2>
      <p style="color: var(--text-muted); font-size: 15px; margin-top: 8px;">
        Trasparenza non dichiarata a parole, ma codificata in protocolli verificabili.
      </p>
    </div>

    <div class="grid-3" style="margin-bottom: 50px;">
      
      <div class="glass-card" style="padding: 26px;">
        <div style="margin-bottom: 12px;"><?= icon_gold('shield', 30) ?></div>
        <h4 style="color: #ffffff; font-size: 17px; margin-bottom: 8px;">Certificazione SIC-ID Immutabile</h4>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Ogni nomina, cattedra e attestato rilasciato reca un codice alfanumerico univoco depositato in database. Verificabile in tempo reale da chiunque con funzione di consultazione pubblica istantanea.
        </p>
      </div>

      <div class="glass-card" style="padding: 26px;">
        <div style="margin-bottom: 12px;"><?= icon_gold('users', 30) ?></div>
        <h4 style="color: #ffffff; font-size: 17px; margin-bottom: 8px;">110+ Enti Censiti & Verificati</h4>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Il nostro network dialoga con 40 Ordini Professionali, 7 Collegi, 22 Associazioni di Categoria, 14 Pubbliche Amministrazioni e 13 Grandi Imprese industriali e corporate academy.
        </p>
      </div>

      <div class="glass-card" style="padding: 26px;">
        <div style="margin-bottom: 12px;"><?= icon_gold('scale', 30) ?></div>
        <h4 style="color: #ffffff; font-size: 17px; margin-bottom: 8px;">Deontologia & Condizioni Chiare</h4>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Nessuna falsa promessa, nessun marketing aggressivo. Quota annuale formale (299,00 € annui IVA compresa se dovuta), contrattualistica trasparente e rispetto dei regolamenti nazionali degli ordini.
        </p>
      </div>

    </div>

  </div>
</section>

<!-- =========================================================================
     6. CALL TO ACTION FINALE (APERTURA E APPARTENENZA)
     ========================================================================= -->
<section class="section" style="padding: 90px 20px; background: radial-gradient(circle at center, rgba(212, 175, 55, 0.15) 0%, rgba(7, 9, 14, 0.98) 75%); text-align: center; border-top: 1px solid var(--border-gold);">
  <div class="container" style="max-width: 880px;">
    
    <img src="/assets/visual/Campus_Emblema_Transparent.png" alt="CAMPUS" style="width: 85px; margin-bottom: 20px; filter: drop-shadow(0 0 20px rgba(212,175,55,0.45));">
    
    <span style="font-size: 11.5px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 3px; font-weight: 800; display: block; margin-bottom: 10px;">
      Bando Aperto per la Faculty Fondatrice
    </span>

    <h2 style="font-size: clamp(30px, 4.5vw, 48px); margin-bottom: 18px; line-height: 1.2; font-family: 'Cinzel', serif; color: #ffffff;">
      Il Tuo Sapere Merita la Cattedra.
    </h2>

    <p style="color: var(--text-muted); font-size: 17px; margin-bottom: 35px; line-height: 1.6; max-width: 700px; margin-left: auto; margin-right: auto;">
      Se sei un professionista iscritto a un albo, un docente o un esperto tecnico di settore, porta la tua esperienza all'interno della <strong>Faculty Ufficiale CAMPUS</strong>.
    </p>

    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; margin-bottom: 20px;">
      <a href="/apply.php" class="btn-gold" style="font-size: 15px; padding: 16px 36px; display: inline-flex; align-items: center; gap: 10px; font-weight: 700;">
        <?= icon_gold('document', 18) ?> CANDIDATI ALLA FACULTY ORA
      </a>
      <a href="/manifesto-docenti-a4.html" target="_blank" class="btn-outline-gold" style="font-size: 15px; padding: 16px 36px; display: inline-flex; align-items: center; gap: 10px;">
        <?= icon_gold('print', 18) ?> Manifesto Ufficiale A4
      </a>
    </div>

    <div style="font-size: 12px; color: var(--text-dim);">
      Tempo medio compilazione: 5 minuti · Emissione istantanea del protocollo SIC-ID
    </div>

  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
