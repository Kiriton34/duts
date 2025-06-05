<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/DutsAccount.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../utils/JWT.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$duts_account = new DutsAccount($db);

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path_parts = explode('/', trim($path, '/'));

switch($method) {
    case 'POST':
        if (end($path_parts) == 'login') {
            $data = json_decode(file_get_contents("php://input"), true);
            
            $required = ['username', 'password'];
            $missing = Validator::required($required, $data);
            if (!empty($missing)) {
                Response::error("Campos requeridos: " . implode(', ', $missing));
            }

            $user_data = $user->login($data['username'], $data['password']);
            if ($user_data) {
                $token = JWT::encode([
                    'user_id' => $user_data['id'],
                    'username' => $user_data['username'],
                    'tipo_usuario' => $user_data['tipo_usuario'],
                    'exp' => time() + (24 * 60 * 60) // 24 horas
                ]);

                Response::success("Login exitoso", [
                    'user' => $user_data,
                    'token' => $token
                ]);
            } else {
                Response::error("Credenciales inválidas", 401);
            }
        }
        break;

    default:
        Response::error("Método no permitido", 405);
}
?>