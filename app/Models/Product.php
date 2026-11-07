<?php

require_once __DIR__ . '/../Core/Database.php';

class Product
{
    private PDO $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }


    public function getAllProducts(): array
{
        $sql = "SELECT * FROM products
            WHERE is_deleted = FALSE";

    $stmt = $this->db->prepare($sql);

    $stmt->execute();

    return $stmt->fetchAll();
}



public function getProductById(int $id): array|false
{
    $sql = "SELECT * FROM products
            WHERE product_id = :id
            AND is_deleted = FALSE";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'id' => $id
    ]);

    return $stmt->fetch();
}




public function getProductsByCategory(int $catId): array
{
      $sql = "SELECT * FROM products
            WHERE cat_id = :cat_id
            AND is_deleted = FALSE";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'cat_id' => $catId
    ]);

    return $stmt->fetchAll();
}


public function searchProducts(string $keyword): array
{
     $sql = "SELECT * FROM products
            WHERE name LIKE :keyword
            AND is_deleted = FALSE";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'keyword' => '%' . $keyword . '%'
    ]);

    return $stmt->fetchAll();
}


public function getProductVariants(int $productId): array
{
      $sql = "SELECT pv.variant_id,
                   pv.product_id,
                   pv.size,
                   pv.color,
                   pv.quantity
            FROM product_variants pv

            JOIN products p
                ON pv.product_id = p.product_id

            WHERE pv.product_id = :product_id
            AND p.is_deleted = FALSE";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'product_id' => $productId
    ]);

    return $stmt->fetchAll();
}







public function createVariant(
    int $productId,
    string $size,
    string $color,
    int $quantity
): int {

    // Check if product exists and is not deleted
    $sql = "SELECT product_id
            FROM products
            WHERE product_id = :product_id
              AND is_deleted = FALSE";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'product_id' => $productId
    ]);

    if (!$stmt->fetch()) {
        throw new DomainException(
            'Product not found or deleted'
        );
    }

    // Check if same size and color already exist
    $sql = "SELECT variant_id
            FROM product_variants
            WHERE product_id = :product_id
              AND size = :size
              AND color = :color";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'product_id' => $productId,
        'size' => $size,
        'color' => $color
    ]);

    if ($stmt->fetch()) {
        throw new DomainException(
            'This variant already exists'
        );
    }

    // Insert new variant
    $sql = "INSERT INTO product_variants
                (product_id, size, color, quantity)
            VALUES
                (:product_id, :size, :color, :quantity)";

    $stmt = $this->db->prepare($sql);

    try {
        $stmt->execute([
            'product_id' => $productId,
            'size' => $size,
            'color' => $color,
            'quantity' => $quantity
        ]);
    } catch (PDOException $e) {
        // MySQL duplicate key error
        if ($e->getCode() === '23000') {
            throw new DomainException(
                'This variant already exists',
                0,
                $e
            );
        }

        throw $e;
    }

    return (int) $this->db->lastInsertId();
}




public function updateVariantQuantity(
    int $variantId,
    int $quantity
): bool {

    $sql = "UPDATE product_variants
            SET quantity = :quantity
            WHERE variant_id = :variant_id";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'quantity' => $quantity,
        'variant_id' => $variantId
    ]);

    // rowCount can be 0 if the value was unchanged.
    // Check whether the variant actually exists.
    $check = $this->db->prepare(
        "SELECT variant_id
         FROM product_variants
         WHERE variant_id = :variant_id"
    );

    $check->execute([
        'variant_id' => $variantId
    ]);

    return (bool) $check->fetch();
}






public function createProduct(
    string $name,
    string $description,
    float $price,
    int $catId,
    ?string $imageUrl
): int {

    $sql = "INSERT INTO products
            (name, description, price, cat_id, image_url)
            VALUES
            (:name, :description, :price, :cat_id, :image_url)";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'name' => $name,
        'description' => $description,
        'price' => $price,
        'cat_id' => $catId,
        'image_url' => $imageUrl
    ]);

    return (int) $this->db->lastInsertId();
}







public function updateProduct(
    int $id,
    string $name,
    string $description,
    float $price,
    int $catId,
    ?string $imageUrl
): bool {

    $sql = "UPDATE products
            SET name = :name,
                description = :description,
                price = :price,
                cat_id = :cat_id,
                image_url = :image_url
            WHERE product_id = :id
AND is_deleted = FALSE";

    $stmt = $this->db->prepare($sql);

    return $stmt->execute([
        'id' => $id,
        'name' => $name,
        'description' => $description,
        'price' => $price,
        'cat_id' => $catId,
        'image_url' => $imageUrl
    ]);
}





public function deleteProduct(int $id): bool
{
    $sql = "UPDATE products
            SET is_deleted = TRUE
            WHERE product_id = :id
            AND is_deleted = FALSE";

    $stmt = $this->db->prepare($sql);

    $stmt->execute(['id' => $id]);

    return $stmt->rowCount() > 0;
}









public function restoreProduct(int $id): bool
{
    $sql = "UPDATE products
            SET is_deleted = FALSE
            WHERE product_id = :id
            AND is_deleted = TRUE";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'id' => $id
    ]);

    return $stmt->rowCount() > 0;
}


}