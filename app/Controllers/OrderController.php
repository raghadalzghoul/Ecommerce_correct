<?php

require_once __DIR__ . '/../Models/Order.php';

class OrderController
{
    private Order $order;

    public function __construct()
    {
        $this->order = new Order();
    }

    public function place(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check login
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);

            echo json_encode([
                'success' => false,
                'message' => 'Please login first'
            ]);
            return;
        }

        // Read JSON body
        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (!is_array($data)) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid JSON body'
            ]);
            return;
        }

        $address = trim(
            (string) ($data['shipping_address'] ?? '')
        );

        $paymentMethod = $data['payment_method'] ?? '';

        if ($address === '' || strlen($address) > 1000) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid shipping address'
            ]);
            return;
        }

        if ($paymentMethod !== 'cod') {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Only COD is supported'
            ]);
            return;
        }

        try {

            $result = $this->order->placeOrder(
                (int) $_SESSION['user_id'],
                $address
            );

            http_response_code(201);

            echo json_encode([
                'success' => true,
                'message' => 'Order placed successfully',
                'order' => $result
            ]);

        } catch (DomainException $e) {

            http_response_code(409);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);

        } catch (Throwable $e) {

            error_log($e->getMessage());

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => 'Failed to place order'
            ]);
        }
    }










    public function myOrders(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);

        echo json_encode([
            'success' => false,
            'message' => 'Please login first'
        ]);

        return;
    }

    try {

        $orders = $this->order->getOrdersByUser(
            (int) $_SESSION['user_id']
        );

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'orders' => $orders
        ]);

    } catch (Throwable $e) {

        error_log($e->getMessage());

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Failed to fetch orders'
        ]);
    }
}






public function show(): void
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

    $orderId = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );

    if (!$orderId || $orderId <= 0) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid order ID'
        ]);
        return;
    }

    try {
        $order = $this->order->getOrderById(
            $orderId,
            (int) $_SESSION['user_id']
        );

        if (!$order) {
            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'Order not found'
            ]);
            return;
        }

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'order' => $order
        ]);

    } catch (Throwable $e) {
        error_log($e->getMessage());

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Failed to fetch order details'
        ]);
    }
}






public function adminShow(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);

        echo json_encode([
            'success' => false,
            'message' => 'Please login first'
        ]);

        return;
    }

    // Check if user is admin
    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);

        echo json_encode([
            'success' => false,
            'message' => 'Admin access required'
        ]);

        return;
    }

    // Validate order ID
    $orderId = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );

    if (!$orderId || $orderId <= 0) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid order ID'
        ]);

        return;
    }

    try {
        $order = $this->order->getAdminOrderById($orderId);

        if (!$order) {
            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'Order not found'
            ]);

            return;
        }

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'order' => $order
        ]);

    } catch (Throwable $e) {
        error_log($e->getMessage());

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Failed to fetch order details'
        ]);
    }
}







public function adminUpdateStatus(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check authentication
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);

        echo json_encode([
            'success' => false,
            'message' => 'Please login first'
        ]);
        return;
    }

    // Admin only
    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);

        echo json_encode([
            'success' => false,
            'message' => 'Admin access required'
        ]);
        return;
    }

    // Validate order ID
    $orderId = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );

    if (!$orderId || $orderId <= 0) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid order ID'
        ]);
        return;
    }

    // Read JSON body
    $data = json_decode(
        file_get_contents('php://input'),
        true
    );

    if (!is_array($data)) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid JSON body'
        ]);
        return;
    }

    $newStatus = $data['status'] ?? null;

    if (
        !is_string($newStatus) ||
        !in_array(
            $newStatus,
            ['processing', 'shipped', 'delivered'],
            true
        )
    ) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid status'
        ]);
        return;
    }

    try {

        $updated = $this->order->updateOrderStatus(
            $orderId,
            $newStatus
        );

        if (!$updated) {
            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'Order not found'
            ]);
            return;
        }

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'message' => 'Order status updated successfully',
            'order_id' => $orderId,
            'status' => $newStatus
        ]);

    } catch (DomainException $e) {

        http_response_code(409);

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);

    } catch (Throwable $e) {

        error_log($e->getMessage());

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Failed to update order status'
        ]);
    }
}







public function adminIndex(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check login
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);

        echo json_encode([
            'success' => false,
            'message' => 'Please login first'
        ]);
        return;
    }

    // Check admin role
    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);

        echo json_encode([
            'success' => false,
            'message' => 'Admin access required'
        ]);
        return;
    }

    try {
        $orders = $this->order->getAllOrders();

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'orders' => $orders
        ]);

    } catch (Throwable $e) {
        error_log($e->getMessage());

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Failed to fetch orders'
        ]);
    }
}












public function adminCancel(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // 1. Check login
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);

        echo json_encode([
            'success' => false,
            'message' => 'Please login first'
        ]);
        return;
    }

    // 2. Admin only
    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);

        echo json_encode([
            'success' => false,
            'message' => 'Admin access required'
        ]);
        return;
    }

    // 3. Validate order ID
    $orderId = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );

    if (!$orderId || $orderId <= 0) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid order ID'
        ]);
        return;
    }

    try {

        $cancelled = $this->order->cancelOrder($orderId);

        if (!$cancelled) {
            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'Order not found'
            ]);
            return;
        }

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'order_id' => $orderId,
            'status' => 'cancelled'
        ]);

    } catch (DomainException $e) {

        http_response_code(409);

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);

    } catch (Throwable $e) {

        error_log($e->getMessage());

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Failed to cancel order'
        ]);
    }
}


}