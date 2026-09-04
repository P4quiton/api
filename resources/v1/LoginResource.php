<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';

class LoginResource {

    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function login()
{
    header("Content-Type: application/json");

    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->email) || empty($data->password)) {
        http_response_code(400);
        echo json_encode([
            "message" => "Datos incompletos"
        ]);
        return;
    }

    $query = "SELECT id, nombre, email, password
              FROM usuarios
              WHERE email = :email
              LIMIT 1";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":email", $data->email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($data->password, $user["password"])) {
        http_response_code(401);
        echo json_encode([
            "message" => "Credenciales inválidas"
        ]);
        return;
    }

    http_response_code(200);

    echo json_encode([
        "message" => "Autenticación exitosa",
        "usuario" => [
            "id" => $user["id"],
            "nombre" => $user["nombre"],
            "email" => $user["email"]
        ]
    ]);
}
}
