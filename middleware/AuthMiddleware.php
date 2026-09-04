<?php

require_once __DIR__ . '/../config/database.php';

class AuthMiddleware
{
    public static function validate()
    {
        header("Content-Type: application/json");

        $headers = getallheaders();

        if (
            !isset($headers["Authorization"]) &&
            !isset($headers["authorization"])
        ) {
            self::unauthorized();
        }

        $authorization = $headers["Authorization"]
            ?? $headers["authorization"];

        if (!preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
            self::unauthorized();
        }

        $token = $matches[1];

        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT
                    t.id AS token_id,
                    t.user_id,
                    t.token,
                    t.expires_at,
                    t.revoked,
                    u.username,
                    u.email,
                    u.status
                  FROM api_tokens t
                  INNER JOIN api_users u
                    ON t.user_id = u.id
                  WHERE t.token = :token
                  LIMIT 1";

        $stmt = $db->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();

        $auth = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$auth) {
            self::unauthorized();
        }

        if ((int)$auth["revoked"] === 1) {
            self::unauthorized();
        }

        if ($auth["status"] !== "ACTIVE") {
            self::unauthorized();
        }

        if (strtotime($auth["expires_at"]) <= time()) {
            self::unauthorized();
        }

        return [
            "id" => $auth["user_id"],
            "username" => $auth["username"],
            "email" => $auth["email"],
            "token" => $token
        ];
    }

    private static function unauthorized()
    {
        http_response_code(401);

        echo json_encode([
            "error" => "unauthorized",
            "message" => "Token inválido, expirado o no proporcionado"
        ]);

        exit;
    }
}