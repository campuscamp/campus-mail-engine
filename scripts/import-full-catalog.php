<?php
/**
 * CAMPUS.CAMP — Importazione Catalogo Completo (2.117+ Corsi) in SQLite via PHP PDO
 */
declare(strict_types=1);

require_once __DIR__ . '/../../public_html/includes/database.php';

$catalogSource = 'C:\\81PLUS_GLOBAL_MASTER\\81plus.net\\LISTINO81+\\data\\catalog_data.js';

if (!file_exists($catalogSource)) {
    die("File sorgente non trovato: $catalogSource\n");
}

$raw = file_get_contents($catalogSource);

// Extract JSON array from "window.LISTINO81_DATA = [...]"
if (!preg_match('/window\.LISTINO81_DATA\s*=\s*(\[[\s\S]*?\]);/s', $raw, $matches)) {
    die("Impossibile estrarre l'array LISTINO81_DATA con regex\n");
}

$data = json_decode($matches[1], true);
if (!is_array($data)) {
    die("Errore decodifica JSON\n");
}

echo "Totale record nel listino: " . count($data) . "\n";

$db = Database::getConnection();
$db->beginTransaction();

// Clean table
$db->exec("DELETE FROM courses");

$stmt = $db->prepare("
    INSERT INTO courses (
        code, title, faculty, school, cfp_credits, description, is_active
    ) VALUES (?, ?, ?, ?, ?, ?, 1)
");

$count = 0;
foreach ($data as $item) {
    // Only courses
    if (empty($item['corso']) || $item['corso'] !== 1) {
        continue;
    }

    $count++;
    $code = sprintf('CRS-%05d', $count);
    $title = trim($item['n'] ?? 'Corso Specialistico');
    $faculty = trim($item['cat'] ?? ($item['tier'] ?? 'Formazione Continua'));
    $school = trim($item['sub'] ?? ($item['mode'] ?? 'Online'));
    $eur = isset($item['eur']) ? (float)$item['eur'] : 0.0;
    $cfp = $eur > 0 ? (int)min(max(round($eur / 25), 2), 30) : 4;
    $desc = trim($item['d'] ?? "Corso accademico specialistico in {$faculty}.");

    $stmt->execute([$code, $title, $faculty, $school, $cfp, $desc]);
}

$db->commit();

echo "🎉 INSERITI CON SUCCESSO {$count} CORSI UFFICIALI NEL DATABASE SQLITE!\n";
