<?php
require_once 'config/cors.php';
require_once 'utils/Response.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path_parts = explode('/', trim($path, '/'));

// Router básico
if (isset($path_parts[1])) {
    switch($path_parts[1]) {
        case 'auth':
            require_once 'api/auth.php';
            break;
        case 'users':
            require_once 'api/users.php';
            break;
        case 'duts':
            require_once 'api/duts.php';
            break;
        case 'eventos':
            require_once 'api/eventos.php';
            break;
        default:
            Response::error("Endpoint no encontrado", 404);
    }
} else {
    Response::success("API DUTS - Plataforma Financiera Digital UTS", [
        "version" => "1.0.0",
        "endpoints" => [
            "POST /auth/login" => "Iniciar sesión",
            "POST /users" => "Crear usuario",
            "GET /users/{id}" => "Obtener usuario",
            "GET /users/profile/{id}" => "Ver perfil",
            "PUT /users/{id}" => "Actualizar usuario",
            "DELETE /users/{id}" => "Eliminar usuario",
            "GET /duts/balance/{id}" => "Consultar saldo",
            "POST /duts/transfer" => "Transferir DUTS",
            "GET /duts/history/{id}" => "Historial transacciones"
        ]
    ]);
}
?>