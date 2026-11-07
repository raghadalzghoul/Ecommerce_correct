<?php

class Database{
    private PDO $connection;
    public function __construct(){

    $config = parse_ini_file(
        __DIR__ .'/../../.env'
    );

      if($config===false){
        throw new RuntimeException(
            "DateBase configuretion not found"

        );
        
    }

    $host=$config['DB_HOST'];
    $port=$config['DB_PORT'];
    $dbname = $config['DB_NAME'];
    $username = $config['DB_USER'];
    $password = $config['DB_PASS'];

     try {
            $this->connection = new PDO(
                "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );

        } catch (PDOException $e) {
            error_log($e->getMessage());

            throw new RuntimeException(
                "Database connection failed.",
                0,
                $e
            );
        }
    }

    public function connect(): PDO
    {
        return $this->connection;
    }
}