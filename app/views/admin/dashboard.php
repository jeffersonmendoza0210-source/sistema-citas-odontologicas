<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php 
    // Usamos la variable sincronizada en AuthController
    $rol = $_SESSION['user_role_id'] ?? 0; 
    $nombre = $_SESSION['user_name'] ?? 'Usuario';
    
    // El saludo ahora será exacto gracias a date_default_timezone_set en el header
    $hora = date('H');
    $saludo = ($hora < 12) ? 'Buenos días' : (($hora < 18) ? 'Buenas tardes' : 'Buenas noches');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0"><?php echo $saludo . ', ' . $nombre; ?></h3>
        <p class="text-muted small">Bienvenido a tu panel de control.</p>
    </div>
    <div class="text-end d-none d-md-block">
        <h5 class="fw-bold mb-0 text-primary"><?php echo date('h:i A'); ?></h5>
        <small class="text-muted"><?php echo date('d/m/Y'); ?></small>
    </div>
</div>

<?php if($rol == 1): // VISTA ADMINISTRADOR ?>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white h-100">
                <div class="card-body">
                    <h2 class="fw-bold mb-0"><?php echo $data['total_citas'] ?? 0; ?></h2>
                    <small class="opacity-75">Citas Totales</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white h-100">
                <div class="card-body">
                    <h2 class="fw-bold mb-0 text-success"><?php echo $data['total_medicos'] ?? 0; ?></h2>
                    <small class="text-muted">Médicos Activos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white h-100">
                <div class="card-body">
                    <h2 class="fw-bold mb-0 text-info"><?php echo $data['total_pacientes'] ?? 0; ?></h2>
                    <small class="text-muted">Pacientes Registrados</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-dark text-white h-100">
                <div class="card-body text-center py-3">
                    <a href="<?php echo BASE_URL; ?>/citas" class="btn btn-light btn-sm fw-bold w-100 mb-2">Ver Agenda</a>
                    <a href="<?php echo BASE_URL; ?>/reportes" class="btn btn-outline-light btn-sm w-100">Reportes</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                    <span>Actividad de Hoy</span>
                    <span class="badge bg-primary text-white"><?php echo date('d/m/Y'); ?></span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr><th>Hora</th><th>Paciente</th><th>Médico</th><th>Estado</th></tr>
                            </thead>
                            <tbody>
                                <?php 
                                // CORRECCIÓN: Manejo de datos según si es objeto PDO o Array
                                $citas = $data['citas_hoy'];
                                if(!empty($citas)): 
                                    foreach($citas as $cita): ?>
                                    <tr>
                                        <td class="fw-bold text-primary"><?php echo date('H:i', strtotime($cita['fecha_cita'])); ?></td>
                                        <td><?php echo htmlspecialchars($cita['paciente']); ?></td>
                                        <td class="small text-muted"><?php echo htmlspecialchars($cita['medico']); ?></td>
                                        <td>
                                            <?php 
                                            $st = $cita['estado'];
                                            $badge = ($st=='Pendiente')?'bg-warning':(($st=='Confirmada')?'bg-primary':(($st=='Finalizada')?'bg-success':'bg-danger'));
                                            ?>
                                            <span class="badge <?php echo $badge; ?>"><?php echo $st; ?></span>
                                        </td>
                                    </tr>
                                    <?php endforeach; 
                                else: ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted">No hay citas para hoy.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold">Distribución de Citas</div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="adminChart" style="max-height: 200px;"></canvas>
                </div>
            </div>
        </div>
    </div>

                                    
    <?php elseif($rol == 2): // VISTA MÉDICO ?>
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(45deg, #0d6efd, #0099ff);">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h2 class="fw-bold mb-0"><?php echo count($data['citas_hoy'] ?? []); ?></h2>
                        <span class="text-white-50 small fw-bold text-uppercase">Citas para hoy</span>
                    </div>
                    <div class="icon-shape bg-white text-primary rounded-circle p-3">
                        <i class="fas fa-calendar-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h2 class="fw-bold mb-0 text-dark"><?php echo $data['total_pacientes'] ?? 0; ?></h2>
                        <span class="text-muted small fw-bold text-uppercase">Mis Pacientes</span>
                    </div>
                    <div class="text-info opacity-25">
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-dark text-white h-100">
                <div class="card-body d-flex flex-column justify-content-center text-center">
                    <p class="small mb-2 opacity-75">Accesos rápidos</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo BASE_URL; ?>/pacientes" class="btn btn-primary btn-sm">
                            <i class="fas fa-search me-1"></i> Buscar Paciente
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-list-ul me-2 text-primary"></i>Agenda del Día</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light small text-uppercase">
                                <tr>
                                    <th class="ps-4">Hora</th>
                                    <th>Paciente</th>
                                    <th class="text-end pe-4">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($data['citas_hoy'])): 
                                    foreach($data['citas_hoy'] as $cita): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary"><?php echo date('h:i A', strtotime($cita['fecha_cita'])); ?></td>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($cita['paciente']); ?></div>
                                            <span class="badge rounded-pill bg-light text-dark border small"><?php echo $cita['estado']; ?></span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="<?php echo BASE_URL; ?>/pacientes/historial?id=<?php echo $cita['id_paciente']; ?>" class="btn btn-sm btn-outline-primary shadow-sm">
                                                <i class="fas fa-file-medical"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="3" class="text-center py-5 text-muted">No hay citas para hoy.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-calendar-alt me-2 text-primary"></i>Calendario de Citas</h5>
                </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js'></script>

    <style>
        #calendar { background: #fff; height: 500px; font-size: 0.9rem; }
        .fc-toolbar-title { font-size: 1.1rem !important; font-weight: bold; text-transform: capitalize; }
        .fc-event { cursor: pointer; border: none !important; font-size: 0.8rem; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listMonth'
                },
                // Aquí pasamos el JSON que creamos en el controlador
                events: <?php echo $data['eventos_json'] ?? '[]'; ?>,
                eventTimeFormat: { hour: 'numeric', minute: '2-digit', meridiem: 'short' },
                eventClick: function(info) {
                    alert('Paciente: ' + info.event.title + '\nEstado: ' + info.event.extendedProps.status || 'Programada');
                }
            });
            calendar.render();
        });
    </script>
<?php endif; ?>

<script>
    // Inicialización del gráfico con datos seguros
    const ctx = document.getElementById('adminChart');
    if(ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($data['chart_labels'] ?? []); ?>,
                datasets: [{
                    data: <?php echo json_encode($data['chart_data'] ?? []); ?>,
                    backgroundColor: ['#ffc107', '#0d6efd', '#198754', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: { plugins: { legend: { position: 'bottom' } } }
        });
    }
</script>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>       