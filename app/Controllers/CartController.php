<?php

require_once __DIR__ . '/../Models/Cart.php';

class CartController
{
    private Cart $cart;

    public function __construct()
    {
        $this->cart = new Cart();
    }

    public function add(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // User must be logged in
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

        // Validate inputs
        $variantId = filter_var(
            $data['variant_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        $quantity = filter_var(
            $data['quantity'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (
            $variantId === false || $variantId === null ||
            $quantity === false || $quantity === null ||
            $variantId <= 0 || $quantity <= 0
        ) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid variant ID or quantity'
            ]);
            return;
        }

        try {
            $this->cart->addItem(
                (int) $_SESSION['user_id'],
                $variantId,
                $quantity
            );

            http_response_code(200);

            echo json_encode([
                'success' => true,
                'message' => 'Product added to cart successfully'
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
                'message' => 'Failed to add product to cart'
            ]);
        }
    }







    public function index(): void
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

    try {
        $items = $this->cart->getCartItems(
            (int) $_SESSION['user_id']
        );

        $total = 0;

        foreach ($items as $item) {
            $total += (float) $item['subtotal'];
        }

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'items' => $items,
            'total' => number_format($total, 2, '.', '')
        ]);

    } catch (PDOException $e) {
        error_log($e->getMessage());

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Failed to fetch cart'
        ]);
    }
}





public function update(): void
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

    // Get cart item ID from URL
    $cartItemId = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );

    if (!$cartItemId || $cartItemId <= 0) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid cart item ID'
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

    // Validate quantity
    $quantity = filter_var(
        $data['quantity'] ?? null,
        FILTER_VALIDATE_INT
    );

    if (
        $quantity === false ||
        $quantity === null ||
        $quantity <= 0
    ) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Quantity must be greater than zero'
        ]);
        return;
    }

    try {

        $updated = $this->cart->updateItemQuantity(
            (int) $_SESSION['user_id'],
            $cartItemId,
            $quantity
        );

        if (!$updated) {
            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'Cart item not found'
            ]);
            return;
        }

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'message' => 'Cart quantity updated successfully'
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
            'message' => 'Failed to update cart quantity'
        ]);
    }
}


public function remove(): void
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

    // Get cart item ID from URL
    $cartItemId = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );

    if (!$cartItemId || $cartItemId <= 0) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid cart item ID'
        ]);
        return;
    }

    try {

        $removed = $this->cart->removeItem(
            (int) $_SESSION['user_id'],
            $cartItemId
        );

        if (!$removed) {
            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'Cart item not found'
            ]);
            return;
        }

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'message' => 'Item removed from cart successfully'
        ]);

    } catch (Throwable $e) {

        error_log($e->getMessage());

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Failed to remove cart item'
        ]);
    }
}








}