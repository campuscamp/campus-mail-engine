<?php
$dbPath = 'c:/81PLUS_GLOBAL_MASTER/campus.camp/public_html/storage/database/campus.sqlite';
$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "1. Aggiornamento Scuole in SQLite...\n";
$pdo->exec("UPDATE courses SET school = 'CAMPUS SCHOOL' WHERE school LIKE '%SCHOOL%'");
$pdo->exec("UPDATE courses SET school = 'CAMPUS MASTER' WHERE school LIKE '%MASTER%'");
$pdo->exec("UPDATE courses SET school = 'CAMPUS ACADEMY' WHERE school LIKE '%ACADEMY%'");

echo "2. Rimozione prefissi e riferimenti 81 dai codici...\n";
$pdo->exec("UPDATE courses SET code = REPLACE(code, 'AC81-', 'CMP-')");
$pdo->exec("UPDATE courses SET code = REPLACE(code, '81+', 'CAMPUS')");
$pdo->exec("UPDATE courses SET code = REPLACE(code, '81PLUS', 'CAMPUS')");
$pdo->exec("UPDATE courses SET code = REPLACE(code, 'ELITE81', 'ELITE')");
$pdo->exec("UPDATE courses SET code = REPLACE(code, 'PRO81', 'PRO')");
$pdo->exec("UPDATE courses SET code = REPLACE(code, 'BASIC81', 'BASIC')");

echo "3. Rimozione riferimenti 81+ da titoli e descrizioni...\n";
$pdo->exec("UPDATE courses SET title = REPLACE(title, '81PLUS', 'CAMPUS')");
$pdo->exec("UPDATE courses SET title = REPLACE(title, '81+', 'CAMPUS')");
$pdo->exec("UPDATE courses SET description = REPLACE(description, '81PLUS', 'CAMPUS')");
$pdo->exec("UPDATE courses SET description = REPLACE(description, '81+', 'CAMPUS')");
$pdo->exec("UPDATE courses SET description = REPLACE(description, 'ACADEMYCAMPUS', 'CAMPUS ACADEMY')");
$pdo->exec("UPDATE courses SET description = REPLACE(description, 'MASTERCAMPUS', 'CAMPUS MASTER')");
$pdo->exec("UPDATE courses SET description = REPLACE(description, 'SCHOOLCAMPUS', 'CAMPUS SCHOOL')");

echo "4. Verifica conteggio scuole dopo pulizia:\n";
$stmt = $pdo->query("SELECT school, count(*) as cnt FROM courses GROUP BY school");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "   - {$row['school']}: {$row['cnt']} corsi\n";
}

$tot = $pdo->query("SELECT count(*) FROM courses WHERE title LIKE '%81+%' OR code LIKE '%81+%' OR description LIKE '%81+%' OR school LIKE '%81+%'")->fetchColumn();
echo "Totale riferimenti 81+ residui nel DB: {$tot}\n";
