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
    if ($_POST['TipoServicio'] == 'Selecciona una opcion') {
        $_SESSION['Mensaje'].='Debes seleccionar un Tipo de Servicio. <br />';
    }
    if ($_POST['Estilista'] == 'Selecciona una opcion') {
        $_SESSION['Mensaje'].='Debes seleccionar un Estilista. <br />';
    }
    if ($_POST['Cliente'] == 'Selecciona una opcion') {
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

function Listar_Tipos($vConexion) {

    $Listado=array();

      //1) genero la consulta que deseo
        $SQL = "SELECT IdTipoServicio , Denominacion
        FROM tipo_servicio
        ORDER BY Denominacion";

        //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
        $rs = mysqli_query($vConexion, $SQL);
        
        //3) el resultado deberá organizarse en una matriz, entonces lo recorro
        $i=0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['ID'] = $data['IdTipoServicio'];
            $Listado[$i]['DENOMINACION'] = $data['Denominacion'];
            $i++;
        }

    //devuelvo el listado generado en el array $Listado. (Podra salir vacio o con datos)..
    return $Listado;
}

function Listar_Estilistas($vConexion) {

    $Listado=array();

      //1) genero la consulta que deseo
        $SQL = "SELECT IdEstilista , Apellido , Nombre
        FROM estilista
        ORDER BY Apellido";

        //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
        $rs = mysqli_query($vConexion, $SQL);
        
        //3) el resultado deberá organizarse en una matriz, entonces lo recorro
        $i=0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['ID'] = $data['IdEstilista'];
            $Listado[$i]['APELLIDO'] = $data['Apellido'];
            $Listado[$i]['NOMBRE'] = $data['Nombre'];
            $i++;
        }

    //devuelvo el listado generado en el array $Listado. (Podra salir vacio o con datos)..
    return $Listado;
}

function Listar_Clientes_Turnos($vConexion) {

    $Listado=array();

      //1) genero la consulta que deseo
        $SQL = "SELECT idCliente , apellido , nombre
        FROM clientes
        ORDER BY Apellido";

        //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
        $rs = mysqli_query($vConexion, $SQL);
        
        //3) el resultado deberá organizarse en una matriz, entonces lo recorro
        $i=0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['ID'] = $data['idCliente'];
            $Listado[$i]['APELLIDO'] = $data['apellido'];
            $Listado[$i]['NOMBRE'] = $data['nombre'];
            $i++;
        }

    //devuelvo el listado generado en el array $Listado. (Podra salir vacio o con datos)..
    return $Listado;
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

function Listar_Turnos($vConexion) {

    $Listado=array();

      //1) genero la consulta que deseo

        $SQL = "SELECT T.IdTurno, T.Fecha, T.Horario, C.nombre, C.apellido, E.IdEstado as estado, ES.Nombre, ES.Apellido, T.IdTipoServicio
        FROM clientes C, estado E, estilista ES, turnos T
        WHERE T.IdCliente=C.idCliente AND T.IdEstado=E.IdEstado
        AND T.IdEstilista=ES.IdEstilista ";
        
        if($_SESSION['Usuario_Nivel'] == '2'){
            //si soy estilista solo veo mis consultas
            if($_SESSION['Usuario_Id'] == 3){
                //Listo lo de Lorena
                $SQL .="AND T.IdEstilista=2 ";
            }elseif($_SESSION['Usuario_Id'] == 4){
                //Listo lo de Natalia
                $SQL .="AND T.IdEstilista=1 ";
            }    

        }

        $SQL .= "ORDER BY T.Fecha DESC, T.Horario";

        //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
        $rs = mysqli_query($vConexion, $SQL);
        
        //3) el resultado deberá organizarse en una matriz, entonces lo recorro
        $i=0;
        while ($data = mysqli_fetch_array($rs)) {
            //paso el contenido del tipo de servicio a un array

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

function InsertarTurnos($vConexion){
    //divido el array a una cadena separada por coma para guardar
    $string = implode(',', $_POST['TipoServicio']);

    $SQL_Insert="INSERT INTO turnos ( Horario, Fecha, IdTipoServicio, IdEstilista, IdEstado, IdCliente)
    VALUES ('".$_POST['Horario']."' , '".$_POST['Fecha']."' , '".$string."', '".$_POST['Estilista']."', '1', '".$_POST['Cliente']."')";


    if (!mysqli_query($vConexion, $SQL_Insert)) {
        //si surge un error, finalizo la ejecucion del script con un mensaje
        die('<h4>Error al intentar insertar el registro.</h4>');
    }

    return true;
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

function Listar_Historia($vConexion) {
    $Listado = array();

    $SQL = "SELECT 
                h.idHistoriaMedica,
                p.nombre AS nombre_paciente,
                p.apellido AS apellido_paciente,
                p.dni,
                h.enfermedades,
                h.medicamentos,
                h.servicios,
                h.esparcimiento
            FROM historiamedica h
            INNER JOIN pacientes p ON h.idPaciente = p.idPaciente";

    $rs = mysqli_query($vConexion, $SQL);

    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['ID_HISTORIA'] = $data['idHistoriaMedica'];
        $Listado[$i]['NOMBREPACIENTE'] = $data['nombre_paciente'];
        $Listado[$i]['APELLIDOPACIENTE'] = $data['apellido_paciente'];
        $Listado[$i]['DNI'] = $data['dni'];
        $Listado[$i]['ENFERMEDADES'] = $data['enfermedades'];
        $Listado[$i]['MEDICAMENTOS'] = $data['medicamentos'];
        $Listado[$i]['SERVICIOS'] = $data['servicios'];
        $Listado[$i]['ESPARCIMIENTO'] = $data['esparcimiento'];
        $i++;
    }

    return $Listado;
}

function Eliminar_Historia($vConexion , $vIdConsulta) {

    //soy admin 
        $SQL_MiConsulta="SELECT idHistoriaMedica FROM historiamedica
                        WHERE idHistoriaMedica = $vIdConsulta ";
   
    
    $rs = mysqli_query($vConexion, $SQL_MiConsulta);
        
    $data = mysqli_fetch_array($rs);

    if (!empty($data['idHistoriaMedica']) ) {
        //si se cumple todo, entonces elimino:
        mysqli_query($vConexion, "DELETE FROM historiamedica WHERE idHistoriaMedica = $vIdConsulta");
        return true;

    }else {
        return false;
    }
    
}

function Listar_Historia_Parametro($vConexion, $criterio, $parametro) {
    $Listado = array();

    // Escapamos el parámetro de búsqueda para prevenir inyecciones SQL
    $parametro = mysqli_real_escape_string($vConexion, $parametro);

    // Armamos el WHERE según el criterio seleccionado
    switch ($criterio) {
        case 'Nombre':
            $where = "p.nombre LIKE '%$parametro%' OR p.apellido LIKE '%$parametro%'";
            break;
        case 'DNI':
            $where = "p.dni LIKE '%$parametro%'";
            break;
        case 'Enfermedades':
            $where = "h.enfermedades LIKE '%$parametro%'";
            break;
        case 'Medicamentos': // OJO: corregido de "Medocamentos"
            $where = "h.medicamentos LIKE '%$parametro%'";
            break;
        case 'Servicios':
            $where = "h.servicios LIKE '%$parametro%'";
            break;
        case 'Esparcimiento':
            $where = "h.esparcimiento LIKE '%$parametro%'";
            break;
        default:
            $where = "1=1"; // Sin filtro si no coincide ningún criterio
            break;
    }

    // Consulta SQL con filtro aplicado
    $SQL = "SELECT 
                h.idHistoriaMedica,
                p.nombre AS nombre_paciente,
                p.apellido AS apellido_paciente,
                p.dni,
                h.enfermedades,
                h.medicamentos,
                h.servicios,
                h.esparcimiento
            FROM historiamedica h
            INNER JOIN pacientes p ON h.idPaciente = p.idPaciente
            WHERE $where";

    $rs = mysqli_query($vConexion, $SQL);

    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['ID_HISTORIA'] = $data['idHistoriaMedica'];
        $Listado[$i]['NOMBREPACIENTE'] = $data['nombre_paciente'];
        $Listado[$i]['APELLIDOPACIENTE'] = $data['apellido_paciente'];
        $Listado[$i]['DNI'] = $data['dni'];
        $Listado[$i]['ENFERMEDADES'] = $data['enfermedades'];
        $Listado[$i]['MEDICAMENTOS'] = $data['medicamentos'];
        $Listado[$i]['SERVICIOS'] = $data['servicios'];
        $Listado[$i]['ESPARCIMIENTO'] = $data['esparcimiento'];
        $i++;
    }

    return $Listado;
}



?>