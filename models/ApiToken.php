<?php

class ApiToken
{
    private $conn;
    private $table_name = "api_tokens";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function revokeTokensByUser($userId)
    {
        $query = "UPDATE " . $this->table_name . "
                  SET revoked = TRUE
                  WHERE user_id = :user_id
                  AND revoked = FALSE";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);

        return $stmt->execute();
    }

    public function create($userId, $token, $expiresAt)
    {
        $query = "INSERT INTO " . $this->table_name . "
                  (user_id, token, expires_at, revoked)
                  VALUES (:user_id, :token, :expires_at, FALSE)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":token", $token);
        $stmt->bindParam(":expires_at", $expiresAt);

        return $stmt->execute();
    }

    public function revokeByToken($token)
    {
        $query = "UPDATE " . $this->table_name . "
                SET revoked = TRUE
                WHERE token = :token";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);

        return $stmt->execute();
    }

    public function findWithUser($token)
    {
        $query = "SELECT
                    t.id AS token_id,
                    t.user_id,
                    t.token,
                    t.expires_at,
                    t.revoked,
                    u.username,
                    u.email,
                    u.status
                FROM " . $this->table_name . " t
                INNER JOIN api_users u
                    ON t.user_id = u.id
                WHERE t.token = :token
                LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}