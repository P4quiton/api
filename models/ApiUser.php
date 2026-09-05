<?php

class ApiUser
{
    private $conn;
    private $table_name = "api_users";

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

    public function read()
    {
        $query = "SELECT
                    id,
                    username,
                    email,
                    status,
                    created_at,
                    updated_at
                FROM " . $this->table_name . "
                ORDER BY id ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function create($username, $email, $password, $status = 'ACTIVE')
    {
        $query = "INSERT INTO " . $this->table_name . "
                (username, email, password_hash, status)
                VALUES (:username, :email, :password_hash, :status)";

        $stmt = $this->conn->prepare($query);

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password_hash", $passwordHash);
        $stmt->bindParam(":status", $status);

        return $stmt->execute();
    }

    public function readOne($id)
    {
        $query = "SELECT
                    id,
                    username,
                    email,
                    status,
                    created_at,
                    updated_at
                FROM " . $this->table_name . "
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $username, $email, $status)
    {
        $query = "UPDATE " . $this->table_name . "
                SET username = :username,
                    email = :email,
                    status = :status
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . "
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }
}