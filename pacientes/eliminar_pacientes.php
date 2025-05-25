<?php
    session_start();
    if (empty($_SESSION['Usuario_Nombre']) ) {
        header('Location: ../core/cerrarsesion.php');
        exit;
    }
    
    require_once '../funciones/conexion.php';
    $MiConexion = ConexionBD();
   

    require_once '../funciones/select_general.php';

    if ( Eliminar_Paciente($MiConexion , $_GET['ID_PACIENTE']) != false ) {
        $_SESSION['Mensaje'].='Se ha eliminado el paciente correctamente';
        $_SESSION['Estilo']='success';
    }else {
        $_SESSION['Mensaje'].='No se pudo borrar el paciente. <br /> ';
        $_SESSION['Estilo']='warning';
    }
    
   
    header('Location: ../pacientes/listados_pacientes.php');
    exit;
?>