<?php
require_once __DIR__ . '/../../public_html/includes/database.php';

$jsonFile = __DIR__ . '/../data/courses_clean.json';
$corsi = json_decode(file_get_contents($jsonFile), true);

echo "Caricamento di " . count($corsi) . " corsi in corso...\n";

$db = Database::getConnection();
$db->beginTransaction();
$db->exec('DELETE FROM courses');

$stmt = $db->prepare('INSERT INTO courses (code, title, faculty, school, level, cfp_credits, description) VALUES (?, ?, ?, ?, ?, ?, ?)');

$count = 0;
foreach ($corsi as $item) {
    $count++;
    $code = sprintf('CRS-%05d', $count);
    $title = trim($item['n'] ?? 'Corso Specialistico');
    $faculty = trim($item['cat'] ?? ($item['tier'] ?? 'Formazione Continua'));
    $school = trim($item['sub'] ?? ($item['mode'] ?? 'Online'));
    $level = !empty($item['tier']) ? $item['tier'] : 'Specialistico';
    $eur = isset($item['eur']) ? (float)$item['eur'] : 0.0;
    $cfp = $eur > 0 ? (int)min(max(round($eur / 25), 2), 30) : 4;
    $desc = trim($item['d'] ?? ("Corso accademico specialistico in " . $faculty));
    $stmt->execute([$code, $title, $faculty, $school, $level, $cfp, $desc]);
}
$db->commit();

echo "🎉 COMPLETATO: Inseriti {$count} corsi ufficiali in campus.sqlite!\n";
