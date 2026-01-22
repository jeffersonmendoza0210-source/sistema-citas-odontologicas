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

$db = new Database();
$conn = $db->connect();
$configModel = new Configuracion($conn);
$empresa = $configModel->obtener();

$nombre_app = $empresa['nombre_clinica'] ?? 'MediCitas';
$logo_app   = $empresa['logo'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Crear cuenta - <?= $nombre_app ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
body{
    background:linear-gradient(135deg,#667eea,#764ba2);
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    font-family:'Segoe UI',sans-serif;
}

/* 🔽 CARD MÁS COMPACTO */
.card-wide{
    width:90%;
    max-width:620px;   /* ⬅ MÁS PEQUEÑO */
    border-radius:16px;
    border:none;
    box-shadow:0 10px 25px rgba(0,0,0,.18);
    overflow:hidden;
}

/* 🔽 PADDING REDUCIDO */
.card-body{
    padding:1.4rem 1.8rem;
}

.card-header{
    background:#fff;
    padding:16px;
    border-bottom:none;
}

/* 🔽 LOGO MÁS PEQUEÑO */
.logo-img{
    max-height:55px;
    max-width:140px;
    object-fit:contain;
    margin-bottom:4px;
}

/* 🔽 TÍTULOS MÁS DISCRETOS */
.section-title{
    font-size:.7rem;
    font-weight:700;
    color:#6c757d;
    margin-bottom:4px;
    letter-spacing:.05em;
}

/* 🔽 INPUTS MÁS BAJITOS */
.form-control,
.form-select{
    padding:7px 10px;
    font-size:.85rem;
    background:#f8f9fa;
    border:1px solid #eee;
}

.input-group-text{
    padding:0 10px;
    font-size:.85rem;
}

/* 🔽 MENOS ESPACIO ENTRE CAMPOS */
.mb-3{
    margin-bottom:.5rem!important;
}

/* 🔽 ALERTA MÁS DELGADA */
.alert{
    padding:.55rem .75rem;
    font-size:.75rem;
}

/* 🔽 BOTÓN MÁS COMPACTO */
.btn-primary{
    background:#667eea;
    border:none;
    padding:8px 22px;
    font-size:.85rem;
    font-weight:600;
}

.btn-primary:hover{
    background:#764ba2;
}

/* SECCIÓN MÉDICO */
#medicoSection{
    display:none;
}


</style>
</head>

<body>

<div class="card card-wide">

<div class="card-header text-center">

    <?php if($logo_app && file_exists(APP_ROOT . '/../public/uploads/' . $logo_app)): ?>
        <img src="<?= BASE_URL ?>/uploads/<?= $logo_app ?>" 
             alt="Logo" 
             class="logo-img">
    <?php else: ?>
        <div class="text-primary mb-2">
            <i class="fas fa-clinic-medical fa-3x"></i>
        </div>
    <?php endif; ?>

    <h3 class="fw-bold mb-1"><?= $nombre_app ?></h3>
    <p class="text-muted mb-0">Registro de usuario</p>
</div>


<div class="card-body p-4">
<form action="<?= BASE_URL ?>/auth/store" method="POST">

<div class="row g-4">

<!-- COLUMNA IZQUIERDA -->
<div class="col-md-6">

<div class="section-title">TIPO DE USUARIO</div>
<div class="mb-3 input-group">
    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
    <select name="rol" id="rol" class="form-select" required>
        <option value="3">Paciente</option>
        <option value="2">Médico</option>
    </select>
</div>

<div class="section-title">DATOS PERSONALES</div>

<div class="mb-3 input-group">
    <span class="input-group-text"><i class="fas fa-user"></i></span>
    <input type="text" name="nombre" class="form-control" placeholder="Nombre completo" required>
</div>

<div class="mb-3 input-group">
    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
    <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
</div>

<div class="mb-3 input-group">
    <span class="input-group-text"><i class="fas fa-phone"></i></span>
    <input type="text" name="telefono" class="form-control" placeholder="Teléfono">
</div>

<div class="section-title">SEGURIDAD</div>

<div class="mb-3 input-group">
    <span class="input-group-text"><i class="fas fa-lock"></i></span>
    <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
</div>

<div class="mb-3 input-group">
    <span class="input-group-text"><i class="fas fa-lock"></i></span>
    <input type="password" name="password_confirm" class="form-control" placeholder="Confirmar contraseña" required>
</div>

</div>

<!-- COLUMNA DERECHA -->
<div class="col-md-6">

<div id="medicoSection">

<div class="section-title">INFORMACIÓN MÉDICA</div>

<div class="mb-3 input-group">
    <span class="input-group-text"><i class="fas fa-stethoscope"></i></span>
    <input type="text" name="especialidad" class="form-control" placeholder="Especialidad médica">
</div>

<div class="mb-3 input-group">
    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
    <input type="text" name="cmp" class="form-control" placeholder="CMP / Colegiatura">
</div>

</div>

<div class="alert alert-light border mt-4">
    <i class="fas fa-info-circle text-primary me-2"></i>
    <small>
        Si eres médico, completa tu información profesional.
        Tu cuenta podrá requerir validación.
    </small>
</div>

</div>

</div>

<div class="text-center mt-4">
    <button class="btn btn-primary rounded-pill px-5">
        Crear cuenta
    </button>
</div>

<div class="text-center mt-3">
    <a href="<?= BASE_URL ?>/login" class="small">
        <i class="fas fa-arrow-left me-1"></i> Volver al login
    </a>
</div>

</form>
</div>

</div>

<script>
const rol = document.getElementById('rol');
const medicoSection = document.getElementById('medicoSection');

rol.addEventListener('change',()=>{
    medicoSection.style.display = rol.value == 2 ? 'block' : 'none';
});
</script>

</body>
</html>
