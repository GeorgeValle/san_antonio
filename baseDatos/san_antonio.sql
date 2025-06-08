-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-06-2025 a las 05:26:35
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `san_antonio`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_turno`
--

CREATE TABLE `detalle_turno` (
  `idDetalleTurno` int(11) NOT NULL,
  `idTurno` int(11) NOT NULL,
  `idServicio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_turno`
--

INSERT INTO `detalle_turno` (`idDetalleTurno`, `idTurno`, `idServicio`) VALUES
(1, 1, 5),
(2, 1, 6),
(6, 3, 1),
(7, 3, 3),
(8, 4, 2),
(9, 5, 4),
(10, 5, 5),
(11, 6, 6),
(12, 7, 7),
(13, 7, 1),
(14, 8, 2),
(15, 8, 3),
(16, 9, 4),
(17, 10, 5),
(18, 10, 6),
(19, 11, 7),
(20, 12, 1),
(21, 12, 2),
(22, 13, 3),
(23, 14, 4),
(24, 14, 5),
(25, 15, 6),
(26, 16, 7),
(27, 16, 1),
(28, 17, 2),
(29, 18, 3),
(30, 18, 4),
(31, 19, 5),
(32, 20, 6),
(33, 20, 7),
(34, 21, 1),
(35, 22, 2),
(36, 22, 3),
(37, 23, 4),
(38, 24, 5),
(39, 24, 6),
(40, 25, 7),
(41, 26, 1),
(42, 26, 2),
(43, 27, 3),
(44, 28, 4),
(45, 28, 5),
(46, 29, 6),
(47, 30, 7),
(48, 30, 1),
(49, 31, 2),
(50, 32, 3),
(51, 32, 4),
(52, 2, 5),
(53, 2, 7),
(54, 2, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historiamedica`
--

CREATE TABLE `historiamedica` (
  `idHistoriaMedica` int(11) NOT NULL,
  `idPaciente` int(11) NOT NULL,
  `dni` int(10) NOT NULL,
  `enfermedades` varchar(300) NOT NULL,
  `medicamentos` varchar(300) NOT NULL,
  `servicios` varchar(300) NOT NULL,
  `esparcimiento` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historiamedica`
--

INSERT INTO `historiamedica` (`idHistoriaMedica`, `idPaciente`, `dni`, `enfermedades`, `medicamentos`, `servicios`, `esparcimiento`) VALUES
(15, 10, 38410257, 'Dolor de cabeza, Tos, fiebre, prueba, prueba 2, prueba 3, prueba 4, prueba 5', 'Hola, Hol', 'enfermero, medico clinico, nutricionista', 'taller de dibujo, taller de musica, taller de lectura, taller de canto, taller de baile, yoga');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes`
--

CREATE TABLE `pacientes` (
  `idPaciente` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `apellido` varchar(150) NOT NULL,
  `telefono` bigint(11) NOT NULL,
  `dni` bigint(11) NOT NULL,
  `idTipoPaciente` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pacientes`
--

INSERT INTO `pacientes` (`idPaciente`, `nombre`, `apellido`, `telefono`, `dni`, `idTipoPaciente`) VALUES
(2, 'Bruno', 'Carossi', 2147483647, 38103736, 1),
(10, 'Santiago', 'Montenegro', 325478541, 38410257, 1),
(11, 'Jorge', 'Valle', 654788214, 31548752, 1),
(13, 'Ernesto', 'Sabato', 123456789, 17664875, 1),
(18, 'Juan', 'Pérez', 1123456789, 40123456, 1),
(19, 'María', 'González', 1134567890, 40234567, 2),
(20, 'Carlos', 'Rodríguez', 1145678901, 40345678, 3),
(21, 'Ana', 'Fernández', 1156789012, 40456789, 1),
(22, 'Pedro', 'López', 1167890123, 40567890, 2),
(23, 'Sofía', 'Martínez', 1178901234, 40678901, 3),
(24, 'Luis', 'Sánchez', 1189012345, 40789012, 1),
(25, 'Laura', 'Ramírez', 1190123456, 40890123, 2),
(26, 'Diego', 'Torres', 1201234567, 40901234, 3),
(27, 'Elena', 'Jiménez', 1212345678, 41012345, 1),
(28, 'Martín', 'Herrera', 1223456789, 41123456, 2),
(29, 'Cecilia', 'Castro', 1234567890, 41234567, 3),
(30, 'Ricardo', 'Morales', 1245678901, 41345678, 1),
(31, 'Valeria', 'Navarro', 1256789012, 41456789, 2),
(32, 'Fernando', 'Ortiz', 1267890123, 41567890, 3),
(33, 'Gabriela', 'Méndez', 1278901234, 41678901, 1),
(34, 'Esteban', 'Cabrera', 1289012345, 41789012, 2),
(35, 'Paula', 'Suárez', 1290123456, 41890123, 3),
(36, 'Héctor', 'Rojas', 1301234567, 41901234, 1),
(37, 'Camila', 'Silva', 1312345678, 42012345, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `idServicio` int(11) NOT NULL,
  `denominacion` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`idServicio`, `denominacion`) VALUES
(1, 'psiquiatria'),
(2, 'psicologo'),
(3, 'terapista ocupacional'),
(4, 'asistente social'),
(5, 'enfermero'),
(6, 'nutricionista'),
(7, 'medico clinico');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_paciente`
--

CREATE TABLE `tipo_paciente` (
  `idTipoPaciente` int(11) NOT NULL,
  `denominacion` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_paciente`
--

INSERT INTO `tipo_paciente` (`idTipoPaciente`, `denominacion`) VALUES
(1, 'Verde'),
(2, 'Amarillo'),
(3, 'Rojo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turnos`
--

CREATE TABLE `turnos` (
  `idTurno` int(11) NOT NULL,
  `idPaciente` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `horario` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `turnos`
--

INSERT INTO `turnos` (`idTurno`, `idPaciente`, `fecha`, `horario`) VALUES
(2, 10, '2025-06-27', '16:00:00'),
(3, 2, '2024-06-01', '08:30:00'),
(4, 10, '2024-06-01', '09:00:00'),
(5, 11, '2024-06-01', '09:30:00'),
(6, 13, '2024-06-01', '10:00:00'),
(7, 18, '2024-06-01', '10:30:00'),
(8, 19, '2024-06-01', '11:00:00'),
(9, 20, '2024-06-01', '11:30:00'),
(10, 21, '2024-06-01', '16:00:00'),
(11, 22, '2024-06-01', '16:30:00'),
(12, 23, '2024-06-01', '17:00:00'),
(13, 24, '2024-06-02', '08:30:00'),
(14, 25, '2024-06-02', '09:00:00'),
(15, 26, '2024-06-02', '09:30:00'),
(16, 27, '2024-06-02', '10:00:00'),
(17, 28, '2024-06-02', '10:30:00'),
(18, 29, '2024-06-02', '11:00:00'),
(19, 30, '2024-06-02', '11:30:00'),
(20, 31, '2024-06-02', '16:00:00'),
(21, 32, '2024-06-02', '16:30:00'),
(22, 33, '2024-06-02', '17:00:00'),
(23, 34, '2024-06-03', '08:30:00'),
(24, 35, '2024-06-03', '09:00:00'),
(25, 36, '2024-06-03', '09:30:00'),
(26, 37, '2024-06-03', '10:00:00'),
(27, 2, '2024-06-03', '10:30:00'),
(28, 10, '2024-06-03', '11:00:00'),
(29, 11, '2024-06-03', '11:30:00'),
(30, 13, '2024-06-03', '16:00:00'),
(31, 18, '2024-06-03', '16:30:00'),
(32, 19, '2024-06-03', '17:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `idUsuario` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `clave` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`idUsuario`, `nombre`, `clave`) VALUES
(1, 'bruno', 'Bruno2025'),
(2, 'santiago', 'Santi2025'),
(3, 'george', 'George2025');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `detalle_turno`
--
ALTER TABLE `detalle_turno`
  ADD PRIMARY KEY (`idDetalleTurno`);

--
-- Indices de la tabla `historiamedica`
--
ALTER TABLE `historiamedica`
  ADD PRIMARY KEY (`idHistoriaMedica`);

--
-- Indices de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`idPaciente`),
  ADD KEY `fk_paciente_tipo` (`idTipoPaciente`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`idServicio`);

--
-- Indices de la tabla `tipo_paciente`
--
ALTER TABLE `tipo_paciente`
  ADD PRIMARY KEY (`idTipoPaciente`);

--
-- Indices de la tabla `turnos`
--
ALTER TABLE `turnos`
  ADD PRIMARY KEY (`idTurno`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idUsuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `detalle_turno`
--
ALTER TABLE `detalle_turno`
  MODIFY `idDetalleTurno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de la tabla `historiamedica`
--
ALTER TABLE `historiamedica`
  MODIFY `idHistoriaMedica` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `idPaciente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `idServicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tipo_paciente`
--
ALTER TABLE `tipo_paciente`
  MODIFY `idTipoPaciente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `turnos`
--
ALTER TABLE `turnos`
  MODIFY `idTurno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD CONSTRAINT `fk_paciente_tipo` FOREIGN KEY (`idTipoPaciente`) REFERENCES `tipo_paciente` (`idTipoPaciente`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
