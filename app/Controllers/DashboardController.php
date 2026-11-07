
<?php

require_once __DIR__ . '/../Models/Dashboard.php';

class DashboardController
{
    private Dashboard $dashboard;

    public function __construct()
    {
        $this->dashboard = new Dashboard();
    }

    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);

            echo json_encode([
                'success' => false,
                'message' => 'Please login first'
            ]);
            return;
        }

        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);

            echo json_encode([
                'success' => false,
                'message' => 'Admin access required'
            ]);
            return;
        }

        try {
            $statistics = $this->dashboard->getStatistics();

            http_response_code(200);

            echo json_encode([
                'success' => true,
                'statistics' => $statistics
            ]);

        } catch (Throwable $e) {
            error_log($e->getMessage());

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch dashboard statistics'
            ]);
        }
    }
}
