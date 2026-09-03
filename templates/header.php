<?php
/**
 * CAMPUS.CAMP — Master Header Shell
 */

if (!defined('CAMPUS_INIT')) {
    require_once __DIR__ . '/config.php';
}
require_once __DIR__ . '/icons.php';

$pageTitle = $pageTitle ?? 'CAMPUS — Discere · Docere · Crescere';
$pageDesc = $pageDesc ?? 'Istituzione Accademica, Centro Nazionale di Alta Formazione e Polo per l\'Innovazione delle Professioni e dei Mestieri.';
$canonicalUrl = $canonicalUrl ?? (CAMPUS_DOMAIN . $_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title><?= sanitize_output($pageTitle) ?></title>
  <meta name="description" content="<?= sanitize_output($pageDesc) ?>">
  <link rel="canonical" href="<?= sanitize_output($canonicalUrl) ?>">

  <!-- OpenGraph & Twitter Cards -->
  <meta property="og:title" content="<?= sanitize_output($pageTitle) ?>">
  <meta property="og:description" content="<?= sanitize_output($pageDesc) ?>">
  <meta property="og:url" content="<?= sanitize_output($canonicalUrl) ?>">
  <meta property="og:image" content="<?= CAMPUS_DOMAIN ?>/assets/visual/Campus_Emblema_Transparent.png">
  <meta property="og:type" content="website">
  <meta name="twitter:card" content="summary_large_image">

  <!-- PWA & Mobile Web App Meta -->
  <meta name="theme-color" content="#07090e">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="CAMPUS">
  <link rel="manifest" href="/manifest.webmanifest">
  <link rel="icon" type="image/x-icon" href="/favicon.ico?v=2">
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/visual/favicon-32x32.png?v=2">
  <link rel="icon" type="image/png" sizes="16x16" href="/assets/visual/favicon-16x16.png?v=2">
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=2">

  <!-- Google Fonts: Cinzel (Classical Academic Serif) + Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Canonical Design System with Dynamic Versioning -->
  <link rel="stylesheet" href="/css/campus.css?v=<?= file_exists(__DIR__ . '/../css/campus.css') ? filemtime(__DIR__ . '/../css/campus.css') : '2.0' ?>">
  
  <!-- Structured Data Schema.org EducationalOrganization -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "EducationalOrganization",
    "name": "CAMPUS",
    "alternateName": "CAMPUS.CAMP",
    "url": "https://campus.camp",
    "logo": "https://campus.camp/assets/visual/Campus_Emblema_Transparent.png",
    "sameAs": [
      "https://github.com/campuscamp"
    ],
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "Via Mantovana, 78",
      "addressLocality": "Porto Viro",
      "addressRegion": "RO",
      "postalCode": "45014",
      "addressCountry": "IT"
    },
    "description": "Istituzione Accademica e Polo per la Formazione Continua delle Professioni e dei Mestieri."
  }
  </script>
</head>
<body>

  <!-- Top Navigation Bar -->
  <header class="navbar">
    <div class="nav-container">
      <a href="/" class="nav-brand">
        <img src="/assets/visual/Campus_Emblema_Transparent.png" alt="CAMPUS Emblema" class="nav-logo">
        <div class="nav-brand-text">
          <span class="nav-brand-name">CAMPUS</span>
          <span class="nav-brand-motto">DISCERE · DOCERE · CRESCERE</span>
        </div>
      </a>

      <nav>
        <ul class="nav-links">
          <li><a href="/" class="nav-link <?= ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/index.php') ? 'active' : '' ?>">Home</a></li>
          <li><a href="/about.php" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'about') ? 'active' : '' ?>">Chi Siamo</a></li>
          <li><a href="/courses.php" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'courses') ? 'active' : '' ?>">Corsi & Scuole</a></li>
          <!-- B2B DROPDOWN MENU -->
          <li class="nav-item-dropdown">
            <a href="/b2b/" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/b2b') ? 'active' : '' ?>" style="display: flex; align-items: center; gap: 4px;">
              B2B <span style="font-size: 9px; color: var(--gold-light);">▼</span>
            </a>
            <div class="nav-dropdown-menu">
              <div class="nav-dropdown-header">PER LE AZIENDE</div>
              <a href="/b2b/corporate-academy.php" class="nav-dropdown-item">Corporate Academy</a>
              <a href="/b2b/convenzioni.php" class="nav-dropdown-item">Convenzioni</a>
              <a href="/b2b/talent.php" class="nav-dropdown-item">Talent & Recruiting</a>
              <a href="/research.php" class="nav-dropdown-item">Ricerca & Innovazione</a>
              <a href="/b2b/partner.php" class="nav-dropdown-item">Diventa Partner</a>
              <a href="/b2b/sponsor.php" class="nav-dropdown-item">Diventa Sponsor</a>
              <a href="/b2b/apply.php?goal=CAMPUS_POINT" class="nav-dropdown-item">CAMPUS Point</a>
            </div>
          </li>
          <!-- TERRITORI DROPDOWN MENU -->
          <li class="nav-item-dropdown">
            <a href="/territori" class="nav-link <?= (str_contains($_SERVER['REQUEST_URI'], 'territori') || str_contains($_SERVER['REQUEST_URI'], 'campus-city')) ? 'active' : '' ?>" style="display: flex; align-items: center; gap: 4px;">
              Territori <span style="font-size: 9px; color: var(--gold-light);">▼</span>
            </a>
            <div class="nav-dropdown-menu">
              <div class="nav-dropdown-header">CAMPUS FOR CITIES</div>
              <a href="/campus-city/apply.php" class="nav-dropdown-item">Diventa Città CAMPUS</a>
              <a href="/campus-city/#modello" class="nav-dropdown-item">Modello CAMPUS City</a>
              <a href="/campus-city/#strutture" class="nav-dropdown-item">Strutture candidabili</a>
              <a href="/campus-city/#benefici" class="nav-dropdown-item">Benefici per il territorio</a>
              <a href="/campus-city/studio-fattibilita.php" class="nav-dropdown-item">Studio di fattibilità</a>
              <a href="/porto-viro.php" class="nav-dropdown-item">CAMPUS Porto Viro</a>
              <a href="/campus-city/network.php" class="nav-dropdown-item">Rete dei Comuni</a>
              <a href="/campus-city/apply.php" class="nav-dropdown-item">Candidatura PA</a>
            </div>
          </li>
          <li><a href="/portal.php" class="nav-link <?= (str_contains($_SERVER['REQUEST_URI'], 'portal') || str_contains($_SERVER['REQUEST_URI'], 'user')) ? 'active' : '' ?>">Portale Docenti</a></li>
          <li><a href="/contact.php" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'contact') ? 'active' : '' ?>">Contatti</a></li>
          <li>
            <a href="/b2b/apply.php" class="btn-gold" style="padding: 8px 16px; font-size: 11px; white-space: nowrap; letter-spacing: 0.8px;">
              PORTA LA TUA AZIENDA IN CAMPUS
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="main-content">
