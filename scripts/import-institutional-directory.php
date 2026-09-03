<?php

require_once __DIR__ . '/../../public_html/includes/config.php';
require_once __DIR__ . '/../../public_html/includes/database.php';

$pdo = Database::getConnection();

echo "Creazione tabella institutional_directory...\n";

$pdo->exec("
CREATE TABLE IF NOT EXISTS institutional_directory (
    id TEXT PRIMARY KEY,
    category_group TEXT NOT NULL,
    entity_type TEXT NOT NULL,
    organization_name TEXT NOT NULL,
    acronym TEXT,
    sector TEXT,
    contact_role TEXT,
    email TEXT,
    pec TEXT,
    phone TEXT,
    website TEXT,
    territory_level TEXT,
    region TEXT,
    province TEXT,
    address TEXT,
    description TEXT,
    status TEXT DEFAULT 'ATTIVO',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_inst_group ON institutional_directory(category_group);
CREATE INDEX IF NOT EXISTS idx_inst_type ON institutional_directory(entity_type);
CREATE INDEX IF NOT EXISTS idx_inst_email ON institutional_directory(email);
CREATE INDEX IF NOT EXISTS idx_inst_territory ON institutional_directory(territory_level);
CREATE INDEX IF NOT EXISTS idx_inst_province ON institutional_directory(province);
");

$jsonPath = __DIR__ . '/../data/institutional-directory.json';
if (!file_exists($jsonPath)) {
    die("File non trovato: {$jsonPath}\n");
}

$records = json_decode(file_get_contents($jsonPath), true);
if (!is_array($records)) {
    die("Errore decodifica JSON\n");
}

$stmt = $pdo->prepare("
INSERT OR REPLACE INTO institutional_directory 
(id, category_group, entity_type, organization_name, acronym, sector, contact_role, email, pec, phone, website, territory_level, region, province, address, description, status)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$count = 0;
$pdo->beginTransaction();

foreach ($records as $r) {
    $stmt->execute([
        $r['id'],
        $r['category_group'],
        $r['entity_type'],
        $r['organization_name'],
        $r['acronym'] ?? '',
        $r['sector'] ?? '',
        $r['contact_role'] ?? '',
        $r['email'] ?? '',
        $r['pec'] ?? '',
        $r['phone'] ?? '',
        $r['website'] ?? '',
        $r['territory_level'] ?? '',
        $r['region'] ?? '',
        $r['province'] ?? '',
        $r['address'] ?? '',
        $r['description'] ?? '',
        $r['status'] ?? 'ATTIVO'
    ]);
    $count++;
}

$pdo->commit();

echo "COMPLETATO! Inseriti {$count} contatti qualificati in institutional_directory!\n";

$res = $pdo->query("SELECT category_group, entity_type, count(*) as c FROM institutional_directory GROUP BY category_group, entity_type")->fetchAll(PDO::FETCH_ASSOC);
print_r($res);
