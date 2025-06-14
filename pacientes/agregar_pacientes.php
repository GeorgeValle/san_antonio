<?php
session_start();

if (empty($_SESSION['Usuario_Nombre']) ) { // si el usuario no esta logueado no lo deja entrar
  header('Location: ../core/cerrarsesion.php');
  exit;
}

require_once ('../shared/encabezado.inc.php'); //Aca uso el encabezado que esta seccionados en otro archivo

require_once ('../shared/barraLateral.inc.php'); //Aca uso el encabezaso que esta seccionados en otro archivo

require_once '../funciones/conexion.php';
require_once '../funciones/select_general.php';
$MiConexion=ConexionBD(); 

$Mensaje='';
$Estilo='warning';
if (!empty($_POST['BotonRegistrar'])) {
    //estoy en condiciones de poder validar los datos
    $Mensaje=Validar_Paciente($MiConexion);
    if (empty($Mensaje)) {
        if (InsertarPacientes($MiConexion) != false) {
            $Mensaje = 'Se ha registrado correctamente.';
            $_POST = array(); 
            $Estilo = 'success'; 
        }
    }
}

?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Pacientes</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="../core/index.php">Menu</a></li>
          <li class="breadcrumb-item">Pacientes</li>
          <li class="breadcrumb-item active">Agregar Pacientes</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Agregar Pacientes</h5>

              <!-- Horizontal Form -->
              <form method='post'>
                <?php if (!empty($Mensaje)) { ?>
                    <div class="alert alert-<?php echo $Estilo; ?> alert-dismissable">
                    <?php echo $Mensaje; ?>
                    </div>
                <?php } ?>
                <div class="row mb-3">
                  <label for="inputEmail3" class="col-sm-2 col-form-label">Nombre</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="Nombre" id="nombre"
                    value="<?php echo !empty($_POST['Nombre']) ? $_POST['Nombre'] : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputEmail3" class="col-sm-2 col-form-label">Apellido</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="Apellido" id="apellido"
                    value="<?php echo !empty($_POST['Apellido']) ? $_POST['Apellido'] : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputEmail3" class="col-sm-2 col-form-label">Telefono</label>
                  <div class="col-sm-10">
                    <input type="number" class="form-control" name="Telefono" id="dtelefono"
                    value="<?php echo !empty($_POST['Telefono']) ? $_POST['Telefono'] : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputEmail3" class="col-sm-2 col-form-label">DNI</label>
                  <div class="col-sm-10">
                    <input type="number" class="form-control" name="DNI" id="dni"
                    value="<?php echo !empty($_POST['DNI']) ? $_POST['DNI'] : ''; ?>">
                  </div>
                </div>
                <div class="text-center">
                  <button type="submit" class="btn btn-primary" value="Registrar" name="BotonRegistrar">Agregar</button>
                  <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
              </form><!-- End Horizontal Form -->

    </section>

  </main><!-- End #main -->

  <?php
require ('../shared/footer.inc.php'); //Aca uso el FOOTER que esta seccionados en otro archivo
?>


</body>

</html>