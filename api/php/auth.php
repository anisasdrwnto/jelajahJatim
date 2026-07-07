<?php
function authenticate($pdo) {
    $headers = getallheaders();
    
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['message' => 'Token tidak ditemukan']);
        exit;
    }

    $token = str_replace('Bearer ', '', $headers['Authorization']);

    $stmt = $pdo->prepare("SELECT * FROM api_tokens WHERE token = ?");
    $stmt->execute([$token]);
    $row = $stmt->fetch();

    if (!$row) {
        http_response_code(401);
        echo json_encode(['message' => 'Token tidak valid']);
        exit;
    }

    return $row;
}