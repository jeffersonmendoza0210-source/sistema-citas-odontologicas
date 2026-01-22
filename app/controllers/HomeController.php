<?php
// app/controllers/HomeController.php

require_once APP_ROOT . '/config/Database.php';
require_once APP_ROOT . '/models/Cita.php';
require_once APP_ROOT . '/models/Medico.php';
require_once APP_ROOT . '/models/Paciente.php';

class HomeController {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $database = new Database();
        $db = $database->connect();
        
        $citaModel = new Cita($db);
        $medicoModel = new Medico($db);
        $pacienteModel = new Paciente($db);
        
        $rol = $_SESSION['id_de_rol_de_usuario'] ?? $_SESSION['user_role_id'] ?? 0;
        $hoy = date('Y-m-d');
        $data = [];

        // 1. Datos básicos (Citas de hoy para la tabla)
        $data['total_citas'] = $citaModel->contarTotal();
        $data['total_medicos'] = $medicoModel->leer()->rowCount();
        $data['total_pacientes'] = $pacienteModel->leer()->rowCount();
        $data['citas_hoy'] = $citaModel->leer($hoy)->fetchAll(PDO::FETCH_ASSOC);
        
        // 2. Estadísticas para el gráfico de dona
        $stats = $citaModel->obtenerEstadisticasEstado();
        $data['chart_labels'] = [];
        $data['chart_data'] = [];
        $data['chart_colors'] = [];
        
        foreach($stats as $stat) {
            $data['chart_labels'][] = $stat['estado'];
            $data['chart_data'][] = $stat['cantidad'];
            $data['chart_colors'][] = $this->getColor($stat['estado']);
        }

        // 3. Lógica para el Calendario (Obtener todas las citas)
        // Traemos todas las citas para llenar el calendario mensual
        $todas_citas = $citaModel->leer()->fetchAll(PDO::FETCH_ASSOC);
        $eventos = [];

        foreach($todas_citas as $c) {
            $eventos[] = [
                'title'  => $c['paciente'],
                'start'  => $c['fecha_cita'], // Debe venir en formato YYYY-MM-DD HH:mm:ss
                'color'  => $this->getColor($c['estado']),
                'allDay' => false
            ];
        }
        
        // Convertimos a JSON para que JavaScript lo lea en la vista
        $data['eventos_json'] = json_encode($eventos);

        require_once APP_ROOT . '/views/admin/dashboard.php';
    }

    private function getColor($estado) {
        $colors = [
            'Pendiente'  => '#ffc107', 
            'Confirmada' => '#0d6efd', 
            'Finalizada' => '#198754', 
            'Cancelada'  => '#dc3545'
        ];
        return $colors[$estado] ?? '#6c757d';
    }
}