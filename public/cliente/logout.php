<?php

session_start();

require_once __DIR__ . '/api_client.php';

if (isset($_SESSION["access_token"])) {
    $token = $_SESSION["access_token"];

    apiRequest(
        '/logout',
        'POST',
        null,
        $token
    );
}

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        "",
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

header("Location: login.php");
exit;