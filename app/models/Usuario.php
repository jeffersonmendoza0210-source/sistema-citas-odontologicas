<?php

class Usuario {

    // ==========================
    // CONEXIÓN Y TABLA
    // ==========================
    private $conn;
    private $table = 'usuarios';

    // ==========================
    // PROPIEDADES DEL MODELO
    // (Evita "Obsoleto" en PHP 8.2)
    // ==========================
    public ?int $id_usuario = null;
    public ?string $nombre = null;
    public ?string $email = null;
    public ?string $telefono = null;
    public ?string $password = null;
    public ?int $id_rol = null;
    public ?string $avatar = null;

    // ==========================
    // CONSTRUCTOR
    // ==========================
    public function __construct($db) {
        $this->conn = $db;
    }

    // ==========================
    // 1. LOGIN: BUSCAR POR EMAIL
    // ==========================
    public function getByEmail($email) {
        $query = 'SELECT 
                    u.id_usuario,
                    u.nombre,
                    u.email,
                    u.password,
                    u.id_rol,
                    u.avatar,
                    r.nombre AS rol_nombre
                  FROM ' . $this->table . ' u
                  JOIN roles r ON u.id_rol = r.id_rol
                  WHERE u.email = :email
                  LIMIT 1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ==========================
    // 2. OBTENER POR ID
    // ==========================
    public function getById($id) {
        $query = 'SELECT 
                    u.id_usuario,
                    u.nombre,
                    u.email,
                    u.password,
                    u.avatar,
                    r.nombre AS rol_nombre
                  FROM ' . $this->table . ' u
                  JOIN roles r ON u.id_rol = r.id_rol
                  WHERE u.id_usuario = :id
                  LIMIT 1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ==========================
    // 3. CREAR USUARIO
    // ==========================
    public function crear() {

        $sql = "INSERT INTO usuarios (nombre, email, telefono, password, id_rol)
                VALUES (:nombre, :email, :telefono, :password, :rol)";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':nombre'   => $this->nombre,
            ':email'    => $this->email,
            ':telefono' => $this->telefono,
            ':password' => $this->password,
            ':rol'      => $this->id_rol
        ]);

        $this->id_usuario = (int) $this->conn->lastInsertId();
        return true;
    }

    // ==========================
    // 4. ACTUALIZAR PASSWORD
    // ==========================
    public function actualizarPassword() {
        $sql = "UPDATE usuarios 
                SET password = :password 
                WHERE id_usuario = :id";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':password' => $this->password,
            ':id'       => $this->id_usuario
        ]);
    }

    // ==========================
    // 5. ACTUALIZAR PERFIL
    // ==========================
    public function actualizar($id, $nombre, $password = null, $avatar = null) {

        $query = "UPDATE " . $this->table . " SET nombre = :nombre";

        if ($password) {
            $query .= ", password = :password";
        }
        if ($avatar) {
            $query .= ", avatar = :avatar";
        }

        $query .= " WHERE id_usuario = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($password) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $hash, PDO::PARAM_STR);
        }
        if ($avatar) {
            $stmt->bindParam(':avatar', $avatar, PDO::PARAM_STR);
        }

        return $stmt->execute();
    }
}
