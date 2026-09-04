<?php

class ApiUser
{
    private $conn;
    private $table_name = "api_users";

    public $id;
    public $username;
    public $email;
    public $password_hash;
    public $status;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function findByUsername($username)
    {
        $query = "SELECT id, username, email, password_hash, status
                  FROM " . $this->table_name . "
                  WHERE username = :username
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}