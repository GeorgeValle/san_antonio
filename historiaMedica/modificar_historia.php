<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../core/cerrarsesion.php');
    exit;
}

require('../shared/encabezado.inc.php');
require('../shared/barraLateral.inc.php');

require_once '../funciones/conexion.php';
$conexion = ConexionBD();

$mensaje = '';
if (!isset($_GET['ID_HISTORIA'])) {
    echo "<div class='alert alert-danger'>ID de historia médica no proporcionado.</div>";
    exit;
}

$idHistoria = intval($_GET['ID_HISTORIA']);

// Obtener los datos de la historia médica con paciente
$sql = "SELECT hm.*, p.nombre, p.apellido, p.dni 
        FROM historiaMedica hm
        INNER JOIN pacientes p ON hm.idPaciente = p.idPaciente
        WHERE hm.idHistoriaMedica = ?";
$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "i", $idHistoria);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$historia = mysqli_fetch_assoc($resultado);
if (!$historia) {
    echo "<div class='alert alert-danger'>Historia médica no encontrada.</div>";
    exit;
}

// Función para obtener servicios del paciente
function ObtenerServiciosPorPaciente($conexion, $idPaciente) {
    $sql = "SELECT s.denominacion, COUNT(*) AS cantidad
            FROM turnos t
            INNER JOIN servicios s ON t.idServicio = s.idServicio
            WHERE t.idPaciente = ?
            GROUP BY s.denominacion";
    $stmt = mysqli_prepare($conexion, $sql);
    if ($stmt === false) {
        error_log("Error al preparar ObtenerServiciosPorPaciente: " . mysqli_error($conexion));
        return [];
    }
    mysqli_stmt_bind_param($stmt, "i", $idPaciente);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $servicios = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $servicios[] = $fila;
    }
    mysqli_stmt_close($stmt);
    return $servicios;
}

// Procesar el formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Enfermedades y medicamentos vienen como array de múltiples select
    $enfermedades = isset($_POST['Enfermedades']) ? $_POST['Enfermedades'] : [];
    $medicamentos = isset($_POST['Medicamentos']) ? $_POST['Medicamentos'] : [];
    $esparcimiento = isset($_POST['Esparcimiento']) ? $_POST['Esparcimiento'] : [];

    // Convertir arrays a strings separados por coma para guardar en DB
    $enfermedadesStr = implode(', ', array_map('trim', $enfermedades));
    $medicamentosStr = implode(', ', array_map('trim', $medicamentos));
    $esparcimientoStr = implode(', ', array_map('trim', $esparcimiento));

    $sqlUpdate = "UPDATE historiaMedica 
                  SET enfermedades = ?, medicamentos = ?, esparcimiento = ?
                  WHERE idHistoriaMedica = ?";
    $stmtUpdate = mysqli_prepare($conexion, $sqlUpdate);
    mysqli_stmt_bind_param($stmtUpdate, "sssi", $enfermedadesStr, $medicamentosStr, $esparcimientoStr, $idHistoria);
    mysqli_stmt_execute($stmtUpdate);

    if (mysqli_stmt_affected_rows($stmtUpdate) >= 0) {
        $mensaje = "Historia médica actualizada correctamente.";
        // Actualizar el arreglo $historia para mostrar en el form
        $historia['enfermedades'] = $enfermedadesStr;
        $historia['medicamentos'] = $medicamentosStr;
        $historia['esparcimiento'] = $esparcimientoStr;
    } else {
        $mensaje = "Error al actualizar historia médica.";
    }
}

// Obtener servicios para mostrar
$servicios = ObtenerServiciosPorPaciente($conexion, $historia['idPaciente']);

// Convertir las cadenas guardadas en DB a arrays para el select multiple
function stringToArray($str) {
    $arr = array_map('trim', explode(',', $str));
    if (count($arr) === 1 && $arr[0] === '') return [];
    return $arr;
}
$enfermedadesArray = stringToArray($historia['enfermedades']);
$medicamentosArray = stringToArray($historia['medicamentos']);
$esparcimientoArray = stringToArray($historia['esparcimiento']);
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Modificar Historia Médica</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../inicio/index.php">Menú</a></li>
                <li class="breadcrumb-item">Historia Médica</li>
                <li class="breadcrumb-item active">Modificar</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">

                <?php if ($mensaje): ?>
                <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
                <?php endif; ?>

                <form class="row g-3" method="POST">

                    <div class="col-md-6">
                        <label class="form-label">Nombre y Apellido</label>
                        <input type="text" class="form-control"
                            value="<?= htmlspecialchars($historia['nombre'] . ' ' . $historia['apellido']) ?>" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">DNI</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($historia['dni']) ?>"
                            readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Servicios (automático)</label>
                        <textarea class="form-control" rows="2" readonly>
<?php
foreach ($servicios as $s) {
    echo htmlspecialchars($s['denominacion'] . ' (' . $s['cantidad'] . ' veces)') . "\n";
}
?>
                        </textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Enfermedades</label>
                        <div class="input-group mb-2">
                            <input type="text" id="enfermedadInput" class="form-control"
                                placeholder="Agregar enfermedad">
                            <button type="button" class="btn btn-outline-secondary"
                                onclick="agregarOpcion('enfermedadesSelect', 'enfermedadInput')">Agregar</button>
                            <button type="button" class="btn btn-outline-danger"
                                onclick="quitarOpcion('enfermedadesSelect')">Quitar</button>
                        </div>
                        <select class="form-select" name="Enfermedades[]" id="enfermedadesSelect" multiple required>
                            <?php foreach ($enfermedadesArray as $enfermedad): ?>
                            <option selected><?= htmlspecialchars($enfermedad) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Medicamentos</label>
                        <div class="input-group mb-2">
                            <input type="text" id="medicamentoInput" class="form-control"
                                placeholder="Agregar medicamento">
                            <button type="button" class="btn btn-outline-secondary"
                                onclick="agregarOpcion('medicamentosSelect', 'medicamentoInput')">Agregar</button>
                            <button type="button" class="btn btn-outline-danger"
                                onclick="quitarOpcion('medicamentosSelect')">Quitar</button>
                        </div>
                        <select class="form-select" name="Medicamentos[]" id="medicamentosSelect" multiple required>
                            <?php foreach ($medicamentosArray as $medicamento): ?>
                            <option selected><?= htmlspecialchars($medicamento) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Esparcimiento</label>
                        <select class="form-select" name="Esparcimiento[]" id="esparcimientoSelect" multiple required>
                            <?php
                            // Opciones fijas para esparcimiento
                            $opcionesEsparcimiento = [
                                'taller de dibujo', 'taller de musica', 'taller de lectura',
                                'taller de canto', 'taller de baile', 'yoga'
                            ];
                            foreach ($opcionesEsparcimiento as $opcion):
                                $selected = in_array($opcion, $esparcimientoArray) ? 'selected' : '';
                            ?>
                            <option value="<?= $opcion ?>" <?= $selected ?>><?= ucfirst($opcion) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="text-center mt-3">
                        <button class="btn btn-primary" type="submit">Guardar Cambios</button>
                        <a href="listados_historia.php" class="btn btn-secondary">Volver al listado</a>
                    </div>
                </form>

            </div>
        </div>
        <!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    </section>
</main>

<?php require('../shared/footer.inc.php'); ?>

<script>
function agregarOpcion(selectId, inputId) {
    const input = document.getElementById(inputId);
    const select = document.getElementById(selectId);
    const val = input.value.trim();
    if (val !== '') {
        // Evitar duplicados
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].value.toLowerCase() === val.toLowerCase()) {
                alert('Ya existe ese valor en la lista');
                input.value = '';
                return;
            }
        }
        const option = document.createElement('option');
        option.text = val;
        option.value = val;
        option.selected = true;
        select.add(option);
        input.value = '';
    }
}

function quitarOpcion(selectId) {
    const select = document.getElementById(selectId);
    let opcionesSeleccionadas = [];
    for (let i = select.options.length - 1; i >= 0; i--) {
        if (select.options[i].selected) {
            select.remove(i);
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
        $('#esparcimientoSelect').select2({
            placeholder: 'Selecciona actividades de esparcimiento',
            width: '100%'
        });
    });
</script>