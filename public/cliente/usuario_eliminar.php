<?php

session_start();

require_once __DIR__ . '/api_client.php';

if (!isset($_SESSION["access_token"])) {
    header("Location: login.php");
    exit;
}

$token = $_SESSION["access_token"];
$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: usuarios.php");
    exit;
}

$response = apiRequest(
    '/users/' . $id,
    'DELETE',
    null,
    $token
);

if ($response["status"] === 401) {
    $_SESSION = [];
    session_destroy();

    header("Location: login.php");
    exit;
}

header("Location: usuarios.php");
exit;