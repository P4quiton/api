<?php

session_start();

require_once __DIR__ . '/api_client.php';

if (!isset($_SESSION["access_token"])) {
    header("Location: login.php");
    exit;
}

$token = $_SESSION["access_token"];

$response = apiRequest(
    '/users',
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

$usuarios = $response["data"]["records"] ?? [];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios</title>
</head>
<body>

<h1>Usuarios</h1>

<p>
    <a href="dashboard.php">Volver</a>
</p>

<p>
    <a href="usuario_crear.php">Crear usuario</a>
</p>

<?php if (empty($usuarios)): ?>

    <p>No hay usuarios registrados.</p>

<?php else: ?>

<table border="1" cellpadding="8">

    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Status</th>
        <th>Creado</th>
        <th>Actualizado</th>
        <th>Acciones</th>
    </tr>

    <?php foreach ($usuarios as $usuario): ?>

    <tr>
        <td><?php echo htmlspecialchars($usuario["id"]); ?></td>
        <td><?php echo htmlspecialchars($usuario["username"]); ?></td>
        <td><?php echo htmlspecialchars($usuario["email"]); ?></td>
        <td><?php echo htmlspecialchars($usuario["status"]); ?></td>
        <td><?php echo htmlspecialchars($usuario["created_at"]); ?></td>
        <td><?php echo htmlspecialchars($usuario["updated_at"]); ?></td>

        <td>
            <a href="usuario_editar.php?id=<?php echo $usuario["id"]; ?>">
                Editar
            </a>

            |

            <a href="usuario_eliminar.php?id=<?php echo $usuario["id"]; ?>">
                Eliminar
            </a>
        </td>
    </tr>

    <?php endforeach; ?>

</table>

<?php endif; ?>

</body>
</html>