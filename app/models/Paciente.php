<?php
class Paciente {
    private $conn;
    private $table = 'usuarios';

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. LISTAR PACIENTES
    public function leer() {
        $query = 'SELECT id_usuario, nombre, documento_identidad, email, telefono, 
                         grupo_sanguineo, alergias, enfermedades_cronicas, 
                         fecha_creacion 
                  FROM ' . $this->table . ' 
                  WHERE id_rol = 3 
                  ORDER BY nombre ASC';
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function obtenerPorId($id) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id_usuario = :id LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 2. GESTIÓN DE ARCHIVOS (Aquí estaba el error del "Undefined Method")
    public function obtenerArchivos($id_paciente) {
        $query = "SELECT id_archivo, nombre_archivo, ruta_archivo, fecha_subida 
                  FROM archivos_paciente 
                  WHERE id_paciente = :id 
                  ORDER BY fecha_subida DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_paciente);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerArchivoPorId($id) {
        $query = "SELECT ruta_archivo FROM archivos_paciente WHERE id_archivo = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function eliminarArchivoDB($id) {
        $query = "DELETE FROM archivos_paciente WHERE id_archivo = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function guardarArchivo($id, $nombre, $ruta) {
        $query = "INSERT INTO archivos_paciente (id_paciente, nombre_archivo, ruta_archivo) 
                  VALUES (:id, :nom, :ruta)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            'id' => $id, 
            'nom' => $nombre, 
            'ruta' => $ruta
        ]);
    }

    // 3. ODONTOGRAMA
    public function obtenerOdontograma($id_paciente) {
    $query = "SELECT pieza, cara, estado, notas FROM odontogramas WHERE id_paciente = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id_paciente);
    $stmt->execute();

    $resultados = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Genera la llave para que el JS reconozca el diente (ej: 18_top)
        $key = $row['pieza'] . '_' . $row['cara'];
        $resultados[$key] = $row;
    }
    return $resultados;
}

public function guardarOdontograma($id_paciente, $pieza, $cara, $estado, $notas = '') {
    // Verificar si ya existe la pieza y cara para este paciente
    $query = "SELECT id FROM odontogramas WHERE id_paciente = :id AND pieza = :pieza AND cara = :cara";
    $stmt = $this->conn->prepare($query);
    $stmt->execute(['id' => $id_paciente, 'pieza' => $pieza, 'cara' => $cara]);

    if ($stmt->rowCount() > 0) {
        $sql = "UPDATE odontogramas SET estado = :estado, notas = :notas 
                WHERE id_paciente = :id AND pieza = :pieza AND cara = :cara";
    } else {
        $sql = "INSERT INTO odontogramas (id_paciente, pieza, cara, estado, notas) 
                VALUES (:id, :pieza, :cara, :estado, :notas)";
    }

    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([ // Esto devuelve true o false
        'id' => $id_paciente,
        'pieza' => $pieza,
        'cara' => $cara,
        'estado' => $estado,
        'notas' => $notas
    ]);
}

    // 4. HISTORIAL Y CRUD
    public function obtenerHistorial($id_paciente) {
        $query = "SELECT c.fecha_cita, c.motivo, c.diagnostico, c.prescripcion, c.estado,
                         c.peso, c.talla, c.temperatura, c.presion_arterial,
                         m_u.nombre as medico, e.nombre as especialidad
                  FROM citas c
                  JOIN medicos m ON c.id_medico = m.id_medico
                  JOIN usuarios m_u ON m.id_usuario = m_u.id_usuario
                  JOIN especialidades e ON m.id_especialidad = e.id_especialidad
                  WHERE c.id_paciente = :id
                  ORDER BY c.fecha_cita DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_paciente);
        $stmt->execute();
        return $stmt;
    }

    public function crear($datos) {
        $query = 'INSERT INTO ' . $this->table . ' (nombre, documento_identidad, email, telefono, grupo_sanguineo, alergias, enfermedades_cronicas, password, id_rol) 
                  VALUES (:nombre, :dni, :email, :telefono, :sangre, :alergias, :cronicas, :password, 3)';
        $stmt = $this->conn->prepare($query);
        $passwordHash = password_hash($datos['password'], PASSWORD_BCRYPT);
        return $stmt->execute([
            'nombre' => $datos['nombre'],
            'dni' => $datos['dni'],
            'email' => $datos['email'],
            'telefono' => $datos['telefono'],
            'sangre' => $datos['sangre'],
            'alergias' => $datos['alergias'],
            'cronicas' => $datos['cronicas'],
            'password' => $passwordHash
        ]);
    }

    public function actualizar($datos) {
        $sql = 'UPDATE ' . $this->table . ' 
                SET nombre = :nombre, documento_identidad = :dni, email = :email, telefono = :telefono, 
                    grupo_sanguineo = :sangre, alergias = :alergias, enfermedades_cronicas = :cronicas';
        if (!empty($datos['password'])) { $sql .= ', password = :password'; }
        $sql .= ' WHERE id_usuario = :id';

        $stmt = $this->conn->prepare($sql);
        $params = [
            'id' => $datos['id'],
            'nombre' => $datos['nombre'],
            'dni' => $datos['dni'],
            'email' => $datos['email'],
            'telefono' => $datos['telefono'],
            'sangre' => $datos['sangre'],
            'alergias' => $datos['alergias'],
            'cronicas' => $datos['cronicas']
        ];
        if (!empty($datos['password'])) {
            $params['password'] = password_hash($datos['password'], PASSWORD_BCRYPT);
        }
        return $stmt->execute($params);
    }

    public function eliminar($id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id_usuario = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}