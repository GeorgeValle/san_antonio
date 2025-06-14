<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../core/cerrarsesion.php');
    exit;
}

require('../shared/encabezado.inc.php');
require('../shared/barraLateral.inc.php');

require_once '../funciones/conexion.php';
$MiConexion = ConexionBD();
require_once '../funciones/select_general.php';

$idPaciente = isset($_GET['idPaciente']) ? intval($_GET['idPaciente']) : 0;

$servicios = ObtenerServiciosPorPaciente($MiConexion, $idPaciente);
echo json_encode($servicios);

$ListadoPacientes = Listar_Pacientes($MiConexion);
$CantidadPacientes = count($ListadoPacientes);

$_SESSION['Estilo'] = 'alert';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<p>Formulario enviado correctamente.</p>";

    if (InsertarHistoria($MiConexion)) {
        $_SESSION['Mensaje'] = 'Historia médica registrada correctamente.';
        $_SESSION['Estilo'] = 'success';
        $_POST = array();
    } else {
        $_SESSION['Mensaje'] = 'Error al registrar historia médica.';
        $_SESSION['Estilo'] = 'danger';
    }
}
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Historia Médica</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../inicio/index.php">Menu</a></li>
                <li class="breadcrumb-item">Historia Médica</li>
                <li class="breadcrumb-item active">Agregar</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Agregar Historia Médica</h5>

                <form class="row g-3" method="post">
                    <?php if (!empty($_SESSION['Mensaje'])) { ?>
                        <div class="alert alert-<?php echo $_SESSION['Estilo']; ?>">
                            <?php echo $_SESSION['Mensaje']; ?>
                        </div>
                    <?php } ?>

                    <!-- Paciente -->
                    <div class="col-md-6">
                        <label class="form-label">Paciente</label>
                        <select class="form-select" name="Paciente" id="paciente" required>
                            <option value="">Selecciona un paciente</option>
                            <?php foreach ($ListadoPacientes as $p) { ?>
                                <option value="<?= $p['ID_PACIENTE']; ?>"
                                    data-dni="<?= $p['DNI']; ?>">
                                    <?= $p['NOMBRE'] . ' ' . $p['APELLIDO']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- DNI -->
                    <div class="col-md-6">
                        <label class="form-label">DNI</label>
                        <input type="text" class="form-control" name="DNI" id="dni" readonly required>
                    </div>

                    <!-- Enfermedades -->
                    <div class="col-md-6">
                        <label class="form-label">Enfermedades</label>
                        <input type="text" class="form-control" name="Enfermedades" required>
                    </div>

                    <!-- Medicamentos -->
                    <div class="col-md-6">
                        <label class="form-label">Medicamentos</label>
                        <input type="text" class="form-control" name="Medicamentos" required>
                    </div>

                    <!-- Servicios -->
                    <div class="col-12">
                        <label class="form-label">Servicios (automático)</label>
                        <textarea class="form-control" name="Servicios" id="servicios" rows="2" readonly></textarea>
                    </div>

                    <!-- Esparcimiento -->
                    <div class="col-12">
                        <label class="form-label">Esparcimiento</label>
                        <textarea class="form-control" name="Esparcimiento" rows="2" required></textarea>
                    </div>

                    <!-- Botones -->
                    <div class="text-center">
                        <button class="btn btn-primary" type="submit" name="Registrar">Registrar</button>
                        <button type="reset" class="btn btn-secondary">Limpiar Campos</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php
$_SESSION['Mensaje'] = '';
require('../shared/footer.inc.php');
?>

<script>
    document.getElementById('paciente').addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const dni = selected.getAttribute('data-dni');
        document.getElementById('dni').value = dni;

        const pacienteId = this.value;
        if (pacienteId) {
            fetch('servicios_paciente.php?idPaciente=' + pacienteId)
                .then(response => response.json())
                .then(data => {
                    const servicios = data.join(', ');
                    document.getElementById('servicios').value = servicios;
                });
        } else {
            document.getElementById('servicios').value = '';
        }
    });
</script>