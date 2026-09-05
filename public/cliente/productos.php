<?php

session_start();

require_once __DIR__ . '/api_client.php';

if (!isset($_SESSION["access_token"])) {
    header("Location: login.php");
    exit;
}

$token = $_SESSION["access_token"];

$response = apiRequest(
    '/productos',
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

$productos = $response["data"] ?? [];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos</title>
</head>
<body>

<h1>Productos</h1>

<p>
    <a href="dashboard.php">Volver</a>
</p>

<p>
    <a href="producto_crear.php">Crear producto</a>
</p>

<?php if (empty($productos)): ?>

    <p>No hay productos registrados.</p>

<?php else: ?>

<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>SKU</th>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Precio</th>
        <th>Stock</th>
        <th>Acciones</th>
    </tr>

    <?php foreach ($productos as $producto): ?>

    <tr>
        <td><?php echo htmlspecialchars($producto["id"]); ?></td>
        <td><?php echo htmlspecialchars($producto["sku"]); ?></td>
        <td><?php echo htmlspecialchars($producto["name"]); ?></td>
        <td><?php echo htmlspecialchars($producto["description"]); ?></td>
        <td><?php echo htmlspecialchars($producto["price"]); ?></td>
        <td><?php echo htmlspecialchars($producto["stock"]); ?></td>

        <td>
            <a href="producto_editar.php?id=<?php echo $producto["id"]; ?>">
                Editar
            </a>

            |

            <a href="producto_eliminar.php?id=<?php echo $producto["id"]; ?>">
                Eliminar
            </a>
        </td>
    </tr>

    <?php endforeach; ?>

</table>

<?php endif; ?>

</body>
</html>