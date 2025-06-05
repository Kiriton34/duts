<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/InscripcionEvento.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/JWT.php';

$database = new Database();
$db = $database->getConnection();
$event = new Event($db);
$inscripcion = new InscripcionEvento($db);

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path_parts = explode('/', trim($path, '/'));

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
        // Inscribir usuario a evento
        if (isset($path_parts[2]) && isset($path_parts[3]) && $path_parts[3] == 'inscribir') {
            $token = verifyToken();
            $evento_id = $path_parts[2];
            $user_id = $token['user_id'];
            // Validar si ya está inscrito
            if ($inscripcion->yaInscrito($evento_id, $user_id)) {
                Response::error("Ya estás inscrito en este evento");
            }
            // Validar cupo
            $cupo = $event->getCupoDisponible($evento_id);
            if ($cupo <= 0) {
                Response::error("No hay cupos disponibles para este evento");
            }
            $ok = $inscripcion->inscribir($evento_id, $user_id);
            Response::success("Usuario inscrito al evento", ['evento_id' => $evento_id, 'user_id' => $user_id]);
            break;
        }
        // Darse de baja de evento
        if (isset($path_parts[2]) && isset($path_parts[3]) && $path_parts[3] == 'cancelar') {
            $token = verifyToken();
            $evento_id = $path_parts[2];
            $user_id = $token['user_id'];
            $ok = $inscripcion->cancelar($evento_id, $user_id);
            Response::success("Inscripción cancelada", ['evento_id' => $evento_id, 'user_id' => $user_id]);
            break;
        }
        // Crear evento
        $token = verifyToken();
        $data = json_decode(file_get_contents("php://input"), true);
        $data['organizador_id'] = $token['user_id'];
        $id = $event->create($data);
        Response::success("Evento creado", ['evento_id' => $id]);
        break;

    case 'GET':
        // Listar inscritos a evento
        if (isset($path_parts[2]) && isset($path_parts[3]) && $path_parts[3] == 'inscritos') {
            $evento_id = $path_parts[2];
            $data = $inscripcion->inscritos($evento_id);
            Response::success("Inscritos al evento", $data);
            break;
        }
        // Ver evento por ID
        if (isset($path_parts[2]) && is_numeric($path_parts[2])) {
            $data = $event->getById($path_parts[2]);
            Response::success("Evento obtenido", $data);
            break;
        }
        // Listar eventos (con filtro por tipo)
        $tipo = $_GET['tipo'] ?? null;
        $data = $event->getAll($tipo);
        Response::success("Lista de eventos", $data);
        break;

    case 'PUT':
        $token = verifyToken();
        $id = $path_parts[2] ?? null;
        $data = json_decode(file_get_contents("php://input"), true);
        $ok = $event->update($id, $data);
        Response::success("Evento actualizado", ['evento_id' => $id]);
        break;

    case 'DELETE':
        $token = verifyToken();
        $id = $path_parts[2] ?? null;
        $ok = $event->delete($id);
        Response::success("Evento eliminado", ['evento_id' => $id]);
        break;

    default:
        Response::error("Método no permitido", 405);
}
?>