<?php
class InscripcionEvento {
    private $conn;
    private $table = "inscripciones_eventos";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function inscribir($evento_id, $user_id) {
        $query = "INSERT INTO {$this->table} (evento_id, user_id, estado) VALUES (?, ?, 'inscrito')";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$evento_id, $user_id]);
    }

    public function inscritos($evento_id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE evento_id=?");
        $stmt->execute([$evento_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function yaInscrito($evento_id, $user_id) {
        $stmt = $this->conn->prepare("SELECT id FROM {$this->table} WHERE evento_id=? AND user_id=? AND estado='inscrito'");
        $stmt->execute([$evento_id, $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    public function cancelar($evento_id, $user_id) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET estado='cancelado' WHERE evento_id=? AND user_id=? AND estado='inscrito'");
        return $stmt->execute([$evento_id, $user_id]);
    }
}
?>