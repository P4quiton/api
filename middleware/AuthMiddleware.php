<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ApiToken.php';

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

        $authorization =
            $headers["Authorization"]
            ?? $headers["authorization"];

        if (!preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
            self::unauthorized();
        }

        $token = $matches[1];

        $database = new Database();
        $db = $database->getConnection();

        $apiToken = new ApiToken($db);

        $auth = $apiToken->findWithUser($token);

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