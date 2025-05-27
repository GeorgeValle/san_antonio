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
require_once '../funciones/select_general.php';

$MiConexion = ConexionBD();

$DatosHistoria = array();
$DatosPaciente = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prueba rápida para ver si el POST se recibe
    error_log("POST recibido en modificar_historia.php");

    if (Modificar_Historia($MiConexion)) {
        error_log("Modificación exitosa");
        $_SESSION['Mensaje'] = "La historia médica se modificó correctamente.";
        $_SESSION['Estilo'] = 'success';
        header("Location: listados_historia.php");
        exit;
    } else {
        error_log("Error en Modificar_Historia");
    }
}

if (!empty($_POST['ModificarHistoria'])) {
    if (Modificar_Historia($MiConexion)) {
        $_SESSION['Mensaje'] = "La historia médica se modificó correctamente.";
        $_SESSION['Estilo'] = 'success';
        header("Location: listados_historia.php"); // Asegúrate de tener este archivo
        exit;
    } else {
        $_SESSION['Mensaje'] = "Error al modificar la historia médica.";
        $_SESSION['Estilo'] = 'danger';
    }
} else if (!empty($_GET['ID_HISTORIA'])) {
    $DatosHistoria = Datos_Historia($MiConexion, $_GET['ID_HISTORIA']);
    $DatosPaciente = Datos_Paciente($MiConexion, $DatosHistoria['ID_PACIENTE']);
}
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Modificar Historia Médica</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../inicio/index.php">Inicio</a></li>
                <li class="breadcrumb-item">Historia Médica</li>
                <li class="breadcrumb-item active">Modificar</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Editar Historia Médica</h5>

                <form class="row g-3" method="post">
                    <?php if (!empty($_SESSION['Mensaje'])) { ?>
                    <div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissible">
                        <?php echo $_SESSION['Mensaje']; ?>
                    </div>
                    <?php } ?>

                    <div class="col-12">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" value="<?php echo $DatosPaciente['NOMBRE']; ?>"
                            readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label">DNI</label>
                        <input type="text" class="form-control" value="<?php echo $DatosPaciente['DNI']; ?>" readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Enfermedades</label>
                        <textarea name="Enfermedades"
                            class="form-control"><?php echo $DatosHistoria['ENFERMEDADES']; ?></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Medicamentos</label>
                        <textarea name="Medicamentos"
                            class="form-control"><?php echo $DatosHistoria['MEDICAMENTOS']; ?></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Servicios</label>
                        <textarea name="Servicios" class="form-control"
                            readonly><?php echo $DatosHistoria['SERVICIOS']; ?></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Esparcimiento</label>
                        <textarea name="Esparcimiento"
                            class="form-control"><?php echo $DatosHistoria['ESPARCIMIENTO']; ?></textarea>
                    </div>

                    <input type="hidden" name="IdHistoria" value="<?php echo $DatosHistoria['ID_HISTORIA']; ?>" />

                    <div class="text-center">
                        <button class="btn btn-primary" type="submit" name="ModificarHistoria">Guardar Cambios</button>
                        <a href="listado_historia.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php
$_SESSION['Mensaje'] = '';
require('../shared/footer.inc.php');
ob_end_flush();
?>