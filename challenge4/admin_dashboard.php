<?php
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/auth.php';

session_start();
requireLogin();

try {
    $stmt = $pdo->query("
        SELECT
            o.id          AS order_id,
            o.qty,
            o.total,
            o.status,
            o.created_at,
            u.name        AS user_name,
            p.name        AS product_name
        FROM orders o
        LEFT JOIN users u    ON u.id = o.user_id
        LEFT JOIN products p ON p.id = o.product_id
        ORDER BY o.id DESC
    ");
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("[ADMIN DASHBOARD ERROR] " . $e->getMessage());
    http_response_code(500);
    echo "Sistem kami lagi bermasalah. Coba lagi nanti, ya!";
    exit();
}
?>
<?php foreach ($orders as $order): ?>
    <?php
        $displayUserName    = $order['user_name']    ?? '[User sudah dihapus]';
        $displayProductName = $order['product_name'] ?? '[Produk ini sudah dihapus oleh Admin]';
    ?>
    <div>
        <span><?= htmlspecialchars($displayUserName, ENT_QUOTES, 'UTF-8'); ?></span>
        -
        <span><?= htmlspecialchars($displayProductName, ENT_QUOTES, 'UTF-8'); ?></span>
        -
        <span>Rp <?= number_format((float) $order['total'], 0, ',', '.'); ?></span>
        -
        <span><?= htmlspecialchars($order['status'] ?? 'pending', ENT_QUOTES, 'UTF-8'); ?></span>
    </div>
<?php endforeach; ?>
