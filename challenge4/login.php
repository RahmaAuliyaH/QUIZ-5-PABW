<?php
header('Content-Type: application/json');

require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/rate_limiter.php';
require __DIR__ . '/includes/auth.php';

session_start();

rateLimit('login', 5, 60);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Request harus pakai method POST!"]);
    exit();
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(["message" => "Email dan password wajib diisi."]);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["message" => "Format email tidak valid."]);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        refreshUserSession((int) $user['id'], $pdo);
        echo json_encode(["message" => "Login berhasil"]);
    } else {

        http_response_code(401);
        echo json_encode(["message" => "Email atau password salah."]);
    }
} catch (PDOException $e) {
    error_log("[LOGIN ERROR] " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["message" => "Sistem kami lagi bermasalah. Coba lagi nanti, ya!"]);
}
