<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/ApiUser.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';

class UserResourceV2
{
    private $db;
    private $apiUser;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->apiUser = new ApiUser($this->db);
    }

    // GET /api/v2/users
    public function index()
    {
        $authUser = AuthMiddleware::validate();

        header("Content-Type: application/json");

        $stmt = $this->apiUser->read();

        $users = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $row;
        }

        http_response_code(200);

        echo json_encode([
            "records" => $users
        ]);
    }

    // GET /api/v2/users/{id}
    public function show($id)
    {
        $authUser = AuthMiddleware::validate();

        header("Content-Type: application/json");

        $usuario = $this->apiUser->readOne($id);

        if ($usuario) {
            http_response_code(200);
            echo json_encode($usuario);
            return;
        }

        http_response_code(404);

        echo json_encode([
            "message" => "Usuario API no encontrado"
        ]);
    }

    // POST /api/v2/users
    public function store()
    {
        $authUser = AuthMiddleware::validate();

        header("Content-Type: application/json");

        $data = json_decode(file_get_contents("php://input"));

        if (
            empty($data->username) ||
            empty($data->email) ||
            empty($data->password)
        ) {
            http_response_code(400);

            echo json_encode([
                "message" => "Datos incompletos"
            ]);

            return;
        }

        $status = $data->status ?? "ACTIVE";

        if (!in_array($status, ["ACTIVE", "INACTIVE"])) {
            http_response_code(400);

            echo json_encode([
                "message" => "Status inválido"
            ]);

            return;
        }

        try {
            $created = $this->apiUser->create(
                $data->username,
                $data->email,
                $data->password,
                $status
            );

            if ($created) {
                http_response_code(201);

                echo json_encode([
                    "message" => "Usuario API creado exitosamente"
                ]);

                return;
            }

            http_response_code(500);

            echo json_encode([
                "message" => "No se pudo crear el usuario"
            ]);

        } catch (PDOException $e) {
            http_response_code(409);

            echo json_encode([
                "message" => "El username o email ya existe"
            ]);
        }
    }

    // PUT /api/v2/users/{id}
    public function update($id)
    {
        $authUser = AuthMiddleware::validate();

        header("Content-Type: application/json");

        $data = json_decode(file_get_contents("php://input"));

        if (
            empty($data->username) ||
            empty($data->email) ||
            empty($data->status)
        ) {
            http_response_code(400);

            echo json_encode([
                "message" => "Datos incompletos"
            ]);

            return;
        }

        if (!in_array($data->status, ["ACTIVE", "INACTIVE"])) {
            http_response_code(400);

            echo json_encode([
                "message" => "Status inválido"
            ]);

            return;
        }

        try {
            $updated = $this->apiUser->update(
                $id,
                $data->username,
                $data->email,
                $data->status
            );

            if ($updated) {
                http_response_code(200);

                echo json_encode([
                    "message" => "Usuario API actualizado exitosamente"
                ]);

                return;
            }

            http_response_code(500);

            echo json_encode([
                "message" => "No se pudo actualizar el usuario"
            ]);

        } catch (PDOException $e) {
            http_response_code(409);

            echo json_encode([
                "message" => "El username o email ya existe"
            ]);
        }
    }

    // DELETE /api/v2/users/{id}
    public function destroy($id)
    {
        $authUser = AuthMiddleware::validate();

        header("Content-Type: application/json");

        $usuario = $this->apiUser->readOne($id);

        if (!$usuario) {
            http_response_code(404);

            echo json_encode([
                "message" => "Usuario API no encontrado"
            ]);

            return;
        }

        if ($this->apiUser->delete($id)) {
            http_response_code(200);

            echo json_encode([
                "message" => "Usuario API eliminado exitosamente"
            ]);

            return;
        }

        http_response_code(500);

        echo json_encode([
            "message" => "No se pudo eliminar el usuario"
        ]);
    }
}
?>