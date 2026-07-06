<?php
header('Content-Type: application/json'); 

require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/auth.php';

session_start();
requireLogin();

$id = (int) $_SESSION['user_data']['id']; 

try {
    $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if (!$data) {
        http_response_code(404);
        echo json_encode(["message" => "Profil tidak ditemukan."]);
        exit();
    }

    echo json_encode([
        "name"  => htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8'),
        "email" => htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8'),
    ]);
} catch (PDOException $e) {
    error_log("[PROFILE ERROR] " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["message" => "Sistem kami lagi bermasalah. Coba lagi nanti, ya!"]);
}
