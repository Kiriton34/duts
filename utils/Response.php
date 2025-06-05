<?php
class Response {
    public static function json($success, $message, $data = null, $status_code = 200) {
        http_response_code($status_code);
        echo json_encode([
            "success" => $success,
            "message" => $message,
            "data" => $data,
            "timestamp" => date('Y-m-d H:i:s')
        ]);
        exit;
    }

    public static function error($message, $status_code = 400) {
        self::json(false, $message, null, $status_code);
    }

    public static function success($message, $data = null) {
        self::json(true, $message, $data, 200);
    }
}
?>