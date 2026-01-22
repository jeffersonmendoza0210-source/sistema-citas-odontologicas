<?php
class Medico {
    private $conn;
    private $table = 'medicos';

    public function __construct($db) { $this->conn = $db; }

    public function leer() {
        $query = 'SELECT m.id_medico, u.nombre, u.email, e.nombre as especialidad, m.id_especialidad FROM ' . $this->table . ' m JOIN usuarios u ON m.id_usuario = u.id_usuario JOIN especialidades e ON m.id_especialidad = e.id_especialidad ORDER BY u.nombre ASC';
        $stmt = $this->conn->prepare($query); $stmt->execute(); return $stmt;
    }

    public function actualizar($datos) {
        try {
            $this->conn->beginTransaction();
            $queryRel = "SELECT id_usuario FROM medicos WHERE id_medico = :id_medico";
            $stmtRel = $this->conn->prepare($queryRel);
            $stmtRel->bindParam(':id_medico', $datos['id_medico']);
            $stmtRel->execute();
            $id_usuario = $stmtRel->fetchColumn();

            if (!empty($datos['password'])) {
                $queryU = "UPDATE usuarios SET nombre = :nombre, email = :email, password = :pass WHERE id_usuario = :id_u";
                $passHash = password_hash($datos['password'], PASSWORD_BCRYPT);
            } else {
                $queryU = "UPDATE usuarios SET nombre = :nombre, email = :email WHERE id_usuario = :id_u";
            }
            $stmtU = $this->conn->prepare($queryU);
            $stmtU->bindParam(':nombre', $datos['nombre']);
            $stmtU->bindParam(':email', $datos['email']);
            $stmtU->bindParam(':id_u', $id_usuario);
            if (!empty($datos['password'])) $stmtU->bindParam(':pass', $passHash);
            $stmtU->execute();

            $queryM = "UPDATE medicos SET id_especialidad = :id_esp WHERE id_medico = :id_m";
            $stmtM = $this->conn->prepare($queryM);
            $stmtM->bindParam(':id_esp', $datos['id_especialidad']);
            $stmtM->bindParam(':id_m', $datos['id_medico']);
            $stmtM->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) { $this->conn->rollBack(); return false; }
    }

    public function obtenerPorId($id) {
        $query = 'SELECT m.id_medico, u.nombre, e.nombre as especialidad FROM ' . $this->table . ' m JOIN usuarios u ON m.id_usuario = u.id_usuario JOIN especialidades e ON m.id_especialidad = e.id_especialidad WHERE m.id_medico = :id';
        $stmt = $this->conn->prepare($query); $stmt->bindParam(':id', $id); $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerEspecialidades() {
        $query = 'SELECT * FROM especialidades';
        $stmt = $this->conn->prepare($query); $stmt->execute(); return $stmt;
    }

    public function crear($datos) {
        try {
            $this->conn->beginTransaction();
            $queryUser = "INSERT INTO usuarios (nombre, email, password, id_rol) VALUES (:nombre, :email, :password, 2)";
            $stmtUser = $this->conn->prepare($queryUser);
            $passHash = password_hash($datos['password'], PASSWORD_BCRYPT);
            $stmtUser->bindParam(':nombre', $datos['nombre']);
            $stmtUser->bindParam(':email', $datos['email']);
            $stmtUser->bindParam(':password', $passHash);
            $stmtUser->execute();
            $idUsuario = $this->conn->lastInsertId();
            $queryMedico = "INSERT INTO medicos (id_usuario, id_especialidad) VALUES (:id_usuario, :id_especialidad)";
            $stmtMedico = $this->conn->prepare($queryMedico);
            $stmtMedico->bindParam(':id_usuario', $idUsuario);
            $stmtMedico->bindParam(':id_especialidad', $datos['id_especialidad']);
            $stmtMedico->execute();
            $this->conn->commit();
            return true;
        } catch (Exception $e) { $this->conn->rollBack(); return false; }
    }

    public function obtenerHorarios($id) {
        $query = "SELECT * FROM horarios_medicos WHERE id_medico = :id ORDER BY FIELD(dia_semana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'), hora_inicio";
        $stmt = $this->conn->prepare($query); $stmt->bindParam(':id', $id); $stmt->execute(); return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function agregarHorario($id, $dia, $ini, $fin) {
        $query = "INSERT INTO horarios_medicos (id_medico, dia_semana, hora_inicio, hora_fin) VALUES (:id, :dia, :ini, :fin)";
        $stmt = $this->conn->prepare($query); $stmt->bindParam(':id', $id); $stmt->bindParam(':dia', $dia); $stmt->bindParam(':ini', $ini); $stmt->bindParam(':fin', $fin); return $stmt->execute();
    }

    public function eliminarHorario($id) {
        $query = "DELETE FROM horarios_medicos WHERE id_horario = :id";
        $stmt = $this->conn->prepare($query); $stmt->bindParam(':id', $id); return $stmt->execute();
    }
    public function verificaHorarioLaboral($fechaHora, $id_medico)
{
    // Obtener día y hora
    $timestamp = strtotime($fechaHora);
    $hora = date('H:i:s', $timestamp);

    // Convertir día numérico a texto (como lo usas en tu BD)
    $dias = [
        'Sunday'    => 'Domingo',
        'Monday'    => 'Lunes',
        'Tuesday'   => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday'  => 'Jueves',
        'Friday'    => 'Viernes',
        'Saturday'  => 'Sábado'
    ];

    $diaTexto = $dias[date('l', $timestamp)];

    $query = "
        SELECT 1 
        FROM horarios_medicos
        WHERE id_medico = :id_medico
          AND dia_semana = :dia
          AND :hora BETWEEN hora_inicio AND hora_fin
        LIMIT 1
    ";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id_medico', $id_medico);
    $stmt->bindParam(':dia', $diaTexto);
    $stmt->bindParam(':hora', $hora);
    $stmt->execute();

    return $stmt->rowCount() > 0;
}

}
