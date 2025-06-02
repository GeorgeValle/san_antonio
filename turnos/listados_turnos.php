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
$ListadoTurnos = Listar_Turnos($MiConexion);
$CantidadTurnos = count($ListadoTurnos);

//estoy en condiciones de poder buscar segun el parametro
if (!empty($_POST['Buscar'])) {

  $parametro = $_POST['parametro'];
  $criterio = $_POST['criterio'];
  $ListadoTurnos = Listar_Turnos_Parametro($MiConexion,$criterio,$parametro);
  $CantidadTurnos = count($ListadoTurnos);

}

?>

<main id="main" class="main">

<div class="pagetitle">
  <h1>Listado Turnos</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="../core/index.php">Menu</a></li>
      <li class="breadcrumb-item">Turnos</li>
      <li class="breadcrumb-item active">Listado Turnos</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section">
    
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Listado Turnos</h5>
          
          <?php if (!empty($_SESSION['Mensaje'])) { ?>
            <div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
              <?php echo $_SESSION['Mensaje'] ?>
            </div>
          <?php } ?>
            
          <style> .btn-xs { padding: 0.25rem 0.5rem; font-size: 0.75rem; line-height: 1.5; border-radius: 0.2rem; } </style>
          <style> .btn-hidden { display: none; } </style>

          <Form method="POST">
            <div class="row mb-4">
              <label for="inputEmail3" class="col-sm-1 col-form-label">Buscar</label>
                <div class="col-sm-3">
                  <input type="text" class="form-control" name="parametro" id="parametro">
                </div>
                <div class="col-sm-3 mt-2">
                  <button type="submit" class="btn btn-success btn-xs d-inline-block" value="buscar" name="Buscar">Buscar</button>
                  <button type="submit" class="btn btn-danger btn-xs d-inline-block" value="limpiar" name="Limpiar">Limpiar</button> 
                </div>
                <div class="col-sm-5 mt-2">
                      <div class="form-check form-check-inline small-text">
                        <input class="form-check-input" type="radio" name="criterio" id="gridRadios1" value="Paciente" checked>
                        <label class="form-check-label" for="gridRadios1">
                          Residente
                        </label>
                      </div>
                      <div class="form-check form-check-inline small-text">
                        <input class="form-check-input" type="radio" name="criterio" id="gridRadios2" value="Servicio">
                        <label class="form-check-label" for="gridRadios2">
                          Servicio
                        </label>
                      </div>
                      <div class="form-check form-check-inline small-text">
                        <input class="form-check-input" type="radio" name="criterio" id="gridRadios3" value="Fecha">
                        <label class="form-check-label" for="gridRadios3">
                          Fecha
                      </div>
                      <div class="form-check form-check-inline small-text">
                        <input class="form-check-input" type="radio" name="criterio" id="gridRadios3" value="DNI">
                        <label class="form-check-label" for="gridRadios3">
                          DNI
                      </div>
                </div> 
            </div>
          
          </form>
          <!-- Table with stripped rows -->
          <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Fecha</th>
                <th scope="col">Horario</th>
                <th scope="col">Servicio</th>
                <th scope="col">Residente</th>
                <th scope="col">Tipo</th>
                <th scope="col">Acciones</th>
              </tr>
            </thead>
            <tbody>
                <?php 
                //borro la variable anterior de descarga
                //$_SESSION['Descarga']="";
                for ($i=0; $i<$CantidadTurnos; $i++) { 



                  //Metodo para pintar las filas
                  //list($Title, $Color) = ColorDeFila($ListadoTurnos[$i]['FECHA'],$ListadoTurnos[$i]['ESTADO']); 
                ?>
                    <tr">
                        <th scope="row"><?php echo $i+1; ?></th>
                        <td><?php echo $ListadoTurnos[$i]['FECHA']; ?></td>
                        <td><?php echo $ListadoTurnos[$i]['HORARIO']; ?></td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="dropdownServicios<?php echo $i; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                    Ver servicios (<?php echo count($ListadoTurnos[$i]['SERVICIOS']); ?>)
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownServicios<?php echo $i; ?>">
                                    <?php if (!empty($ListadoTurnos[$i]['SERVICIOS'])): ?>
                                        <?php foreach ($ListadoTurnos[$i]['SERVICIOS'] as $servicio): ?>
                                            <li><span class="dropdown-item-text"><?php echo htmlspecialchars($servicio['nombre']); ?></span></li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li><span class="dropdown-item-text">Sin servicios</span></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </td>
                        <td><?php echo $ListadoTurnos[$i]['NOMBRE_PACIENTE']?>, <?php echo $ListadoTurnos[$i]['APELLIDO_PACIENTE']?></td>
                        <td><?php echo $ListadoTurnos[$i]['TIPO_PACIENTE']?></td>
                        <td>
                          <!-- eliminar la consulta -->
                          <a href="../turnos/eliminar_turnos.php?ID_TURNO=<?php echo $ListadoTurnos[$i]['ID_TURNO']; ?>" 
                            title="Eliminar" 
                            onclick="return confirm('Confirma eliminar este turno?');">
                            <i class="bi bi-trash-fill text-danger fs-5"></i>
                          </a>

                          <a href="../turnos/modificar_turnos.php?ID_TURNO=<?php echo $ListadoTurnos[$i]['ID_TURNO']; ?>" 
                            title="Modificar">
                            <i class="bi bi-pencil-fill text-warning fs-5"></i>
                          </a>
                      
                        </td>
                    </tr>
                <?php 
                } 
                //le agrego un espacio cuando termino de cargar
                //$_SESSION['Descarga'] .= "\n";
                ?>
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