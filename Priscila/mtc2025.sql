-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-07-2025 a las 14:16:24
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `mtc2025`
--
CREATE DATABASE IF NOT EXISTS `mtc2025` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `mtc2025`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrador`
--

DROP TABLE IF EXISTS `administrador`;
CREATE TABLE `administrador` (
  `CI` int(8) NOT NULL,
  `Nombre_Completo` varchar(120) NOT NULL,
  `Mail` varchar(120) NOT NULL,
  `Teléfono` int(9) NOT NULL,
  `Contraseña` int(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `administrador`:
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alerta`
--

DROP TABLE IF EXISTS `alerta`;
CREATE TABLE `alerta` (
  `Numero_Alerta` int(4) NOT NULL,
  `Fecha` date NOT NULL,
  `Hora` date NOT NULL,
  `Foto_de_Rotura` varchar(100) NOT NULL,
  `Foto_de_arreglo` varchar(100) NOT NULL,
  `Tipo` varchar(7) NOT NULL,
  `Descripción` varchar(135) NOT NULL,
  `Estado` tinyint(1) NOT NULL,
  `Numero_deUbi` int(4) DEFAULT NULL,
  `CI_deUsuario` int(8) DEFAULT NULL,
  `Codigo_deCiudad` int(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `alerta`:
--   `CI_deUsuario`
--       `usuario` -> `CI`
--   `Codigo_deCiudad`
--       `ubicacion` -> `Codigo_deCiudad`
--   `Numero_deUbi`
--       `ubicacion` -> `Numero`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciudad`
--

DROP TABLE IF EXISTS `ciudad`;
CREATE TABLE `ciudad` (
  `Codigo` int(4) NOT NULL,
  `Nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `ciudad`:
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `disponibilidad`
--

DROP TABLE IF EXISTS `disponibilidad`;
CREATE TABLE `disponibilidad` (
  `CI_deUsuario` int(8) NOT NULL,
  `Dia` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `disponibilidad`:
--   `CI_deUsuario`
--       `usuario` -> `CI`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entre`
--

DROP TABLE IF EXISTS `entre`;
CREATE TABLE `entre` (
  `Numero_deUbi` int(4) NOT NULL,
  `Codigo_deCiudad` int(4) NOT NULL,
  `Calle_1` varchar(100) NOT NULL,
  `Calle_2` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `entre`:
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gestiona`
--

DROP TABLE IF EXISTS `gestiona`;
CREATE TABLE `gestiona` (
  `CI_deAdministrador` int(8) NOT NULL,
  `Num_deAlerta` int(4) NOT NULL,
  `Fecha` date NOT NULL,
  `Hora` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `gestiona`:
--   `Num_deAlerta`
--       `alerta` -> `Numero_Alerta`
--   `CI_deAdministrador`
--       `administrador` -> `CI`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inspecciona`
--

DROP TABLE IF EXISTS `inspecciona`;
CREATE TABLE `inspecciona` (
  `CI_deInspector` int(8) NOT NULL,
  `Numero_Alerta` int(4) DEFAULT NULL,
  `Fecha_deInspeccion` date NOT NULL,
  `Observacion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `inspecciona`:
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inspector`
--

DROP TABLE IF EXISTS `inspector`;
CREATE TABLE `inspector` (
  `CI` int(8) NOT NULL,
  `Nombre_Completo` varchar(120) NOT NULL,
  `Mail` varchar(120) NOT NULL,
  `Teléfono` int(9) NOT NULL,
  `Contraseña` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `inspector`:
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plan_vereda`
--

DROP TABLE IF EXISTS `plan_vereda`;
CREATE TABLE `plan_vereda` (
  `CI_deUsuario` int(8) NOT NULL,
  `Dirección` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `plan_vereda`:
--   `CI_deUsuario`
--       `usuario` -> `CI`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicacion`
--

DROP TABLE IF EXISTS `ubicacion`;
CREATE TABLE `ubicacion` (
  `Numero` int(4) NOT NULL,
  `Codigo_deCiudad` int(4) NOT NULL,
  `Nombre_Calle` varchar(150) NOT NULL,
  `Numero_dePuerta` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `ubicacion`:
--   `Codigo_deCiudad`
--       `ciudad` -> `Codigo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `CI` int(8) NOT NULL,
  `Nombre_Completo` varchar(120) NOT NULL,
  `Teléfono` int(9) NOT NULL,
  `Email` varchar(120) NOT NULL,
  `Contraseña` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `usuario`:
--

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`CI`);

--
-- Indices de la tabla `alerta`
--
ALTER TABLE `alerta`
  ADD PRIMARY KEY (`Numero_Alerta`),
  ADD KEY `CI_deUsuario` (`CI_deUsuario`),
  ADD KEY `Codigo_deCiudad` (`Codigo_deCiudad`,`Numero_deUbi`);

--
-- Indices de la tabla `ciudad`
--
ALTER TABLE `ciudad`
  ADD PRIMARY KEY (`Codigo`);

--
-- Indices de la tabla `disponibilidad`
--
ALTER TABLE `disponibilidad`
  ADD PRIMARY KEY (`CI_deUsuario`,`Dia`);

--
-- Indices de la tabla `entre`
--
ALTER TABLE `entre`
  ADD PRIMARY KEY (`Numero_deUbi`,`Codigo_deCiudad`);

--
-- Indices de la tabla `gestiona`
--
ALTER TABLE `gestiona`
  ADD PRIMARY KEY (`CI_deAdministrador`),
  ADD KEY `Num_deAlerta` (`Num_deAlerta`);

--
-- Indices de la tabla `inspecciona`
--
ALTER TABLE `inspecciona`
  ADD PRIMARY KEY (`CI_deInspector`);

--
-- Indices de la tabla `inspector`
--
ALTER TABLE `inspector`
  ADD PRIMARY KEY (`CI`);

--
-- Indices de la tabla `plan_vereda`
--
ALTER TABLE `plan_vereda`
  ADD PRIMARY KEY (`CI_deUsuario`);

--
-- Indices de la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  ADD PRIMARY KEY (`Codigo_deCiudad`,`Numero`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`CI`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alerta`
--
ALTER TABLE `alerta`
  ADD CONSTRAINT `alerta_ibfk_1` FOREIGN KEY (`CI_deUsuario`) REFERENCES `usuario` (`CI`),
  ADD CONSTRAINT `alerta_ibfk_2` FOREIGN KEY (`Codigo_deCiudad`,`Numero_deUbi`) REFERENCES `ubicacion` (`Codigo_deCiudad`, `Numero`);

--
-- Filtros para la tabla `disponibilidad`
--
ALTER TABLE `disponibilidad`
  ADD CONSTRAINT `disponibilidad_ibfk_1` FOREIGN KEY (`CI_deUsuario`) REFERENCES `usuario` (`CI`);

--
-- Filtros para la tabla `gestiona`
--
ALTER TABLE `gestiona`
  ADD CONSTRAINT `gestiona_ibfk_1` FOREIGN KEY (`Num_deAlerta`) REFERENCES `alerta` (`Numero_Alerta`),
  ADD CONSTRAINT `gestiona_ibfk_2` FOREIGN KEY (`CI_deAdministrador`) REFERENCES `administrador` (`CI`);

--
-- Filtros para la tabla `plan_vereda`
--
ALTER TABLE `plan_vereda`
  ADD CONSTRAINT `plan_vereda_ibfk_1` FOREIGN KEY (`CI_deUsuario`) REFERENCES `usuario` (`CI`);

--
-- Filtros para la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  ADD CONSTRAINT `ubicacion_ibfk_1` FOREIGN KEY (`Codigo_deCiudad`) REFERENCES `ciudad` (`Codigo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
