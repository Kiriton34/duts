<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../models/DutsAccount.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../utils/JWT.php';

$database = new Database();
$db = $database->getConnection();
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
        $token = verifyToken();
        
        if (end($path_parts) == 'transfer') {
            // Transferir DUTS
            $data = json_decode(file_get_contents("php://input"), true);
            
            $required = ['id_destino', 'cantidad'];
            $missing = Validator::required($required, $data);
            if (!empty($missing)) {
                Response::error("Campos requeridos: " . implode(', ', $missing));
            }

            if (!Validator::numeric($data['cantidad']) || $data['cantidad'] <= 0) {
                Response::error("Cantidad inválida");
            }

            try {
                $duts_account->transfer(
                    $token['user_id'], 
                    $data['id_destino'], 
                    $data['cantidad'], 
                    $data['concepto'] ?? ''
                );
                Response::success("Transferencia realizada exitosamente");
            } catch (Exception $e) {
                Response::error($e->getMessage());
            }
        }
        break;

    case 'GET':
        $token = verifyToken();
        
        if (isset($path_parts[2]) && $path_parts[2] == 'balance') {
            // Consultar saldo
            $user_id = $path_parts[3] ?? $token['user_id'];
            $balance = $duts_account->getBalance($user_id);
            
            Response::success("Saldo obtenido", [
                'user_id' => $user_id,
                'saldo_actual' => $balance,
                'necesarios_para_graduarse' => max(0, 100000 - $balance)
            ]);
            
        } elseif (isset($path_parts[2]) && $path_parts[2] == 'history') {
            // Historial de transacciones
            $user_id = $path_parts[3] ?? $token['user_id'];
            $history = $duts_account->getTransactionHistory($user_id);
            
            Response::success("Historial obtenido", $history);
        } elseif (isset($path_parts[2]) && $path_parts[2] == 'stats' && isset($path_parts[3])) {
            $periodo = $path_parts[3];
            $sql = "";
            $param = $periodo;
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $periodo)) { // Día
                $sql = "SELECT fecha, AVG(saldo_final) as promedio_saldo FROM estadisticas_duts WHERE fecha = ? GROUP BY fecha";
            } elseif (preg_match('/^\d{4}-\d{2}$/', $periodo)) { // Mes
                $sql = "SELECT DATE_FORMAT(fecha, '%Y-%m') as periodo, AVG(saldo_final) as promedio_saldo FROM estadisticas_duts WHERE DATE_FORMAT(fecha, '%Y-%m') = ? GROUP BY periodo";
            } elseif (preg_match('/^\d{4}$/', $periodo)) { // Año
                $sql = "SELECT YEAR(fecha) as periodo, AVG(saldo_final) as promedio_saldo FROM estadisticas_duts WHERE YEAR(fecha) = ? GROUP BY periodo";
            } elseif (preg_match('/^\d{4}-S[12]$/', $periodo)) { // Semestre
                $year = substr($periodo, 0, 4);
                $sem = substr($periodo, 5, 2);
                if ($sem == 'S1') {
                    $sql = "SELECT 'S1' as semestre, AVG(saldo_final) as promedio_saldo FROM estadisticas_duts WHERE YEAR(fecha) = ? AND MONTH(fecha) BETWEEN 1 AND 6";
                } else {
                    $sql = "SELECT 'S2' as semestre, AVG(saldo_final) as promedio_saldo FROM estadisticas_duts WHERE YEAR(fecha) = ? AND MONTH(fecha) BETWEEN 7 AND 12";
                }
                $param = $year;
            }
            if ($sql) {
                $stmt = $db->prepare($sql);
                $stmt->execute([$param]);
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                Response::success("Promedios DUTS por período", $data);
            } else {
                Response::error("Formato de periodo no válido", 400);
            }
        }
        break;

    default:
        Response::error("Método no permitido", 405);
}
?>