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
    $DatosTurno  =   array();
    //me aseguro que la consulta exista
    $SQL = "SELECT * FROM turnos 
            WHERE IdTurno = $vIdTurno";

    $rs = mysqli_query($vConexion, $SQL);

    $data = mysqli_fetch_array($rs) ;
    if (!empty($data)) {
        $DatosTurno['ID_TURNO'] = $data['IdTurno'];
        $DatosTurno['HORARIO'] = $data['Horario'];
        $DatosTurno['FECHA'] = $data['Fecha'];
        $DatosTurno['TIPO_SERVICIO'] = $data['IdTipoServicio'];
        $DatosTurno['ESTILISTA'] = $data['IdEstilista'];
        $DatosTurno['ESTADO'] = $data['IdEstado'];
        $DatosTurno['CLIENTE'] = $data['IdCliente'];
    }
    return $DatosTurno;

}

function Datos_Turno_Comprobante($vConexion, $vIdTurno) {
    $DatosTurno = array();

    // Consulta para obtener los datos del turno junto con los valores de las tablas relacionadas
    $SQL = "SELECT 
                t.IdTurno, 
                t.Horario, 
                t.Fecha, 
                ts.Denominacion AS TIPO_SERVICIO, 
                CONCAT(e.Apellido, ', ', e.Nombre) AS ESTILISTA, 
                es.Denominacion AS ESTADO, 
                CONCAT(c.apellido, ', ', c.nombre) AS CLIENTE
            FROM turnos t
            LEFT JOIN tipo_servicio ts ON t.IdTipoServicio = ts.IdTipoServicio
            LEFT JOIN estilista e ON t.IdEstilista = e.IdEstilista
            LEFT JOIN estado es ON t.IdEstado = es.IdEstado
            LEFT JOIN clientes c ON t.IdCliente = c.idCliente
            WHERE t.IdTurno = $vIdTurno";

    $rs = mysqli_query($vConexion, $SQL);

    $data = mysqli_fetch_array($rs);
    if (!empty($data)) {
        $DatosTurno['ID_TURNO'] = $data['IdTurno'];
        $DatosTurno['HORARIO'] = $data['Horario'];
        $DatosTurno['FECHA'] = $data['Fecha'];
        $DatosTurno['TIPO_SERVICIO'] = $data['TIPO_SERVICIO'];
        $DatosTurno['ESTILISTA'] = $data['ESTILISTA'];
        $DatosTurno['ESTADO'] = $data['ESTADO'];
        $DatosTurno['CLIENTE'] = $data['CLIENTE'];
    }

    return $DatosTurno;
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

function Modificar_Turno($vConexion) {
    //divido el array a una cadena separada por coma para guardar
    $string = implode(',', $_POST['TipoServicio']);

    $fecha = mysqli_real_escape_string($vConexion, $_POST['Fecha']);
    $horario = mysqli_real_escape_string($vConexion, $_POST['Horario']);
    $tipoServicio = mysqli_real_escape_string($vConexion, $string);
    $estilista = mysqli_real_escape_string($vConexion, $_POST['Estilista']);
    $cliente = mysqli_real_escape_string($vConexion, $_POST['Cliente']);
    $estado = mysqli_real_escape_string($vConexion, $_POST['Estado']);
    $idTurno = mysqli_real_escape_string($vConexion, $_POST['IdTurno']);

    $SQL_MiConsulta = "UPDATE turnos 
    SET Fecha = '$fecha',
    Horario = '$horario',
    IdTipoServicio = '$tipoServicio',
    IdEstilista = '$estilista',
    IdCliente = '$cliente',
    IdEstado = '$estado'
    WHERE IdTurno = '$idTurno'";

    if ( mysqli_query($vConexion, $SQL_MiConsulta) != false) {
        return true;
    }else {
        return false;
    }
    
}

function Listar_Estados_Turnos($vConexion) {

    $Listado=array();

      //1) genero la consulta que deseo
        $SQL = "SELECT IdEstado , Denominacion
        FROM estado
        ORDER BY IdEstado";

        //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
        $rs = mysqli_query($vConexion, $SQL);
        
        //3) el resultado deberá organizarse en una matriz, entonces lo recorro
        $i=0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['ID'] = $data['IdEstado'];
            $Listado[$i]['DENOMINACION'] = $data['Denominacion'];
            $i++;
        }

    //devuelvo el listado generado en el array $Listado. (Podra salir vacio o con datos)..
    return $Listado;
}

function Listar_Turnos_Parametro($vConexion,$criterio,$parametro) {
    $Listado=array();

      //1) genero la consulta que deseo

        switch ($criterio) { 
        case 'Cliente': 
            $SQL = "SELECT T.IdTurno, T.Fecha, T.Horario, C.nombre, C.apellido, E.IdEstado as estado, ES.Nombre, ES.Apellido,T.IdTipoServicio
        FROM clientes C, estado E, estilista ES, turnos T
        WHERE (C.nombre LIKE '%$parametro%' OR C.apellido LIKE '%$parametro%') 
        AND T.IdCliente=C.idCliente AND T.IdEstado=E.IdEstado
        AND T.IdEstilista=ES.IdEstilista
        ORDER BY T.Fecha, T.Horario";
        break;
        case 'Estilista':
            $SQL = "SELECT T.IdTurno, T.Fecha, T.Horario, C.nombre, C.apellido, E.denominacion as estado, ES.Nombre, ES.Apellido,T.IdTipoServicio
        FROM clientes C, estado E, estilista ES, turnos T
        WHERE (ES.Nombre LIKE '%$parametro%' OR ES.Apellido LIKE '%$parametro%') 
        AND T.IdCliente=C.idCliente AND T.IdEstado=E.IdEstado
        AND T.IdEstilista=ES.IdEstilista
        ORDER BY T.Fecha, T.Horario";
        break;
        case 'Fecha':
            $SQL = "SELECT T.IdTurno, T.Fecha, T.Horario, C.nombre, C.apellido, E.denominacion as estado, ES.Nombre, ES.Apellido,T.IdTipoServicio
        FROM clientes C, estado E, estilista ES, turnos T
        WHERE T.Fecha LIKE '%$parametro%' 
        AND T.IdCliente=C.idCliente AND T.IdEstado=E.IdEstado
        AND T.IdEstilista=ES.IdEstilista
        ORDER BY T.Fecha, T.Horario";
        break;
        case 'TipoServicio':
            $SQL = "SELECT T.IdTurno, T.Fecha, T.Horario, C.nombre, C.apellido, E.denominacion as estado, ES.Nombre, ES.Apellido,T.IdTipoServicio
        FROM clientes C, estado E, estilista ES, turnos T
        WHERE TP.Denominacion LIKE '%$parametro%' 
        AND T.IdCliente=C.idCliente AND T.IdEstado=E.IdEstado
        AND T.IdEstilista=ES.IdEstilista
        ORDER BY T.Fecha, T.Horario";
        break;
        }    

        //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
        $rs = mysqli_query($vConexion, $SQL);
        
        //3) el resultado deberá organizarse en una matriz, entonces lo recorro
        $i=0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['ID_TURNO'] = $data['IdTurno'];
            $Listado[$i]['FECHA'] = $data['Fecha'];
            $Listado[$i]['HORARIO'] = $data['Horario'];
            $Listado[$i]['NOMBRE_C'] = $data['nombre'];
            $Listado[$i]['APELLIDO_C'] = $data['apellido'];
            $Listado[$i]['ESTADO'] = $data['estado'];
            $Listado[$i]['NOMBRE_E'] = $data['Nombre'];
            $Listado[$i]['APELLIDO_E'] = $data['Apellido'];
            $Listado[$i]['TIPO_SERVICIO'] = $data['IdTipoServicio'];
            $i++;
        }

    //devuelvo el listado generado en el array $Listado. (Podra salir vacio o con datos)..
    return $Listado;

}

function ColorDeFila($vFecha,$vEstado) {
    $Title='';
    $Color=''; 
    $FechaActual = date("Y-m-d");

    if ($vFecha < $FechaActual && $vEstado!=3){
        //la fecha del viaje es mayor a mañana?
        $Title='Turno Vencido';
        $Color='table-danger'; 
    
    } else if ($vEstado == 2){
        //Turno en Curso
        $Title='Turno en Curso';
        $Color='table-warning'; 
    } else if ($vEstado==3){
        //Turno Completado
        $Title='Turno Completado';
        $Color='table-success'; 
    } else if ($vEstado == 1){
        //Turno pendiente
        $Title='Turno Pendiente';
        $Color='table-primary';
    }
        
    
    return [$Title, $Color];

}

function Listar_Horarios_Ocupados($MiConexion, $fecha) {
    $query = "SELECT Horario FROM turnos WHERE Fecha = ?";
    $stmt = $MiConexion->prepare($query);
    $stmt->bind_param("s", $fecha);
    $stmt->execute();
    $result = $stmt->get_result();

    $horariosOcupados = [];
    while ($row = $result->fetch_assoc()) {
        $horariosOcupados[] = $row['Horario'];
    }

    return $horariosOcupados;
}

?>