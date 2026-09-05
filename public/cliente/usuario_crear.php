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

    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $status = $_POST["status"] ?? "ACTIVE";

    if (
        $username === "" ||
        $email === "" ||
        $password === ""
    ) {
        $error = "Completa todos los campos obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El correo electrónico no es válido.";
    } else {

        $response = apiRequest(
            '/users',
            'POST',
            [
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'status' => $status
            ],
            $token
        );

        if ($response["status"] === 201) {
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
            ?? "No se pudo crear el usuario.";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear usuario API</title>
</head>
<body>

<h1>Crear usuario API</h1>

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
        required
    >

    <br><br>

    <label for="email">Email:</label>
    <input
        type="email"
        id="email"
        name="email"
        required
    >

    <br><br>

    <label for="password">Contraseña:</label>
    <input
        type="password"
        id="password"
        name="password"
        required
    >

    <br><br>

    <label for="status">Status:</label>
    <select id="status" name="status">
        <option value="ACTIVE">ACTIVE</option>
        <option value="INACTIVE">INACTIVE</option>
    </select>

    <br><br>

    <button type="submit">Crear usuario</button>

</form>

</body>
</html>