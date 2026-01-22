<?php
require_once APP_ROOT . '/config/Database.php';
require_once APP_ROOT . '/models/Cita.php';
require_once APP_ROOT . '/models/Medico.php';
require_once APP_ROOT . '/models/Paciente.php';
require_once APP_ROOT . '/models/Configuracion.php';
require_once APP_ROOT . '/models/Servicio.php';
require_once APP_ROOT . '/models/Pago.php';
require_once APP_ROOT . '/models/Auditoria.php';

class CitaController {

public function obtenerPorId($id) {
    $sql = "SELECT c.*, 
            p.nombre as paciente, p.telefono as paciente_telefono,
            m.nombre as medico, m.especialidad
            FROM citas c
            INNER JOIN usuarios p ON c.id_paciente = p.id_usuario
            INNER JOIN medicos m ON c.id_medico = m.id_medico
            WHERE c.id_cita = :id";
            
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $database = new Database();
        $db = $database->connect();

        $citaModel = new Cita($db);
        $medicoModel = new Medico($db);
        $pacienteModel = new Paciente($db);
        $configModel = new Configuracion($db);
        $servicioModel = new Servicio($db);

        $fechaFiltro = $_GET['fecha'] ?? null;
        $estadoFiltro = $_GET['estado'] ?? null;

        $rol = $_SESSION['user_role_id'] ?? 0;
        $idMedicoFiltro = ($rol == 2) ? ($_SESSION['medico_id'] ?? null) : null;
        $idPacienteFiltro = ($rol == 3) ? $_SESSION['user_id'] : null;

        $resultado = $citaModel->leer($fechaFiltro, $estadoFiltro, $idMedicoFiltro, $idPacienteFiltro);

        $listaMedicos = $medicoModel->leer();
        $listaPacientes = $pacienteModel->leer();
        $empresa = $configModel->obtener();
        $listaServicios = $servicioModel->leerActivos();

        require_once APP_ROOT . '/views/admin/citas.php';
    }

    public function listarEventos() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $database = new Database();
        $db = $database->connect();
        $citaModel = new Cita($db);

        $rol = $_SESSION['user_role_id'] ?? 0;
        $idMedicoFiltro = ($rol == 2) ? ($_SESSION['medico_id'] ?? null) : null;
        $idPacienteFiltro = ($rol == 3) ? $_SESSION['user_id'] : null;

        $citas = $citaModel->leer(null, null, $idMedicoFiltro, $idPacienteFiltro);
        $eventos = [];

        while($row = $citas->fetch(PDO::FETCH_ASSOC)) {
            $color = '#ffc107';
            if($row['estado'] == 'Confirmada') $color = '#0d6efd';
            if($row['estado'] == 'Finalizada') $color = '#198754';
            if($row['estado'] == 'Cancelada') $color = '#dc3545';

            $servicioTxt = $row['nombre_servicio'] ? ' - ' . $row['nombre_servicio'] : '';
            $titulo = ($rol == 3) ? 'Cita' . $servicioTxt : $row['paciente'] . $servicioTxt;

            $eventos[] = [
                'id' => $row['id_cita'],
                'title' => $titulo,
                'start' => $row['fecha_cita'],
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'medico' => $row['medico'],
                    'estado' => $row['estado'],
                    'motivo' => $row['motivo']
                ]
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($eventos);
        exit;
    }

    // ----------------------------------------
    // Validación de Negocio y Disponibilidad
    // ----------------------------------------
   // Validación de Negocio
private function validarReglasNegocio($fechaHora, $id_medico) {
    $fechaIngresada = strtotime($fechaHora);
    $fechaActual = time();
    
    // 1. No permitir fechas pasadas
    if ($fechaIngresada < ($fechaActual - 300)) return 'pasado';

    // 2. Validar Horario Médico
    $database = new Database();
    $db = $database->connect();
    $medicoModel = new Medico($db);

    // Se usa el método existente de tu modelo
    if (!$medicoModel->verificaHorarioLaboral($fechaHora, $id_medico)) {
        return 'fuera_horario';
    }

    return 'ok';
}


    private function validarDisponibilidadMedico($id_medico, $fechaHora) {
        $database = new Database();
        $db = $database->connect();
        $medicoModel = new Medico($db);

        $diaSemana = date('l', strtotime($fechaHora));
        $horaCita = date('H:i:s', strtotime($fechaHora));

        // Obtener horarios del médico
        $horarios = $medicoModel->obtenerHorariosDia($id_medico, ucfirst($diaSemana));

        if(empty($horarios)) {
            // Si no tiene horarios definidos → usar horario por defecto
            $horaInicio = '08:00:00';
            $horaFin = '20:00:00';
            return ($horaCita >= $horaInicio && $horaCita <= $horaFin);
        }

        foreach($horarios as $h) {
            if($horaCita >= $h['hora_inicio'] && $horaCita <= $h['hora_fin']) return true;
        }

        return false;
    }

    // ----------------------------------------
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $database = new Database();
            $db = $database->connect();
            $citaModel = new Cita($db);
            $auditoria = new Auditoria($db);

            $rol = $_SESSION['user_role_id'] ?? 0;
            $datos = [
                'id_paciente' => ($rol == 3) ? $_SESSION['user_id'] : $_POST['paciente_id'],
                'id_medico' => ($rol == 2) ? $_SESSION['medico_id'] : $_POST['medico_id'],
                'id_servicio' => $_POST['id_servicio'],
                'fecha_cita' => $_POST['fecha'],
                'motivo' => $_POST['motivo']
            ];

            $validacion = $this->validarReglasNegocio($datos['fecha_cita'], $datos['id_medico']);
            if($validacion != 'ok') { header('Location: ' . BASE_URL . '/citas?msg=' . $validacion); exit; }

            if($citaModel->verificarDisponibilidad($datos['id_medico'], $datos['fecha_cita'])) {
                header('Location: ' . BASE_URL . '/citas?msg=ocupado'); exit;
            }

            if($citaModel->crear($datos)) {
                $auditoria->registrar($_SESSION['user_id'], 'CREAR', 'citas', 0, 'Cita Agendada');
                header('Location: ' . BASE_URL . '/citas?msg=creado');
            } else {
                header('Location: ' . BASE_URL . '/citas?msg=error');
            }
        }
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $database = new Database();
            $db = $database->connect();
            $citaModel = new Cita($db);
            $auditoria = new Auditoria($db);
            $rol = $_SESSION['user_role_id'] ?? 0;

            $datos = [
                'id_cita' => $_POST['id_cita'],
                'id_medico' => ($rol == 2) ? $_SESSION['medico_id'] : $_POST['medico_id'],
                'id_servicio' => $_POST['id_servicio'],
                'fecha_cita' => $_POST['fecha'],
                'motivo' => $_POST['motivo'],
                'estado' => $_POST['estado']
            ];

            if ($datos['estado'] == 'Pendiente' || $datos['estado'] == 'Confirmada') {
                $validacion = $this->validarReglasNegocio($datos['fecha_cita'], $datos['id_medico']);
                if($validacion != 'ok') { header('Location: ' . BASE_URL . '/citas?msg=' . $validacion); exit; }
            }

            if ($citaModel->verificarDisponibilidad($datos['id_medico'], $datos['fecha_cita'], $datos['id_cita'])) {
                header('Location: ' . BASE_URL . '/citas?msg=ocupado'); exit;
            }

            if($citaModel->actualizar($datos)) {
                $auditoria->registrar($_SESSION['user_id'], 'ACTUALIZAR', 'citas', $datos['id_cita'], 'Estado: '.$datos['estado']);
                header('Location: ' . BASE_URL . '/citas?msg=actualizado');
            } else {
                header('Location: ' . BASE_URL . '/citas?msg=error');
            }
        }
    }

   public function finalizar() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $database = new Database();
        $db = $database->connect();
        $citaModel = new Cita($db);

        $id = $_POST['id_cita'];
        $dias = !empty($_POST['dias_reposo']) ? (int)$_POST['dias_reposo'] : 0;
        $fin_reposo = ($dias > 0) ? date('Y-m-d', strtotime("+$dias days")) : null;

        // Mapeo exacto según tu imagen de la tabla 'citas'
        $params = [
            $id,
            $_POST['diagnostico'],
            $_POST['prescripcion'],
            $_POST['peso'] ?? null,
            $_POST['talla'] ?? null,
            $_POST['presion'] ?? null, // Columna presion_arterial en DB
            $_POST['temperatura'] ?? null,
            $dias,
            $fin_reposo
        ];

        if($citaModel->finalizarAtencion(...$params)) {
            // Buscamos los datos del paciente para el mensaje
            $detalles = $citaModel->obtenerPorId($id);
            
            $waLink = "";
            if ($detalles && !empty($detalles['paciente_telefono'])) {
                $telefono = '51' . preg_replace('/\D/', '', $detalles['paciente_telefono']);
                $msj = "Hola " . $detalles['paciente'] . ", su receta es: " . $_POST['prescripcion'];
                $waLink = "&wa=" . urlencode("https://wa.me/$telefono?text=" . urlencode($msj));
            }

            header('Location: ' . BASE_URL . '/citas?msg=atendido' . $waLink);
        } else {
            header('Location: ' . BASE_URL . '/citas?msg=error');
        }
        exit;
    }
}


    public function cobrar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $database = new Database();
            $db = $database->connect();
            $pagoModel = new Pago($db);
            $auditoria = new Auditoria($db);

            $datos = [
                'id_cita' => $_POST['id_cita'],
                'monto' => $_POST['monto'],
                'metodo_pago' => $_POST['metodo_pago'],
                'observaciones' => $_POST['observaciones']
            ];

            if($pagoModel->registrar($datos)) {
                $auditoria->registrar($_SESSION['user_id'], 'PAGO', 'pagos', 0, 'Cobro: ' . $datos['monto']);
                header('Location: ' . BASE_URL . '/citas?msg=pagado');
            } else {
                header('Location: ' . BASE_URL . '/citas?msg=error');
            }
        }
    }

    public function eliminar() {
        if (isset($_GET['id'])) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            
            if ($_SESSION['user_role_id'] != 1) { 
                header('Location: ' . BASE_URL . '/citas?msg=error_permisos'); 
                exit; 
            }

            $database = new Database();
            $db = $database->connect();
            $citaModel = new Cita($db);
            $auditoria = new Auditoria($db);

            if ($citaModel->eliminar($_GET['id'])) {
                $auditoria->registrar($_SESSION['user_id'], 'ELIMINAR', 'citas', $_GET['id'], 'Cita Borrada');
                header('Location: ' . BASE_URL . '/citas?msg=eliminado');
            } else {
                header('Location: ' . BASE_URL . '/citas?msg=error');
            }
        }
    }
}
