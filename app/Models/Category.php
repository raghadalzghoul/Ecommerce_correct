<?php

require_once __DIR__ . '/../Core/Database.php';

class Category
{
    private PDO $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

 public function createCategory(
    string $name,
    string $description,
    string $imageUrl
): int {

    $sql = "INSERT INTO categories
                (cat_name, cat_description, image_url)
            VALUES
                (:name, :description, :image_url)";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'name' => $name,
        'description' => $description,
        'image_url' => $imageUrl
    ]);

    return (int) $this->db->lastInsertId();
}















public function getAllCategories(): array
{
    $sql = "SELECT
                cat_id,
                cat_name,
                cat_description,
                image_url
            FROM categories
            ORDER BY cat_id ASC";

    $stmt = $this->db->prepare($sql);

    $stmt->execute();

    return $stmt->fetchAll();
}

















public function getCategoryById(int $categoryId): array|false
{
    $sql = "SELECT
                cat_id,
                cat_name,
                cat_description,
                image_url
            FROM categories
            WHERE cat_id = :cat_id";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'cat_id' => $categoryId
    ]);

    return $stmt->fetch();
}





public function updateCategory(
    int $categoryId,
    string $name,
    string $description,
    string $imageUrl
): void {

    $sql = "UPDATE categories
            SET cat_name = :name,
                cat_description = :description,
                image_url = :image_url
            WHERE cat_id = :cat_id";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'name' => $name,
        'description' => $description,
        'image_url' => $imageUrl,
        'cat_id' => $categoryId
    ]);
}










public function deleteCategory(int $categoryId): bool
{
    $this->db->beginTransaction();

    try {

        // 1. Check if category exists
        $sql = "SELECT cat_id
                FROM categories
                WHERE cat_id = :cat_id
                FOR UPDATE";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'cat_id' => $categoryId
        ]);

        if (!$stmt->fetch()) {
            $this->db->rollBack();
            return false;
        }

        // 2. Check if category has products
        $sql = "SELECT COUNT(*)
                FROM products
                WHERE cat_id = :cat_id";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'cat_id' => $categoryId
        ]);

        $productCount = (int) $stmt->fetchColumn();

        if ($productCount > 0) {
            throw new DomainException(
                'Cannot delete category with existing products'
            );
        }

        // 3. Delete category
        $sql = "DELETE FROM categories
                WHERE cat_id = :cat_id";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'cat_id' => $categoryId
        ]);

        $this->db->commit();

        return true;

    } catch (PDOException $e) {

        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }

        // Foreign key constraint
        if (
            $e->getCode() === '23000' ||
            $e->getCode() === '23001'
        ) {
            throw new DomainException(
                'Cannot delete category with existing products',
                0,
                $e
            );
        }

        throw $e;

    } catch (Throwable $e) {

        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }

        throw $e;
    }
}



}