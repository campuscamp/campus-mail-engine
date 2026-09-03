<?php
/**
 * CAMPUS.CAMP — Importazione Catalogo Ufficiale UNIVERSITY81+ (2.119 Corsi)
 * Fonte: https://81plus.net/university81.html / academy_catalog.js
 */
declare(strict_types=1);

require_once __DIR__ . '/../../public_html/includes/database.php';

$source = 'C:\\81PLUS_GLOBAL_MASTER\\81plus.net\\0-81PLUS.NET\\academy_catalog.js';

if (!file_exists($source)) {
    die("File academy_catalog.js non trovato in: $source\n");
}

$raw = file_get_contents($source);

// Extract JSON array
if (!preg_match('/window\.ACADEMY_CATALOG\s*=\s*(\[[\s\S]*?\]);/s', $raw, $matches)) {
    die("Impossibile estrarre window.ACADEMY_CATALOG con regex\n");
}

$items = json_decode($matches[1], true);
if (!is_array($items)) {
    die("Errore decodifica JSON da academy_catalog.js\n");
}

echo "Trovati " . count($items) . " corsi in UNIVERSITY81+!\n";

$db = Database::getConnection();
$db->beginTransaction();

// Clear courses table
$db->exec("DELETE FROM courses");

$stmt = $db->prepare("
    INSERT INTO courses (
        code, title, faculty, school, level, cfp_credits, description
    ) VALUES (?, ?, ?, ?, ?, ?, ?)
");

function formatTierSchool(string $tier): string {
    if ($tier === 'PRO81+') return 'MASTER81+';
    if ($tier === 'ELITE81+') return 'ACADEMY81+';
    return 'SCHOOL81+';
}

$count = 0;
foreach ($items as $c) {
    $count++;
    $code = trim($c['id'] ?? sprintf('AC81-%04d', $count));
    $title = trim($c['name'] ?? 'Corso Specialistico');
    $faculty = trim($c['cat'] ?? 'Area Formativa Generale');
    $school = formatTierSchool($c['tier'] ?? 'BASIC81+');
    $mode = trim($c['mode'] ?? 'Online');
    
    $price = isset($c['price']) ? (float)$c['price'] : 0.0;
    if ($price >= 500) {
        $cfp = min((int)round($price / 50), 40);
    } elseif ($price > 0) {
        $cfp = min(max((int)round($price / 25), 2), 24);
    } else {
        $cfp = 4;
    }

    $desc = "Percorso accademico erogato nell'ambito di {$school} ({$faculty}). Modalità: {$mode}.";

    $stmt->execute([$code, $title, $faculty, $school, $mode, $cfp, $desc]);
}

$db->commit();

echo "🎉 INSERITI CON SUCCESSO {$count} CORSI E MASTER DI UNIVERSITY81+ IN campus.sqlite!\n";
