<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Producto.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';


class ProductoResourceV2
{
    private $db;
    private $producto;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->producto = new Producto($this->db);
    }

    // GET /api/v2/productos
    public function index()
    {
        $authUser = AuthMiddleware::validate();

        header("Content-Type: application/json");

        $stmt = $this->producto->read();

        $productos = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $productos[] = $row;
        }

        http_response_code(200);
        echo json_encode($productos);
    }

    // GET /api/v2/productos/{id}
    public function show($id)
    {
        $authUser = AuthMiddleware::validate();

        header("Content-Type: application/json");

        $this->producto->id = $id;

        $producto = $this->producto->readOne();

        if ($producto) {
            http_response_code(200);
            echo json_encode($producto);
        } else {
            http_response_code(404);
            echo json_encode([
                "message" => "Producto no encontrado"
            ]);
        }
    }

    // POST /api/v2/productos
    public function store()
    {
        $authUser = AuthMiddleware::validate();
        
        header("Content-Type: application/json");

        $data = json_decode(file_get_contents("php://input"));

        if (
            empty($data->sku) ||
            empty($data->name) ||
            !isset($data->price)
        ) {
            http_response_code(400);
            echo json_encode([
                "message" => "Datos incompletos"
            ]);
            return;
        }

        $this->producto->sku = $data->sku;
        $this->producto->name = $data->name;
        $this->producto->description = $data->description ?? "";
        $this->producto->price = $data->price;
        $this->producto->stock = $data->stock ?? 0;

        if ($this->producto->create()) {
            http_response_code(201);

            echo json_encode([
                "message" => "Producto creado",
                "id" => $this->producto->id
            ]);
        } else {
            http_response_code(500);

            echo json_encode([
                "message" => "No se pudo crear el producto"
            ]);
        }
    }

    // PUT /api/v2/productos/{id}
    public function update($id)
    {
        $authUser = AuthMiddleware::validate();

        header("Content-Type: application/json");

        $data = json_decode(file_get_contents("php://input"));

        if (
            empty($data->sku) ||
            empty($data->name) ||
            !isset($data->price)
        ) {
            http_response_code(400);
            echo json_encode([
                "message" => "Datos incompletos"
            ]);
            return;
        }

        $this->producto->id = $id;
        $this->producto->sku = $data->sku;
        $this->producto->name = $data->name;
        $this->producto->description = $data->description ?? "";
        $this->producto->price = $data->price;
        $this->producto->stock = $data->stock ?? 0;

        if ($this->producto->update()) {
            http_response_code(200);
            echo json_encode([
                "message" => "Producto actualizado"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "message" => "No se pudo actualizar el producto"
            ]);
        }
    }

    // DELETE /api/v2/productos/{id}
    public function destroy($id)
    {
        $authUser = AuthMiddleware::validate();

        header("Content-Type: application/json");

        $this->producto->id = $id;

        if ($this->producto->delete()) {
            http_response_code(200);
            echo json_encode([
                "message" => "Producto eliminado"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "message" => "No se pudo eliminar el producto"
            ]);
        }
    }
}
?>
