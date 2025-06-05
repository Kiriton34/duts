<?php
class DutsAccount {
    private $conn;
    private $table_name = "cuentas_duts";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createAccount($user_id) {
        $query = "INSERT INTO " . $this->table_name . " (user_id) VALUES (:user_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }

    public function getBalance($user_id) {
        $query = "SELECT saldo_actual FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['saldo_actual'] : 0;
    }

    public function updateBalance($user_id, $new_balance) {
        $query = "UPDATE " . $this->table_name . " SET saldo_actual = :balance WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":balance", $new_balance);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }

    public function transfer($id_origen, $id_destino, $cantidad, $concepto = '') {
        try {
            $this->conn->beginTransaction();

            // Verificar saldo suficiente
            $saldo_origen = $this->getBalance($id_origen);
            if ($saldo_origen < $cantidad) {
                throw new Exception("Saldo insuficiente");
            }

            // Actualizar saldos
            $nuevo_saldo_origen = $saldo_origen - $cantidad;
            $saldo_destino = $this->getBalance($id_destino);
            $nuevo_saldo_destino = $saldo_destino + $cantidad;

            $this->updateBalance($id_origen, $nuevo_saldo_origen);
            $this->updateBalance($id_destino, $nuevo_saldo_destino);

            // Registrar transacción
            $query = "INSERT INTO transacciones_duts (id_origen, id_destino, cantidad, concepto, tipo_transaccion) 
                      VALUES (:id_origen, :id_destino, :cantidad, :concepto, 'transferencia')";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id_origen", $id_origen);
            $stmt->bindParam(":id_destino", $id_destino);
            $stmt->bindParam(":cantidad", $cantidad);
            $stmt->bindParam(":concepto", $concepto);
            $stmt->execute();

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function getTransactionHistory($user_id, $limit = 50) {
        $query = "SELECT t.*, 
                         u1.nombres as origen_nombre, u1.apellidos as origen_apellido,
                         u2.nombres as destino_nombre, u2.apellidos as destino_apellido
                  FROM transacciones_duts t
                  LEFT JOIN users u1 ON t.id_origen = u1.id
                  LEFT JOIN users u2 ON t.id_destino = u2.id
                  WHERE t.id_origen = :user_id OR t.id_destino = :user_id
                  ORDER BY t.fecha_transaccion DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>