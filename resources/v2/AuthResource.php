<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/ApiUser.php';
require_once __DIR__ . '/../../models/ApiToken.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';

class AuthResource
{
    private $db;
    private $apiUser;
    private $apiToken;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();

        $this->apiUser = new ApiUser($this->db);
        $this->apiToken = new ApiToken($this->db);
    }

    public function login()
    {
        header("Content-Type: application/json");

        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->username) || empty($data->password)) {
            http_response_code(400);

            echo json_encode([
                "error" => "bad_request",
                "message" => "Datos incompletos"
            ]);

            return;
        }

        $user = $this->apiUser->findByUsername($data->username);

        if (
            !$user ||
            $user["status"] !== "ACTIVE" ||
            !password_verify($data->password, $user["password_hash"])
        ) {
            http_response_code(401);

            echo json_encode([
                "error" => "invalid_credentials",
                "message" => "Usuario o contraseña incorrectos"
            ]);

            return;
        }

        // Invalidar tokens anteriores
        $this->apiToken->revokeTokensByUser($user["id"]);

        // Token aleatorio de 32 bytes = 64 caracteres hex
        $token = bin2hex(random_bytes(32));

        // Expira en 60 minutos
        $expiresAt = date(
            "Y-m-d H:i:s",
            strtotime("+60 minutes")
        );

        $created = $this->apiToken->create(
            $user["id"],
            $token,
            $expiresAt
        );

        if (!$created) {
            http_response_code(500);

            echo json_encode([
                "error" => "server_error",
                "message" => "No se pudo generar el token"
            ]);

            return;
        }

        http_response_code(200);

        echo json_encode([
            "access_token" => $token,
            "token_type" => "Bearer",
            "expires_at" => $expiresAt
        ]);
    }

    public function logout()
    {

        $authUser = AuthMiddleware::validate();

        $query = "UPDATE api_tokens
                SET revoked = TRUE
                WHERE token = :token";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":token", $authUser["token"]);

        if ($stmt->execute()) {
            http_response_code(200);

            echo json_encode([
                "message" => "Logout exitoso"
            ]);

            return;
        }

        http_response_code(500);

        echo json_encode([
            "error" => "server_error",
            "message" => "No se pudo cerrar la sesión"
        ]);
    }
}