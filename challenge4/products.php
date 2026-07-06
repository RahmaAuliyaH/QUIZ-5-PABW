<?php
require __DIR__ . '/config/db.php';

try {
    $stmt = $pdo->query("SELECT id, name, price FROM products ORDER BY id ASC");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("[PRODUCTS ERROR] " . $e->getMessage());
    http_response_code(500);
    echo "Sistem kami lagi bermasalah. Coba lagi nanti, ya!";
    exit();
}
?>
<?php foreach ($products as $row): ?>
<div>
    <h3><?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
    <p>Rp <?= number_format((float) $row['price'], 0, ',', '.'); ?></p>
</div>
<?php endforeach; ?>
