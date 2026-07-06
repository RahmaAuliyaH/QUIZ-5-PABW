<?php
function requireLogin(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['login']) || empty($_SESSION['user_data'])) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(["message" => "Kamu harus login dulu untuk mengakses ini."]);
        exit();
    }
}

function refreshUserSession(int $userId, PDO $pdo): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['login']     = true;
        $_SESSION['user_data'] = $user;
    }
}
