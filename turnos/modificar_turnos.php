<?php
ob_start(); // Inicia el buffering de salida
session_start();

if (empty($_SESSION['Usuario_Nombre']) ) { // si el usuario no esta logueado no lo deja entrar
  header('Location: ../inicio/cerrarsesion.php');
  exit;
}

require ('../shared/encabezado.inc.php'); //Aca uso el encabezado que esta seccionados en otro archivo

require ('../shared/barraLateral.inc.php'); //Aca uso el encabezaso que esta seccionados en otro archivo

require_once '../funciones/conexion.php';
$MiConexion=ConexionBD(); 

require_once '../funciones/select_general.php';

// Listados para selects
$ListadoPacientes = Listar_Pacientes($MiConexion);
$CantidadPacientes = count($ListadoPacientes);

$ListadoServicios = Listar_Servicios($MiConexion);
$CantidadServicios = count($ListadoServicios);

// Array para mantener datos del turno actual
$DatosTurnoActual = array();

if (!empty($_POST['ModificarTurno'])) {
    // Validación simple (puedes mejorarla)
    $_SESSION['Mensaje'] = '';
    if (empty($_POST['Fecha']) || empty($_POST['Horario']) || empty($_POST['Paciente']) || empty($_POST['Servicio'])) {
        $_SESSION['Mensaje'] = 'Todos los campos son obligatorios.';
        $_SESSION['Estilo'] = 'warning';
        $DatosTurnoActual['ID_TURNO'] = $_POST['IdTurno'];
        $DatosTurnoActual['FECHA'] = $_POST['Fecha'];
        $DatosTurnoActual['HORARIO'] = $_POST['Horario'];
        $DatosTurnoActual['ID_PACIENTE'] = $_POST['Paciente'];
        $DatosTurnoActual['ID_SERVICIO'] = $_POST['Servicio'];
    } else {
        // Modificar turno
        if (Modificar_Turno($MiConexion, $_POST)) {
            $_SESSION['Mensaje'] = "El turno se ha modificado correctamente!";
            $_SESSION['Estilo'] = 'success';
            header('Location: ../turnos/listados_turnos.php');
            exit;
        } else {
            $_SESSION['Mensaje'] = "Error al modificar el turno.";
            $_SESSION['Estilo'] = 'danger';
        }
    }
} else if (!empty($_GET['ID_TURNO'])) {
    // Cargar datos del turno para editar
    $DatosTurnoActual=Datos_Turno( $MiConexion, $_GET['ID_TURNO']);
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

                    <div class="col-12">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control"  name="Fecha" id="fecha"
                        value="<?php echo !empty($DatosTurnoActual['FECHA']) ? $DatosTurnoActual['FECHA'] : ''; ?>">
                    </div>

                    <div class="col-12">
                        <label for="hora" class="form-label">Horario</label>
                        <input type="time" class="form-control" name="Horario"
                        value="<?php echo !empty($DatosTurnoActual['HORARIO']) ? $DatosTurnoActual['HORARIO'] : ''; ?>">
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
                        <label for="servicio" class="form-label">Servicio</label>
                        <select class="form-select" name="Servicio" id="servicio">
                            <option value="">Selecciona un servicio</option>
                            <?php
                            for ($i = 0; $i < $CantidadServicios; $i++) {
                                $selected = (!empty($DatosTurnoActual['ID_SERVICIO']) && $DatosTurnoActual['ID_SERVICIO'] == $ListadoServicios[$i]['ID']) ? 'selected' : '';
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

</body>

</html>