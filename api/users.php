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

// Función para verificar token
function verifyToken() {
    $headers = apache_request_headers();
    if (!isset($headers['Authorization'])) {
        Response::error("Token requerido", 401);
    }

    $token = str_replace('Bearer ', '', $headers['Authorization']);
    $decoded = JWT::decode($token);
    
    if (!$decoded || $decoded['exp'] < time()) {
        Response::error("Token inválido o expirado", 401);
    }

    return $decoded;
}

switch($method) {
    case 'POST':
        // Crear usuario
        $data = json_decode(file_get_contents("php://input"), true);
        
        $required = ['nombres', 'apellidos', 'email', 'ciudad', 'pais', 'programa', 'semestre', 'username', 'password', 'tipo_usuario'];
        $missing = Validator::required($required, $data);
        if (!empty($missing)) {
            Response::error("Campos requeridos: " . implode(', ', $missing));
        }

        if (!Validator::email($data['email'])) {
            Response::error("Email inválido");
        }

        $user_id = $user->create($data);
        if ($user_id) {
            // Crear cuenta DUTS
            $duts_account->createAccount($user_id);
            Response::success("Usuario creado exitosamente", ['user_id' => $user_id]);
        } else {
            Response::error("Error al crear usuario", 500);
        }
        break;

    case 'GET':
        $token = verifyToken();
        
        if (isset($path_parts[2]) && $path_parts[2] == 'profile') {
            // Ver perfil
            $user_id = $path_parts[3] ?? $token['user_id'];
            $profile = $user->getProfile($user_id);
            
            if ($profile) {
                Response::success("Perfil obtenido", $profile);
            } else {
                Response::error("Usuario no encontrado", 404);
            }
        } else {
            // Obtener usuario por ID
            $user_id = $path_parts[2] ?? $token['user_id'];
            $user_data = $user->getById($user_id);
            
            if ($user_data) {
                Response::success("Usuario obtenido", $user_data);
            } else {
                Response::error("Usuario no encontrado", 404);
            }
        }
        break;

    case 'PUT':
        $token = verifyToken();
        $user_id = $path_parts[2] ?? $token['user_id'];
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        if ($user->update($user_id, $data)) {
            Response::success("Usuario actualizado exitosamente");
        } else {
            Response::error("Error al actualizar usuario", 500);
        }
        break;

    case 'DELETE':
        $token = verifyToken();
        $user_id = $path_parts[2] ?? $token['user_id'];
        
        if ($user->delete($user_id)) {
            Response::success("Usuario eliminado exitosamente");
        } else {
            Response::error("Error al eliminar usuario", 500);
        }
        break;

    default:
        Response::error("Método no permitido", 405);
}
?>