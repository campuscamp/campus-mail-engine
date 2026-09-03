<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/icons.php';

$pageTitle = 'Chi Siamo — Istituzione Accademica CAMPUS';
$pageDesc = 'La storia, la missione e i principi fondativi di CAMPUS. Istituzione accademica di alta formazione che adotta i massimi standard internazionali universitari.';

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top: 50px; padding-bottom: 80px;">
  <div class="container">
    
    <!-- HERO HEADER -->
    <div style="text-align: center; max-width: 960px; margin: 0 auto 55px auto;">
      <span style="font-size: 11.5px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2.8px; font-weight: 800; display: inline-flex; align-items: center; gap: 8px;">
        <?= icon_gold('institution', 16) ?> Identità Istituzionale & Standard Globali
      </span>
      <h1 style="font-size: clamp(32px, 4.8vw, 52px); margin: 14px 0 16px 0; line-height: 1.15; font-family: 'Cinzel', serif;">
        L'Architettura dell'<span class="gold-text">Alta Formazione</span><br>Fondata sull'Eccellenza Mondiale
      </h1>
      <div style="font-size: 13.5px; color: var(--gold-light); letter-spacing: 3.5px; margin-bottom: 24px; text-transform: uppercase; font-weight: 700;">
        DISCERE · DOCERE · CRESCERE
      </div>
      <p style="color: var(--text-muted); font-size: 17px; line-height: 1.7; max-width: 820px; margin: 0 auto;">
        <strong>CAMPUS</strong> è un'Istituzione Accademica Superiore fondata per colmare il divario tra la speculazione teorica tradizionale e la complessità operativa delle moderne professioni. Applichiamo i rigorosi canoni pedagogici, etici e scientifici delle più prestigiose accademie mondiali per formare la classe dirigente, tecnica e dirigenziale del Paese.
      </p>
    </div>

    <!-- MANIFESTO STATUARIO -->
    <div class="glass-card" style="border: 2px solid var(--border-gold); padding: 45px 35px; margin-bottom: 60px; box-shadow: var(--gold-glow);">
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 35px; align-items: center;">
        <div>
          <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
            Atto Fondativo & Visione
          </span>
          <h2 style="font-size: 28px; color: #ffffff; margin: 10px 0 16px 0;">
            La Sapienza Pratica Elevata a <span class="gold-text">Dignità di Cattedra</span>
          </h2>
          <p style="color: var(--text-muted); font-size: 15px; line-height: 1.7; margin-bottom: 16px;">
            Nel panorama contemporaneo, le professioni evolvono a un ritmo mai registrato prima. I percorsi formativi autoreferenziali non bastano più. CAMPUS istituzionalizza un patto d'onore: affidare l'insegnamento a chi la professione la esercita quotidianamente ai massimi livelli di responsabilità, affiancato da comitati scientifici indipendenti.
          </p>
          <p style="color: var(--text-muted); font-size: 15px; line-height: 1.7;">
            Ogni corso, seminario o master rilasciato da CAMPUS rispetta il <strong>Quadro Europeo delle Qualifiche (EQF)</strong> e i regolamenti nazionali per la Formazione Continua Obbligatoria (CFP), garantendo certezza legale e spendibilità immediata delle competenze acquisite.
          </p>
        </div>

        <div style="background: rgba(0,0,0,0.55); border: 1px solid var(--border-subtle); padding: 35px; border-radius: var(--radius-sm); text-align: center;">
          <img src="/assets/visual/Campus_Emblema_Transparent.png" alt="CAMPUS" style="width: 100px; margin-bottom: 20px; filter: drop-shadow(0 0 20px rgba(212,175,55,0.4));">
          <h4 style="color: #ffffff; font-size: 20px; margin-bottom: 10px; font-family: 'Cinzel', serif;">Autorità, Indipendenza, Rigore</h4>
          <p style="color: var(--text-dim); font-size: 13.5px; line-height: 1.6; margin-bottom: 24px;">
            Dalla sede accademica nel territorio del Delta del Po fino alle aule telematiche ad alta definizione, CAMPUS certifica ogni discente attraverso il protocollo crittografico <strong>SIC-ID</strong>.
          </p>
          <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="/courses.php" class="btn-gold" style="font-size: 12.5px; padding: 10px 22px;">
              Esplora i 2.119 Corsi
            </a>
            <a href="/faculty.php" class="btn-outline-gold" style="font-size: 12.5px; padding: 10px 22px;">
              Bando Docenti
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- GLI STANDARD ACCADEMICI GLOBALI -->
    <div style="text-align: center; max-width: 850px; margin: 0 auto 40px auto;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Benchmark Internazionali
      </span>
      <h2 style="font-size: clamp(26px, 3.8vw, 38px); color: #ffffff; margin-top: 8px;">
        Gli Standard Accademici delle Grandi Università Mondiali
      </h2>
      <p style="color: var(--text-muted); font-size: 15px; margin-top: 10px; line-height: 1.6;">
        CAMPUS mutua e sintetizza le metodologie d'eccellenza delle più celebri istituzioni mondiali, adattandole al quadro normativo ed economico italiano.
      </p>
    </div>

    <div class="grid-2" style="margin-bottom: 60px;">
      
      <div class="glass-card" style="border-left: 4px solid var(--gold-primary); padding: 30px;">
        <div style="display: flex; gap: 16px; align-items: flex-start;">
          <div style="margin-top: 4px;"><?= icon_gold('academic', 34) ?></div>
          <div>
            <span style="font-size: 11px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700;">Modello Oxford & Cambridge</span>
            <h3 style="color: #ffffff; font-size: 19px; margin: 4px 0 10px 0;">Collegiate & Tutorial System</h3>
            <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6;">
              Non una didattica di massa impersonale, ma cattedre strutturate in comunità di apprendimento. Il discente è affiancato da docenti tutor e membri della Faculty che guidano l'approfondimento critico, la discussione metodologica e l'acquisizione di un'autonomia di giudizio elevata.
            </p>
          </div>
        </div>
      </div>

      <div class="glass-card" style="border-left: 4px solid var(--gold-light); padding: 30px;">
        <div style="display: flex; gap: 16px; align-items: flex-start;">
          <div style="margin-top: 4px;"><?= icon_gold('briefcase', 34) ?></div>
          <div>
            <span style="font-size: 11px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700;">Modello Harvard & MIT</span>
            <h3 style="color: #ffffff; font-size: 19px; margin: 4px 0 10px 0;">Case-Method & Applied Science</h3>
            <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6;">
              L'apprendimento non si esaurisce nella memorizzazione di testi legislativi o nozioni astratte: ogni modulo didattico analizza perizie reali, contenziosi giurisprudenziali, progetti BIM, audit 231 e cantieri complessi, traducendo la norma in perizia operativa.
            </p>
          </div>
        </div>
      </div>

      <div class="glass-card" style="border-left: 4px solid var(--gold-metallic); padding: 30px;">
        <div style="display: flex; gap: 16px; align-items: flex-start;">
          <div style="margin-top: 4px;"><?= icon_gold('shield', 34) ?></div>
          <div>
            <span style="font-size: 11px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700;">Modello Alma Mater Bologna</span>
            <h3 style="color: #ffffff; font-size: 19px; margin: 4px 0 10px 0;">Universitas Magistrorum et Scholarium</h3>
            <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6;">
              Nel solco della più antica tradizione universitaria d'Occidente, CAMPUS celebra la libera corporazione dei docenti e dei discenti: una comunità orizzontale in cui l'aggiornamento deontologico è fondamento della libertà civile e dell'etica d'impresa.
            </p>
          </div>
        </div>
      </div>

      <div class="glass-card" style="border-left: 4px solid var(--gold-primary); padding: 30px;">
        <div style="display: flex; gap: 16px; align-items: flex-start;">
          <div style="margin-top: 4px;"><?= icon_gold('crown', 34) ?></div>
          <div>
            <span style="font-size: 11px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700;">Standard Europeo EQF</span>
            <h3 style="color: #ffffff; font-size: 19px; margin: 4px 0 10px 0;">Descrittori di Dublino & Certificazione</h3>
            <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6;">
              Tutti i programmi formativi sono mappati in base ai criteri europei: conoscenze teorico-scientifiche, abilità cognitive e pratiche, autonomia operativa e responsabilità. Rilascio di certificazioni legalmente riconosciute con protocollo digitale immutabile.
            </p>
          </div>
        </div>
      </div>

    </div>

    <!-- LA GOVERNANCE ISTITUZIONALE -->
    <div style="text-align: center; max-width: 850px; margin: 0 auto 40px auto;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Organizzazione Accademica
      </span>
      <h2 style="font-size: clamp(26px, 3.8vw, 38px); color: #ffffff; margin-top: 8px;">
        Governance & Corpi Accademici
      </h2>
      <p style="color: var(--text-muted); font-size: 15px; margin-top: 10px; line-height: 1.6;">
        Una struttura di governo collegiale e trasparente, garanzia di indipendenza didattica e conformità alle normative vigenti.
      </p>
    </div>

    <div class="grid-3" style="margin-bottom: 60px;">
      
      <div class="glass-card" style="border-top: 3px solid var(--gold-primary); padding: 32px 24px;">
        <div style="margin-bottom: 16px;"><?= icon_gold('institution', 36) ?></div>
        <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 12px;">Senato Accademico & Rettorato</h3>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6;">
          Definisce le linee guida didattiche generali, il piano strategico di sviluppo dell'Ateneo, l'armonizzazione delle cattedre dipartimentali e la vigilanza sul codice etico e deontologico.
        </p>
      </div>

      <div class="glass-card" style="border-top: 3px solid var(--gold-light); padding: 32px 24px;">
        <div style="margin-bottom: 16px;"><?= icon_gold('scale', 36) ?></div>
        <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 12px;">Comitati Scientifici Paritetici</h3>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6;">
          Presieduti da docenti ordinari e integrati dai rappresentanti ufficiali degli Ordini Professionali e delle Associazioni di Categoria. Validano i sillabi di ogni singolo insegnamento.
        </p>
      </div>

      <div class="glass-card" style="border-top: 3px solid var(--gold-metallic); padding: 32px 24px;">
        <div style="margin-bottom: 16px;"><?= icon_gold('shield', 36) ?></div>
        <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 12px;">Commissione di Peer-Review</h3>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6;">
          Organismo terzo e indipendente che sottopone a revisione tra pari i materiali didattici, le prove di verifica e il livello di gradimento espresso dai discenti con cadenza semestrale.
        </p>
      </div>

    </div>

    <!-- LE TRE SCUOLE FONDATIVE DELL'ATENEO -->
    <div style="text-align: center; max-width: 850px; margin: 0 auto 40px auto;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Le Tre Anime Didattiche
      </span>
      <h2 style="font-size: clamp(26px, 3.8vw, 38px); color: #ffffff; margin-top: 8px;">
        Le Scuole di Specializzazione CAMPUS
      </h2>
      <p style="color: var(--text-muted); font-size: 15px; margin-top: 10px; line-height: 1.6;">
        Un catalogo strutturato in 3 macro-dipartimenti integrati, per un totale di oltre 2.119 corsi accreditati e monitorati.
      </p>
    </div>

    <div class="grid-3" style="margin-bottom: 60px;">
      
      <div class="glass-card" style="padding: 30px; border: 1px solid var(--border-gold);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
          <span class="badge" style="background: rgba(212,175,55,0.15); color: var(--gold-primary); border: 1px solid var(--gold-primary); font-size: 11px;">
            CAMPUS SCHOOL
          </span>
          <strong style="color: var(--gold-light); font-size: 14px; font-family: monospace;">816 Corsi</strong>
        </div>
        <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 12px;">Scuola dei Mestieri & Tecnica Superiore</h3>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin-bottom: 18px;">
          Formazione tecnica abilitante, sicurezza nei cantieri D.Lgs. 81/08, impiantistica, topografia, restauro monumentale e cantieristica nautica per geometri, periti ed operatori specializzati.
        </p>
        <a href="/courses.php?school=CAMPUS+SCHOOL" class="btn-outline-gold" style="font-size: 12px; padding: 8px 16px; width: 100%; text-align: center;">
          Consulta la Scuola →
        </a>
      </div>

      <div class="glass-card" style="padding: 30px; border: 1px solid var(--border-gold);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
          <span class="badge" style="background: rgba(212,175,55,0.15); color: var(--gold-primary); border: 1px solid var(--gold-primary); font-size: 11px;">
            CAMPUS MASTER
          </span>
          <strong style="color: var(--gold-light); font-size: 14px; font-family: monospace;">1.200 Corsi</strong>
        </div>
        <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 12px;">Scuola di Dottorato & Master Executive</h3>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin-bottom: 18px;">
          Percorsi executive per professionisti iscritti ad albi: ingegneria idraulica e strutturale, revisione contabile, fiscalità internazionale, compliance 231, diritto societario e management sanitario.
        </p>
        <a href="/courses.php?school=CAMPUS+MASTER" class="btn-outline-gold" style="font-size: 12px; padding: 8px 16px; width: 100%; text-align: center;">
          Consulta la Scuola →
        </a>
      </div>

      <div class="glass-card" style="padding: 30px; border: 1px solid var(--border-gold);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
          <span class="badge" style="background: rgba(212,175,55,0.15); color: var(--gold-primary); border: 1px solid var(--gold-primary); font-size: 11px;">
            CAMPUS ACADEMY
          </span>
          <strong style="color: var(--gold-light); font-size: 14px; font-family: monospace;">103 Corsi</strong>
        </div>
        <h3 style="color: #ffffff; font-size: 19px; margin-bottom: 12px;">Accademia d'Élite & Frontiera Digitale</h3>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin-bottom: 18px;">
          Percorsi ad altissimo valore aggiunto su intelligenza artificiale applicata, cybersecurity difensiva, bonifiche e bio-economia del Delta, transizione energetica e finanza quantitativa.
        </p>
        <a href="/courses.php?school=CAMPUS+ACADEMY" class="btn-outline-gold" style="font-size: 12px; padding: 8px 16px; width: 100%; text-align: center;">
          Consulta la Scuola →
        </a>
      </div>

    </div>

    <!-- I SEI PILASTRI DI SERIETÀ E DEONTOLOGIA -->
    <div style="text-align: center; max-width: 850px; margin: 0 auto 40px auto;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Patto Etico e Trasparenza
      </span>
      <h2 style="font-size: clamp(26px, 3.8vw, 38px); color: #ffffff; margin-top: 8px;">
        I 6 Pilastri di Serietà Istituzionale CAMPUS
      </h2>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(310px, 1fr)); gap: 20px; margin-bottom: 60px;">
      
      <div class="glass-card" style="padding: 24px;">
        <h4 style="color: var(--gold-light); font-size: 16px; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
          <?= icon_gold('document', 16) ?> 1. Bando Pubblico Trasparente
        </h4>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Tutti i docenti sono reclutati tramite bando ufficiale con criteri chiari di ammissibilità: iscrizione all'albo da almeno 3 anni o comprovata esperienza tecnica, curriculum documentato e assenza di carichi pendenti.
        </p>
      </div>

      <div class="glass-card" style="padding: 24px;">
        <h4 style="color: var(--gold-light); font-size: 16px; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
          <?= icon_gold('star', 16) ?> 2. Crediti CFP Certificati
        </h4>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Le ore formative sono progettate nel rigoroso rispetto dei regolamenti dei Consigli Nazionali degli Ordini per il riconoscimento automatico dei crediti formativi professionali obbligatori.
        </p>
      </div>

      <div class="glass-card" style="padding: 24px;">
        <h4 style="color: var(--gold-light); font-size: 16px; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
          <?= icon_gold('shield', 16) ?> 3. Immutabilità Crittografica SIC-ID
        </h4>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Ogni attestato, certificato e nomina della Faculty è registrato nel sistema centrale con protocollo immutabile SIC-ID, verificabile in tempo reale da datori di lavoro, Ordini e tribunali.
        </p>
      </div>

      <div class="glass-card" style="padding: 24px;">
        <h4 style="color: var(--gold-light); font-size: 16px; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
          <?= icon_gold('institution', 16) ?> 4. Radicamento Fisico e Polo Delta
        </h4>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Non siamo una società di comodo virtuale: CAMPUS ha il proprio polo accademico a Porto Viro (RO), nel Parco del Delta del Po, sede di laboratori idraulici, ambientali e cantieristici d'avanguardia.
        </p>
      </div>

      <div class="glass-card" style="padding: 24px;">
        <h4 style="color: var(--gold-light); font-size: 16px; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
          <?= icon_gold('check', 16) ?> 5. Trasparenza Contrattuale Assoluta
        </h4>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Condizioni Generali di Membership formalizzate per iscritto (299,00 € annui IVA compresa se dovuta), nessun rinnovo tacito ingannevole, retribuzioni certe e contrattualizzate per gli incarichi di docenza.
        </p>
      </div>

      <div class="glass-card" style="padding: 24px;">
        <h4 style="color: var(--gold-light); font-size: 16px; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
          <?= icon_gold('laptop', 16) ?> 6. Infrastruttura Digitale Sovrana
        </h4>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
          Piattaforma e-learning e applicazione PWA proprietarie ospitate su cluster europei ad alta sicurezza, nel pieno rispetto del GDPR, del Codice della Privacy e dei protocolli AgID per la PA.
        </p>
      </div>

    </div>

    <!-- STATS NUMERICHE IN ORO -->
    <div class="glass-card" style="border: 2px solid var(--border-gold); padding: 40px 20px; margin-bottom: 60px; text-align: center;">
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px;">
        <div>
          <div style="font-size: clamp(36px, 4.5vw, 48px); font-weight: 900; color: var(--gold-light); font-family: 'Cinzel', serif;">2.119+</div>
          <div style="font-size: 13px; text-transform: uppercase; letter-spacing: 1.5px; color: #ffffff; margin-top: 6px;">Insegnamenti a Catalogo</div>
          <div style="font-size: 11.5px; color: var(--text-dim); margin-top: 2px;">Corsi, Master e CFP</div>
        </div>
        <div>
          <div style="font-size: clamp(36px, 4.5vw, 48px); font-weight: 900; color: var(--gold-light); font-family: 'Cinzel', serif;">110+</div>
          <div style="font-size: 13px; text-transform: uppercase; letter-spacing: 1.5px; color: #ffffff; margin-top: 6px;">Organismi nel Network</div>
          <div style="font-size: 11.5px; color: var(--text-dim); margin-top: 2px;">Ordini, Imprese e Pubbliche Amministrazioni</div>
        </div>
        <div>
          <div style="font-size: clamp(36px, 4.5vw, 48px); font-weight: 900; color: var(--gold-light); font-family: 'Cinzel', serif;">3</div>
          <div style="font-size: 13px; text-transform: uppercase; letter-spacing: 1.5px; color: #ffffff; margin-top: 6px;">Scuole Dipartimentali</div>
          <div style="font-size: 11.5px; color: var(--text-dim); margin-top: 2px;">School · Master · Academy</div>
        </div>
        <div>
          <div style="font-size: clamp(36px, 4.5vw, 48px); font-weight: 900; color: var(--gold-light); font-family: 'Cinzel', serif;">100%</div>
          <div style="font-size: 13px; text-transform: uppercase; letter-spacing: 1.5px; color: #ffffff; margin-top: 6px;">Certificazione Digitale</div>
          <div style="font-size: 11.5px; color: var(--text-dim); margin-top: 2px;">Protocollo Immutabile SIC-ID</div>
        </div>
      </div>
    </div>

    <!-- CTA FINALE -->
    <div style="text-align: center; max-width: 800px; margin: 0 auto;">
      <h3 style="font-size: 28px; color: #ffffff; margin-bottom: 14px; font-family: 'Cinzel', serif;">
        Unisciti alla Costruzione del Nuovo Polo Accademico
      </h3>
      <p style="color: var(--text-muted); font-size: 15.5px; line-height: 1.6; margin-bottom: 30px;">
        La Call per la costituzione della Faculty Fondatrice è attiva per tutti i professionisti iscritti ad albi, docenti universitari e tecnici d'impresa d'eccellenza.
      </p>
      <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
        <a href="/apply.php" class="btn-gold" style="font-size: 15px; padding: 16px 36px; display: inline-flex; align-items: center; gap: 8px;">
          <?= icon_gold('institution', 18) ?> Candidati nella Faculty
        </a>
        <a href="/manifesto-docenti-a4.html" target="_blank" class="btn-outline-gold" style="font-size: 15px; padding: 16px 36px; display: inline-flex; align-items: center; gap: 8px;">
          <?= icon_gold('print', 18) ?> Manifesto Ufficiale A4
        </a>
      </div>
    </div>

  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
