<?php

session_start();

require_once __DIR__ . '/api_client.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

if ($username === "" || $password === "") {
    $_SESSION["login_error"] = "Todos los campos son obligatorios.";
    header("Location: login.php");
    exit;
}

$response = apiRequest(
    '/login',
    'POST',
    [
        'username' => $username,
        'password' => $password
    ]
);


if (
    $response["status"] === 200 &&
    isset($response["data"]["access_token"])
) {
    session_regenerate_id(true);

    $_SESSION["access_token"] = $response["data"]["access_token"];
    $_SESSION["token_type"] = $response["data"]["token_type"] ?? "Bearer";
    $_SESSION["expires_at"] = $response["data"]["expires_at"] ?? null;

    header("Location: dashboard.php");
    exit;
}

$_SESSION["login_error"] =
    $response["data"]["message"]
    ?? "No se pudo iniciar sesión.";

header("Location: login.php");
exit;