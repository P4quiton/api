<?php

session_start();

if (isset($_SESSION["access_token"])) {
    header("Location: dashboard.php");
    exit;
}

$error = $_SESSION["login_error"] ?? null;
unset($_SESSION["login_error"]);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login API v2</title>
</head>
<body>

    <h1>Iniciar sesión</h1>

    <?php if ($error): ?>
        <p style="color:red;">
            <?php echo htmlspecialchars($error); ?>
        </p>
    <?php endif; ?>

    <form action="autenticar.php" method="POST">

        <label for="username">Usuario:</label>
        <input
            type="text"
            id="username"
            name="username"
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

        <button type="submit">Iniciar sesión</button>

    </form>

</body>
</html>