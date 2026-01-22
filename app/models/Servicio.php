<?php

class Servicio
{
    private $conn;
    private $table = 'servicios';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // ============================
    // LISTAR
    // ============================
    public function obtenerTodos()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY nombre_servicio ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================
    // CREAR - CORREGIDO
    // ============================
    public function crear($datos)
    {
        $sql = "INSERT INTO {$this->table}
                (nombre_servicio, descripcion, precio, estado)
                VALUES (:nombre_servicio, :descripcion, :precio, :estado)";

        $stmt = $this->conn->prepare($sql);

        // Usamos las llaves que vienen del controlador: 'nombre_servicio'
        return $stmt->execute([
            ':nombre_servicio' => trim($datos['nombre_servicio']),
            ':descripcion'     => trim($datos['descripcion'] ?? ''),
            ':precio'          => (float)$datos['precio'],
            ':estado'          => $datos['estado'] ?? 'Activo'
        ]);
    }

    // ============================
    // ACTUALIZAR - CORREGIDO
    // ============================
    public function actualizar($datos) {
    $sql = "UPDATE {$this->table}
            SET nombre_servicio = :nombre_servicio,
                descripcion = :descripcion,
                precio = :precio,
                estado = :estado
            WHERE id_servicio = :id_servicio";

    $stmt = $this->conn->prepare($sql);

    return $stmt->execute([
        ':id_servicio'     => $datos['id_servicio'],     // Coincide con el controlador
        ':nombre_servicio' => $datos['nombre_servicio'], // Coincide con el controlador
        ':descripcion'     => $datos['descripcion'],
        ':precio'          => $datos['precio'],
        ':estado'          => $datos['estado']
    ]);
}
/**
 * Obtiene solo los servicios con estado 'Activo' para el formulario de citas
 */
public function leerActivos() {
    // Usamos nombre_servicio según la imagen de tu DB
    $sql = "SELECT id_servicio, nombre_servicio, precio 
            FROM {$this->table} 
            WHERE estado = 'Activo' 
            ORDER BY nombre_servicio ASC";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt; // Retornamos el statement para que el controlador lo recorra
}

    // ============================
    // ELIMINAR
    // ============================
    public function eliminar($id)
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM {$this->table} WHERE id_servicio = :id"
        );
        return $stmt->execute([':id' => $id]);
    }
}
