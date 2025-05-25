<?php
ob_start();
session_start();

if (empty($_SESSION['Usuario_Nombre']) ) { // si el usuario no esta logueado no lo deja entrar
  header('Location: ../core/cerrarsesion.php');
  exit;
}

require ('../shared/encabezado.inc.php'); //Aca uso el encabezado que esta seccionados en otro archivo

require ('../shared/barraLateral.inc.php'); //Aca uso el encabezaso que esta seccionados en otro archivo

require_once '../funciones/conexion.php';
$MiConexion=ConexionBD();

//ahora voy a llamar el script gral para usar las funciones necesarias
require_once '../funciones/select_general.php';
 
//este array contendra los datos de la consulta original, y cuando 
//pulse el boton, mantendrá los datos ingresados hasta que se validen y se puedan modificar
$DatosClienteActual=array();

if (!empty($_POST['BotonModificarCliente'])) {
    Validar_Paciente_Modificar();

    if (empty($_SESSION['Mensaje'])) { //ya toque el boton modificar y el mensaje esta vacio...
        
        if (Modificar_Paciente($MiConexion) != false) {
            $_SESSION['Mensaje'] = "Tu paciente se ha modificado correctamente!";
            $_SESSION['Estilo']='success';
            header('Location: ../pacientes/listados_pacientes.php');
            exit;
        }

    }else {  //ya toque el boton modificar y el mensaje NO esta vacio...
        $_SESSION['Estilo']='warning';
        $DatosPacienteActual['ID_PACIENTE'] = !empty($_POST['IdPaciente']) ? $_POST['IdPaciente'] :'';
        $DatosPacienteActual['NOMBRE'] = !empty($_POST['Nombre']) ? $_POST['Nombre'] :'';
        $DatosPacienteActual['APELLIDO'] = !empty($_POST['Apellido']) ? $_POST['Apellido'] :'';
        $DatosPacienteActual['TELEFONO'] = !empty($_POST['Telefono']) ? $_POST['Telefono'] :'';
    }

}else if (!empty($_GET['ID_PACIENTE'])) {
    //verifico que traigo el nro de consulta por GET si todabia no toque el boton de Modificar
    //busco los datos de esta consulta y los muestro
    $DatosPacienteActual = Datos_Paciente($MiConexion , $_GET['ID_PACIENTE']);
}

?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Pacientes</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="../inicio/index.php">Menu</a></li>
          <li class="breadcrumb-item">Pacientes</li>
          <li class="breadcrumb-item active">Modificar Pacientes</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Modificar Pacientes</h5>

              <!-- Horizontal Form -->
                <form method='post'>
                <?php if (!empty($_SESSION['Mensaje'])) { ?>
                    <div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
                        <?php echo $_SESSION['Mensaje']; ?>
                    </div>
                <?php } ?>

                <div class="row mb-3">
                  <label for="inputEmail3" class="col-sm-2 col-form-label">Nombre</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="Nombre" id="nombre"
                    value="<?php echo !empty($DatosPacienteActual['NOMBRE']) ? $DatosPacienteActual['NOMBRE'] : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputEmail3" class="col-sm-2 col-form-label">Apellido</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="Apellido" id="apellido"
                    value="<?php echo !empty($DatosPacienteActual['APELLIDO']) ? $DatosPacienteActual['APELLIDO'] : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputEmail3" class="col-sm-2 col-form-label">Telefono</label>
                  <div class="col-sm-10">
                    <input type="number" class="form-control" name="Telefono" id="dtelefono"
                    value="<?php echo !empty($DatosPacienteActual['TELEFONO']) ? $DatosPacienteActual['TELEFONO'] : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputEmail3" class="col-sm-2 col-form-label">DNI</label>
                  <div class="col-sm-10">
                    <input type="number" class="form-control" name="DNI" id="dni"
                    value="<?php echo !empty($DatosPacienteActual['DNI']) ? $DatosPacienteActual['DNI'] : ''; ?>">
                  </div>
                </div>

                <div class="text-center">
                  
                    <input type='hidden' name="IdPaciente" value="<?php echo $DatosPacienteActual['ID_PACIENTE']; ?>" />
                    
                    <button type="submit" class="btn btn-primary" value="Modificar" name="BotonModificarCliente">Modificar</button>
                    <a href="../pacientes/listados_pacientes.php" 
                    class="btn btn-success btn-info " 
                    title="Listado"> Volver al listado  </a>
                </div>
              </form><!-- End Horizontal Form -->

    </section>

  </main><!-- End #main -->

<?php
    $_SESSION['Mensaje']='';
    require ('../shared/footer.inc.php'); //Aca uso el FOOTER que esta seccionados en otro archivo
    ob_end_flush();
?>


</body>

</html>