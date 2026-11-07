
<?php

require_once __DIR__ . '/../Core/Database.php';

class Dashboard
{
    private PDO $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function getStatistics(): array
    {
        $sql = "
            SELECT
                (
                    SELECT COALESCE(SUM(total_amount), 0)
                    FROM orders
                    WHERE status = 'delivered'
                ) AS total_revenue,

                (
                    SELECT COUNT(*)
                    FROM orders
                ) AS orders_count,

                (
                    SELECT COUNT(*)
                    FROM products
                    WHERE is_deleted = 0
                ) AS product_count,

                (
                    SELECT COUNT(*)
                    FROM users
                    WHERE role = 'user'
                ) AS customers_count
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
