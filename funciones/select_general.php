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

function Validar_Paciente($vConexion) {
    $_SESSION['Mensaje'] = '';
    
    // Validación del Nombre
    $nombre = trim($_POST['Nombre']);
    if (strlen($nombre) < 3) {
        $_SESSION['Mensaje'] .= 'Debes ingresar un nombre con al menos 3 caracteres. <br />';
    } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/', $nombre)) {
        $_SESSION['Mensaje'] .= 'El nombre solo puede contener letras y espacios. <br />';
    }
    
    // Validación del Apellido
    $apellido = trim($_POST['Apellido']);
    if (strlen($apellido) < 3) {
        $_SESSION['Mensaje'] .= 'Debes ingresar un apellido con al menos 3 caracteres. <br />';
    } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/', $apellido)) {
        $_SESSION['Mensaje'] .= 'El apellido solo puede contener letras y espacios. <br />';
    }
    
    // Validación del Teléfono
    if (strlen($_POST['Telefono']) < 3) {
        $_SESSION['Mensaje'] .= 'Debes ingresar un teléfono con al menos 3 caracteres. <br />';
    }
    
    // Validación del DNI
    if (strlen($_POST['DNI']) < 8) {
        $_SESSION['Mensaje'] .= 'Debes ingresar un DNI con al menos 8 caracteres. <br />';
    }
    
    // Validación del Tipo de Paciente
    if ($_POST['idTipoPaciente'] == 'Seleccione un tipo') {
        $_SESSION['Mensaje'] .= 'Debes seleccionar un tipo de paciente. <br />';
    }
    
    // Validación de DNI único
    $dni = mysqli_real_escape_string($vConexion, $_POST['DNI']);
    $SQL_Check = "SELECT idPaciente FROM pacientes WHERE dni = '$dni' LIMIT 1";
    $resultado = mysqli_query($vConexion, $SQL_Check);
    
    if (mysqli_num_rows($resultado) > 0) {
        $_SESSION['Mensaje'] .= 'Ya existe un paciente registrado con este DNI. <br />';
    }

    // Limpieza de todos los campos
    foreach($_POST as $Id => $Valor) {
        $_POST[$Id] = trim($Valor);
        $_POST[$Id] = strip_tags($Valor);
    }

    return $_SESSION['Mensaje'];
}

function Validar_Paciente_Modificar(){
    $_SESSION['Mensaje']='';

    $nombre = trim($_POST['Nombre']);
    if (strlen($nombre) < 3) {
        $_SESSION['Mensaje'] .= 'Debes ingresar un nombre con al menos 3 caracteres. <br />';
    } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/', $nombre)) {
        $_SESSION['Mensaje'] .= 'El nombre solo puede contener letras y espacios. <br />';
    }
    
    $apellido = trim($_POST['Apellido']);
    if (strlen($apellido) < 3) {
        $_SESSION['Mensaje'] .= 'Debes ingresar un apellido con al menos 3 caracteres. <br />';
    } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/', $apellido)) {
        $_SESSION['Mensaje'] .= 'El apellido solo puede contener letras y espacios. <br />';
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

function Listar_Pacientes_Parametro($conexion, $criterio, $parametro) {

    if ($criterio === 'nombre') {
        $sql = "SELECT p.idPaciente AS ID_PACIENTE, 
                       p.nombre AS NOMBRE, 
                       p.apellido AS APELLIDO,
                       p.telefono AS TELEFONO,
                       p.dni AS DNI,
                       p.idTipoPaciente AS ID_TIPO_PACIENTE,
                       tp.denominacion AS TIPO_PACIENTE
                FROM pacientes p
                LEFT JOIN tipo_paciente tp ON p.idTipoPaciente = tp.idTipoPaciente
                WHERE p.nombre LIKE ? 
                   OR p.apellido LIKE ? 
                   OR CONCAT(p.nombre, ' ', p.apellido) LIKE ?
                ORDER BY p.idPaciente DESC";
        $parametro_like = "%$parametro%";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $parametro_like, $parametro_like, $parametro_like);
    } else {
        $sql = "SELECT p.idPaciente AS ID_PACIENTE, 
                       p.nombre AS NOMBRE, 
                       p.apellido AS APELLIDO,
                       p.telefono AS TELEFONO,
                       p.dni AS DNI,
                       p.idTipoPaciente AS ID_TIPO_PACIENTE,
                       tp.denominacion AS TIPO_PACIENTE
                FROM pacientes p
                LEFT JOIN tipo_paciente tp ON p.idTipoPaciente = tp.idTipoPaciente
                WHERE p.$criterio LIKE ?
                ORDER BY p.idPaciente DESC";
        $parametro_like = "%$parametro%";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "s", $parametro_like);
    }

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
    // Iniciar transacción
    mysqli_begin_transaction($vConexion);
    
    try {
        // 1. Insertar datos básicos del turno
        $idPaciente = mysqli_real_escape_string($vConexion, $_POST['Paciente']);
        $fecha = mysqli_real_escape_string($vConexion, $_POST['Fecha']);
        $horario = mysqli_real_escape_string($vConexion, $_POST['Horario']);
        
        $SQL_Insert = "INSERT INTO turnos (idPaciente, fecha, horario) 
                      VALUES ('$idPaciente', '$fecha', '$horario')";
        
        if (!mysqli_query($vConexion, $SQL_Insert)) {
            throw new Exception("Error al insertar turno: " . mysqli_error($vConexion));
        }
        
        // Obtener ID del turno insertado
        $idTurno = mysqli_insert_id($vConexion);
        
        // 2. Insertar servicios asociados
        foreach ($_POST['TipoServicio'] as $idServicio) {
            $idServicio = mysqli_real_escape_string($vConexion, $idServicio);
            
            $SQL_Detalle = "INSERT INTO detalle_turno (idTurno, idServicio) 
                           VALUES ('$idTurno', '$idServicio')";
            
            if (!mysqli_query($vConexion, $SQL_Detalle)) {
                throw new Exception("Error al insertar detalle de turno: " . mysqli_error($vConexion));
            }
        }
        
        // Confirmar transacción
        mysqli_commit($vConexion);
        return true;
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        mysqli_rollback($vConexion);
        error_log($e->getMessage());
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

function Datos_Turno($vConexion, $vIdTurno) {
    $DatosTurnoActual = array();
    
    // Datos básicos del turno
    $SQL = "SELECT * FROM turnos WHERE idTurno = '$vIdTurno' LIMIT 1";
    $rs = mysqli_query($vConexion, $SQL); 
    
    if ($data = mysqli_fetch_assoc($rs)) {
        $DatosTurnoActual['ID_TURNO'] = $data['idTurno'];
        $DatosTurnoActual['FECHA'] = $data['fecha'];
        
        // Normalizar el formato del horario a HH:MM
        $horario = $data['horario'];
        if (strlen($horario) == 5) { // Ya está en formato HH:MM
            $DatosTurnoActual['HORARIO'] = $horario;
        } else {
            // Convertir de H:MM:SS a HH:MM
            $timeParts = explode(':', $horario);
            $DatosTurnoActual['HORARIO'] = sprintf("%02d:%02d", $timeParts[0], $timeParts[1]);
        }
        
        $DatosTurnoActual['ID_PACIENTE'] = $data['idPaciente'];
        
        // Obtener servicios asociados al turno
        $SQL_Servicios = "SELECT idServicio FROM detalle_turno WHERE idTurno = '$vIdTurno'";
        $rs_servicios = mysqli_query($vConexion, $SQL_Servicios);
        
        $servicios = array();
        while ($servicio = mysqli_fetch_assoc($rs_servicios)) {
            $servicios[] = $servicio['idServicio'];
        }
        
        $DatosTurnoActual['SERVICIOS'] = $servicios;
    }
    
    return $DatosTurnoActual;
}

function Modificar_Turno($vConexion, $datos) {
    // Iniciar transacción
    mysqli_begin_transaction($vConexion);
    
    try {
        // 1. Actualizar datos básicos del turno
        $idTurno = mysqli_real_escape_string($vConexion, $datos['IdTurno']);
        $fecha = mysqli_real_escape_string($vConexion, $datos['Fecha']);
        $horario = mysqli_real_escape_string($vConexion, $datos['Horario']);
        $idPaciente = mysqli_real_escape_string($vConexion, $datos['Paciente']);
        
        $SQL_Update = "UPDATE turnos SET 
                      fecha = '$fecha',
                      horario = '$horario',
                      idPaciente = '$idPaciente'
                      WHERE idTurno = '$idTurno'";
        
        if (!mysqli_query($vConexion, $SQL_Update)) {
            throw new Exception("Error al actualizar turno: " . mysqli_error($vConexion));
        }
        
        // 2. Eliminar servicios anteriores
        $SQL_Delete = "DELETE FROM detalle_turno WHERE idTurno = '$idTurno'";
        if (!mysqli_query($vConexion, $SQL_Delete)) {
            throw new Exception("Error al eliminar servicios anteriores: " . mysqli_error($vConexion));
        }
        
        // 3. Insertar nuevos servicios seleccionados
        if (!empty($datos['TipoServicio']) && is_array($datos['TipoServicio'])) {
            foreach ($datos['TipoServicio'] as $idServicio) {
                $idServicio = mysqli_real_escape_string($vConexion, $idServicio);
                $SQL_Insert = "INSERT INTO detalle_turno (idTurno, idServicio) VALUES ('$idTurno', '$idServicio')";
                
                if (!mysqli_query($vConexion, $SQL_Insert)) {
                    throw new Exception("Error al insertar servicio: " . mysqli_error($vConexion));
                }
            }
        }
        
        // Confirmar transacción
        mysqli_commit($vConexion);
        return true;
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        mysqli_rollback($vConexion);
        error_log($e->getMessage());
        return false;
    }
}

function Validar_Turno() {
    $mensaje = '';
    
    // Validaciones básicas
    if (empty($_POST['Fecha']) || empty($_POST['Horario']) || empty($_POST['Paciente']) || 
        empty($_POST['TipoServicio']) || !is_array($_POST['TipoServicio'])) {
        $mensaje = 'Todos los campos son obligatorios y debe seleccionar al menos un servicio.';
        $_SESSION['Estilo'] = 'warning';
        return $mensaje;
    }
    
    // Validar servicios duplicados
    require_once '../funciones/conexion.php';
    $conexion = ConexionBD();
    
    $fecha = $_POST['Fecha'];
    $horario = $_POST['Horario'];
    $servicios = $_POST['TipoServicio'];
    
    // Array para almacenar los nombres de los servicios duplicados
    $serviciosDuplicados = array();
    
    foreach ($servicios as $idServicio) {
        $idServicio = mysqli_real_escape_string($conexion, $idServicio);
        
        $sql = "SELECT COUNT(*) as total 
                FROM detalle_turno dt
                JOIN turnos t ON dt.idTurno = t.idTurno
                WHERE dt.idServicio = '$idServicio' 
                AND t.fecha = '$fecha' 
                AND t.horario = '$horario'";
        
        $resultado = mysqli_query($conexion, $sql);
        $fila = mysqli_fetch_assoc($resultado);
        
        if ($fila['total'] > 0) {
            // Obtener nombre del servicio
            $sqlNombre = "SELECT denominacion FROM servicios WHERE idServicio = '$idServicio'";
            $resultNombre = mysqli_query($conexion, $sqlNombre);
            $servicio = mysqli_fetch_assoc($resultNombre);
            
            // Agregar el nombre del servicio duplicado al array
            $serviciosDuplicados[] = $servicio['denominacion'];
        }
    }
    
    // Si hay servicios duplicados, construir el mensaje
    if (!empty($serviciosDuplicados)) {
        $mensaje = "Los siguientes servicios ya están asignados a otro turno en la misma fecha y hora: ";
        $mensaje .= implode(", ", $serviciosDuplicados);
        $_SESSION['Estilo'] = 'warning';
    }
    
    return $mensaje;
}

function Validar_Turno_modificar($vconexion, $vidTurnoActual) {
    $mensaje = '';
    
    // Validaciones básicas
    if (empty($_POST['Fecha']) || empty($_POST['Horario']) || empty($_POST['Paciente']) || 
        empty($_POST['TipoServicio']) || !is_array($_POST['TipoServicio'])) {
        $mensaje = 'Todos los campos son obligatorios y debe seleccionar al menos un servicio.';
        $_SESSION['Estilo'] = 'warning';
        return $mensaje;
    }
    
    $fecha = $_POST['Fecha'];
    $horario = $_POST['Horario'];
    $servicios = $_POST['TipoServicio'];
    
    // Array para almacenar los nombres de los servicios duplicados
    $serviciosDuplicados = array();
    
    foreach ($servicios as $idServicio) {
        $idServicio = mysqli_real_escape_string($vconexion, $idServicio);
        
        $sql = "SELECT COUNT(*) as total 
                FROM detalle_turno dt
                JOIN turnos t ON dt.idTurno = t.idTurno
                WHERE dt.idServicio = '$idServicio' 
                AND t.fecha = '$fecha' 
                AND t.horario = '$horario'";
        
        // Excluir el turno actual si se está modificando
        if ($vidTurnoActual) {
            $sql .= " AND t.idTurno != '$vidTurnoActual'";
        }
        
        $resultado = mysqli_query($vconexion, $sql);
        $fila = mysqli_fetch_assoc($resultado);
        
        if ($fila['total'] > 0) {
            $sqlNombre = "SELECT denominacion FROM servicios WHERE idServicio = '$idServicio'";
            $resultNombre = mysqli_query($vconexion, $sqlNombre);
            $servicio = mysqli_fetch_assoc($resultNombre);
            $serviciosDuplicados[] = $servicio['denominacion'];
        }
    }
    
    if (!empty($serviciosDuplicados)) {
        $mensaje = "Los siguientes servicios ya están asignados a otro turno en la misma fecha y hora: ";
        $mensaje .= implode(", ", $serviciosDuplicados);
        $_SESSION['Estilo'] = 'warning';
    }
    
    return $mensaje;
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
        $serviciosTurnos = ObtenerServiciosPorPaciente1($vConexion, $idPaciente);
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
            INNER JOIN detalle_turno dt ON t.idTurno = dt.idTurno
            INNER JOIN servicios s ON dt.idServicio = s.idServicio
            WHERE t.idPaciente = $idPaciente
            GROUP BY s.denominacion"; 

    $rs = mysqli_query($conexion, $SQL);
    $servicios = [];

    while ($data = mysqli_fetch_assoc($rs)) {
        $servicios[] = $data;
    }

    return $servicios;
}

function ObtenerServiciosPorPaciente1($conexion, $idPaciente) {
    $SQL = "SELECT s.denominacion
            FROM turnos t
            INNER JOIN detalle_turno dt ON t.idTurno = dt.idTurno
            INNER JOIN servicios s ON dt.idServicio = s.idServicio
            WHERE t.idPaciente = $idPaciente
            GROUP BY s.denominacion"; 

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

