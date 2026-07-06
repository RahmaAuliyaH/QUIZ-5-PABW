<?php
require 'config/db.php';
$hash = password_hash('123', PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->execute(['Rahma', 'rahma@gmail.com', $hash]);
echo "User berhasil dibuat!";