<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Method tidak diizinkan']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$stmt = $pdo->prepare("SELECT * FROM mst_users WHERE mus_email = ?");
$stmt->execute([$data['email']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($data['password'], $user['mus_password'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Email atau password salah']);
    exit;
}

$token = bin2hex(random_bytes(32));
$stmt = $pdo->prepare("INSERT INTO api_tokens (user_id, token) VALUES (?, ?)");
$stmt->execute([$user['mus_id_users'], $token]);

echo json_encode(['token' => $token]);