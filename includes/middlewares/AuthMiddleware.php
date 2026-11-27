<?php

require_once __DIR__ . '/MiddlewareInterface.php';

class AuthMiddleware implements MiddlewareInterface
{
    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function handle(): void
    {
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            // Jika request adalah API, kembalikan HTTP 401 Unauthorized JSON
            if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }

            header('Location: ' . $this->basePath . '/login');
            exit;
        }
    }
}