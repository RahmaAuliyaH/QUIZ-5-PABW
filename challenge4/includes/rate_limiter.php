<?php
function rateLimit(string $key, int $maxRequests = 10, int $perSeconds = 60): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $bucketKey = "ratelimit_{$key}_{$ip}";
    $now = time();

    if (!isset($_SESSION[$bucketKey])) {
        $_SESSION[$bucketKey] = ['count' => 0, 'start' => $now];
    }

    $bucket = $_SESSION[$bucketKey];

    if ($now - $bucket['start'] > $perSeconds) {
        $bucket = ['count' => 0, 'start' => $now];
    }

    $bucket['count']++;
    $_SESSION[$bucketKey] = $bucket;

    if ($bucket['count'] > $maxRequests) {
        http_response_code(429);
        header('Content-Type: application/json');
        echo json_encode(["message" => "Kamu nge-spam! Coba lagi nanti setelah 1 menit."]);
        exit();
    }
}
