<?php
class Validator {
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function required($fields, $data) {
        $missing = [];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $missing[] = $field;
            }
        }
        return $missing;
    }

    public static function numeric($value) {
        return is_numeric($value) && $value >= 0;
    }
}
?>