<?php
header('Content-Type: application/json');

require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/rate_limiter.php';
require __DIR__ . '/includes/auth.php';

session_start();
requireLogin();
rateLimit('update_profile', 10, 60);

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(["message" => "Request harus pakai method PUT!"]);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
    http_response_code(400);
    echo json_encode(["message" => "Format JSON yang kamu kirim rusak!"]);
    exit();
}

$id    = (int) $_SESSION['user_data']['id']; 
$name  = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');

if ($name === '' || $email === '') {
    http_response_code(400);
    echo json_encode(["message" => "Nama dan email wajib diisi."]);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["message" => "Format email tidak valid."]);
    exit();
}

try {
    $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
    $stmt->execute([
        'name'  => $name,
        'email' => $email,
        'id'    => $id,
    ]);

    refreshUserSession($id, $pdo);

    echo json_encode(["message" => "Berhasil"]);
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        http_response_code(409);
        echo json_encode(["message" => "Email sudah dipakai akun lain."]);
        exit();
    }

    error_log("[UPDATE PROFILE ERROR] " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["message" => "Sistem kami lagi bermasalah. Coba lagi nanti, ya!"]);
}
