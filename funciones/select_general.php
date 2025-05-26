<?php

function InsertarPacientes($vConexion) {

    $dni = mysqli_real_escape_string($vConexion, $_POST['DNI']);
    $nombre = mysqli_real_escape_string($vConexion, $_POST['Nombre']);
    $apellido = mysqli_real_escape_string($vConexion, $_POST['Apellido']);
    $telefono = mysqli_real_escape_string($vConexion, $_POST['Telefono']);
    
    $SQL_Insert = "INSERT INTO pacientes (nombre, apellido, telefono, dni)
                  VALUES ('$nombre', '$apellido', '$telefono', '$dni')";
    
    if (!mysqli_query($vConexion, $SQL_Insert)) {
        die('<h4>Error al intentar insertar el registro.</h4>');
    }
    
    return true;
}

function Validar_Paciente($vConexion){
    $_SESSION['Mensaje']='';
    if (strlen($_POST['Nombre']) < 3) {
        $_SESSION['Mensaje'].='Debes ingresar un nombre con al menos 3 caracteres. <br />';
    }
    if (strlen($_POST['Apellido']) < 3) {
        $_SESSION['Mensaje'].='Debes ingresar un apellido con al menos 3 caracteres. <br />';
    }
    if (strlen($_POST['Telefono']) < 3) {
        $_SESSION['Mensaje'].='Debes ingresar un telefono con al menos 3 caracteres. <br />';
    }
    if (strlen($_POST['DNI']) < 8) {
        $_SESSION['Mensaje'].='Debes ingresar un DNI con al menos 8 caracteres. <br />';
    }
        
    $dni = mysqli_real_escape_string($vConexion, $_POST['DNI']);
    $SQL_Check = "SELECT idPaciente FROM pacientes WHERE dni = '$dni' LIMIT 1";
    $resultado = mysqli_query($vConexion, $SQL_Check);
    
    if (mysqli_num_rows($resultado) > 0) {
        // Si existe un paciente con ese dni, retornamos un error
        $_SESSION['Mensaje'].='Ya existe un cliente registrado con este DNI';
    }

    //con esto aseguramos que limpiamos espacios y limpiamos de caracteres de codigo ingresados
    foreach($_POST as $Id=>$Valor){
        $_POST[$Id] = trim($_POST[$Id]);
        $_POST[$Id] = strip_tags($_POST[$Id]);
    }

    return $_SESSION['Mensaje'];
}

function Validar_Paciente_Modificar(){
    $_SESSION['Mensaje']='';
    if (strlen($_POST['Nombre']) < 3) {
        $_SESSION['Mensaje'].='Debes ingresar un nombre con al menos 3 caracteres. <br />';
    }
    if (strlen($_POST['Apellido']) < 3) {
        $_SESSION['Mensaje'].='Debes ingresar un apellido con al menos 3 caracteres. <br />';
    }
    if (strlen($_POST['Telefono']) < 3) {
        $_SESSION['Mensaje'].='Debes ingresar un telefono con al menos 3 caracteres. <br />';
    }
    if (strlen($_POST['DNI']) < 8) {
        $_SESSION['Mensaje'].='Debes ingresar un DNI con al menos 8 caracteres. <br />';
    }

    //con esto aseguramos que limpiamos espacios y limpiamos de caracteres de codigo ingresados
    foreach($_POST as $Id=>$Valor){
        $_POST[$Id] = trim($_POST[$Id]);
        $_POST[$Id] = strip_tags($_POST[$Id]);
    }

    return $_SESSION['Mensaje'];
}

function Listar_Pacientes($vConexion) {

    $Listado=array();

      //1) genero la consulta que deseo
        $SQL = "SELECT * FROM pacientes";

        //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
        $rs = mysqli_query($vConexion, $SQL);
        
        //3) el resultado deberá organizarse en una matriz, entonces lo recorro
        $i=0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['ID_PACIENTE'] = $data['idPaciente'];
            $Listado[$i]['NOMBRE'] = $data['nombre'];
            $Listado[$i]['APELLIDO'] = $data['apellido'];
            $Listado[$i]['TELEFONO'] = $data['telefono'];
            $Listado[$i]['DNI'] = $data['dni'];
            $i++;
        }

    //devuelvo el listado generado en el array $Listado. (Podra salir vacio o con datos)..
    return $Listado;
}

function Listar_Pacientes_Parametro($vConexion,$criterio,$parametro) {
    $Listado=array();

      //1) genero la consulta que deseo segun el parametro
        $sql = "";
        switch ($criterio) { 
            case 'Nombre': 
        // Divide el parámetro en partes (nombre y apellido)
        $partes = explode(' ', trim($parametro));
        $nombre = isset($partes[0]) ? $partes[0] : '';
        $apellido = isset($partes[1]) ? $partes[1] : '';
        
        if ($nombre && $apellido) {
            // Si hay nombre y apellido (ej: "karen ba")
            $sql = "SELECT * FROM pacientes 
                    WHERE (nombre LIKE '$nombre%' AND apellido LIKE '$apellido%')";
        } else {
            // Si solo hay un término (ej: "baz")
            $sql = "SELECT * FROM pacientes 
                    WHERE (nombre LIKE '%$parametro%' OR apellido LIKE '%$parametro%')";
        }
        break;
        case 'DNI':
        $sql = "SELECT * FROM pacientes WHERE dni LIKE '%$parametro%'";
        break;
        case 'Telefono':
        $sql = "SELECT * FROM pacientes WHERE telefono LIKE '%$parametro%'";
        break;
        }    
        //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
        $rs = mysqli_query($vConexion, $sql);
        
        //3) el resultado deberá organizarse en una matriz, entonces lo recorro
        $i=0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['ID_PACIENTE'] = $data['idPaciente'];
            $Listado[$i]['NOMBRE'] = $data['nombre'];
            $Listado[$i]['APELLIDO'] = $data['apellido'];
            $Listado[$i]['TELEFONO'] = $data['telefono'];
            $Listado[$i]['DNI'] = $data['dni'];
            $i++;
        }

    //devuelvo el listado generado en el array $Listado. (Podra salir vacio o con datos)..
    return $Listado;
}

function Eliminar_Paciente($vConexion , $vIdConsulta) {

    //soy admin 
        $SQL_MiConsulta="SELECT idPaciente FROM pacientes 
                        WHERE idPaciente = $vIdConsulta ";
   
    
    $rs = mysqli_query($vConexion, $SQL_MiConsulta);
        
    $data = mysqli_fetch_array($rs);

    if (!empty($data['idPaciente']) ) {
        //si se cumple todo, entonces elimino:
        mysqli_query($vConexion, "DELETE FROM pacientes WHERE idPaciente = $vIdConsulta");
        return true;

    }else {
        return false;
    }
    
}

function Datos_Paciente($vConexion , $vIdCliente) {
    $DatosCliente  =   array();
    //me aseguro que la consulta exista
    $SQL = "SELECT * FROM pacientes 
            WHERE idPaciente = $vIdCliente";

    $rs = mysqli_query($vConexion, $SQL);

    $data = mysqli_fetch_array($rs) ;
    if (!empty($data)) {
        $DatosCliente['ID_PACIENTE'] = $data['idPaciente'];
        $DatosCliente['NOMBRE'] = $data['nombre'];
        $DatosCliente['APELLIDO'] = $data['apellido'];
        $DatosCliente['TELEFONO'] = $data['telefono'];
        $DatosCliente['DNI'] = $data['dni'];
    }
    return $DatosCliente;

}

function Modificar_Paciente($vConexion) {
    $nombre = mysqli_real_escape_string($vConexion, $_POST['Nombre']);
    $apellido = mysqli_real_escape_string($vConexion, $_POST['Apellido']);
    $telefono = mysqli_real_escape_string($vConexion, $_POST['Telefono']);
    $dni = mysqli_real_escape_string($vConexion, $_POST['DNI']);
    $idPaciente = mysqli_real_escape_string($vConexion, $_POST['IdPaciente']);

    $SQL_MiConsulta = "UPDATE pacientes 
    SET nombre = '$nombre',
    apellido = '$apellido',
    telefono = '$telefono',
    dni = '$dni'
    WHERE idPaciente = '$idPaciente'";

    if ( mysqli_query($vConexion, $SQL_MiConsulta) != false) {
        return true;
    }else {
        return false;
    }
    
}

function Listar_Servicios($vConexion) {

    $Listado=array();

      //1) genero la consulta que deseo
        $SQL = "SELECT idServicio , denominacion
        FROM servicios
        ORDER BY denominacion";

        //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
        $rs = mysqli_query($vConexion, $SQL);
        
        //3) el resultado deberá organizarse en una matriz, entonces lo recorro
        $i=0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['ID'] = $data['idServicio'];
            $Listado[$i]['DENOMINACION'] = $data['denominacion'];
            $i++;
        }

    //devuelvo el listado generado en el array $Listado. (Podra salir vacio o con datos)..
    return $Listado;
}

function InsertarTurnos($vConexion) {
    // 1. Escapar los datos para prevenir inyección SQL
    $idPaciente = mysqli_real_escape_string($vConexion, $_POST['Paciente']);
    $idServicio = mysqli_real_escape_string($vConexion, $_POST['Servicio']);
    $fecha = mysqli_real_escape_string($vConexion, $_POST['Fecha']);
    $hora = mysqli_real_escape_string($vConexion, $_POST['Horario']);

    // 2. Crear la consulta SQL
    $SQL_Insert = "INSERT INTO turnos (
        idPaciente, 
        idServicio, 
        fecha, 
        hora
    ) VALUES (
        '$idPaciente',
        '$idServicio',
        '$fecha',
        '$hora'
    )";

    // 3. Ejecutar la consulta
    if (!mysqli_query($vConexion, $SQL_Insert)) {
        // Registrar el error para depuración
        error_log("Error al insertar turno: " . mysqli_error($vConexion));
        return false;
    }

    return true;
}

function Listar_Turnos($vConexion) {
    $Listado = array();

    // Consulta SQL modificada
    $SQL = "SELECT 
                T.idTurno,
                T.fecha,
                T.hora,
                P.NOMBRE AS nombre_paciente,
                P.APELLIDO AS apellido_paciente,
                S.DENOMINACION AS servicio,
                T.idPaciente,
                T.idServicio
            FROM 
                turnos T
            INNER JOIN 
                pacientes P ON T.idPaciente = P.idPaciente
            INNER JOIN 
                servicios S ON T.idServicio = S.idServicio
            ORDER BY 
                T.fecha DESC, 
                T.hora";

    $rs = mysqli_query($vConexion, $SQL);
    
    if (!$rs) {
        // Manejo de error en la consulta
        error_log("Error en Listar_Turnos: " . mysqli_error($vConexion));
        return $Listado; // Devuelve array vacío si hay error
    }

    $i = 0;
    while ($data = mysqli_fetch_assoc($rs)) {
        $Listado[$i]['ID_TURNO'] = $data['idTurno'];
        $Listado[$i]['FECHA'] = $data['fecha'];
        $Listado[$i]['HORARIO'] = $data['hora'];
        $Listado[$i]['NOMBRE_PACIENTE'] = $data['nombre_paciente'];
        $Listado[$i]['APELLIDO_PACIENTE'] = $data['apellido_paciente'];
        $Listado[$i]['SERVICIO'] = $data['servicio'];
        $Listado[$i]['ID_PACIENTE'] = $data['idPaciente'];
        $Listado[$i]['ID_SERVICIO'] = $data['idServicio'];
        $i++;
    }

    return $Listado;
}

function Listar_Turnos_Parametro($vConexion, $criterio, $parametro) {
    $Listado = array();

    // Construir la consulta según el criterio
    switch ($criterio) {
        case 'Paciente':
            $SQL = "SELECT 
                        T.idTurno,
                        T.fecha,
                        T.hora,
                        P.NOMBRE AS nombre_paciente,
                        P.APELLIDO AS apellido_paciente,
                        S.DENOMINACION AS servicio,
                        T.idPaciente,
                        T.idServicio
                    FROM 
                        turnos T
                    INNER JOIN 
                        pacientes P ON T.idPaciente = P.idPaciente
                    INNER JOIN 
                        servicios S ON T.idServicio = S.idServicio
                    WHERE 
                        P.NOMBRE LIKE '%$parametro%' OR P.APELLIDO LIKE '%$parametro%'
                    ORDER BY 
                        T.fecha DESC, 
                        T.hora";
            break;
        case 'Servicio':
            $SQL = "SELECT 
                        T.idTurno,
                        T.fecha,
                        T.hora,
                        P.NOMBRE AS nombre_paciente,
                        P.APELLIDO AS apellido_paciente,
                        S.DENOMINACION AS servicio,
                        T.idPaciente,
                        T.idServicio
                    FROM 
                        turnos T
                    INNER JOIN 
                        pacientes P ON T.idPaciente = P.idPaciente
                    INNER JOIN 
                        servicios S ON T.idServicio = S.idServicio
                    WHERE 
                        S.DENOMINACION LIKE '%$parametro%'
                    ORDER BY 
                        T.fecha DESC, 
                        T.hora";
            break;
        case 'Fecha':
            $SQL = "SELECT 
                        T.idTurno,
                        T.fecha,
                        T.hora,
                        P.NOMBRE AS nombre_paciente,
                        P.APELLIDO AS apellido_paciente,
                        S.DENOMINACION AS servicio,
                        T.idPaciente,
                        T.idServicio
                    FROM 
                        turnos T
                    INNER JOIN 
                        pacientes P ON T.idPaciente = P.idPaciente
                    INNER JOIN 
                        servicios S ON T.idServicio = S.idServicio
                    WHERE 
                        T.fecha LIKE '%$parametro%'
                    ORDER BY 
                        T.fecha DESC, 
                        T.hora";
            break;
        default:
            // Si no hay criterio válido, devolver array vacío
            return $Listado;
    }

    $rs = mysqli_query($vConexion, $SQL);

    if (!$rs) {
        error_log("Error en Listar_Turnos_Parametro: " . mysqli_error($vConexion));
        return $Listado;
    }

    $i = 0;
    while ($data = mysqli_fetch_assoc($rs)) {
        $Listado[$i]['ID_TURNO'] = $data['idTurno'];
        $Listado[$i]['FECHA'] = $data['fecha'];
        $Listado[$i]['HORARIO'] = $data['hora'];
        $Listado[$i]['NOMBRE_PACIENTE'] = $data['nombre_paciente'];
        $Listado[$i]['APELLIDO_PACIENTE'] = $data['apellido_paciente'];
        $Listado[$i]['SERVICIO'] = $data['servicio'];
        $Listado[$i]['ID_PACIENTE'] = $data['idPaciente'];
        $Listado[$i]['ID_SERVICIO'] = $data['idServicio'];
        $i++;
    }

    return $Listado;
}

function Eliminar_Turno($vConexion , $vIdConsulta) {

    //soy admin 
        $SQL_MiConsulta="SELECT IdTurno FROM turnos 
                        WHERE IdTurno = $vIdConsulta ";
   
    
    $rs = mysqli_query($vConexion, $SQL_MiConsulta);
        
    $data = mysqli_fetch_array($rs);

    if (!empty($data['IdTurno']) ) {
        //si se cumple todo, entonces elimino:
        mysqli_query($vConexion, "DELETE FROM turnos WHERE IdTurno = $vIdConsulta");
        return true;

    }else {
        return false;
    }
    
}

function Datos_Turno($vConexion , $vIdTurno) {
    $DatosTurnoActual  =   array();
    //me aseguro que la consulta exista
    $SQL = "SELECT * FROM turnos WHERE idTurno = '$vIdTurno' LIMIT 1";
    $rs = mysqli_query($vConexion, $SQL); 
    if ($data = mysqli_fetch_assoc($rs)) {
        $DatosTurnoActual['ID_TURNO'] = $data['idTurno'];
        $DatosTurnoActual['FECHA'] = $data['fecha'];
        $DatosTurnoActual['HORARIO'] = $data['hora'];
        $DatosTurnoActual['ID_PACIENTE'] = $data['idPaciente'];
        $DatosTurnoActual['ID_SERVICIO'] = $data['idServicio'];
    }
    return $DatosTurnoActual;
}

function Modificar_Turno($conexion, $datos) {
    $idTurno = mysqli_real_escape_string($conexion, $datos['IdTurno']);
    $fecha = mysqli_real_escape_string($conexion, $datos['Fecha']);
    $hora = mysqli_real_escape_string($conexion, $datos['Horario']);
    $idPaciente = mysqli_real_escape_string($conexion, $datos['Paciente']);
    $idServicio = mysqli_real_escape_string($conexion, $datos['Servicio']);

    $SQL = "UPDATE turnos SET 
                fecha = '$fecha',
                hora = '$hora',
                idPaciente = '$idPaciente',
                idServicio = '$idServicio'
            WHERE idTurno = '$idTurno'";

    return mysqli_query($conexion, $SQL);
}

function Validar_Turno(){
    $_SESSION['Mensaje']='';
    if (strlen($_POST['Fecha']) < 4) {
        $_SESSION['Mensaje'].='Debes seleccionar una fecha. <br />';
    }
    if (strlen($_POST['Horario']) < 4) {
        $_SESSION['Mensaje'].='Debes seleccionar un horario. <br />';
    }
    if ($_POST['Servicio'] == 'Selecciona una opcion') {
        $_SESSION['Mensaje'].='Debes seleccionar un Tipo de Servicio. <br />';
    }
    if ($_POST['Paciente'] == 'Selecciona una opcion') {
        $_SESSION['Mensaje'].='Debes seleccionar un Cliente. <br />';
    }

    //con esto aseguramos que limpiamos espacios y limpiamos de caracteres de codigo ingresados
    //foreach($_POST as $Id=>$Valor){
    //    $_POST[$Id] = trim($_POST[$Id]);
    //    $_POST[$Id] = strip_tags($_POST[$Id]);
    //}

    return $_SESSION['Mensaje'];
}

?>