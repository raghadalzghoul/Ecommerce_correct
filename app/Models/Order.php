<?php

require_once __DIR__ . '/../Core/Database.php';

class Order
{
    private PDO $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function placeOrder(
        int $userId,
        string $shippingAddress
    ): array {

        $this->db->beginTransaction();

        try {

            // 1. Lock user's cart
            $stmt = $this->db->prepare(
                "SELECT cart_id
                 FROM cart
                 WHERE user_id = :user_id
                 FOR UPDATE"
            );

            $stmt->execute(['user_id' => $userId]);
            $cart = $stmt->fetch();

            if (!$cart) {
                throw new DomainException('Cart is empty');
            }

            $cartId = (int) $cart['cart_id'];

            // 2. Get items and lock stock rows
            $stmt = $this->db->prepare(
                "SELECT
                    ci.variant_id,
                    ci.quantity,
                    pv.quantity AS stock,
                    p.price,
                    p.is_deleted
                 FROM cart_items ci
                 JOIN product_variants pv
                    ON ci.variant_id = pv.variant_id
                 JOIN products p
                    ON pv.product_id = p.product_id
                 WHERE ci.cart_id = :cart_id
                 ORDER BY ci.variant_id
                 FOR UPDATE"
            );

            $stmt->execute(['cart_id' => $cartId]);
            $items = $stmt->fetchAll();

            if (!$items) {
                throw new DomainException('Cart is empty');
            }

            // 3. Validate products and stock
            // Calculate total using integer cents
            $totalCents = 0;

            foreach ($items as $item) {

                if ((int) $item['is_deleted'] === 1) {
                    throw new DomainException(
                        'Cart contains a deleted product'
                    );
                }

                if (
                    (int) $item['quantity'] >
                    (int) $item['stock']
                ) {
                    throw new DomainException(
                        'Not enough stock for variant ' .
                        $item['variant_id']
                    );
                }

                $price = (string) $item['price'];

                // Prices are stored as DECIMAL(10,2)
                if (!preg_match(
                    '/^\d+(\.\d{1,2})?$/',
                    $price
                )) {
                    throw new RuntimeException(
                        'Invalid stored price'
                    );
                }

                [$whole, $fraction] = array_pad(
                    explode('.', $price, 2),
                    2,
                    '0'
                );

                $priceCents =
                    ((int) $whole * 100) +
                    (int) str_pad($fraction, 2, '0');

                $totalCents +=
                    $priceCents * (int) $item['quantity'];
            }

            $total = sprintf(
                '%d.%02d',
                intdiv($totalCents, 100),
                $totalCents % 100
            );

            // 4. Create order
            $stmt = $this->db->prepare(
                "INSERT INTO orders
                    (user_id, total_amount, status,
                     payment_method, shipping_address)
                 VALUES
                    (:user_id, :total_amount, 'pending',
                     'cod', :shipping_address)"
            );

            $stmt->execute([
                'user_id' => $userId,
                'total_amount' => $total,
                'shipping_address' => $shippingAddress
            ]);

            $orderId = (int) $this->db->lastInsertId();

            // 5. Save order items
            $insertItem = $this->db->prepare(
                "INSERT INTO order_items
                    (order_id, variant_id, quantity, unit_price)
                 VALUES
                    (:order_id, :variant_id, :quantity, :unit_price)"
            );

            // 6. Decrease stock
            $updateStock = $this->db->prepare(
                "UPDATE product_variants
                 SET quantity = quantity - :quantity
                 WHERE variant_id = :variant_id
                   AND quantity >= :required_quantity"
            );

            foreach ($items as $item) {

                $insertItem->execute([
                    'order_id' => $orderId,
                    'variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price']
                ]);

                $updateStock->execute([
                    'quantity' => $item['quantity'],
                    'variant_id' => $item['variant_id'],
                    'required_quantity' => $item['quantity']
                ]);

                if ($updateStock->rowCount() !== 1) {
                    throw new DomainException(
                        'Stock changed during checkout'
                    );
                }
            }

            // 7. Clear cart
            $stmt = $this->db->prepare(
                "DELETE FROM cart_items
                 WHERE cart_id = :cart_id"
            );

            $stmt->execute(['cart_id' => $cartId]);

            // 8. Commit everything
            $this->db->commit();

            return [
                'order_id' => $orderId,
                'total_amount' => $total,
                'status' => 'pending',
                'payment_method' => 'cod'
            ];

        } catch (Throwable $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }






    public function getOrdersByUser(int $userId): array
{
    // 1. Get user's orders
    $sql = "SELECT
                order_id,
                total_amount,
                status,
                payment_method,
                shipping_address,
                created_at
            FROM orders
            WHERE user_id = :user_id
            ORDER BY created_at DESC, order_id DESC";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'user_id' => $userId
    ]);

    $orders = $stmt->fetchAll();

    // 2. Get items for each order
    $itemsSql = "SELECT
                    oi.variant_id,
                    p.product_id,
                    p.name,
                    p.image_url,
                    pv.size,
                    pv.color,
                    oi.quantity,
                    oi.unit_price,
                    (oi.quantity * oi.unit_price) AS subtotal

                FROM order_items oi

                JOIN product_variants pv
                    ON oi.variant_id = pv.variant_id

                JOIN products p
                    ON pv.product_id = p.product_id

                WHERE oi.order_id = :order_id";

    $itemsStmt = $this->db->prepare($itemsSql);

    foreach ($orders as &$order) {

        $itemsStmt->execute([
            'order_id' => $order['order_id']
        ]);

        $order['items'] = $itemsStmt->fetchAll();
    }

    unset($order);

    return $orders;
}






public function getOrderById(int $orderId, int $userId): array|false
{
    // Get order only if it belongs to the logged-in user
    $sql = "SELECT
                order_id,
                total_amount,
                status,
                payment_method,
                shipping_address,
                created_at
            FROM orders
            WHERE order_id = :order_id
              AND user_id = :user_id";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'order_id' => $orderId,
        'user_id' => $userId
    ]);

    $order = $stmt->fetch();

    if (!$order) {
        return false;
    }

    // Get items belonging to this order
    $sql = "SELECT
                oi.order_item_id,
                oi.variant_id,
                p.product_id,
                p.name,
                p.image_url,
                pv.size,
                pv.color,
                oi.quantity,
                oi.unit_price,
                (oi.quantity * oi.unit_price) AS subtotal
            FROM order_items oi
            JOIN product_variants pv
                ON oi.variant_id = pv.variant_id
            JOIN products p
                ON pv.product_id = p.product_id
            WHERE oi.order_id = :order_id
            ORDER BY oi.order_item_id";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'order_id' => $orderId
    ]);

    $order['items'] = $stmt->fetchAll();

    return $order;
}





public function getAdminOrderById(int $orderId): array|false
{
    // 1. Get order information with customer details
    $sql = "SELECT
                o.order_id,
                o.user_id,
                u.name AS customer_name,
                u.email AS customer_email,
                u.phone AS customer_phone,
                o.total_amount,
                o.status,
                o.payment_method,
                o.shipping_address,
                o.created_at

            FROM orders o

            JOIN users u
                ON o.user_id = u.user_id

            WHERE o.order_id = :order_id";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'order_id' => $orderId
    ]);

    $order = $stmt->fetch();

    if (!$order) {
        return false;
    }

    // 2. Get products inside this order
    $itemsSql = "SELECT
                    oi.order_item_id,
                    oi.variant_id,
                    p.product_id,
                    p.name AS product_name,
                    p.image_url,
                    pv.size,
                    pv.color,
                    oi.quantity,
                    oi.unit_price,
                    (oi.quantity * oi.unit_price) AS subtotal

                FROM order_items oi

                JOIN product_variants pv
                    ON oi.variant_id = pv.variant_id

                JOIN products p
                    ON pv.product_id = p.product_id

                WHERE oi.order_id = :order_id

                ORDER BY oi.order_item_id";

    $itemsStmt = $this->db->prepare($itemsSql);

    $itemsStmt->execute([
        'order_id' => $orderId
    ]);

    $order['items'] = $itemsStmt->fetchAll();

    return $order;
}





public function getAllOrders(): array
{
    $sql = "SELECT
                o.order_id,
                o.user_id,
                u.name AS customer_name,
                u.email AS customer_email,
                o.total_amount,
                o.status,
                o.payment_method,
                o.shipping_address,
                o.created_at

            FROM orders o

            JOIN users u
                ON o.user_id = u.user_id

            ORDER BY o.created_at DESC, o.order_id DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
}




public function updateOrderStatus(
    int $orderId,
    string $newStatus
): bool {

    $allowedTransitions = [
        'pending' => 'processing',
        'processing' => 'shipped',
        'shipped' => 'delivered'
    ];

    $this->db->beginTransaction();

    try {

        // Lock order to prevent concurrent updates
        $sql = "SELECT status
                FROM orders
                WHERE order_id = :order_id
                FOR UPDATE";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'order_id' => $orderId
        ]);

        $order = $stmt->fetch();

        if (!$order) {
            $this->db->rollBack();
            return false;
        }

        $currentStatus = $order['status'];

        // Validate status transition
        if (
            !isset($allowedTransitions[$currentStatus]) ||
            $allowedTransitions[$currentStatus] !== $newStatus
        ) {
            throw new DomainException(
                'Invalid order status transition'
            );
        }

        // Update order status
        $sql = "UPDATE orders
                SET status = :status
                WHERE order_id = :order_id";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'status' => $newStatus,
            'order_id' => $orderId
        ]);

        $this->db->commit();

        return true;

    } catch (Throwable $e) {

        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }

        throw $e;
    }
}





public function cancelOrder(int $orderId): bool
{
    $this->db->beginTransaction();

    try {

        // 1. Lock the order
        $sql = "SELECT status
                FROM orders
                WHERE order_id = :order_id
                FOR UPDATE";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'order_id' => $orderId
        ]);

        $order = $stmt->fetch();

        if (!$order) {
            $this->db->rollBack();
            return false;
        }

        // 2. Check whether cancellation is allowed
        if (!in_array(
            $order['status'],
            ['pending', 'processing'],
            true
        )) {
            throw new DomainException(
                'This order cannot be cancelled'
            );
        }

        // 3. Get ordered variants and quantities
        $sql = "SELECT variant_id, quantity
                FROM order_items
                WHERE order_id = :order_id
                ORDER BY variant_id";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'order_id' => $orderId
        ]);

        $items = $stmt->fetchAll();

        if (empty($items)) {
            throw new DomainException(
                'Order has no items'
            );
        }

        // 4. Restore inventory
        $sql = "UPDATE product_variants
                SET quantity = quantity + :quantity
                WHERE variant_id = :variant_id";

        $stockStmt = $this->db->prepare($sql);

        foreach ($items as $item) {

            $stockStmt->execute([
                'quantity' => $item['quantity'],
                'variant_id' => $item['variant_id']
            ]);

            if ($stockStmt->rowCount() !== 1) {
                throw new RuntimeException(
                    'Failed to restore inventory'
                );
            }
        }

        // 5. Mark order as cancelled
        $sql = "UPDATE orders
                SET status = 'cancelled'
                WHERE order_id = :order_id";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'order_id' => $orderId
        ]);

        // 6. Save all changes
        $this->db->commit();

        return true;

    } catch (Throwable $e) {

        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }

        throw $e;
    }
}


}