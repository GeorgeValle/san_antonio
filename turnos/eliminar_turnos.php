<?php
    session_start();
    if (empty($_SESSION['Usuario_Nombre']) ) {
        header('Location: ../inicio/cerrarsesion.php');
        exit;
    }
    
    require_once '../funciones/conexion.php';
    $MiConexion = ConexionBD();
   

    require_once '../funciones/select_general.php';

    if ( Eliminar_Turno($MiConexion , $_GET['ID_TURNO']) != false ) {
        $_SESSION['Mensaje'].='Se ha eliminado el turno seleccionado';
        $_SESSION['Estilo']='success';
    }else {
        $_SESSION['Mensaje'].='No se pudo borrar el turno. <br /> ';
        $_SESSION['Estilo']='warning';
    }
    
    header('Location: ../turnos/listados_turnos.php');
    exit;
?>