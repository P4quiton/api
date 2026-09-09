<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Tarea.php';

class TareaResource
{
    private $db;
    private $tarea;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->tarea = new Tarea($this->db);
    }

    // GET /api/v3/tareas
    public function index()
    {
        header("Content-Type: application/json");

        $stmt = $this->tarea->read();

        $tareas = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row["id"] = (int) $row["id"];
            $row["completada"] = (bool) $row["completada"];

            $tareas[] = $row;
        }

        http_response_code(200);
        echo json_encode($tareas);
    }

    // GET /api/v3/tareas/{id}
    public function show($id)
    {
        header("Content-Type: application/json");

        $this->tarea->id = $id;

        $tarea = $this->tarea->readOne();

        if (!$tarea) {
            http_response_code(404);

            echo json_encode([
                "message" => "Tarea no encontrada"
            ]);

            return;
        }

        $tarea["id"] = (int) $tarea["id"];
        $tarea["completada"] = (bool) $tarea["completada"];

        http_response_code(200);
        echo json_encode($tarea);
    }

    // POST /api/v3/tareas
    public function store()
    {
        header("Content-Type: application/json");

        $data = json_decode(file_get_contents("php://input"));

        if (
            empty($data->titulo) ||
            !isset($data->completada) ||
            !is_bool($data->completada)
        ) {
            http_response_code(400);

            echo json_encode([
                "message" => "Datos incompletos o inválidos"
            ]);

            return;
        }

        $this->tarea->titulo = $data->titulo;
        $this->tarea->completada = $data->completada;

        if (!$this->tarea->create()) {
            http_response_code(500);

            echo json_encode([
                "message" => "No se pudo crear la tarea"
            ]);

            return;
        }

        $this->tarea->id = $this->tarea->id;
        $tareaCreada = $this->tarea->readOne();

        $tareaCreada["id"] = (int) $tareaCreada["id"];
        $tareaCreada["completada"] = (bool) $tareaCreada["completada"];

        http_response_code(201);
        echo json_encode($tareaCreada);
    }

    // PUT /api/v3/tareas/{id}
    public function update($id)
    {
        header("Content-Type: application/json");

        $this->tarea->id = $id;

        if (!$this->tarea->readOne()) {
            http_response_code(404);

            echo json_encode([
                "message" => "Tarea no encontrada"
            ]);

            return;
        }

        $data = json_decode(file_get_contents("php://input"));

        if (
            empty($data->titulo) ||
            !isset($data->completada) ||
            !is_bool($data->completada)
        ) {
            http_response_code(400);

            echo json_encode([
                "message" => "Datos incompletos o inválidos"
            ]);

            return;
        }

        $this->tarea->titulo = $data->titulo;
        $this->tarea->completada = $data->completada;

        if (!$this->tarea->update()) {
            http_response_code(500);

            echo json_encode([
                "message" => "No se pudo actualizar la tarea"
            ]);

            return;
        }

        $tareaActualizada = $this->tarea->readOne();

        $tareaActualizada["id"] = (int) $tareaActualizada["id"];
        $tareaActualizada["completada"] = (bool) $tareaActualizada["completada"];

        http_response_code(200);
        echo json_encode($tareaActualizada);
    }
}