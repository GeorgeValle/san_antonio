<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../core/cerrarsesion.php');
    exit;
}

require('../shared/encabezado.inc.php');
require('../shared/barraLateral.inc.php');
require_once '../funciones/conexion.php';
require_once '../funciones/select_general.php';

$MiConexion = ConexionBD();

// Inicializo listado por defecto
$ListadoHistoria = Listar_Historia($MiConexion);
$CantidadHistoria = count($ListadoHistoria);

// Búsqueda
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['BotonBuscar']) && !empty($_POST['parametro']) && !empty($_POST['gridRadios'])) {
        $parametro = trim($_POST['parametro']);
        $criterio = $_POST['gridRadios'];
        $ListadoHistoria = Listar_Historia_Parametro($MiConexion, $criterio, $parametro);
        $CantidadHistoria = count($ListadoHistoria);
    } elseif (isset($_POST['BotonLimpiar'])) {
        // Vuelvo al listado completo
        $ListadoHistoria = Listar_Historia($MiConexion);
        $CantidadHistoria = count($ListadoHistoria);
    }
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
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Listado Historia</h5>

                <?php if (!empty($_SESSION['Mensaje'])): ?>
                    <div class="alert alert-<?= $_SESSION['Estilo'] ?> alert-dismissible fade show" role="alert">
                        <?= $_SESSION['Mensaje'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row mb-4">
                        <label class="col-sm-1 col-form-label">Buscar</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="parametro" id="parametro">
                        </div>

                        <div class="col-sm-3 mt-2">
                            <button type="submit" class="btn btn-success btn-sm" name="BotonBuscar">Buscar</button>
                            <button type="submit" class="btn btn-danger btn-sm" name="BotonLimpiar">Limpiar</button>
                        </div>

                        <div class="col-sm-5 mt-2">
                            <?php
                            $criterios = [
                                'Nombre' => 'Nombre',
                                'DNI' => 'DNI',
                                'Enfermedades' => 'Enfermedades',
                                'Medicamentos' => 'Medicamentos',
                                'Servicios' => 'Servicios',
                                'Esparcimiento' => 'Esparcimiento'
                            ];
                            foreach ($criterios as $id => $label): ?>
                                <div class="form-check form-check-inline small-text">
                                    <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios<?= $id ?>" value="<?= $id ?>" <?= ($id === 'Nombre') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="gridRadios<?= $id ?>"><?= $label ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </form>

                <!-- Tabla de historias -->
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Paciente</th>
                            <th>DNI</th>
                            <th>Enfermedades</th>
                            <th>Medicamentos</th>
                            <th>Servicios</th>
                            <th>Esparcimiento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i = 0; $i < $CantidadHistoria; $i++): ?>
                            <tr>
                                <th scope="row"><?= $i + 1 ?></th>
                                <td><?= $ListadoHistoria[$i]['NOMBREPACIENTE'] . ' ' . $ListadoHistoria[$i]['APELLIDOPACIENTE'] ?></td>
                                <td><?= $ListadoHistoria[$i]['DNI'] ?></td>

                                <!-- Enfermedades -->
                                <td>
                                    <div class="dropdown">
                                        <button class="dropdown-toggle btn btn-light btn-sm" type="button" data-bs-toggle="dropdown">
                                            <?= !empty($ListadoHistoria[$i]['ENFERMEDADES'][0]) ? $ListadoHistoria[$i]['ENFERMEDADES'][0] : 'Ninguno' ?>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php foreach ($ListadoHistoria[$i]['ENFERMEDADES'] as $item): ?>
                                                <li><span class="dropdown-item-text"><?= $item ?></span></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </td>

                                <!-- Medicamentos -->
                                <td>
                                    <div class="dropdown">
                                        <button class="dropdown-toggle btn btn-light btn-sm" type="button" data-bs-toggle="dropdown">
                                            <?= !empty($ListadoHistoria[$i]['MEDICAMENTOS'][0]) ? $ListadoHistoria[$i]['MEDICAMENTOS'][0] : 'Ninguno' ?>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php foreach ($ListadoHistoria[$i]['MEDICAMENTOS'] as $item): ?>
                                                <li><span class="dropdown-item-text"><?= $item ?></span></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </td>

                                <!-- Servicios -->
                                <td>
                                    <div class="dropdown">
                                        <button class="dropdown-toggle btn btn-light btn-sm" type="button" data-bs-toggle="dropdown">
                                            <?= !empty($ListadoHistoria[$i]['SERVICIOS'][0]['denominacion']) ? $ListadoHistoria[$i]['SERVICIOS'][0]['denominacion'] : 'Ninguno' ?>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php foreach ($ListadoHistoria[$i]['SERVICIOS'] as $item): ?>
                                                <li><span class="dropdown-item-text"><?= $item['denominacion'] ?></span></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </td>

                                <!-- Esparcimiento -->
                                <td>
                                    <div class="dropdown">
                                        <button class="dropdown-toggle btn btn-light btn-sm" type="button" data-bs-toggle="dropdown">
                                            <?= !empty($ListadoHistoria[$i]['ESPARCIMIENTO'][0]) ? $ListadoHistoria[$i]['ESPARCIMIENTO'][0] : 'Ninguno' ?>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php foreach ($ListadoHistoria[$i]['ESPARCIMIENTO'] as $item): ?>
                                                <li><span class="dropdown-item-text"><?= $item ?></span></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </td>

                                <!-- Acciones -->
                                <td>
                                    <a href="../historiaMedica/eliminar_historia.php?ID_HISTORIA=<?= $ListadoHistoria[$i]['ID_HISTORIA'] ?>" 
                                       title="Eliminar" onclick="return confirm('Confirma eliminar esta historia médica?');">
                                        <i class="bi bi-trash-fill text-danger fs-5 me-2"></i>
                                    </a>
                                    <a href="modificar_historia.php?ID_HISTORIA=<?= $ListadoHistoria[$i]['ID_HISTORIA'] ?>" title="Modificar">
                                        <i class="bi bi-pencil-fill text-primary fs-5"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </section>
</main>

<?php
// Limpiar mensaje una vez mostrado
$_SESSION['Mensaje'] = '';
require('../shared/footer.inc.php');
?>
</body>
</html>
