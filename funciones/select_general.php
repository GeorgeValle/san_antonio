<?php

function InsertarPacientes($vConexion) {

    $dni = mysqli_real_escape_string($vConexion, $_POST['DNI']);
    $nombre = mysqli_real_escape_string($vConexion, $_POST['Nombre']);
    $apellido = mysqli_real_escape_string($vConexion, $_POST['Apellido']);
    $telefono = mysqli_real_escape_string($vConexion, $_POST['Telefono']);
    $tipoPaciente = mysqli_real_escape_string($vConexion, $_POST['idTipoPaciente']);
    
    $SQL_Insert = "INSERT INTO pacientes (nombre, apellido, telefono, dni, idTipoPaciente)
                  VALUES ('$nombre', '$apellido', '$telefono', '$dni', '$tipoPaciente')";
    
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
    if (strlen($_POST['idTipoPaciente']) == 'Seleccione un tipo') {
        $_SESSION['Mensaje'].='Debes ingresar un tipo de paciente. <br />';
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
        if (empty($_POST['idTipoPaciente'])) {
        $_SESSION['Mensaje'] .= 'Debe seleccionar un tipo de paciente.<br>';
    }

    //con esto aseguramos que limpiamos espacios y limpiamos de caracteres de codigo ingresados
    foreach($_POST as $Id=>$Valor){
        $_POST[$Id] = trim($_POST[$Id]);
        $_POST[$Id] = strip_tags($_POST[$Id]);
    }

    return $_SESSION['Mensaje'];
}

function Listar_Pacientes($conexion) {
    $sql = "SELECT p.idPaciente AS ID_PACIENTE, 
                   p.nombre AS NOMBRE, 
                   p.apellido AS APELLIDO,
                   p.telefono AS TELEFONO,
                   p.dni AS DNI,
                   p.idTipoPaciente AS ID_TIPO_PACIENTE,
                   tp.denominacion AS TIPO_PACIENTE
            FROM pacientes p
            LEFT JOIN tipo_paciente tp ON p.idTipoPaciente = tp.idTipoPaciente
            ORDER BY p.idPaciente DESC";
    
    $resultado = mysqli_query($conexion, $sql);
    $pacientes = array();
    
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $pacientes[] = $fila;
        }
    }
    
    return $pacientes;
}

function ListarTiposPaciente($MiConexion) {
    $Listado=array();

    //1) genero la consulta que deseo
    $SQL = "SELECT idTipoPaciente, denominacion FROM tipo_paciente ORDER BY idTipoPaciente";

    //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
    $rs = mysqli_query($MiConexion, $SQL);

    //3) el resultado deberá organizarse en una matriz, entonces lo recorro
    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['id_tipo_paciente'] = $data['idTipoPaciente'];
        $Listado[$i]['denominacion'] = $data['denominacion'];
        $i++;
    }

    //devuelvo el listado generado en el array $Listado. (Podra salir vacio o con datos)..
    return $Listado;
}

function Listar_Pacientes_Parametro($conexion, $criterio, $parametro) {
    $sql = "SELECT p.idPaciente AS ID_PACIENTE, 
                   p.nombre AS NOMBRE, 
                   p.apellido AS APELLIDO,
                   p.telefono AS TELEFONO,
                   p.dni AS DNI,
                   p.idTipoPaciente AS ID_TIPO_PACIENTE,
                   tp.denominacion AS TIPO_PACIENTE
            FROM pacientes p
            LEFT JOIN tipos_paciente tp ON p.idTipoPaciente = tp.id_tipo_paciente
            WHERE p.$criterio LIKE ?
            ORDER BY p.idPaciente DESC";
    
    $stmt = mysqli_prepare($conexion, $sql);
    $parametro_like = "%$parametro%";
    mysqli_stmt_bind_param($stmt, "s", $parametro_like);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    $pacientes = array();
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $pacientes[] = $fila;
    }
    
    return $pacientes;
}

function Eliminar_Paciente($vConexion, $vIdConsulta) {
    // Buscar si el paciente existe
    $SQL_MiConsulta = "SELECT idPaciente FROM pacientes WHERE idPaciente = $vIdConsulta";
    $rs = mysqli_query($vConexion, $SQL_MiConsulta);
    $data = mysqli_fetch_array($rs);

    if (!empty($data['idPaciente'])) {
        // Eliminar turnos relacionados primero
        mysqli_query($vConexion, "DELETE FROM turnos WHERE idPaciente = $vIdConsulta");

        // Luego eliminar el paciente
        mysqli_query($vConexion, "DELETE FROM pacientes WHERE idPaciente = $vIdConsulta");
        return true;
    } else {
        return false;
    }
}

function Datos_Paciente($conexion, $idPaciente) {
    $sql = "SELECT nombre AS NOMBRE, dni AS DNI, telefono AS TELEFONO, 
                   apellido AS APELLIDO, idPaciente AS ID_PACIENTE,
                   idTipoPaciente AS ID_TIPO_PACIENTE 
            FROM pacientes 
            WHERE idPaciente = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idPaciente);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($resultado);
}

function Modificar_Paciente($conexion) {
    // Asegúrate de incluir idTipoPaciente en la consulta SQL
    $sql = "UPDATE pacientes SET 
            nombre = ?, 
            apellido = ?, 
            telefono = ?, 
            dni = ?,
            idTipoPaciente = ?
            WHERE idPaciente = ?";
    
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "sssiii", 
        $_POST['Nombre'],
        $_POST['Apellido'],
        $_POST['Telefono'],
        $_POST['DNI'],
        $_POST['idTipoPaciente'],
        $_POST['IdPaciente']
    );
    
    return mysqli_stmt_execute($stmt);
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
    // Iniciar transacción para asegurar integridad de datos
    mysqli_begin_transaction($vConexion);
    
    try {
        // 1. Escapar datos básicos del turno
        $idPaciente = mysqli_real_escape_string($vConexion, $_POST['Paciente']);
        $fecha = mysqli_real_escape_string($vConexion, $_POST['Fecha']);
        $hora = mysqli_real_escape_string($vConexion, $_POST['Horario']);
        
        // 2. Insertar el turno principal
        $SQL_Insert = "INSERT INTO turnos (idPaciente, fecha, horario) 
                      VALUES ('$idPaciente', '$fecha', '$hora')";
        
        if (!mysqli_query($vConexion, $SQL_Insert)) {
            throw new Exception("Error al insertar turno: " . mysqli_error($vConexion));
        }
        
        // 3. Obtener el ID del turno recién insertado
        $idTurno = mysqli_insert_id($vConexion);
        
        // 4. Insertar los servicios seleccionados en detalle_turno
        if (!empty($_POST['TipoServicio']) && is_array($_POST['TipoServicio'])) {
            foreach ($_POST['TipoServicio'] as $idServicio) {
                $idServicio = mysqli_real_escape_string($vConexion, $idServicio);
                $SQL_Detalle = "INSERT INTO detalle_turno (idTurno, idServicio) 
                               VALUES ('$idTurno', '$idServicio')";
                
                if (!mysqli_query($vConexion, $SQL_Detalle)) {
                    throw new Exception("Error al insertar detalle: " . mysqli_error($vConexion));
                }
            }
        } else {
            throw new Exception("Debe seleccionar al menos un servicio");
        }
        
        // Confirmar transacción si todo salió bien
        mysqli_commit($vConexion);
        return true;
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        mysqli_rollback($vConexion);
        error_log($e->getMessage());
        $_SESSION['Mensaje'] = "Error al registrar el turno: " . $e->getMessage();
        return false;
    }
}

function Listar_Turnos($vConexion) {
    $Listado = array();

    // Consulta SQL modificada para obtener servicios individuales
    $SQL = "SELECT 
                T.idTurno,
                T.fecha,
                T.horario,
                TP.denominacion AS tipo_paciente,
                P.nombre AS nombre_paciente,
                P.apellido AS apellido_paciente,
                T.idPaciente,
                S.idServicio,
                S.denominacion AS nombre_servicio
            FROM 
                turnos T
            INNER JOIN 
                pacientes P ON T.idPaciente = P.idPaciente
            LEFT JOIN 
                tipo_paciente TP ON P.idTipoPaciente = TP.idTipoPaciente
            LEFT JOIN 
                detalle_turno DT ON T.idTurno = DT.idTurno
            LEFT JOIN 
                servicios S ON DT.idServicio = S.idServicio
            ORDER BY 
                T.fecha DESC, 
                T.horario";

    $rs = mysqli_query($vConexion, $SQL);
    
    if (!$rs) {
        error_log("Error en Listar_Turnos: " . mysqli_error($vConexion));
        return $Listado;
    }

    // Agrupar los resultados por turno
    $turnos = array();
    while ($data = mysqli_fetch_assoc($rs)) {
        $idTurno = $data['idTurno'];
        
        if (!isset($turnos[$idTurno])) {
            $turnos[$idTurno] = array(
                'ID_TURNO' => $data['idTurno'],
                'FECHA' => $data['fecha'],
                'HORARIO' => $data['horario'],
                'NOMBRE_PACIENTE' => $data['nombre_paciente'],
                'APELLIDO_PACIENTE' => $data['apellido_paciente'],
                'TIPO_PACIENTE' => $data['tipo_paciente'],
                'ID_PACIENTE' => $data['idPaciente'],
                'SERVICIOS' => array()
            );
        }
        
        if (!empty($data['idServicio'])) {
            $turnos[$idTurno]['SERVICIOS'][] = array(
                'id' => $data['idServicio'],
                'nombre' => $data['nombre_servicio']
            );
        }
    }

    // Convertir a formato de lista indexada
    $Listado = array_values($turnos);

    return $Listado;
}

function Listar_Turnos_Parametro($vConexion, $criterio, $parametro) {
    $Listado = array();
    $parametro = mysqli_real_escape_string($vConexion, $parametro);

    // Base de la consulta
    $baseSQL = "SELECT 
                    T.idTurno,
                    T.fecha,
                    T.horario,
                    TP.denominacion AS tipo_paciente,
                    P.nombre AS nombre_paciente,
                    P.apellido AS apellido_paciente,
                    T.idPaciente,
                    S.idServicio,
                    S.denominacion AS nombre_servicio
                FROM 
                    turnos T
                INNER JOIN 
                    pacientes P ON T.idPaciente = P.idPaciente
                LEFT JOIN 
                    tipo_paciente TP ON P.idTipoPaciente = TP.idTipoPaciente
                LEFT JOIN 
                    detalle_turno DT ON T.idTurno = DT.idTurno
                LEFT JOIN 
                    servicios S ON DT.idServicio = S.idServicio
                %WHERE%
                ORDER BY 
                    T.fecha DESC, 
                    T.horario";

    // Construir la condición WHERE según el criterio
    switch ($criterio) {
        case 'Paciente':
            $where = "WHERE P.nombre LIKE '%$parametro%' OR P.apellido LIKE '%$parametro%'";
            break;
        case 'Servicio':
            $where = "WHERE S.denominacion LIKE '%$parametro%'";
            break;
        case 'Fecha':
            $where = "WHERE T.fecha LIKE '%$parametro%'";
            break;
        case 'DNI':
            $where = "WHERE P.dni LIKE '%$parametro%'";
            break;
        default:
            return $Listado;
    }

    $SQL = str_replace('%WHERE%', $where, $baseSQL);
    $rs = mysqli_query($vConexion, $SQL);

    if (!$rs) {
        error_log("Error en Listar_Turnos_Parametro: " . mysqli_error($vConexion));
        return $Listado;
    }

    // Agrupar los resultados por turno
    $turnos = array();
    while ($data = mysqli_fetch_assoc($rs)) {
        $idTurno = $data['idTurno'];
        
        if (!isset($turnos[$idTurno])) {
            $turnos[$idTurno] = array(
                'ID_TURNO' => $data['idTurno'],
                'FECHA' => $data['fecha'],
                'HORARIO' => $data['horario'],
                'NOMBRE_PACIENTE' => $data['nombre_paciente'],
                'APELLIDO_PACIENTE' => $data['apellido_paciente'],
                'TIPO_PACIENTE' => $data['tipo_paciente'],
                'ID_PACIENTE' => $data['idPaciente'],
                'SERVICIOS' => array()
            );
        }
        
        if (!empty($data['idServicio'])) {
            $turnos[$idTurno]['SERVICIOS'][] = array(
                'id' => $data['idServicio'],
                'nombre' => $data['nombre_servicio']
            );
        }
    }

    // Convertir a formato de lista indexada
    $Listado = array_values($turnos);

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

function Validar_Turno() {
    $_SESSION['Mensaje'] = '';
    
    if (strlen($_POST['Fecha']) < 4) {
        $_SESSION['Mensaje'] .= 'Debes seleccionar una fecha. <br />';
        $_SESSION['Estilo'] = 'warning';
    }
    if (strlen($_POST['Horario']) < 4) {
        $_SESSION['Mensaje'] .= 'Debes seleccionar un horario. <br />';
        $_SESSION['Estilo'] = 'warning';
    }
    if ($_POST['Paciente'] == 'Selecciona una opcion') {
        $_SESSION['Mensaje'] .= 'Debes seleccionar un Cliente. <br />';
        $_SESSION['Estilo'] = 'warning';
    }
    
    // Validación para el campo de TipoServicio[] (selección múltiple)
    if (empty($_POST['TipoServicio']) || !is_array($_POST['TipoServicio']) || count($_POST['TipoServicio']) == 0) {
        $_SESSION['Mensaje'] .= 'Debes seleccionar al menos un Tipo de Servicio. <br />';
        $_SESSION['Estilo'] = 'warning';
    }


    return $_SESSION['Mensaje'];
}

function Listar_Historia($vConexion) {
    $Listado = [];

    $SQL = "SELECT 
                h.idHistoriaMedica,
                p.idPaciente,
                p.nombre AS nombre_paciente,
                p.apellido AS apellido_paciente,
                p.dni,
                h.enfermedades,
                h.medicamentos,
                h.esparcimiento
            FROM historiamedica h
            INNER JOIN pacientes p ON h.idPaciente = p.idPaciente";

    $rs = mysqli_query($vConexion, $SQL);
    $i = 0;

    while ($data = mysqli_fetch_array($rs)) {
        $idPaciente = $data['idPaciente'];

        // Obtener servicios actualizados desde turnos
        $serviciosTurnos = ObtenerServiciosPorPaciente($vConexion, $idPaciente);
        $serviciosUnicos = [];
        foreach ($serviciosTurnos as $servicio) {
            $denominacion = trim($servicio['denominacion']);
            if (!in_array($denominacion, $serviciosUnicos)) {
                $serviciosUnicos[] = $denominacion;
            }
        }

        // Separar en arrays los demás campos
        $enfermedades = array_filter(array_map('trim', explode(',', $data['enfermedades'])));
        $medicamentos = array_filter(array_map('trim', explode(',', $data['medicamentos'])));
        $esparcimiento = array_filter(array_map('trim', explode(',', $data['esparcimiento'])));

        $Listado[$i] = [
            'ID_HISTORIA' => $data['idHistoriaMedica'],
            'NOMBREPACIENTE' => $data['nombre_paciente'],
            'APELLIDOPACIENTE' => $data['apellido_paciente'],
            'DNI' => $data['dni'],
            'ENFERMEDADES' => $enfermedades,
            'MEDICAMENTOS' => $medicamentos,
            'SERVICIOS' => $serviciosUnicos, // Servicios desde turnos
            'ESPARCIMIENTO' => $esparcimiento
        ];
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
        case 'Medicamentos': 
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

function InsertarHistoria($conexion) {
    $idPaciente   = mysqli_real_escape_string($conexion, $_POST['Paciente']);
    $dni          = mysqli_real_escape_string($conexion, $_POST['DNI']);

    $enfermedades = isset($_POST['Enfermedades']) ? implode(', ', $_POST['Enfermedades']) : '';
    $medicamentos = isset($_POST['Medicamentos']) ? implode(', ', $_POST['Medicamentos']) : '';
    $esparcimiento = isset($_POST['Esparcimiento']) ? implode(', ', $_POST['Esparcimiento']) : '';

    $enfermedades = mysqli_real_escape_string($conexion, $enfermedades);
    $medicamentos = mysqli_real_escape_string($conexion, $medicamentos);
    $esparcimiento = mysqli_real_escape_string($conexion, $esparcimiento);

    $lista = ObtenerServiciosPorPaciente($conexion, $idPaciente);
    $serviciosUnicos = [];
    foreach ($lista as $s) {
        $denominacion = $s['denominacion'];
        if (!in_array($denominacion, $serviciosUnicos)) {
            $serviciosUnicos[] = $denominacion;
        }
    }
    $textoServicios = implode(', ', $serviciosUnicos);
    $textoServicios = mysqli_real_escape_string($conexion, $textoServicios);
    $textoServicios = mysqli_real_escape_string($conexion, $textoServicios);

    $sql = "INSERT INTO historiamedica
            (idPaciente, dni, enfermedades, medicamentos, servicios, esparcimiento)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conexion, $sql);
    if (!$stmt) {
        error_log("Error al preparar InsertarHistoria: " . mysqli_error($conexion));
        return false;
    }
    mysqli_stmt_bind_param(
        $stmt,
        "isssss",
        $idPaciente,
        $dni,
        $enfermedades,
        $medicamentos,
        $textoServicios,
        $esparcimiento
    );
    $ok = mysqli_stmt_execute($stmt);
    if (!$ok) {
        error_log("Error al ejecutar InsertarHistoria: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
    return $ok;
}

function ObtenerServiciosPorPaciente($conexion, $idPaciente) {
    $SQL = "SELECT s.denominacion
            FROM turnos t
            INNER JOIN servicios s ON t.idServicio = s.idServicio
            WHERE t.idPaciente = $idPaciente";

    $rs = mysqli_query($conexion, $SQL);
    $servicios = [];

    while ($data = mysqli_fetch_assoc($rs)) {
        $servicios[] = $data;
    }

    return $servicios;
}

function Datos_Historia($conexion, $idHistoria) {
    $sql = "SELECT * FROM historiaMedica WHERE idHistoriaMedica = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idHistoria);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($resultado);
}

function Modificar_Historia($conexion) {
    if (
        empty($_POST['IdHistoria']) ||
        !isset($_POST['Enfermedades']) ||
        !isset($_POST['Medicamentos']) ||
        !isset($_POST['Esparcimiento'])
    ) {
        return false;
    }

    $idHistoria = $_POST['IdHistoria'];
    $enfermedades = implode(',', $_POST['Enfermedades']);
    $medicamentos = implode(',', $_POST['Medicamentos']);
    $esparcimiento = implode(',', $_POST['Esparcimiento']);

    $sql = "UPDATE historiaMedica
            SET enfermedades = ?, medicamentos = ?, esparcimiento = ?
            WHERE idHistoriaMedica = ?";
    
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $enfermedades, $medicamentos, $esparcimiento, $idHistoria);

    return mysqli_stmt_execute($stmt);
}

?>

