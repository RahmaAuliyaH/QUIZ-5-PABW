<?php
header('Content-Type: application/json');

require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/rate_limiter.php';
require __DIR__ . '/includes/auth.php';

session_start();
requireLogin(); 

rateLimit('order', 20, 60);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Request harus pakai method POST!"]);
    exit();
}

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
        http_response_code(400);
        echo json_encode(["message" => "Format JSON yang kamu kirim rusak!"]);
        exit();
    }
} else {
    $data = $_POST;
}

$userId    = (int) $_SESSION['user_data']['id']; // dari session, bukan dari client
$productId = isset($data['product_id']) ? (int) $data['product_id'] : 0;
$qty       = isset($data['qty']) ? (int) $data['qty'] : 0;

if ($productId <= 0 || $qty <= 0) {
    http_response_code(400);
    echo json_encode(["message" => "product_id dan qty wajib diisi dengan angka valid."]);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT id, price FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if (!$product) {
        http_response_code(404);
        echo json_encode(["message" => "Produk tidak ditemukan."]);
        exit();
    }

    $totalHargaServer = $product['price'] * $qty;

    $insert = $pdo->prepare("
        INSERT INTO orders (user_id, product_id, qty, total, status)
        VALUES (:user_id, :product_id, :qty, :total, 'pending')
    ");
    $insert->execute([
        'user_id'    => $userId,
        'product_id' => $productId,
        'qty'        => $qty,
        'total'      => $totalHargaServer,
    ]);

    echo json_encode([
        "message" => "Order berhasil",
        "total"   => $totalHargaServer,
        "status"  => "pending",
    ]);
} catch (PDOException $e) {
    error_log("[ORDER ERROR] " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["message" => "Sistem kami lagi bermasalah. Coba lagi nanti, ya!"]);
}
