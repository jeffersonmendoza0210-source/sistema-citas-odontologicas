<?php
// ===============================
// CONFIGURACIÓN INICIAL
// ===============================
if (!class_exists('Database')) {
    require_once __DIR__ . '/../../config/Database.php';
}
if (!class_exists('Configuracion')) {
    require_once __DIR__ . '/../../models/Configuracion.php';
}

$db_login = new Database();
$conn_login = $db_login->connect();
$configModel_login = new Configuracion($conn_login);
$empresa_login = $configModel_login->obtener();

// Valores por defecto
$nombre_app = !empty($empresa_login['nombre_clinica']) ? $empresa_login['nombre_clinica'] : 'MediCitas';
$logo_app   = !empty($empresa_login['logo']) ? $empresa_login['logo'] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= $nombre_app ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .card-login {
            width: 100%;
            max-width: 400px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            border: none;
        }
        .card-header {
            background: white;
            border-bottom: none;
            padding-top: 40px;
            padding-bottom: 20px;
        }
        .btn-primary {
            background: #667eea;
            border: none;
            padding: 12px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-primary:hover {
            background: #764ba2;
            transform: translateY(-2px);
        }
        .form-control {
            padding: 12px;
            background: #f8f9fa;
            border: 1px solid #eee;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #667eea;
            background: white;
        }
        .logo-img {
            max-height: 80px;
            max-width: 80%;
            object-fit: contain;
        }
    </style>
</head>

<body>

<div class="card card-login">
    <!-- HEADER -->
    <div class="card-header text-center">

        <?php if ($logo_app && file_exists(APP_ROOT . '/../public/uploads/' . $logo_app)): ?>
            <img src="<?= BASE_URL ?>/uploads/<?= $logo_app ?>" alt="Logo" class="logo-img mb-3">
        <?php else: ?>
            <div class="mb-3 text-primary">
                <i class="fas fa-tooth fa-4x"></i>
            </div>
        <?php endif; ?>

        <h3 class="fw-bold text-dark mb-0"><?= $nombre_app ?></h3>
        <p class="text-muted small">Acceso al Sistema</p>
    </div>

    <!-- BODY -->
    <div class="card-body p-4 pt-0">

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger text-center py-2 mb-3 shadow-sm border-0">
                <small>
                    <i class="fas fa-exclamation-circle me-1"></i>
                    Credenciales incorrectas
                </small>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/auth/authenticate" method="POST">

            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">
                    Correo Electrónico
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 text-secondary">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" name="email" class="form-control"
                           placeholder="usuario@correo.com" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small text-muted fw-bold">
                    Contraseña
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 text-secondary">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" class="form-control"
                           placeholder="••••••••" required>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary rounded-pill shadow-sm">
                    INGRESAR <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </div>

        </form>

        <!-- LINKS -->
        <div class="text-center mt-3">
            <a href="<?= BASE_URL ?>/auth/register" class="small text-decoration-none">
                <i class="fas fa-user-plus me-1"></i> Crear cuenta
            </a>
            <span class="mx-1 text-muted">|</span>
            <a href="<?= BASE_URL ?>/auth/password" class="small text-decoration-none">
                <i class="fas fa-key me-1"></i> ¿Olvidaste tu contraseña?
            </a>
        </div>

    </div>

    <!-- FOOTER -->
    <div class="card-footer text-center bg-white py-3 border-0">
        <small class="text-muted opacity-75">
            &copy; <?= date('Y') ?> <?= $nombre_app ?>
        </small>
    </div>
</div>

</body>
</html>
