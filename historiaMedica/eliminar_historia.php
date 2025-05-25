<?php
    session_start();
    if (empty($_SESSION['Usuario_Nombre']) ) {
        header('Location: ../core/cerrarsesion.php');
        exit;
    }
    
    require_once '../funciones/conexion.php';
    $MiConexion = ConexionBD();
   

    require_once '../funciones/select_general.php';

    if ( Eliminar_Historia($MiConexion , $_GET['ID_HISTORIA']) != false ) {
        $_SESSION['Mensaje'].='Se ha eliminado la historia medica correctamente';
        $_SESSION['Estilo']='success';
    }else {
        $_SESSION['Mensaje'].='No se pudo borrar la historia medica. <br /> ';
        $_SESSION['Estilo']='warning';
    }
    
   
    header('Location: ../historiaMedica/listados_historia.php');
    exit;
?>