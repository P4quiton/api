<?php

session_start();

require_once __DIR__ . '/api_client.php';

if (!isset($_SESSION["access_token"])) {
    header("Location: login.php");
    exit;
}

$token = $_SESSION["access_token"];

$response = apiRequest(
    '/me',
    'GET',
    null,
    $token
);

if ($response["status"] !== 200) {
    $_SESSION = [];
    session_destroy();

    header("Location: login.php");
    exit;
}

$usuario = $response["data"];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard API v2</title>
</head>
<body>

    <h1>Dashboard</h1>

    <p>
        Bienvenido,
        <strong>
            <?php echo htmlspecialchars($usuario["username"]); ?>
        </strong>
    </p>

    <p>
        Correo:
        <?php echo htmlspecialchars($usuario["email"]); ?>
    </p>

    <hr>

    <p>
        <a href="logout.php">Cerrar sesión</a>
    </p>

    <p>
        <a href="productos.php">Administrar productos</a>
    </p>

    <p>
        <a href="usuarios.php">Administrar usuarios</a>
    </p>
</body>
</html>