<?php
// 1. CONFIGURACIÓN INICIAL
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 2. RUTAS BASE
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domainName = $_SERVER['HTTP_HOST'];
$scriptPath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'); 

define('BASE_URL', $protocol . $domainName . $scriptPath);
define('APP_ROOT', dirname(__DIR__) . '/app');
define('PUBLIC_ROOT', __DIR__); // CAMBIO: Se agrega para corregir el error en PacienteController

// 3. AUTOCARGA DE CONTROLADORES
$controladores = [
    'AuthController', 'HomeController', 'CitaController', 'MedicoController', 
    'PacienteController', 'ServicioController', 'PagoController', 
    'ConfiguracionController', 'PerfilController', 'ReporteController',
    'AuditoriaController', 'EspecialidadController', 'MedicamentoController'
];

foreach ($controladores as $ctrl) {
    $archivo = APP_ROOT . '/controllers/' . $ctrl . '.php';
    if (file_exists($archivo)) {
        require_once $archivo;
    }
}

// 4. PROCESAR URL
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = $scriptPath;

$url = (strpos($request_uri, $base_path) === 0) ? substr($request_uri, strlen($base_path)) : $request_uri;
$url = trim($url, '/');
$url = strtok($url, '?');
$urlArray = explode('/', $url);

$controllerName = !empty($urlArray[0]) ? $urlArray[0] : 'home';
$action = $urlArray[1] ?? 'index';

// 5. SEGURIDAD
$userId = $_SESSION['user_id'] ?? null;

if (!$userId && $controllerName !== 'login' && $controllerName !== 'auth') {
    header('Location: ' . BASE_URL . '/login');
    exit;
}

if ($userId && $controllerName === 'login') {
    header('Location: ' . BASE_URL . '/home');
    exit;
}

// 6. DISPATCHER (MANTENIENDO TU LÓGICA DE CASOS)
switch ($controllerName) {
    
    case 'auth':
        if (class_exists('AuthController')) {
            $controller = new AuthController();
            if ($action == 'authenticate') $controller->authenticate();
            elseif ($action == 'logout') $controller->logout();
            else $controller->login();
        }
        break;

    case 'login':
        if (class_exists('AuthController')) {
            (new AuthController())->login();
        }
        break;

    case 'home':
        if (class_exists('HomeController')) {
            (new HomeController())->index();
        }
        break;

    case 'citas':
        if (class_exists('CitaController')) {
            $c = new CitaController();
            // CAMBIO: Se usa la variable $action que ya definiste arriba
            if ($action == 'guardar') $c->guardar();
            elseif ($action == 'actualizar') $c->actualizar();
            elseif ($action == 'finalizar') $c->finalizar();
            elseif ($action == 'eliminar') $c->eliminar();
            elseif ($action == 'listarEventos') $c->listarEventos();
            elseif ($action == 'cobrar') $c->cobrar();
            else $c->index();
        }
        break;

    case 'medicos':
        if (class_exists('MedicoController')) {
            $c = new MedicoController();
            if ($action == 'guardar') $c->guardar();
            elseif ($action == 'actualizar') $c->actualizar(); 
            elseif ($action == 'horarios') $c->horarios();
            elseif ($action == 'guardarHorario') $c->guardarHorario();
            elseif ($action == 'eliminarHorario') $c->eliminarHorario();
            else $c->index();
        }
        break;

    case 'pacientes':
    if (class_exists('PacienteController')) {
        $c = new PacienteController();

        if ($action == 'guardar') $c->guardar();
        elseif ($action == 'actualizar') $c->actualizar(); 
        elseif ($action == 'eliminar') $c->eliminar(); 
        elseif ($action == 'historial') $c->historial();
        elseif ($action == 'subirArchivo') $c->subirArchivo();
        elseif ($action == 'guardarOdontograma') $c->guardarOdontograma(); // 🔴 CLAVE
        else $c->index();
    }
    break;


    case 'servicios':
        if (class_exists('ServicioController')) {
            $c = new ServicioController();
            if ($action == 'guardar') $c->guardar();
            elseif ($action == 'actualizar') $c->actualizar();
            elseif ($action == 'eliminar') $c->eliminar();
            else $c->index();
        }
        break;

    case 'especialidades':
        if (class_exists('EspecialidadController')) {
            $c = new EspecialidadController();
            if ($action == 'guardar') $c->guardar();
            elseif ($action == 'actualizar') $c->actualizar();
            elseif ($action == 'eliminar') $c->eliminar();
            else $c->index();
        }
        break;

    case 'medicamentos':
        if (class_exists('MedicamentoController')) {
            $c = new MedicamentoController();
            if ($action == 'guardar') $c->guardar();
            elseif ($action == 'actualizar') $c->actualizar();
            elseif ($action == 'eliminar') $c->eliminar();
            else $c->index();
        }
        break;

    case 'pagos':
        if (class_exists('PagoController')) {
            $c = new PagoController();
            if ($action == 'eliminar') $c->eliminar();
            else $c->index();
        }
        break;

    case 'configuracion':
        if (class_exists('ConfiguracionController')) {
            $c = new ConfiguracionController();
            if ($action == 'guardar') $c->guardar();
            else $c->index();
        }
        break;

    case 'perfil':
        if (class_exists('PerfilController')) {
            $c = new PerfilController();
            if ($action == 'actualizar') $c->actualizar();
            else $c->index();
        }
        break;

    case 'reportes':
        if (class_exists('ReporteController')) (new ReporteController())->index();
        break;

    case 'auditoria':
        if (class_exists('AuditoriaController')) (new AuditoriaController())->index();
        break;

    default:
        header('Location: ' . BASE_URL . '/home');
        exit;
}