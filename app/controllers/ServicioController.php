<?php
require_once APP_ROOT . '/models/Servicio.php';
require_once APP_ROOT . '/models/Configuracion.php'; 

class ServicioController {
    private $servicioModel;
    private $db;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        if (!isset($_SESSION['id_de_rol_de_usuario']) || $_SESSION['id_de_rol_de_usuario'] != 1) {
            header('Location: ' . BASE_URL . '/home');
            exit;
        }

        $database = new Database();
        $this->db = $database->connect();
        $this->servicioModel = new Servicio($this->db);
    }

    public function index() {
        $resultado = $this->servicioModel->obtenerTodos();
        $configModel = new Configuracion($this->db);
        $empresa = $configModel->obtener();
        require_once APP_ROOT . '/views/admin/servicios.php';
    }

    public function guardar() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // CORRECCIÓN: Usar 'nombre_servicio' para que coincida con el formulario
        if (empty($_POST['nombre_servicio']) || empty($_POST['precio'])) {
            $_SESSION['msg'] = "Nombre y Precio son obligatorios.";
            $_SESSION['type'] = "warning";
            header('Location: ' . BASE_URL . '/servicios');
            exit;
        }

        $data = [
            'nombre_servicio' => trim($_POST['nombre_servicio']), 
            'descripcion'     => trim($_POST['descripcion']),
            'precio'          => $_POST['precio'],
            'estado'          => 'Activo' 
        ];

        if ($this->servicioModel->crear($data)) {
            $_SESSION['msg'] = "¡Servicio agregado exitosamente!";
            $_SESSION['type'] = "success";
        } else {
            $_SESSION['msg'] = "Error al guardar en la base de datos.";
            $_SESSION['type'] = "danger";
        }
        header('Location: ' . BASE_URL . '/servicios');
        exit;
    }
}

    public function actualizar() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'id_servicio'     => $_POST['id_servicio'],
            'nombre_servicio' => trim($_POST['nombre_servicio']), // Cambiado de 'nombre' a 'nombre_servicio'
            'descripcion'     => trim($_POST['descripcion']),
            'precio'          => $_POST['precio'],
            'estado'          => $_POST['estado']
        ];

        if ($this->servicioModel->actualizar($data)) {
            $_SESSION['msg'] = "Servicio actualizado correctamente.";
            $_SESSION['type'] = "success";
        } else {
            $_SESSION['msg'] = "Error al actualizar.";
            $_SESSION['type'] = "danger";
        }
        header('Location: ' . BASE_URL . '/servicios');
        exit;
    }
}

    public function eliminar() {
        if (isset($_GET['id'])) {
            if ($this->servicioModel->eliminar($_GET['id'])) {
                $_SESSION['msg'] = "Servicio eliminado de la base de datos.";
                $_SESSION['type'] = "success";
            } else {
                $_SESSION['msg'] = "No se pudo eliminar el registro.";
                $_SESSION['type'] = "danger";
            }
        }
        header('Location: ' . BASE_URL . '/servicios');
        exit;
    }
}