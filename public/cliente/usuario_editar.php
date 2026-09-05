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
    header("Location: usuarios.php");
    exit;
}

// Obtener usuario actual
$response = apiRequest(
    '/users/' . $id,
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
    die("Usuario no encontrado.");
}

$usuario = $response["data"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $status = $_POST["status"] ?? "";

    if (
        $username === "" ||
        $email === "" ||
        $status === ""
    ) {
        $error = "Completa todos los campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El correo electrónico no es válido.";
    } else {

        $response = apiRequest(
            '/users/' . $id,
            'PUT',
            [
                'username' => $username,
                'email' => $email,
                'status' => $status
            ],
            $token
        );

        if ($response["status"] === 200) {
            header("Location: usuarios.php");
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
            ?? "No se pudo actualizar el usuario.";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar usuario API</title>
</head>
<body>

<h1>Editar usuario API</h1>

<p>
    <a href="usuarios.php">Volver</a>
</p>

<?php if ($error): ?>
    <p style="color:red;">
        <?php echo htmlspecialchars($error); ?>
    </p>
<?php endif; ?>

<form method="POST">

    <label for="username">Username:</label>
    <input
        type="text"
        id="username"
        name="username"
        value="<?php echo htmlspecialchars($usuario["username"]); ?>"
        required
    >

    <br><br>

    <label for="email">Email:</label>
    <input
        type="email"
        id="email"
        name="email"
        value="<?php echo htmlspecialchars($usuario["email"]); ?>"
        required
    >

    <br><br>

    <label for="status">Status:</label>
    <select id="status" name="status">

        <option
            value="ACTIVE"
            <?php echo $usuario["status"] === "ACTIVE" ? "selected" : ""; ?>
        >
            ACTIVE
        </option>

        <option
            value="INACTIVE"
            <?php echo $usuario["status"] === "INACTIVE" ? "selected" : ""; ?>
        >
            INACTIVE
        </option>

    </select>

    <br><br>

    <button type="submit">Actualizar usuario</button>

</form>

</body>
</html>