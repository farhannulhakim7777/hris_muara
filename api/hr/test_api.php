<?php
/**
 * Test API untuk mengecek error
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/bootstrap.php';

echo json_encode([
    'success' => true,
    'message' => 'API Test OK',
    'session' => [
        'loggedin' => $_SESSION['loggedin'] ?? false,
        'user_id' => $_SESSION['user_id'] ?? null,
        'role' => $_SESSION['role'] ?? null
    ]
]);
?>
