<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nombres, apellidos, email, ciudad, pais, descripcion, lista_intereses, 
                   programa, semestre, username, password, tipo_usuario) 
                  VALUES (:nombres, :apellidos, :email, :ciudad, :pais, :descripcion, 
                          :lista_intereses, :programa, :semestre, :username, :password, :tipo_usuario)";

        $stmt = $this->conn->prepare($query);
        
        // Hash de la contraseña
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt->bindParam(":nombres", $data['nombres']);
        $stmt->bindParam(":apellidos", $data['apellidos']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":ciudad", $data['ciudad']);
        $stmt->bindParam(":pais", $data['pais']);
        $stmt->bindParam(":descripcion", $data['descripcion']);
        $stmt->bindParam(":lista_intereses", $data['lista_intereses']);
        $stmt->bindParam(":programa", $data['programa']);
        $stmt->bindParam(":semestre", $data['semestre']);
        $stmt->bindParam(":username", $data['username']);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":tipo_usuario", $data['tipo_usuario']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function login($username, $password) {
        $query = "SELECT id, nombres, apellidos, username, password, tipo_usuario 
                  FROM " . $this->table_name . " 
                  WHERE username = :username AND activo = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }
        return false;
    }

    public function getById($id) {
        $query = "SELECT id, nombres, apellidos, email, ciudad, pais, descripcion, 
                         lista_intereses, programa, semestre, username, tipo_usuario 
                  FROM " . $this->table_name . " WHERE id = :id AND activo = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getProfile($id) {
        $query = "SELECT nombres, apellidos, lista_intereses, programa, semestre 
                  FROM " . $this->table_name . " WHERE id = :id AND activo = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombres = :nombres, apellidos = :apellidos, email = :email, 
                      ciudad = :ciudad, pais = :pais, descripcion = :descripcion, 
                      lista_intereses = :lista_intereses, programa = :programa, 
                      semestre = :semestre 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":nombres", $data['nombres']);
        $stmt->bindParam(":apellidos", $data['apellidos']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":ciudad", $data['ciudad']);
        $stmt->bindParam(":pais", $data['pais']);
        $stmt->bindParam(":descripcion", $data['descripcion']);
        $stmt->bindParam(":lista_intereses", $data['lista_intereses']);
        $stmt->bindParam(":programa", $data['programa']);
        $stmt->bindParam(":semestre", $data['semestre']);

        return $stmt->execute();
    }

    public function delete($id) {
        $query = "UPDATE " . $this->table_name . " SET activo = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>