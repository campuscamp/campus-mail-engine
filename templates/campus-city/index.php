<?php
/**
 * CAMPUS.CAMP — CAMPUS FOR CITIES
 * Portale Istituzionale per Comuni, Unioni di Comuni, Province ed Enti Territoriali
 * Big Idea: NON È CAMPUS CHE CHIEDE AL COMUNE UNA SEDE. È IL COMUNE CHE CANDIDA IL TERRITORIO.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/icons.php';

$pageTitle = 'CAMPUS for Cities — L\'Infrastruttura Territoriale della Conoscenza per Comuni e PA';
$pageDesc = 'Trasforma uno spazio pubblico esistente in un luogo di formazione, professioni, talenti e impresa. Candidatura del Comune nella rete nazionale CAMPUS.';

$db = Database::getConnection();

// Statistiche Rete Territoriale
$totalCities = (int)$db->query("SELECT COUNT(*) FROM campus_cities")->fetchColumn();
$activeCities = (int)$db->query("SELECT COUNT(*) FROM campus_cities WHERE status = 'ACTIVE'")->fetchColumn();

require_once __DIR__ . '/../includes/header.php';
?>

<!-- HERO SECTION INSTITUTIONAL -->
<section class="hero" style="min-height: 85vh; padding: 100px 20px 80px 20px;">
  <div class="hero-overlay"></div>
  <div class="hero-content" style="max-width: 1050px;">
    
    <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 20px; border-radius: 20px; background: rgba(212,175,55,0.12); border: 1px solid var(--border-gold); margin-bottom: 24px;">
      <span style="font-size: 11px; color: var(--gold-light); text-transform: uppercase; letter-spacing: 2.5px; font-weight: 800;">
        Programma Nazionale per i Comuni e le Pubbliche Amministrazioni
      </span>
    </div>

    <h1 style="font-size: clamp(32px, 5.5vw, 62px); line-height: 1.15; margin-bottom: 24px; font-family: 'Cinzel', serif; letter-spacing: 1.5px;">
      PORTA CAMPUS<br>
      <span class="gold-text">NELLA TUA CITTÀ.</span>
    </h1>

    <p style="font-size: clamp(16px, 2vw, 20px); color: var(--text-muted); max-width: 860px; margin: 0 auto 38px auto; line-height: 1.65;">
      Trasforma uno spazio pubblico esistente — una biblioteca, una sala civica, un ex plesso scolastico — in un'infrastruttura territoriale viva dove persone, competenze, professioni, imprese e istituzioni possono imparare, insegnare e crescere.<br>
      <strong style="color: var(--gold-light); font-family: 'Cinzel', serif; font-size: 15px; letter-spacing: 2px;">DISCERE · DOCERE · CRESCERE.</strong>
    </p>

    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
      <a href="/campus-city/apply.php" class="btn-gold" style="font-size: 15px; padding: 16px 36px; font-weight: 800;" data-analytics="city_hero_apply">
        <?= icon_gold('institution', 18) ?> CANDIDA IL TUO COMUNE
      </a>
      <a href="#modello" class="btn-outline-gold" style="font-size: 15px; padding: 16px 32px;" data-analytics="city_hero_model">
        SCOPRI IL MODELLO CAMPUS CITY ↓
      </a>
      <a href="/campus-city/studio-fattibilita.php" class="btn-outline-gold" style="font-size: 15px; padding: 16px 30px; border-color: rgba(255,255,255,0.25);" data-analytics="city_hero_dossier">
        <?= icon_gold('document', 16) ?> DOSSIER PER LA GIUNTA
      </a>
    </div>

    <div style="margin-top: 40px; display: flex; justify-content: center; gap: 32px; font-size: 12.5px; color: var(--text-dim); flex-wrap: wrap;">
      <span><?= icon_gold('shield', 14) ?> Nessun Nuovo Edificio da Costruire</span>
      <span><?= icon_gold('academic', 14) ?> 2.119 Insegnamenti Istituzionali</span>
      <span><?= icon_gold('crown', 14) ?> Protocollo SIC-ID Territoriale</span>
      <span><?= icon_gold('star', 14) ?> Convenzione Quadro Non Vincolante</span>
    </div>

  </div>
</section>


<!-- IL PERCHÉ: OGNI COMUNE POSSIEDE SPAZI, NON OGNI SPAZIO GENERA OPPORTUNITÀ -->
<section class="section" style="background: rgba(10,10,10,0.6); border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);">
  <div class="container" style="max-width: 1100px;">
    
    <div style="text-align: center; max-width: 820px; margin: 0 auto 50px auto;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2.5px; font-weight: 800;">
        Il Paradosso del Patrimonio Pubblico
      </span>
      <h2 style="font-size: clamp(26px, 3.8vw, 42px); color: #ffffff; margin-top: 8px; font-family: 'Cinzel', serif;">
        OGNI COMUNE POSSIEDE SPAZI.<br>
        <span class="gold-text">NON OGNI SPAZIO GENERA OPPORTUNITÀ.</span>
      </h2>
      <p style="color: var(--text-muted); font-size: 16px; line-height: 1.7; margin-top: 14px;">
        Centinaia di sale civiche, ex scuole e biblioteche restano vuote per ore. CAMPUS non chiede ai Comuni di investire in nuovo cemento o accendere mutui: valorizza il patrimonio esistente trasformandolo in un hub vivo di formazione, lavoro e innovazione.
      </p>
    </div>

    <div class="glass-card" style="padding: 40px; border: 1px solid var(--border-gold);">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 36px; align-items: center;">
        <div>
          <h3 style="color: #ffffff; font-size: 20px; font-family: 'Cinzel', serif; margin-bottom: 14px;">
            L'Infrastruttura Territoriale della Conoscenza
          </h3>
          <p style="color: var(--text-muted); font-size: 14.5px; line-height: 1.7; margin-bottom: 14px;">
            Una <strong>biblioteca</strong> può essere molto più di un deposito di libri: può diventare un'aula di alta formazione continua per i giovani del paese.
          </p>
          <p style="color: var(--text-muted); font-size: 14.5px; line-height: 1.7; margin-bottom: 14px;">
            Una <strong>sala civica</strong> può accogliere convegni con crediti formativi (CFP) per gli ingegneri, periti, avvocati e architetti del territorio.
          </p>
          <p style="color: var(--text-muted); font-size: 14.5px; line-height: 1.7;">
            Un <strong>auditorium</strong> può mettere in relazione diretta le PMI con i diplomati, trattenendo i talenti ed evitando lo spopolamento delle province.
          </p>
        </div>

        <div style="background: rgba(0,0,0,0.55); border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); padding: 26px;">
          <div style="font-size: 12px; color: var(--gold-light); text-transform: uppercase; font-weight: 700; letter-spacing: 1.5px; margin-bottom: 16px;">
            La Formula di Rigenerazione:
          </div>
          <div style="display: flex; flex-direction: column; gap: 14px; font-size: 14px; color: #ffffff;">
            <div style="display: flex; align-items: center; gap: 10px;">
              <span style="color: var(--gold-light); font-weight: 800; font-family: monospace;">[COMUNE]</span>
              <span>Spazio pubblico esistente + Radicamento locale</span>
            </div>
            <div style="text-align: center; color: var(--gold-primary); font-weight: bold; font-size: 18px;">+</div>
            <div style="display: flex; align-items: center; gap: 10px;">
              <span style="color: var(--gold-light); font-weight: 800; font-family: monospace;">[CAMPUS]</span>
              <span>Faculty accademica + 2.119 percorsi + Network aziende</span>
            </div>
            <div style="text-align: center; color: var(--gold-primary); font-weight: bold; font-size: 18px;">=</div>
            <div style="background: rgba(212,175,55,0.15); border: 1px solid var(--gold-primary); padding: 12px; border-radius: var(--radius-sm); text-align: center; font-weight: 800; color: #ffffff; letter-spacing: 1px;">
              CAMPUS CITY — NODO TERRITORIALE ATTIVO
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>


<!-- I 4 MODELLI DI CAMPUS CITY -->
<section id="modello" class="section">
  <div class="container" style="max-width: 1280px;">

    <div style="text-align: center; max-width: 800px; margin: 0 auto 50px auto;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Flessibilità Amministrativa
      </span>
      <h2 style="font-size: clamp(24px, 3.5vw, 38px); color: #ffffff; margin-top: 6px; font-family: 'Cinzel', serif;">
        I 4 MODELLI DI ADESIONE PER LA PA
      </h2>
      <p style="color: var(--text-muted); font-size: 15px;">
        Ogni Comune ha dimensioni e patrimoni differenti. Abbiamo concepito 4 configurazioni per consentire a qualunque amministrazione di candidarsi.
      </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">

      <!-- 01 CAMPUS HUB -->
      <div class="glass-card" style="padding: 28px; border-top: 3px solid var(--gold-primary);">
        <div style="font-size: 11px; color: var(--gold-light); font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">Modello Completo</div>
        <h3 style="color: #ffffff; font-size: 20px; margin: 8px 0 10px 0; font-family: 'Cinzel', serif;">CAMPUS HUB</h3>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6; margin-bottom: 16px;">
          Polo polifunzionale articolato su più sale (aule didattiche, sala conferenze, laboratorio tecnico) con programmazione annuale continuativa.
        </p>
        <div style="font-size: 12px; color: #cbd5e1; display: flex; flex-direction: column; gap: 6px;">
          <span>• Più aule attrezzate e presidio fisico</span>
          <span>• Programmazione corsi, master e convegni</span>
          <span>• Polo di riferimento per l'intero distretto</span>
        </div>
      </div>

      <!-- 02 CAMPUS POINT -->
      <div class="glass-card" style="padding: 28px; border-top: 3px solid var(--gold-primary);">
        <div style="font-size: 11px; color: var(--gold-light); font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">Presidio Civico</div>
        <h3 style="color: #ffffff; font-size: 20px; margin: 8px 0 10px 0; font-family: 'Cinzel', serif;">CAMPUS POINT</h3>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6; margin-bottom: 16px;">
          Un singolo spazio civico, una sala in biblioteca o uno sportello municipale dedicato all'orientamento, alla fruizione digitale e a workshop periodici.
        </p>
        <div style="font-size: 12px; color: #cbd5e1; display: flex; flex-direction: column; gap: 6px;">
          <span>• 1 sala civica o spazio biblioteca</span>
          <span>• Targa ufficiale e postazione digitale</span>
          <span>• Zero costi di allestimento strutturale</span>
        </div>
      </div>

      <!-- 03 CAMPUS PARTNER -->
      <div class="glass-card" style="padding: 28px; border-top: 3px solid var(--gold-primary);">
        <div style="font-size: 11px; color: var(--gold-light); font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">Spazio Condiviso</div>
        <h3 style="color: #ffffff; font-size: 20px; margin: 8px 0 10px 0; font-family: 'Cinzel', serif;">CAMPUS PARTNER</h3>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6; margin-bottom: 16px;">
          Spazio pubblico condiviso in co-progettazione con un istituto scolastico (ITS/VET), un'associazione di categoria, un ordine o un'impresa locale.
        </p>
        <div style="font-size: 12px; color: #cbd5e1; display: flex; flex-direction: column; gap: 6px;">
          <span>• Gestione condivisa pubblico-privata</span>
          <span>• Ottimizzazione delle risorse di gestione</span>
          <span>• Integrazione immediata con la scuola locale</span>
        </div>
      </div>

      <!-- 04 CAMPUS DIGITAL CITY -->
      <div class="glass-card" style="padding: 28px; border-top: 3px solid var(--gold-primary);">
        <div style="font-size: 11px; color: var(--gold-light); font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">Ecosistema Digitale</div>
        <h3 style="color: #ffffff; font-size: 20px; margin: 8px 0 10px 0; font-family: 'Cinzel', serif;">CAMPUS DIGITAL CITY</h3>
        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6; margin-bottom: 16px;">
          Adesione della municipalità alla rete con erogazione prevalentemente digitale: aule virtuali, bando docenti per i residenti e sportello telematico.
        </p>
        <div style="font-size: 12px; color: #cbd5e1; display: flex; flex-direction: column; gap: 6px;">
          <span>• Servizi e formazione erogati online</span>
          <span>• Accesso per la cittadinanza al catalogo</span>
          <span>• Prospettiva di attivazione presidio futuro</span>
        </div>
      </div>

    </div>

  </div>
</section>


<!-- STRUTTURE CANDIDABILI DALLA PA -->
<section id="strutture" class="section" style="background: rgba(10,10,10,0.6); border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);">
  <div class="container" style="max-width: 1240px;">

    <div style="text-align: center; max-width: 800px; margin: 0 auto 45px auto;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Patrimonio Pubblico Valorizzabile
      </span>
      <h2 style="font-size: clamp(24px, 3.5vw, 38px); color: #ffffff; margin-top: 6px; font-family: 'Cinzel', serif;">
        LE STRUTTURE CHE IL TUO COMUNE PUÒ CANDIDARE
      </h2>
      <p style="color: var(--text-muted); font-size: 15px;">
        Qualunque immobile con idoneità d'uso e agibilità può diventare un nodo della rete CAMPUS.
      </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 14px;">
      <?php 
        $facilities = [
          ['Biblioteca Comunale', 'Aule studio, sale lettura o sezioni multimediali trasformate in learning hub.'],
          ['Ex Plesso Scolastico', 'Aule dismesse o piani sottoutilizzati da rigenerare a scopo formativo.'],
          ['Sala Civica / Consiliare', 'Spazi per convegni, seminari specialistici e lezioni magistrali periodiche.'],
          ['Auditorium o Teatro', 'Incontri di distretto, presentazioni con le imprese e cerimonie di diploma.'],
          ['Centro Culturale', 'Attività polivalenti tra cultura, competenze professionali e cittadinanza.'],
          ['Centro Giovani / Informagiovani', 'Sportello di orientamento universitario, career day e talent matching.'],
          ['Palazzo Storico', 'Sale di rappresentanza per master executive e tavoli istituzionali.'],
          ['Coworking Pubblico', 'Postazioni per professionisti e discenti con connessione a banda ultralarga.'],
          ['Laboratorio Territoriale', 'Spazi per sperimentazioni tecniche, artigianato digitale o monitoraggi ambientali.'],
          ['Immobile Confiscato / Rigenerato', 'Restituzione alla collettività attraverso la formazione e la cultura della legalità.'],
          ['Spazio Polifunzionale di Frazione', 'Presidio didattico per garantire pari opportunità anche nelle aree periferiche.'],
          ['Altro Patrimonio Disponibile', 'Qualsiasi bene immobile censito nel piano delle alienazioni e valorizzazioni.']
        ];
        foreach ($facilities as $fac):
      ?>
        <div class="glass-card" style="padding: 20px;">
          <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
            <?= icon_gold('check', 16) ?>
            <strong style="color: #ffffff; font-size: 14.5px;"><?= $fac[0] ?></strong>
          </div>
          <p style="color: var(--text-muted); font-size: 12.5px; line-height: 1.5; margin: 0;">
            <?= $fac[1] ?>
          </p>
        </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>


<!-- 8 MACRO-BENEFICI PER IL TERRITORIO -->
<section id="benefici" class="section">
  <div class="container" style="max-width: 1240px;">

    <div style="text-align: center; max-width: 800px; margin: 0 auto 50px auto;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Ritorno per la Collettività
      </span>
      <h2 style="font-size: clamp(24px, 3.5vw, 38px); color: #ffffff; margin-top: 6px; font-family: 'Cinzel', serif;">
        GLI 8 BENEFICI STRATEGICI PER IL COMUNE
      </h2>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
      
      <div class="glass-card" style="padding: 24px;">
        <div style="color: var(--gold-light); font-size: 12px; font-weight: bold;">01 · CAPITALE UMANO</div>
        <h3 style="color: #ffffff; font-size: 17px; margin: 6px 0 8px 0;">Formazione Continua Locale</h3>
        <p style="color: var(--text-muted); font-size: 13px; line-height: 1.6; margin: 0;">
          Corsi per lavoratori e dipendenti pubblici (PA 110 e lode, upskilling, PNRR) direttamente sul proprio territorio.
        </p>
      </div>

      <div class="glass-card" style="padding: 24px;">
        <div style="color: var(--gold-light); font-size: 12px; font-weight: bold;">02 · GIOVANI & TALENTO</div>
        <h3 style="color: #ffffff; font-size: 17px; margin: 6px 0 8px 0;">Contrasto allo Spopolamento</h3>
        <p style="color: var(--text-muted); font-size: 13px; line-height: 1.6; margin: 0;">
          Offrire ai giovani motivi concreti per restare: tirocini, stage con le imprese locali e connessione con l albo talenti.
        </p>
      </div>

      <div class="glass-card" style="padding: 24px;">
        <div style="color: var(--gold-light); font-size: 12px; font-weight: bold;">03 · IMPRESE LOCALI</div>
        <h3 style="color: #ffffff; font-size: 17px; margin: 6px 0 8px 0;">Sostegno alle PMI di Distretto</h3>
        <p style="color: var(--text-muted); font-size: 13px; line-height: 1.6; margin: 0;">
          Le imprese del territorio trovano un'accademia vicina per formare il personale e commissionare project work.
        </p>
      </div>

      <div class="glass-card" style="padding: 24px;">
        <div style="color: var(--gold-light); font-size: 12px; font-weight: bold;">04 · PROFESSIONISTI</div>
        <h3 style="color: #ffffff; font-size: 17px; margin: 6px 0 8px 0;">Cattedre e CFP per gli Ordini</h3>
        <p style="color: var(--text-muted); font-size: 13px; line-height: 1.6; margin: 0;">
          Opportunità di docenza per i professionisti locali abilitati e crediti formativi professionali senza trasferte.
        </p>
      </div>

      <div class="glass-card" style="padding: 24px;">
        <div style="color: var(--gold-light); font-size: 12px; font-weight: bold;">05 · CULTURA CIVICA</div>
        <h3 style="color: #ffffff; font-size: 17px; margin: 6px 0 8px 0;">Convegni e Divulgazione</h3>
        <p style="color: var(--text-muted); font-size: 13px; line-height: 1.6; margin: 0;">
          Animazione culturale della città con presentazioni, dibattiti e lezioni aperte alla cittadinanza e agli anziani.
        </p>
      </div>

      <div class="glass-card" style="padding: 24px;">
        <div style="color: var(--gold-light); font-size: 12px; font-weight: bold;">06 · INNOVAZIONE</div>
        <h3 style="color: #ffffff; font-size: 17px; margin: 6px 0 8px 0;">Laboratori e Ricerca sul Campo</h3>
        <p style="color: var(--text-muted); font-size: 13px; line-height: 1.6; margin: 0;">
          Applicazione di progetti di ricerca applicata su sfide ambientali, mobilità, digitalizzazione e turismo locale.
        </p>
      </div>

      <div class="glass-card" style="padding: 24px;">
        <div style="color: var(--gold-light); font-size: 12px; font-weight: bold;">07 · ATTRATTIVITÀ</div>
        <h3 style="color: #ffffff; font-size: 17px; margin: 6px 0 8px 0;">Riconoscimento Nazionale</h3>
        <p style="color: var(--text-muted); font-size: 13px; line-height: 1.6; margin: 0;">
          Il Comune entra nella rete di ateneo con visibilità sul portale nazionale, richiamando docenti e corsisti da fuori.
        </p>
      </div>

      <div class="glass-card" style="padding: 24px;">
        <div style="color: var(--gold-light); font-size: 12px; font-weight: bold;">08 · PATRIMONIO PUBBLICO</div>
        <h3 style="color: #ffffff; font-size: 17px; margin: 6px 0 8px 0;">Aumento dell'Utilizzo degli Immobili</h3>
        <p style="color: var(--text-muted); font-size: 13px; line-height: 1.6; margin: 0;">
          Massimizzazione del rendimento sociale degli edifici municipali, con presidio costante e decoro dell'immobile.
        </p>
      </div>

    </div>

  </div>
</section>


<!-- CASO PILOTA: IL POLO TERRITORIALE DI PORTO VIRO -->
<section class="section" style="background: rgba(10,10,10,0.6); border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);">
  <div class="container" style="max-width: 1100px;">
    
    <div class="glass-card" style="padding: 40px; border: 2px solid var(--border-gold);">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 36px; align-items: center;">
        <div>
          <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
            Il Caso Pilota Fondatore
          </span>
          <h3 style="color: #ffffff; font-size: 26px; font-family: 'Cinzel', serif; margin: 8px 0 16px 0;">
            CAMPUS Porto Viro: Il Polo del Delta del Po
          </h3>
          <p style="color: var(--text-muted); font-size: 14.5px; line-height: 1.7; margin-bottom: 14px;">
            A <strong>Porto Viro (Rovigo)</strong>, nel cuore della Riserva della Biosfera MAB UNESCO, è nata la prima concretizzazione del modello CAMPUS City: un polo territoriale e di ricerca applicata insediato in una struttura operativa in Via Mantovana 78.
          </p>
          <p style="color: var(--text-muted); font-size: 14.5px; line-height: 1.7; margin-bottom: 20px;">
            Qui la ricerca sulle transizioni ecologiche e la blue economy dialoga direttamente con la formazione dei tecnici e con il tessuto delle PMI venete ed emiliane. Un modello replicabile in ogni municipalità italiana.
          </p>
          <a href="/porto-viro.php" class="btn-outline-gold" style="font-size: 13px; padding: 10px 22px;">
            Scopri il Polo di Porto Viro →
          </a>
        </div>

        <div style="background: rgba(0,0,0,0.5); border: 1px solid var(--border-subtle); padding: 26px; border-radius: var(--radius-sm); font-size: 13.5px; color: #ffffff;">
          <div style="color: var(--gold-light); font-weight: bold; margin-bottom: 12px; text-transform: uppercase; font-size: 12px;">
            I Dati del Polo Pilota:
          </div>
          <div style="display: flex; flex-direction: column; gap: 10px;">
            <div>• <strong>Bacino:</strong> Oltre 85.000 abitanti nel distretto del Delta</div>
            <div>• <strong>Aule Didattiche & Laboratori:</strong> 450 mq operativi</div>
            <div>• <strong>Protocollo Istituzionale:</strong> SIC-ID-MUNI-PORTOVIRO</div>
            <div>• <strong>Specializzazioni:</strong> Ambiente, Idraulica, Sicurezza, Blu Economy</div>
            <div>• <strong>Rapporto con la PA:</strong> Modello di convenzione quadro aperto</div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>


<!-- FAQ ISTITUZIONALI ANTI-OBIEZIONI PER AMMINISTRATORI E GIUNTE -->
<section class="section">
  <div class="container" style="max-width: 950px;">

    <div style="text-align: center; margin-bottom: 45px;">
      <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">
        Chiarezza Amministrativa
      </span>
      <h2 style="font-size: clamp(24px, 3.5vw, 36px); color: #ffffff; margin-top: 6px; font-family: 'Cinzel', serif;">
        DOMANDE FREQUENTI <span class="gold-text">PER SINDACI E ASSESSORI</span>
      </h2>
    </div>

    <div style="display: flex; flex-direction: column; gap: 14px;">

      <div class="glass-card" style="padding: 22px;">
        <h4 style="color: #ffffff; font-size: 16px; margin-bottom: 8px;">
          Quanto costa al Comune aderire al programma CAMPUS for Cities?
        </h4>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin: 0;">
          L'adesione preliminare e la candidatura non comportano <strong>alcun costo fisso per l'amministrazione</strong>. Il Comune mette a disposizione un bene già presente nel proprio patrimonio (biblioteca, sala civica, immobile) in regime di comodato d'uso, convenzione d'uso temporaneo o concessione di servizi. Le utenze e la gestione ordinaria vengono concordate nello studio di fattibilità.
        </p>
      </div>

      <div class="glass-card" style="padding: 22px;">
        <h4 style="color: #ffffff; font-size: 16px; margin-bottom: 8px;">
          CAMPUS duplica o fa concorrenza a scuole, ITS o centri di formazione già presenti?
        </h4>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin: 0;">
          <strong>No, CAMPUS agisce per convergenza e raccordo.</strong> Non si sostituisce agli istituti scolastici o agli enti di formazione accreditati regionali (es. ENAIP/VET), ma ne valorizza i diplomati offrendo percorsi post-diploma, certificazioni avanzate con crediti formativi (CFP) e collegamento diretto con gli Ordini Professionali e le aziende partner.
        </p>
      </div>

      <div class="glass-card" style="padding: 22px;">
        <h4 style="color: #ffffff; font-size: 16px; margin-bottom: 8px;">
          Quali atti amministrativi servono per formalizzare la presenza di CAMPUS?
        </h4>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin: 0;">
          Il percorso tipico prevede: 1) Candidatura online del Comune; 2) Studio di prefattibilità congiunto; 3) Delibera di Giunta Comunale di approvazione dello schema di <strong>Convenzione Quadro di Cooperazione Istituzionale</strong>; 4) Firma della convenzione da parte del Sindaco o Dirigente competente. Mettiamo a disposizione degli uffici legali e tecnici un template standard verificato.
        </p>
      </div>

      <div class="glass-card" style="padding: 22px;">
        <h4 style="color: #ffffff; font-size: 16px; margin-bottom: 8px;">
          Cosa succede se i risultati non soddisfano le aspettative del Comune?
        </h4>
        <p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin: 0;">
          La Convenzione Quadro ha durata determinata (es. 2 o 3 anni) con clausola espressa di recesso motivato senza penali e verifica annuale dei KPI concordati (numero di iscritti, eventi svolti, aziende coinvolte). Non esiste alcun vincolo perpetuo o ipoteca sull'immobile pubblico.
        </p>
      </div>

    </div>

  </div>
</section>


<!-- CTA FINALE: CAMBIARE LA DINAMICA COMMERCIALE -->
<section class="section" style="text-align: center; padding: 90px 20px;">
  <div class="container" style="max-width: 900px;">
    
    <img src="/assets/visual/Campus_Emblema_Transparent.png" alt="CAMPUS" style="width: 90px; margin-bottom: 20px; filter: drop-shadow(0 0 25px rgba(212,175,55,0.45));">

    <span style="font-size: 11px; color: var(--gold-primary); text-transform: uppercase; letter-spacing: 2.5px; font-weight: 800; display: block; margin-bottom: 8px;">
      Selezione e Accreditamento Territoriale
    </span>

    <h2 style="font-size: clamp(28px, 4.5vw, 44px); color: #ffffff; font-family: 'Cinzel', serif; margin-bottom: 16px;">
      IL TUO COMUNE È PRONTO PER <span class="gold-text">DIVENTARE UNA CITTÀ CAMPUS?</span>
    </h2>

    <p style="font-size: 16px; color: var(--text-muted); line-height: 1.7; margin-bottom: 35px; max-width: 760px; margin-left: auto; margin-right: auto;">
      Il primo passo non comporta alcun impegno vincolante. Ci permette di verificare insieme se esistono le condizioni strutturali ed ecosistemiche per radicare un nodo accademico nel tuo territorio.
    </p>

    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
      <a href="/campus-city/apply.php" class="btn-gold" style="font-size: 16px; padding: 18px 40px; font-weight: 800;" data-analytics="city_footer_apply">
        <?= icon_gold('institution', 18) ?> CANDIDA IL TUO COMUNE ORA
      </a>
      <a href="/campus-city/studio-fattibilita.php" class="btn-outline-gold" style="font-size: 16px; padding: 18px 32px;" data-analytics="city_footer_feasibility">
        <?= icon_gold('document', 16) ?> GENERA STUDIO DI FATTIBILITÀ
      </a>
    </div>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
