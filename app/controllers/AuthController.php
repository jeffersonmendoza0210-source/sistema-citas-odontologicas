<?php
require_once APP_ROOT . '/config/Database.php';
require_once APP_ROOT . '/models/Usuario.php';
require_once APP_ROOT . '/models/Medico.php';

class AuthController {

    private $db;

    public function __construct() {
        // Inicializamos la conexión una sola vez para ahorrar recursos
        $database = new Database();
        $this->db = $database->connect();
    }

    public function login() {
        require_once APP_ROOT . '/views/auth/login.php';
    }

    public function register() {
        require_once APP_ROOT . '/views/auth/register.php';
    }

    public function registro() {
        require_once APP_ROOT . '/views/auth/registro.php';
    }

    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';

            $usuarioModel = new Usuario($this->db);
            $user = $usuarioModel->getByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                session_regenerate_id(true);

                // --- CORRECCIÓN DE SESIONES PARA COMPATIBILIDAD TOTAL ---
                $_SESSION['user_id'] = $user['id_usuario'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_avatar'] = $user['avatar'];
                
                // Esta es la variable CLAVE para el Sidebar y HomeController
                $_SESSION['user_role_id'] = $user['id_rol']; 
                
                // Mantenemos estas por si otros archivos las usan
                $_SESSION['id_usuario'] = $user['id_usuario'];
                $_SESSION['id_de_rol_de_usuario'] = $user['id_rol'];
                $_SESSION['user_role'] = $user['rol_nombre'] ?? 'Usuario';

                $_SESSION['usuario'] = [
                    'id'     => $user['id_usuario'],
                    'nombre' => $user['nombre'],
                    'email'  => $user['email'],
                    'rol'    => $user['rol_nombre'] ?? 'Usuario'
                ];

                // DATOS EXTRA PARA MÉDICO
                // Usamos id_rol que viene de tu tabla usuarios
                if ($user['id_rol'] == 2) {
                    $stmt = $this->db->prepare("SELECT id_medico FROM medicos WHERE id_usuario = :uid");
                    $stmt->execute(['uid' => $user['id_usuario']]);
                    $medicoData = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($medicoData) {
                        $_SESSION['medico_id'] = $medicoData['id_medico'];
                    }
                }

                header('Location: ' . BASE_URL . '/home');
                exit;
            } else {
                header('Location: ' . BASE_URL . '/login?error=1');
                exit;
            }
        }
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($this->db);

            if ($usuario->getByEmail($_POST['email'])) {
                header('Location: ' . BASE_URL . '/auth/register?error=email');
                exit;
            }

            // Asignación de propiedades
            $usuario->nombre = $_POST['nombre'];
            $usuario->email = $_POST['email'];
            $usuario->telefono = $_POST['telefono'];
            $usuario->password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $usuario->id_rol = $_POST['rol'];

            if ($usuario->crear()) {
                // Si es médico, registrar en la tabla medicos
                if ($_POST['rol'] == 2) {
                    $medico = new Medico($this->db);
                    $medico->id_usuario = $usuario->id_usuario;
                    // Ajuste: usar el nombre de columna correcto que espera tu modelo
                    $medico->id_especialidad = $_POST['especialidad'] ?? null; 
                    $medico->crear();
                }

                // INTEGRACIÓN DE ALERTA DE ÉXITO
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['mensaje_alerta'] = [
                    'tipo' => 'success',
                    'texto' => '¡Cuenta creada con éxito! Ya puedes ingresar.'
                ];

                header('Location: ' . BASE_URL . '/login');
                exit;
            }
        }
    }

    public function password() {
        require_once APP_ROOT . '/views/auth/password.php';
    }

    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($this->db);
            $user = $usuario->getByEmail($_POST['email']);

            if (!$user) {
                header('Location: ' . BASE_URL . '/auth/password?error=notfound');
                exit;
            }

            $usuario->id_usuario = $user['id_usuario'];
            $usuario->password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            
            if ($usuario->actualizarPassword()) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['mensaje_alerta'] = [
                    'tipo' => 'success',
                    'texto' => 'Tu contraseña ha sido actualizada correctamente.'
                ];
            }

            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        session_destroy();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}