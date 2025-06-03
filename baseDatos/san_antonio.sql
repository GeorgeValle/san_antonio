-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-06-2025 a las 02:32:29
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
(3, 2, 5),
(4, 2, 7),
(5, 2, 6);

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
(17, 'dsasda', 'sdsada', 35135132131, 2147483647, 2);

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
(1, 17, '2025-06-19', '08:30:00'),
(2, 10, '2025-06-27', '16:00:00');

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
(1, 'bruno', '12345'),
(2, 'santiago', '12345'),
(3, 'george', '12345');

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
  MODIFY `idDetalleTurno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `historiamedica`
--
ALTER TABLE `historiamedica`
  MODIFY `idHistoriaMedica` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `idPaciente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
  MODIFY `idTurno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
