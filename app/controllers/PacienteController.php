<?php
require_once APP_ROOT . '/config/Database.php';
require_once APP_ROOT . '/models/Paciente.php';

class PacienteController {

    private $db;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id_usuario'])) {

            if (
                isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
            ) {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Sesión expirada'
                ]);
                exit;
            }

            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // 🔴 AQUÍ ESTABA EL PROBLEMA
        $database = new Database();
        $this->db = $database->connect();
    }



    public function index() {
        $database = new Database();
        $db = $database->connect();
        $pacienteModel = new Paciente($db);
        $resultado = $pacienteModel->leer();
        require_once APP_ROOT . '/views/admin/pacientes.php';
    }

    public function historial() {
        $id_paciente = $_GET['id'] ?? null;
        if (!$id_paciente) {
            header('Location: ' . BASE_URL . '/pacientes');
            exit;
        }

        $database = new Database();
        $db = $database->connect();
        $pacienteModel = new Paciente($db);

        $paciente = $pacienteModel->obtenerPorId($id_paciente);
        $historial = $pacienteModel->obtenerHistorial($id_paciente);
        $archivos = $pacienteModel->obtenerArchivos($id_paciente); 
        $odontogramaBD = $pacienteModel->obtenerOdontograma($id_paciente);

        require_once APP_ROOT . '/views/admin/historial_clinico.php';
    }

 // Dentro de tu PacienteController.php (o el archivo que maneje las rutas de pacientes)
public function guardarOdontograma() {
    // 1. Borra cualquier espacio en blanco o salida previa de PHP
    if (ob_get_level()) ob_end_clean();
    
    // 2. Establece el encabezado JSON antes de imprimir nada
    header('Content-Type: application/json');

    try {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if ($data) {
            $pacienteModel = new Paciente($this->db);
            $id_paciente = $data['id_paciente'];
            $exito = true;

            foreach ($data['detalles'] as $pieza) {
                $res = $pacienteModel->guardarOdontograma(
                    $id_paciente, 
                    $pieza['pieza'], 
                    $pieza['cara'], 
                    $pieza['estado'], 
                    $pieza['notas'] ?? ''
                );
                if (!$res) $exito = false;
            }
            // 3. Imprime ÚNICAMENTE el JSON
            echo json_encode(["success" => $exito]);
        } else {
            echo json_encode(["success" => false, "message" => "No se recibieron datos"]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }

    // 4. MUY IMPORTANTE: Detener la ejecución para que no cargue el HTML del footer
    exit; 
}




    public function subirArchivo() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id_paciente = $_POST['id_paciente'];
        // Tú definiste esta variable como $nombre_personalizado
        $nombre_personalizado = $_POST['nombre_archivo'] ?? 'Documento sin nombre';

        if (!isset($_FILES['documento']) || $_FILES['documento']['error'] !== UPLOAD_ERR_OK) {
            header('Location: ' . BASE_URL . '/pacientes/historial?id=' . $id_paciente . '&msg=error_archivo');
            exit;
        }

        $database = new Database();
        $db = $database->connect();
        $pacienteModel = new Paciente($db);

        $archivo = $_FILES['documento'];
        $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'pdf'];

        if (in_array($ext, $permitidas)) {
            $folder = PUBLIC_ROOT . '/uploads/';
            if (!file_exists($folder)) mkdir($folder, 0777, true);

            // Tú definiste esta variable como $nuevo_nombre
            $nuevo_nombre = uniqid('DOC_', true) . '.' . $ext;
            
            if (move_uploaded_file($archivo['tmp_name'], $folder . $nuevo_nombre)) {
                // CORRECCIÓN AQUÍ: 
                // Usamos las variables correctas y el modelo que instanciaste arriba ($pacienteModel)
                $pacienteModel->guardarArchivo($id_paciente, $nombre_personalizado, $nuevo_nombre);
                
                header('Location: ' . BASE_URL . '/pacientes/historial?id=' . $id_paciente . '&upload=success');
            } else {
                header('Location: ' . BASE_URL . '/pacientes/historial?id=' . $id_paciente . '&upload=error');
            }
        } else {
            header('Location: ' . BASE_URL . '/pacientes/historial?id=' . $id_paciente . '&msg=formato_no_valido');
        }
        exit;
    }
}

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = (new Database())->connect();
            $pacienteModel = new Paciente($db);

            $datos = [
                'nombre' => $_POST['nombre'],
                'dni' => $_POST['dni'],
                'email' => $_POST['email'],
                'telefono' => $_POST['telefono'],
                'sangre' => $_POST['sangre'],
                'alergias' => $_POST['alergias'],
                'cronicas' => $_POST['cronicas'],
                'password' => password_hash($_POST['password'], PASSWORD_BCRYPT)
            ];

            if($pacienteModel->crear($datos)) {
                header('Location: ' . BASE_URL . '/pacientes?msg=creado');
            } else {
                header('Location: ' . BASE_URL . '/pacientes?msg=error');
            }
            exit;
        }
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = (new Database())->connect();
            $pacienteModel = new Paciente($db);

            $datos = [
                'id' => $_POST['id_usuario'],
                'nombre' => $_POST['nombre'],
                'dni' => $_POST['dni'],
                'email' => $_POST['email'],
                'telefono' => $_POST['telefono'],
                'sangre' => $_POST['sangre'],
                'alergias' => $_POST['alergias'],
                'cronicas' => $_POST['cronicas']
            ];

            if($pacienteModel->actualizar($datos)) {
                header('Location: ' . BASE_URL . '/pacientes?msg=actualizado');
            } else {
                header('Location: ' . BASE_URL . '/pacientes?msg=error');
            }
            exit;
        }
    }

    public function eliminar() {
        if (isset($_GET['id'])) {
            $db = (new Database())->connect();
            $pacienteModel = new Paciente($db);
            $pacienteModel->eliminar($_GET['id']);
            header('Location: ' . BASE_URL . '/pacientes?msg=eliminado');
            exit;
        }
    }
}
