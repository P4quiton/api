<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . '/../core/Router.php';

$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$basePath = $scriptName;

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// ========================================
// API V2
// ========================================

if (strpos($requestUri, '/api/v2/') !== false) {

    require_once __DIR__ . '/../resources/v2/UserResource.php';
    require_once __DIR__ . '/../resources/v2/ProductoResource.php';
    require_once __DIR__ . '/../resources/v2/AuthResource.php';
    require_once __DIR__ . '/../resources/v2/MeResource.php';

    $router = new Router('v2', $basePath);

    $userResource = new UserResourceV2();
    $productoResource = new ProductoResourceV2();
    $authResource = new AuthResource();
    $meResource = new MeResource();

    // Autenticación
    $router->addRoute('POST', '/login', [$authResource, 'login']);
    $router->addRoute('POST', '/logout', [$authResource, 'logout']);
    $router->addRoute('GET', '/me', [$meResource, 'show']);

    // Usuarios protegidos
    $router->addRoute('GET', '/users', [$userResource, 'index']);
    $router->addRoute('GET', '/users/{id}', [$userResource, 'show']);
    $router->addRoute('POST', '/users', [$userResource, 'store']);
    $router->addRoute('PUT', '/users/{id}', [$userResource, 'update']);
    $router->addRoute('DELETE', '/users/{id}', [$userResource, 'destroy']);

    // Productos protegidos
    $router->addRoute('GET', '/productos', [$productoResource, 'index']);
    $router->addRoute('GET', '/productos/{id}', [$productoResource, 'show']);
    $router->addRoute('POST', '/productos', [$productoResource, 'store']);
    $router->addRoute('PUT', '/productos/{id}', [$productoResource, 'update']);
    $router->addRoute('DELETE', '/productos/{id}', [$productoResource, 'destroy']);

    $router->dispatch();
    exit;
}

// ========================================
// API V1
// ========================================

require_once __DIR__ . '/../resources/v1/UserResource.php';
require_once __DIR__ . '/../resources/v1/ProductoResource.php';
require_once __DIR__ . '/../resources/v1/LoginResource.php';

$router = new Router('v1', $basePath);

$userResource = new UserResource();
$productoResource = new ProductoResource();
$loginResource = new LoginResource();

$router->addRoute('GET', '/users', [$userResource, 'index']);
$router->addRoute('GET', '/users/{id}', [$userResource, 'show']);
$router->addRoute('POST', '/users', [$userResource, 'store']);
$router->addRoute('PUT', '/users/{id}', [$userResource, 'update']);
$router->addRoute('DELETE', '/users/{id}', [$userResource, 'destroy']);

$router->addRoute('GET', '/productos', [$productoResource, 'index']);
$router->addRoute('GET', '/productos/{id}', [$productoResource, 'show']);
$router->addRoute('POST', '/productos', [$productoResource, 'store']);
$router->addRoute('PUT', '/productos/{id}', [$productoResource, 'update']);
$router->addRoute('DELETE', '/productos/{id}', [$productoResource, 'destroy']);

$router->addRoute('POST', '/login', [$loginResource, 'login']);

$router->dispatch();