<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>

<?php
// ==========================================
// 1. CONFIGURACIÓN: MONEDA
// ==========================================
$moneda = '$';
if (isset($empresa) && isset($empresa['moneda'])) {
    $moneda = $empresa['moneda'];
} elseif (isset($empresa_header) && isset($empresa_header['moneda'])) {
    $moneda = $empresa_header['moneda'];
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid px-4">
    
    <?php if (isset($_SESSION['msg']) && isset($_SESSION['type'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: "<?= ($_SESSION['type'] == 'success') ? '¡Éxito!' : 'Atención' ?>",
                    text: "<?= $_SESSION['msg'] ?>",
                    icon: "<?= ($_SESSION['type'] == 'danger') ? 'error' : $_SESSION['type'] ?>",
                    confirmButtonColor: '#212529',
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        </script>
        <?php 
            unset($_SESSION['msg']); 
            unset($_SESSION['type']); 
        ?>
    <?php endif; ?>

    <div class="card shadow border-0 mb-4 mt-4">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-tags me-2"></i> Tarifario de Servicios
            </h5>
            <button class="btn btn-light text-dark fw-bold rounded-pill px-4"
                    data-bs-toggle="modal" data-bs-target="#modalServicio">
                <i class="fas fa-plus me-2"></i> Nuevo Servicio
            </button>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tablaPro">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Nombre del Servicio</th>
                            <th>Descripción</th>
                            <th class="text-center">Precio Unitario</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($resultado) && is_array($resultado)): ?>
                        <?php foreach ($resultado as $row): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-dark">
                                    <?= htmlspecialchars($row['nombre_servicio'] ?? '') ?>
                                </td>
                                <td class="text-muted small">
                                    <?= htmlspecialchars($row['descripcion'] ?? '') ?>
                                </td>
                                <td class="text-center fw-bold fs-5 text-success">
                                    <?= $moneda . ' ' . number_format((float)($row['precio'] ?? 0), 2) ?>
                                </td>
                                <td class="text-center">
                                    <?php if (($row['estado'] ?? '') === 'Activo'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-3">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary border-0 me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditar"
                                            onclick="cargarDatos(
                                                '<?= $row['id_servicio'] ?>',
                                                '<?= htmlspecialchars($row['nombre_servicio'] ?? '', ENT_QUOTES) ?>',
                                                '<?= htmlspecialchars($row['descripcion'] ?? '', ENT_QUOTES) ?>',
                                                '<?= $row['precio'] ?>',
                                                '<?= $row['estado'] ?>'
                                            )">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger border-0" 
                                            onclick="confirmarEliminar('<?= $row['id_servicio'] ?>')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i> No hay servicios registrados
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalServicio" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Nuevo Servicio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= BASE_URL ?>/servicios/guardar" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="fw-bold">Nombre del Servicio</label>
                        <input type="text" name="nombre_servicio" class="form-control" required placeholder="Ej. Medicina General">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2" placeholder="Detalles del servicio..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Precio (<?= $moneda ?>)</label>
                        <input type="number" step="0.01" name="precio" class="form-control text-end" required placeholder="0.00">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark w-100">Guardar Servicio</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold text-dark">Editar Servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= BASE_URL ?>/servicios/actualizar" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id_servicio" id="edit_id">
                    <div class="mb-3">
                        <label class="fw-bold">Estado</label>
                        <select name="estado" id="edit_estado" class="form-select">
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Nombre del Servicio</label>
                        <input type="text" name="nombre_servicio" id="edit_nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Descripción</label>
                        <textarea name="descripcion" id="edit_desc" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Precio</label>
                        <input type="number" step="0.01" name="precio" id="edit_precio" class="form-control text-end" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning w-100 fw-bold">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Cargar datos en el modal de edición
function cargarDatos(id, nombre, desc, precio, estado) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nombre').value = nombre;
    document.getElementById('edit_desc').value = desc;
    document.getElementById('edit_precio').value = precio;
    document.getElementById('edit_estado').value = estado;
}

// Función de eliminación con confirmación visual
function confirmarEliminar(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "<?= BASE_URL ?>/servicios/eliminar?id=" + id;
        }
    })
}
</script>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>