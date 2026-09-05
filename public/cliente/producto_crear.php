<?php

session_start();

require_once __DIR__ . '/api_client.php';

if (!isset($_SESSION["access_token"])) {
    header("Location: login.php");
    exit;
}

$token = $_SESSION["access_token"];
$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $sku = trim($_POST["sku"] ?? "");
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = $_POST["price"] ?? "";
    $stock = $_POST["stock"] ?? "";

    if (
        $sku === "" ||
        $name === "" ||
        $price === "" ||
        $stock === ""
    ) {
        $error = "Completa todos los campos obligatorios.";
    } else {

        $response = apiRequest(
            '/productos',
            'POST',
            [
                'sku' => $sku,
                'name' => $name,
                'description' => $description,
                'price' => (float)$price,
                'stock' => (int)$stock
            ],
            $token
        );

        if ($response["status"] === 201) {
            header("Location: productos.php");
            exit;
        }

        if ($response["status"] === 401) {
            $_SESSION = [];
            session_destroy();

            header("Location: login.php");
            exit;
        }

        $error =
            $response["data"]["message"]
            ?? "No se pudo crear el producto.";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear producto</title>
</head>
<body>

<h1>Crear producto</h1>

<p>
    <a href="productos.php">Volver</a>
</p>

<?php if ($error): ?>

    <p style="color:red;">
        <?php echo htmlspecialchars($error); ?>
    </p>

<?php endif; ?>

<form method="POST">

    <label for="sku">SKU:</label>
    <input
        type="text"
        id="sku"
        name="sku"
        required
    >

    <br><br>

    <label for="name">Nombre:</label>
    <input
        type="text"
        id="name"
        name="name"
        required
    >

    <br><br>

    <label for="description">Descripción:</label>
    <textarea
        id="description"
        name="description"
    ></textarea>

    <br><br>

    <label for="price">Precio:</label>
    <input
        type="number"
        id="price"
        name="price"
        step="0.01"
        min="0"
        required
    >

    <br><br>

    <label for="stock">Stock:</label>
    <input
        type="number"
        id="stock"
        name="stock"
        min="0"
        required
    >

    <br><br>

    <button type="submit">Crear producto</button>

</form>

</body>
</html>