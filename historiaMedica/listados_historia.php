<?php
session_start();

if (empty($_SESSION['Usuario_Nombre']) ) { // si el usuario no esta logueado no lo deja entrar
  header('Location: ../core/cerrarsesion.php');
  exit;
}

require ('../shared/encabezado.inc.php'); //Aca uso el encabezado que esta seccionados en otro archivo

require ('../shared/barraLateral.inc.php'); //Aca uso el encabezaso que esta seccionados en otro archivo

//voy a necesitar la conexion: incluyo la funcion de Conexion.
require_once '../funciones/conexion.php';

//genero una variable para usar mi conexion desde donde me haga falta
//no envio parametros porque ya los tiene definidos por defecto
$MiConexion = ConexionBD();

//ahora voy a llamar el script con la funcion que genera mi listado
require_once '../funciones/select_general.php';


//voy a ir listando lo necesario para trabajar en este script: 
$ListadoHistoria = Listar_Historia($MiConexion);
$CantidadHistoria = count($ListadoHistoria);

  //estoy en condiciones de poder buscar segun el parametro
  
    if (!empty($_POST['BotonBuscar'])) {

        $parametro = $_POST['parametro'];
        $criterio = $_POST['gridRadios'];
        $ListadoHistoria=Listar_Historia_Parametro($MiConexion,$criterio,$parametro);
        $CantidadHistoria = count($ListadoHistoria);


}

?>

<main id="main" class="main">

<div class="pagetitle">
  <h1>Listado Historia</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="../core/index.php">Menu</a></li>
      <li class="breadcrumb-item">Historia</li>
      <li class="breadcrumb-item active">Listado Historia</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section">
    
    <div class="card">
        <div class="card-body">
          <h5 class="card-title">Listado Historia</h5>
          <?php if (!empty($_SESSION['Mensaje'])) { ?>
            <div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
              <?php echo $_SESSION['Mensaje'] ?>
            </div>
          <?php } ?>

          <Form method="POST">
          <div class="row mb-4">
            <label for="inputEmail3" class="col-sm-1 col-form-label">Buscar</label>
              <div class="col-sm-3">
                <input type="text" class="form-control" name="parametro" id="parametro">
                </div>

                <style> .btn-xs { padding: 0.25rem 0.5rem; font-size: 0.75rem; line-height: 1.5; border-radius: 0.2rem; } </style>

              <div class="col-sm-3 mt-2">
                <button type="submit" class="btn btn-success btn-xs d-inline-block" value="buscar" name="BotonBuscar">Buscar</button>
                <button type="submit" class="btn btn-danger btn-xs d-inline-block" value="limpiar" name="BotonLimpiar">Limpiar</button>
              </div>
              <div class="col-sm-5 mt-2">
                    <div class="form-check form-check-inline small-text">
                      <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios1" value="Nombre" checked>
                      <label class="form-check-label" for="gridRadios1">
                        Nombre
                      </label>
                    </div>

                    <div class="form-check form-check-inline small-text">
                      <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios2" value="DNI">
                      <label class="form-check-label" for="gridRadios2">
                        DNI
                    </div>

                    <div class="form-check form-check-inline small-text">
                      <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios3" value="Enfermedades">
                      <label class="form-check-label" for="gridRadios3">
                        Enfermedades
                      </label>
                    </div>

                    <div class="form-check form-check-inline small-text">
                      <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios4" value="Medicamentos">
                      <label class="form-check-label" for="gridRadios4">
                        Medicamentos
                      </label>
                    </div>

                    <div class="form-check form-check-inline small-text">
                      <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios5" value="Servicios">
                      <label class="form-check-label" for="gridRadios5">
                        Servicios
                      </label>
                    </div>

                    <div class="form-check form-check-inline small-text">
                      <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios6" value="Esparcimiento">
                      <label class="form-check-label" for="gridRadios6">
                        Esparcimiento
                      </label>
                    </div>
                    
                  </div>
              
          </div>
          </form>
          <!-- Table with stripped rows -->
          <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Paciente</th>
                <th scope="col">DNI</th>
                <th scope="col">Enfermedades</th>
                <th scope="col">Medicamentos</th>
                <th scope="col">Servicios</th>
                <th scope="col">Esparcimiento</th>
              </tr>
            </thead>
            <tbody>
                <?php for ($i=0; $i<$CantidadHistoria; $i++) { ?>
                    <tr>
                        <th scope="row"><?php echo $i+1; ?></th>
                        <td><?php echo $ListadoHistoria[$i]['NOMBREPACIENTE'] . " " . $ListadoHistoria[$i]['APELLIDOPACIENTE']; ?></td>
                        <td><?php echo $ListadoHistoria[$i]['DNI']; ?></td>
                        <td><?php echo $ListadoHistoria[$i]['ENFERMEDADES']; ?></td>
                        <td><?php echo $ListadoHistoria[$i]['MEDICAMENTOS']; ?></td>
                        <td><?php echo $ListadoHistoria[$i]['SERVICIOS']; ?></td>
                        <td><?php echo $ListadoHistoria[$i]['ESPARCIMIENTO']; ?></td>
                        <td>
                          <!-- eliminar la consulta -->
                          <a href="../historiaMedica/eliminar_historia.php?ID_HISTORIA=<?php echo $ListadoHistoria[$i]['ID_HISTORIA']; ?>" 
                            title="Eliminar" 
                            onclick="return confirm('Confirma eliminar esta historia medica?');">
                              <i class="bi bi-trash-fill text-danger fs-5"></i>
                          </a>

                          <a href="../historiaMedica/modificar_historia.php?ID_HISTORIA=<?php echo $ListadoHistoria[$i]['ID_HISTORIA']; ?>" 
                            title="Modificar">
                            <i class="bi bi-pencil-fill text-warning fs-5"></i>
                          </a>
                      
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
          </table>
          <!-- End Table with stripped rows -->

        </div>
    </div>
 
</section>

</main><!-- End #main -->

<?php
  $_SESSION['Mensaje']='';
  require ('../shared/footer.inc.php'); //Aca uso el FOOTER que esta seccionados en otro archivo
?>


</body>

</html>