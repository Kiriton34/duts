<?php
class Event {
    private $conn;
    private $table = "eventos";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} 
            (nombre, descripcion, fecha_inicio, fecha_fin, lugar, costo_duts, cupo_maximo, tipo_evento, organizador_id, activo)
            VALUES (:nombre, :descripcion, :fecha_inicio, :fecha_fin, :lugar, :costo_duts, :cupo_maximo, :tipo_evento, :organizador_id, :activo)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'],
            ':fecha_inicio' => $data['fecha_inicio'],
            ':fecha_fin' => $data['fecha_fin'],
            ':lugar' => $data['lugar'],
            ':costo_duts' => $data['costo_duts'],
            ':cupo_maximo' => $data['cupo_maximo'],
            ':tipo_evento' => $data['tipo_evento'],
            ':organizador_id' => $data['organizador_id'],
            ':activo' => $data['activo'] ?? 1
        ]);
        return $this->conn->lastInsertId();
    }

    public function getAll($tipo = null) {
        $sql = "SELECT * FROM {$this->table} WHERE activo=1";
        if ($tipo) {
            $sql .= " AND tipo_evento = :tipo";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->execute();
        } else {
            $stmt = $this->conn->query($sql);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $query = "UPDATE {$this->table} SET nombre=:nombre, descripcion=:descripcion, fecha_inicio=:fecha_inicio, fecha_fin=:fecha_fin, lugar=:lugar, costo_duts=:costo_duts, cupo_maximo=:cupo_maximo, tipo_evento=:tipo_evento, activo=:activo WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'],
            ':fecha_inicio' => $data['fecha_inicio'],
            ':fecha_fin' => $data['fecha_fin'],
            ':lugar' => $data['lugar'],
            ':costo_duts' => $data['costo_duts'],
            ':cupo_maximo' => $data['cupo_maximo'],
            ':tipo_evento' => $data['tipo_evento'],
            ':activo' => $data['activo'] ?? 1,
            ':id' => $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET activo=0 WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function getCupoDisponible($evento_id) {
        $stmt = $this->conn->prepare("SELECT cupo_maximo FROM {$this->table} WHERE id=?");
        $stmt->execute([$evento_id]);
        $evento = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$evento) return 0;
        $stmt2 = $this->conn->prepare("SELECT COUNT(*) as inscritos FROM inscripciones_eventos WHERE evento_id=? AND estado='inscrito'");
        $stmt2->execute([$evento_id]);
        $inscritos = $stmt2->fetch(PDO::FETCH_ASSOC)['inscritos'];
        return $evento['cupo_maximo'] - $inscritos;
    }
}
?>