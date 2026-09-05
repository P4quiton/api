<?php

session_start();

require_once __DIR__ . '/api_client.php';

if (!isset($_SESSION["access_token"])) {
    header("Location: login.php");
    exit;
}

$token = $_SESSION["access_token"];
$id = $_GET["id"] ?? null;
$error = null;

if (!$id) {
    header("Location: productos.php");
    exit;
}

// Obtener producto actual
$response = apiRequest(
    '/productos/' . $id,
    'GET',
    null,
    $token
);

if ($response["status"] === 401) {
    $_SESSION = [];
    session_destroy();

    header("Location: login.php");
    exit;
}

if ($response["status"] !== 200) {
    die("Producto no encontrado.");
}

$producto = $response["data"];

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
            '/productos/' . $id,
            'PUT',
            [
                'sku' => $sku,
                'name' => $name,
                'description' => $description,
                'price' => (float)$price,
                'stock' => (int)$stock
            ],
            $token
        );

        if ($response["status"] === 200) {
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
            ?? "No se pudo actualizar el producto.";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar producto</title>
</head>
<body>

<h1>Editar producto</h1>

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
        value="<?php echo htmlspecialchars($producto["sku"]); ?>"
        required
    >

    <br><br>

    <label for="name">Nombre:</label>
    <input
        type="text"
        id="name"
        name="name"
        value="<?php echo htmlspecialchars($producto["name"]); ?>"
        required
    >

    <br><br>

    <label for="description">Descripción:</label>
    <textarea
        id="description"
        name="description"
    ><?php echo htmlspecialchars($producto["description"]); ?></textarea>

    <br><br>

    <label for="price">Precio:</label>
    <input
        type="number"
        id="price"
        name="price"
        step="0.01"
        min="0"
        value="<?php echo htmlspecialchars($producto["price"]); ?>"
        required
    >

    <br><br>

    <label for="stock">Stock:</label>
    <input
        type="number"
        id="stock"
        name="stock"
        min="0"
        value="<?php echo htmlspecialchars($producto["stock"]); ?>"
        required
    >

    <br><br>

    <button type="submit">Actualizar producto</button>

</form>

</body>
</html>