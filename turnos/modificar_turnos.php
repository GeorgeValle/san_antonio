<?php
ob_start();
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require('../shared/encabezado.inc.php');
require('../shared/barraLateral.inc.php');
require_once '../funciones/conexion.php';
$MiConexion = ConexionBD(); 
require_once '../funciones/select_general.php';

// Listados para selects
$ListadoPacientes = Listar_Pacientes($MiConexion);
$CantidadPacientes = count($ListadoPacientes);

$ListadoServicios = Listar_Servicios($MiConexion);
$CantidadServicios = count($ListadoServicios);

// Array para mantener datos del turno actual
$DatosTurnoActual = array();

if (!empty($_POST['ModificarTurno'])) {
    $_SESSION['Mensaje'] = '';
    
    // Obtener ID del turno actual si existe
    $idTurnoActual = $_POST['IdTurno'] ?? null;
    
    // Primero validar el turno
    $mensajeValidacion = Validar_Turno_modificar($MiConexion, $idTurnoActual);
    
    if (!empty($mensajeValidacion)) {
        $_SESSION['Mensaje'] = $mensajeValidacion;
        // Mantener los datos ingresados para mostrarlos nuevamente
        $DatosTurnoActual['ID_TURNO'] = $_POST['IdTurno'];
        $DatosTurnoActual['FECHA'] = $_POST['Fecha'];
        $DatosTurnoActual['HORARIO'] = $_POST['Horario'];
        $DatosTurnoActual['ID_PACIENTE'] = $_POST['Paciente'];
        $DatosTurnoActual['SERVICIOS'] = $_POST['TipoServicio'] ?? array();
    } else {
        // Si la validación es exitosa, proceder a modificar
        if (Modificar_Turno($MiConexion, $_POST)) {
            $_SESSION['Mensaje'] = "El turno se ha modificado correctamente!";
            $_SESSION['Estilo'] = 'success';
            header('Location: ../turnos/listados_turnos.php');
            exit;
        } else {
            $_SESSION['Mensaje'] = "Error al modificar el turno.";
            $_SESSION['Estilo'] = 'danger';
            $DatosTurnoActual['ID_TURNO'] = $_POST['IdTurno'];
            $DatosTurnoActual['FECHA'] = $_POST['Fecha'];
            $DatosTurnoActual['HORARIO'] = $_POST['Horario'];
            $DatosTurnoActual['ID_PACIENTE'] = $_POST['Paciente'];
            $DatosTurnoActual['SERVICIOS'] = $_POST['TipoServicio'] ?? array();
        }
    }
} else if (!empty($_GET['ID_TURNO'])) {
    $DatosTurnoActual = Datos_Turno($MiConexion, $_GET['ID_TURNO']);
}
?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Turnos</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="../inicio/index.php">Menu</a></li>
          <li class="breadcrumb-item">Turnos</li>
          <li class="breadcrumb-item active">Modificar Turnos</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Modificar Turnos</h5>

              <!-- Horizontal Form -->
              <form class="row g-3" id='miFormulario' method='post'>
              <?php if (!empty($_SESSION['Mensaje'])) { ?>
                <div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
                    <?php echo $_SESSION['Mensaje']; ?>
                </div>
              <?php } ?>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control"  name="Fecha" id="fecha"
                            value="<?php echo !empty($DatosTurnoActual['FECHA']) ? $DatosTurnoActual['FECHA'] : ''; ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="hora" class="form-label">Horario</label>
                            <select class="form-select" name="Horario" id="hora" required>
                                <?php 
                                $horarios = [
                                    '08:30' => '8:30',
                                    '09:00' => '9:00',
                                    '09:30' => '9:30',
                                    '10:00' => '10:00',
                                    '10:30' => '10:30',
                                    '11:00' => '11:00',
                                    '11:30' => '11:30',
                                    '12:00' => '12:00',
                                    '12:30' => '12:30',
                                    '16:00' => '16:00',
                                    '16:30' => '16:30',
                                    '17:00' => '17:00',
                                    '17:30' => '17:30',
                                    '18:00' => '18:00',
                                    '18:30' => '18:30',
                                    '19:00' => '19:00',
                                    '19:30' => '19:30'
                                ];
                                
                                $horaActual = !empty($DatosTurnoActual['HORARIO']) ? $DatosTurnoActual['HORARIO'] : '';
                                
                                foreach ($horarios as $valor => $texto) {
                                    $selected = ($horaActual === $valor) ? 'selected' : '';
                                    echo "<option value=\"$valor\" $selected>$texto</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="paciente" class="form-label">Paciente</label>
                        <select class="form-select" name="Paciente" id="paciente">
                            <option value="">Selecciona un paciente</option>
                            <?php
                            for ($i = 0; $i < $CantidadPacientes; $i++) {
                                $selected = (!empty($DatosTurnoActual['ID_PACIENTE']) && $DatosTurnoActual['ID_PACIENTE'] == $ListadoPacientes[$i]['ID_PACIENTE']) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $ListadoPacientes[$i]['ID_PACIENTE']; ?>" <?php echo $selected; ?>>
                                    <?php echo $ListadoPacientes[$i]['NOMBRE'] . ' ' . $ListadoPacientes[$i]['APELLIDO']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="servicio" class="form-label">Servicios</label>
                        <select class="js-example-basic-multiple form-select" multiple="multiple" name="TipoServicio[]" id="servicio">
                            <?php 
                            $serviciosSeleccionados = $DatosTurnoActual['SERVICIOS'] ?? array();
                            
                            for ($i = 0; $i < $CantidadServicios; $i++) { 
                                $selected = in_array($ListadoServicios[$i]['ID'], $serviciosSeleccionados) ? 'selected' : '';
                            ?>
                                <option value="<?php echo $ListadoServicios[$i]['ID']; ?>" <?php echo $selected; ?>>
                                    <?php echo $ListadoServicios[$i]['DENOMINACION']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="text-center">

                        <input type='hidden' name="IdTurno" value="<?php echo $DatosTurnoActual['ID_TURNO'] ?? ''; ?>" />

                        <button class="btn btn-primary" type="submit" value="Modificar" name="ModificarTurno">Modificar</button>
                        <a href="../turnos/listados_turnos.php" 
                        class="btn btn-success btn-info " 
                        title="Listado"> Volver al listado  </a>
                    </div>
                </form>
                <!-- Vertical Form --><!-- End Horizontal Form -->

    </section>

  </main><!-- End #main -->

  <?php
  $_SESSION['Mensaje']='';
require ('../shared/footer.inc.php'); //Aca uso el FOOTER que esta seccionados en otro archivo

ob_end_flush(); // Envía la salida al navegador
?>
<script>
    // Inicializar Select2 en el formulario de modificación
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2({
            placeholder: "Seleccione los servicios",
            width: '100%'
        });
    });
</script>
</body>

</html>