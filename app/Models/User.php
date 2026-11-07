<?php

require_once __DIR__ . '/../Core/Database.php';

class User {
    private PDO $db;

  public function __construct()
{
    $database = new Database();
    $this->db = $database->connect();
}



public function findByEmail(string $email)
{
   $sql = "SELECT user_id, name, email, PASSWORD AS password, role
        FROM users
        WHERE email = :email";

    $stmt = $this->db->prepare($sql);

    $stmt->execute(['email' => $email]);

    return $stmt->fetch();
}




public function create(
    string $name,
    string $email,
    string $passwordHash,
    ?string $phone
): int {

    $sql = "INSERT INTO users (name, email, password, phone)
            VALUES (:name, :email, :password, :phone)";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'password' => $passwordHash,
        'phone' => $phone
    ]);

    return (int) $this->db->lastInsertId();
}





public function getUserById(int $userId): array|false
{
    $sql = "SELECT
                user_id,
                name,
                email,
                phone,
                role
            FROM users
            WHERE user_id = :user_id";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'user_id' => $userId
    ]);

    return $stmt->fetch();
}



}

?>
