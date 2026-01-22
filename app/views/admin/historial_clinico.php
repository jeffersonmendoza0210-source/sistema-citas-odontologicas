<?php require_once APP_ROOT . '/views/layouts/header.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* 1. ESTILOS GENERALES Y TIMELINE */
    .timeline { border-left: 3px solid #e9ecef; margin-left: 20px; padding-left: 30px; position: relative; }
    .timeline-item { position: relative; margin-bottom: 40px; }
    .timeline-dot { width: 16px; height: 16px; background: #0d6efd; border-radius: 50%; position: absolute; left: -39px; top: 5px; border: 3px solid white; box-shadow: 0 0 0 2px #0d6efd; }
    .card-historia { border-left: 5px solid #0d6efd; transition: 0.3s; }
    
    /* 2. ESTILOS ODONTOGRAMA */
    .odontograma-scroll { overflow-x: auto; padding-bottom: 10px; }
    .odontograma-full-wrapper { min-width: 1050px; text-align: center; background: #fff; padding: 20px; }
    .diente-container { display: inline-block; margin: 2px; text-align: center; background: #fff; padding: 5px; border-radius: 4px; border: 1px solid #eee; width: 62px; }
    .diente-svg { width: 50px; height: 50px; cursor: pointer; }
    .cara { fill: #fff; stroke: #333; stroke-width: 1.5; transition: 0.2s; }
    .cara:hover { fill: #f8f9fa; stroke: #000; }
    
    /* COLORES DE ESTADO */
    .estado-caries { fill: #dc3545 !important; } 
    .estado-obturado { fill: #0d6efd !important; } 
    .estado-ausente { fill: #343a40 !important; }

    .legend-box { display: inline-block; width: 15px; height: 15px; border-radius: 3px; margin-right: 5px; vertical-align: middle; border: 1px solid #ddd; }
    .bg-caries { background-color: #dc3545; }
    .bg-obturado { background-color: #0d6efd; }
    .bg-ausente { background-color: #343a40; }
    .bg-sano { background-color: #ffffff; }

    /* 3. GALERÍA */
    .img-thumbnail-custom { height: 120px; object-fit: cover; width: 100%; border-radius: 8px; cursor: pointer; transition: 0.3s; border: 1px solid #dee2e6; }
    .img-thumbnail-custom:hover { transform: scale(1.05); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .file-icon-box { height: 120px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 8px; border: 1px solid #dee2e6; }

    /* 4. PESTAÑAS */
    .tab-pane { display: none; }
    .tab-pane.active { display: block !important; opacity: 1 !important; }
    .nav-link { cursor: pointer; }
    .nav-link.active { background-color: #fff !important; border-color: #dee2e6 #dee2e6 #fff !important; color: #0d6efd !important; }

    @media print {
        .no-print { display: none !important; }
        #print-area { display: block !important; }
    }
</style>

<div class="card shadow-sm border-0 mb-4 bg-white">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-1">Expediente Clínico</h4>
                <p class="text-muted mb-0">Paciente: <strong class="text-primary fs-5"><?php echo $paciente['nombre'] ?? 'No encontrado'; ?></strong></p>
                <small class="text-muted"><i class="fas fa-id-card me-1"></i> DNI: <?php echo $paciente['documento_identidad'] ?? 'S/N'; ?></small>
            </div>
            <div class="no-print">
                <button onclick="window.print()" class="btn btn-outline-primary btn-sm me-2"><i class="fas fa-print"></i></button>
                <a href="<?php echo BASE_URL; ?>/pacientes" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
            </div>
        </div>
    </div>
</div>

<ul class="nav nav-tabs mb-4 no-print" id="customTabs">
    <li class="nav-item"><button class="nav-link active fw-bold" data-target="#panel-consultas"><i class="fas fa-notes-medical me-2"></i>CONSULTAS</button></li>
    <li class="nav-item"><button class="nav-link fw-bold" data-target="#panel-odontograma"><i class="fas fa-tooth me-2"></i>ODONTOGRAMA</button></li>
    <li class="nav-item"><button class="nav-link fw-bold" data-target="#panel-archivos"><i class="fas fa-images me-2"></i>SUBIR ARCHIVOS</button></li>
    <li class="nav-item"><button class="nav-link fw-bold" data-target="#panel-graficos"><i class="fas fa-chart-line me-2"></i>EVOLUCION</button></li>
</ul>

<div class="tab-content" id="print-area">
    
    <div class="tab-pane active" id="panel-consultas">
        <div class="timeline p-3">
            <?php if(isset($historial) && $historial->rowCount() > 0): ?>
                <?php while($cita = $historial->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="text-muted small fw-bold mb-1"><?php echo date('d/m/Y', strtotime($cita['fecha_cita'])); ?></div>
                        <div class="card shadow-sm card-historia border-0 mb-3">
                            <div class="card-body">
                                <h6 class="fw-bold text-primary">Dr. <?php echo $cita['medico']; ?></h6>
                                <p class="mb-0 small text-dark"><?php echo nl2br($cita['diagnostico']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-5 text-muted"><i class="fas fa-folder-open fa-3x mb-3"></i><p>No hay consultas registradas.</p></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="tab-pane" id="panel-odontograma">
        <div class="card border-0 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                <h5 class="fw-bold text-primary mb-0"><i class="fas fa-tooth me-2"></i>Esquema Dental Interactivo</h5>
                <div class="bg-light p-2 rounded border d-flex gap-3 px-3">
                    <div class="small"><span class="legend-box bg-sano"></span>Sano</div>
                    <div class="small"><span class="legend-box bg-caries"></span>Caries (Rojo)</div>
                    <div class="small"><span class="legend-box bg-obturado"></span>Obturado (Azul)</div>
                    <div class="small"><span class="legend-box bg-ausente"></span>Ausente (Negro)</div>
                </div>
                <button onclick="guardarOdontogramaServidor()" class="btn btn-success fw-bold"><i class="fas fa-save me-1"></i> Guardar Cambios</button>
            </div>
            
            <div class="odontograma-scroll">
                <div class="odontograma-full-wrapper">
                    <div class="d-flex justify-content-center mb-5">
                        <?php foreach([18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28] as $p): ?>
                            <div class="diente-container">
                                <small class="fw-bold d-block mb-1 text-muted"><?= $p ?></small>
                                <svg class="diente-svg" viewBox="0 0 100 100" data-pieza="<?= $p ?>">
                                    <polygon points="10,10 90,10 75,25 25,25" class="cara" data-cara="V" onclick="gestionarCara(this)"/>
                                    <polygon points="90,10 90,90 75,75 75,25" class="cara" data-cara="D" onclick="gestionarCara(this)"/>
                                    <polygon points="10,90 90,90 75,75 25,75" class="cara" data-cara="L" onclick="gestionarCara(this)"/>
                                    <polygon points="10,10 10,90 25,75 25,25" class="cara" data-cara="M" onclick="gestionarCara(this)"/>
                                    <rect x="25" y="25" width="50" height="50" class="cara" data-cara="O" onclick="gestionarCara(this)"/>
                                </svg>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="d-flex justify-content-center">
                        <?php foreach([48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38] as $p): ?>
                            <div class="diente-container">
                                <svg class="diente-svg" viewBox="0 0 100 100" data-pieza="<?= $p ?>">
                                    <polygon points="10,10 90,10 75,25 25,25" class="cara" data-cara="L" onclick="gestionarCara(this)"/>
                                    <polygon points="90,10 90,90 75,75 75,25" class="cara" data-cara="D" onclick="gestionarCara(this)"/>
                                    <polygon points="10,90 90,90 75,75 25,75" class="cara" data-cara="V" onclick="gestionarCara(this)"/>
                                    <polygon points="10,10 10,90 25,75 25,25" class="cara" data-cara="M" onclick="gestionarCara(this)"/>
                                    <rect x="25" y="25" width="50" height="50" class="cara" data-cara="O" onclick="gestionarCara(this)"/>
                                </svg>
                                <small class="fw-bold d-block mt-1 text-muted"><?= $p ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-5 text-start">
                        <h6 class="fw-bold border-bottom pb-2 text-primary">Detalle de Hallazgos Clínicos</h6>
                        <table class="table table-sm table-hover mt-2 shadow-sm border">
                            <thead class="table-primary">
                                <tr>
                                    <th>Pieza</th>
                                    <th>Cara</th>
                                    <th>Estado</th>
                                    <th>Notas</th>
                                </tr>
                            </thead>
                            <tbody id="body-hallazgos">
                                </tbody>
                        </table>
                    </div>
                    </div>
            </div>
        </div>
    </div>

    <div class="tab-pane" id="panel-archivos">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold mb-3 text-primary">Documentos y Radiografías</h5>
            <form action="<?php echo BASE_URL; ?>/pacientes/subirArchivo" method="POST" enctype="multipart/form-data" class="row g-3 no-print bg-light p-3 rounded border mb-4">
                <input type="hidden" name="id_paciente" value="<?php echo $paciente['id_usuario'] ?? ''; ?>">
                <div class="col-md-5">
                    <label class="small fw-bold">Seleccionar Archivo (Imagen o PDF)</label>
                    <input type="file" name="documento" class="form-control" accept="image/*,.pdf" required>
                </div>
                <div class="col-md-5">
                    <label class="small fw-bold">Nombre del Documento</label>
                    <input type="text" name="nombre_archivo" class="form-control" placeholder="Ej: Placa Panorámica" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">SUBIR</button>
                </div>
            </form>

            <div class="row" id="contenedorArchivos">
                <?php if(!empty($archivos)): ?>
                    <?php foreach($archivos as $arc): ?>
                        <?php 
                            $ruta = $arc['ruta_archivo']; 
                            $nombre = $arc['nombre_archivo'];
                            $extension = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));
                        ?>
                        <div class="col-6 col-md-3 col-lg-2 mb-4 text-center">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="p-2">
                                    <?php if(in_array($extension, ['jpg','jpeg','png','webp'])): ?>
                                        <a href="<?= BASE_URL . '/uploads/' . $ruta ?>" target="_blank">
                                            <img src="<?= BASE_URL . '/uploads/' . $ruta ?>" class="img-thumbnail-custom">
                                        </a>
                                    <?php else: ?>
                                        <div class="file-icon-box">
                                            <i class="fas <?= ($extension == 'pdf') ? 'fa-file-pdf text-danger' : 'fa-file-alt text-secondary' ?> fa-3x"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body p-2">
                                    <p class="small fw-bold text-truncate mb-1"><?= $nombre ?></p>
                                    <a href="<?= BASE_URL . '/uploads/' . $ruta ?>" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2 mt-1">Ver</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5 text-muted"><p>No hay archivos registrados.</p></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="tab-pane" id="panel-graficos">
        <div class="row">
             <div class="col-12 text-center text-muted p-5">
                 <p>Gráficos de evolución disponibles próximamente.</p>
             </div>
        </div>
    </div>
</div>

<script>
    const idPaciente = "<?php echo $paciente['id_usuario'] ?? ''; ?>";
    // Aseguramos que datosOdontograma sea un objeto válido
    let datosOdontograma = <?php echo json_encode($odontogramaBD ?? (object)[]); ?>;

    // --- 1. GESTIÓN PESTAÑAS ---
    document.querySelectorAll('.nav-link').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.nav-link').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            document.querySelector(this.getAttribute('data-target')).classList.add('active');
        });
    });

    // --- 2. FUNCIÓN PARA PINTAR LOS DIENTES SEGÚN LOS DATOS CARGADOS ---
    function pintarOdontogramaInicial() {
        Object.keys(datosOdontograma).forEach(key => {
            const info = datosOdontograma[key];
            // Buscamos el SVG de la pieza y luego la cara dentro de ese SVG
            const svgDiente = document.querySelector(`.diente-svg[data-pieza="${info.pieza}"]`);
            if (svgDiente) {
                const caraPolygon = svgDiente.querySelector(`.cara[data-cara="${info.cara}"]`);
                if (caraPolygon) {
                    // Limpiamos estados anteriores
                    caraPolygon.classList.remove('estado-caries', 'estado-obturado', 'estado-ausente');
                    // Aplicamos el estado si no es normal
                    if (info.estado !== 'normal') {
                        caraPolygon.classList.add('estado-' + info.estado);
                    }
                }
            }
        });
        // También llenamos la tabla de texto abajo
        renderizarTabla();
    }

    // --- 3. GESTIÓN INTERACTIVA (CLIC EN CARA) ---
    function gestionarCara(el) {
        const pieza = el.closest('svg').dataset.pieza;
        const cara = el.dataset.cara;
        const key = pieza + '_' + cara;
        const actual = datosOdontograma[key] || { estado: 'normal', notas: '' };

        Swal.fire({
            title: `Pieza ${pieza} - Cara ${cara}`,
            html: `
                <select id="sw-estado" class="form-select mb-2">
                    <option value="normal" ${actual.estado==='normal'?'selected':''}>Sano / Normal</option>
                    <option value="caries" ${actual.estado==='caries'?'selected':''}>Caries (Rojo)</option>
                    <option value="obturado" ${actual.estado==='obturado'?'selected':''}>Obturado (Azul)</option>
                    <option value="ausente" ${actual.estado==='ausente'?'selected':''}>Ausente (Negro)</option>
                </select>
                <textarea id="sw-notas" class="form-control" placeholder="Notas...">${actual.notas || ''}</textarea>
            `,
            confirmButtonText: 'Aplicar',
            showCancelButton: true
        }).then(res => {
            if(res.isConfirmed) {
                const estado = document.getElementById('sw-estado').value;
                const notas = document.getElementById('sw-notas').value;
                
                datosOdontograma[key] = { pieza, cara, estado, notas };
                
                // Actualizar visualmente el color
                el.classList.remove('estado-caries', 'estado-obturado', 'estado-ausente');
                if(estado !== 'normal') el.classList.add('estado-' + estado);

                renderizarTabla();
            }
        });
    }

    // --- 4. RENDERIZAR TABLA DE HALLAZGOS ---
    function renderizarTabla() {
        const tbody = document.getElementById('body-hallazgos');
        if(!tbody) return;
        tbody.innerHTML = ''; 
        
        Object.values(datosOdontograma).forEach(d => {
            if(d.estado !== 'normal' || (d.notas && d.notas.trim() !== '')) {
                let badgeClass = 'bg-secondary';
                if(d.estado === 'caries') badgeClass = 'bg-danger';
                if(d.estado === 'obturado') badgeClass = 'bg-primary';
                if(d.estado === 'ausente') badgeClass = 'bg-dark';

                tbody.innerHTML += `
                    <tr>
                        <td class="fw-bold">${d.pieza}</td>
                        <td>${d.cara}</td>
                        <td><span class="badge ${badgeClass}">${d.estado.toUpperCase()}</span></td>
                        <td>${d.notas || '-'}</td>
                    </tr>`;
            }
        });
    }

    // --- 5. GUARDAR AL SERVIDOR ---
let guardandoOdontograma = false;

function guardarOdontogramaServidor() {

    if (guardandoOdontograma) return; // evita doble envío

    const detallesEnviar = Object.values(datosOdontograma);

    if (detallesEnviar.length === 0) {
        Swal.fire(
            'Información',
            'No hay cambios detectados en el odontograma para guardar.',
            'info'
        );
        return;
    }

    guardandoOdontograma = true;

    fetch('<?php echo BASE_URL; ?>/pacientes/guardarOdontograma', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            id_paciente: idPaciente,
            detalles: detallesEnviar
        })
    })
    .then(res => {
        if (!res.ok) throw new Error('Error HTTP');
        return res.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire(
                '¡Guardado!',
                'El odontograma se actualizó correctamente.',
                'success'
            );
        } else {
            Swal.fire(
                'Error',
                data.message || 'No se pudo guardar el odontograma.',
                'error'
            );
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error', 'Error de conexión con el servidor.', 'error');
    })
    .finally(() => {
        guardandoOdontograma = false;
    });
}


    // --- 6. INICIALIZACIÓN AL CARGAR PÁGINA ---
    document.addEventListener('DOMContentLoaded', function() {
        // Pintar el dibujo con lo que traemos de la BD
        pintarOdontogramaInicial();

        // Alertas de subida de archivos
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('upload')) {
            const status = urlParams.get('upload');
            if (status === 'success') {
                Swal.fire({ icon: 'success', title: '¡Subida exitosa!', timer: 2000, showConfirmButton: false });
            } else {
                Swal.fire({ icon: 'error', title: 'Error al subir archivo' });
            }
            window.history.replaceState({}, document.title, window.location.pathname + "?id=" + urlParams.get('id'));
        }
    });
    
</script>

<?php require_once APP_ROOT . '/views/layouts/footer.php'; ?>