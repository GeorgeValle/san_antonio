<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) { // Si el usuario no está logueado, no lo deja entrar
    header('Location: ../core/cerrarsesion.php');
    exit;
}

require('../shared/encabezado.inc.php'); // Encabezado
require('../shared/barraLateral.inc.php'); // Barra lateral

require_once '../funciones/conexion.php';
$MiConexion = ConexionBD();

require_once '../funciones/select_general.php';
$ListadoServicios = Listar_Servicios($MiConexion);
$CantidadServicios = count($ListadoServicios);

$ListadoPacientes = Listar_Pacientes($MiConexion);
$CantidadPacientes = count($ListadoPacientes);

$_SESSION['Estilo'] = 'alert';

if (!empty($_POST['Registrar'])) {
    // Validar los datos
    $_SESSION['Mensaje'] = Validar_Turno();
    if (empty($_SESSION['Mensaje'])) {
        if (InsertarTurnos($MiConexion) != false) {
            $_SESSION['Mensaje'] = 'Se ha registrado correctamente.';
            $_POST = array();
            $_SESSION['Estilo'] = 'success';
        }
    }
}
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Turnos</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../inicio/index.php">Menu</a></li>
                <li class="breadcrumb-item">Turnos</li>
                <li class="breadcrumb-item active">Agregar Turnos</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Agregar Turnos</h5>

                <!-- Horizontal Form -->
                <form class="row g-3" id='miFormulario' method='post'>
                    <?php if (!empty($_SESSION['Mensaje'])) { ?>
                        <div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
                            <?php echo $_SESSION['Mensaje']; ?>
                        </div>
                    <?php } ?>

                    <!-- Campo de Paciente -->
                    <div class="col-12">
                        <label for="selector" class="form-label">Paciente</label>
                        <select class="form-select" aria-label="Selector" name="Paciente" id="paciente">
                            <option selected>Selecciona una opción</option>
                            <?php for ($i = 0; $i < $CantidadPacientes; $i++) { ?>
                                <option value="<?php echo $ListadoPacientes[$i]['ID_PACIENTE']; ?>">
                                    <?php echo $ListadoPacientes[$i]['NOMBRE']; ?>,
                                    <?php echo $ListadoPacientes[$i]['APELLIDO']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Campo de Fecha y hora-->
                    <div class="row">
                        <div class="col-md-6">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" name="Fecha" id="fecha" required 
                            min="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="hora" class="form-label">Horario</label>
                            <select class="form-select" name="Horario" id="hora" required>
                                <option value="08:30">8:30</option>
                                <option value="09:00">9:00</option>
                                <option value="09:30">9:30</option>
                                <option value="10:00">10:00</option>
                                <option value="10:30">10:30</option>
                                <option value="11:00">11:00</option>
                                <option value="11:30">11:30</option>
                                <option value="12:00">12:00</option>
                                <option value="12:30">12:30</option>
                                <option value="16:00">16:00</option>
                                <option value="16:30">16:30</option>
                                <option value="17:00">17:00</option>
                                <option value="17:30">17:30</option>
                                <option value="18:00">18:00</option>
                                <option value="18:30">18:30</option>
                                <option value="19:00">19:00</option>
                                <option value="19:30">19:30</option>
                            </select>
                        </div>
                    </div>

                                        <!-- Campo de Tipo de Servicio -->
                    <div class="col-12">
                        <label for="selector" class="form-label">Tipo de Servicio</label>
                        <select class="js-example-basic-multiple form-select" multiple="multiple" name="TipoServicio[]">
                            <?php for ($i = 0; $i < $CantidadServicios; $i++) { ?>
                                <option value="<?php echo $ListadoServicios[$i]['ID']; ?>">
                                    <?php echo $ListadoServicios[$i]['DENOMINACION']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Botones -->
                    <div class="text-center">
                        <button class="btn btn-primary" type="submit" value="Registrar" name="Registrar">Registrar</button>
                        <button type="reset" class="btn btn-secondary">Limpiar Campos</button>
                    </div>
                </form>
                <!-- End Horizontal Form -->
            </div>
        </div>
    </section>
</main><!-- End #main -->

<?php
$_SESSION['Mensaje'] = '';
require('../shared/footer.inc.php'); // Footer
?>

<script>
    // Inicializar Select2
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2();
    });
</script>

</body>
</html>