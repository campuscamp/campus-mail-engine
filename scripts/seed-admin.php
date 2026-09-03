<?php
require_once __DIR__ . '/../public_html/includes/database.php';
$db = Database::getConnection();
$hash = password_hash('mirco', PASSWORD_DEFAULT);
$stmt = $db->prepare("INSERT OR REPLACE INTO admins (id, username, password_hash, full_name, email, role) VALUES (1, 'mirco', ?, 'Mirco Pregnolato', 'info@campus.camp', 'SUPER_ADMIN')");
$stmt->execute([$hash]);
echo "Admin 'mirco' / 'mirco' configurato con successo!\n";
