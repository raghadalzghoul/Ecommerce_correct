<?php

require_once __DIR__ . '/../Core/Database.php';

class Cart
{
    private PDO $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function addItem(
        int $userId,
        int $variantId,
        int $quantity
    ): void {

        $this->db->beginTransaction();

        try {
            // Check variant, stock and product status
            $sql = "SELECT pv.quantity
                    FROM product_variants pv
                    JOIN products p
                        ON p.product_id = pv.product_id
                    WHERE pv.variant_id = :variant_id
                    AND p.is_deleted = FALSE
                    FOR UPDATE";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'variant_id' => $variantId
            ]);

            $variant = $stmt->fetch();

            if (!$variant) {
                throw new DomainException(
                    'Variant not found or product deleted'
                );
            }

            // Get or create user's cart
            $sql = "INSERT INTO cart (user_id)
                    VALUES (:user_id)
                    ON DUPLICATE KEY UPDATE
                        cart_id = LAST_INSERT_ID(cart_id)";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'user_id' => $userId
            ]);

            $cartId = (int) $this->db->lastInsertId();

            // Check if variant is already in cart
            $sql = "SELECT quantity
                    FROM cart_items
                    WHERE cart_id = :cart_id
                    AND variant_id = :variant_id
                    FOR UPDATE";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'cart_id' => $cartId,
                'variant_id' => $variantId
            ]);

            $existing = $stmt->fetch();

            $currentQuantity = $existing
                ? (int) $existing['quantity']
                : 0;

            $newQuantity = $currentQuantity + $quantity;

            if ($newQuantity > (int) $variant['quantity']) {
                throw new DomainException(
                    'Not enough stock available'
                );
            }

            // Insert or increase quantity
            $sql = "INSERT INTO cart_items
                        (cart_id, variant_id, quantity)
                    VALUES
                        (:cart_id, :variant_id, :quantity)
                    ON DUPLICATE KEY UPDATE
                        quantity = quantity + VALUES(quantity)";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                'cart_id' => $cartId,
                'variant_id' => $variantId,
                'quantity' => $quantity
            ]);

            $this->db->commit();

        } catch (Throwable $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }







    public function getCartItems(int $userId): array
{
    $sql = "SELECT
                ci.cart_item_id,
                p.product_id,
                p.name,
                p.image_url,
                pv.variant_id,
                pv.size,
                pv.color,
                p.price,
                ci.quantity,
                (p.price * ci.quantity) AS subtotal

            FROM cart c

            JOIN cart_items ci
                ON c.cart_id = ci.cart_id

            JOIN product_variants pv
                ON ci.variant_id = pv.variant_id

            JOIN products p
                ON pv.product_id = p.product_id

            WHERE c.user_id = :user_id
            AND p.is_deleted = FALSE

            ORDER BY ci.cart_item_id DESC";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'user_id' => $userId
    ]);

    return $stmt->fetchAll();
}









public function updateItemQuantity(
    int $userId,
    int $cartItemId,
    int $quantity
): bool {

    $this->db->beginTransaction();

    try {

        $sql = "SELECT
                    ci.cart_item_id,
                    pv.quantity AS stock_quantity

                FROM cart_items ci

                JOIN cart c
                    ON ci.cart_id = c.cart_id

                JOIN product_variants pv
                    ON ci.variant_id = pv.variant_id

                JOIN products p
                    ON pv.product_id = p.product_id

                WHERE ci.cart_item_id = :cart_item_id
                AND c.user_id = :user_id
                AND p.is_deleted = FALSE

                FOR UPDATE";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'cart_item_id' => $cartItemId,
            'user_id' => $userId
        ]);

        $item = $stmt->fetch();

        if (!$item) {
            $this->db->rollBack();
            return false;
        }

        if ($quantity > (int) $item['stock_quantity']) {
            throw new DomainException(
                'Not enough stock available'
            );
        }

        $sql = "UPDATE cart_items
                SET quantity = :quantity
                WHERE cart_item_id = :cart_item_id";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'quantity' => $quantity,
            'cart_item_id' => $cartItemId
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



public function removeItem(
    int $userId,
    int $cartItemId
): bool {

    $sql = "DELETE ci
            FROM cart_items ci

            JOIN cart c
                ON ci.cart_id = c.cart_id

            WHERE ci.cart_item_id = :cart_item_id
            AND c.user_id = :user_id";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'cart_item_id' => $cartItemId,
        'user_id' => $userId
    ]);

    return $stmt->rowCount() > 0;
}


}