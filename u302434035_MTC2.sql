-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 16-11-2025 a las 21:14:06
-- Versión del servidor: 11.8.3-MariaDB-log
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u302434035_MTC2`
--
CREATE DATABASE IF NOT EXISTS `u302434035_MTC2` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `u302434035_MTC2`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrador`
--

CREATE TABLE `administrador` (
  `CI` int(11) NOT NULL,
  `Nombre_Completo` varchar(120) NOT NULL,
  `Mail` varchar(120) NOT NULL,
  `Telefono` int(11) NOT NULL,
  `Contrasena` varchar(255) NOT NULL,
  `Banner` varchar(255) DEFAULT 'linear-gradient(135deg, #3498db, #2ecc71)',
  `FotoPerfil` varchar(255) DEFAULT 'assets/media/perfil.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alerta`
--

CREATE TABLE `alerta` (
  `Numero_Alerta` int(11) NOT NULL,
  `CI_deUsuario` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `Fecha` date NOT NULL,
  `Hora` time NOT NULL,
  `Foto_de_Rotura` varchar(100) NOT NULL DEFAULT 'uploads/',
  `Foto_de_Arreglo` varchar(100) NOT NULL DEFAULT 'assets/media/mtc.png',
  `Nombre_Tipo` varchar(50) NOT NULL,
  `Descripcion` varchar(135) NOT NULL,
  `Nombre_deCalle` varchar(200) DEFAULT NULL,
  `ID_Estado` int(11) NOT NULL,
  `Numero_deUbi` int(11) NOT NULL,
  `Codigo_deCiudad` int(11) NOT NULL,
  `Latitud` decimal(10,8) DEFAULT NULL,
  `Longitud` decimal(11,8) DEFAULT NULL,
  `ID_Barrio` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alerta`
--

INSERT INTO `alerta` (`Numero_Alerta`, `CI_deUsuario`, `id_usuario`, `Fecha`, `Hora`, `Foto_de_Rotura`, `Foto_de_Arreglo`, `Nombre_Tipo`, `Descripcion`, `Nombre_deCalle`, `ID_Estado`, `Numero_deUbi`, `Codigo_deCiudad`, `Latitud`, `Longitud`, `ID_Barrio`) VALUES
(3, 54226995, 27, '2025-11-16', '14:20:53', 'uploads/rotura_691a07f5960aa.jpg', 'assets/media/mtc.png', 'Calle', 'Uruguay nomas!!!!!!', '24,de julio', 1, 2, 1, NULL, NULL, 1);

--
-- Disparadores `alerta`
--
DELIMITER $$
CREATE TRIGGER `alerta_bi` BEFORE INSERT ON `alerta` FOR EACH ROW BEGIN
  IF NEW.id_usuario IS NOT NULL THEN
    SELECT CI INTO @tmpCI FROM usuario WHERE id_usuario = NEW.id_usuario LIMIT 1;
    SET NEW.CI_deUsuario = @tmpCI;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `alerta_bu` BEFORE UPDATE ON `alerta` FOR EACH ROW BEGIN
  IF NEW.id_usuario IS NOT NULL AND NEW.id_usuario <> OLD.id_usuario THEN
    SELECT CI INTO @tmpCI2 FROM usuario WHERE id_usuario = NEW.id_usuario LIMIT 1;
    SET NEW.CI_deUsuario = @tmpCI2;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `barrio`
--

CREATE TABLE `barrio` (
  `ID_Barrio` int(11) NOT NULL,
  `Nombre` varchar(200) NOT NULL,
  `Codigo_deCiudad` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `barrio`
--

INSERT INTO `barrio` (`ID_Barrio`, `Nombre`, `Codigo_deCiudad`) VALUES
(1, 'Artigas', 1),
(2, 'Grito de Asencio', 1),
(3, 'San Isidro Country', 1),
(4, 'Hipódromo', 1),
(5, 'Nuevo Amanecer', 1),
(6, 'Carrasquito (Sector Oeste)', 1),
(7, 'Aparicio Saravia', 1),
(8, 'Treinta y Tres (33 Orientales)', 1),
(9, 'Cerro', 1),
(10, 'Danubio', 1),
(11, 'Rodó (zona sur)', 1),
(12, 'Suroeste / Paso Molino', 1),
(13, 'Zona Hospital', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calles`
--

CREATE TABLE `calles` (
  `id` int(11) NOT NULL,
  `Nombre_Calle` varchar(255) NOT NULL,
  `Codigo_deCiudad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `calles`
--

INSERT INTO `calles` (`id`, `Nombre_Calle`, `Codigo_deCiudad`) VALUES
(1, '18 de Julio', 1),
(2, '19 de Abril', 1),
(3, '1924 Colombes', 1),
(4, '1928 Amsterdam', 1),
(5, '1930 Centenario', 1),
(6, '1950 Maracaná', 1),
(7, '21 de Setiembre', 1),
(8, '28 de Febrero', 1),
(9, '8 de Octubre', 1),
(10, 'Alegría', 1),
(11, 'Alfredo Magliacca', 1),
(12, 'Andrés Cheveste', 1),
(13, 'Ansina', 1),
(14, 'Atanasio Sierra', 1),
(15, 'Avenida Agraciada', 1),
(16, 'Ángela Erba', 1),
(17, 'Basilio Araújo', 1),
(18, 'Bernabé Comes', 1),
(19, 'Bulevar Viera y Benavídez', 1),
(20, 'Calle 1', 1),
(21, 'Calle 2', 1),
(22, 'Calle 3', 1),
(23, 'Calle 4', 1),
(24, 'Calle 5', 1),
(25, 'Calle 6', 1),
(26, 'Camino Cantoni', 1),
(27, 'Camino de los Argentinos', 1),
(28, 'Eusebio Giménez', 1),
(29, 'Facundo Alzola', 1),
(30, 'Felipe Carapé', 1),
(31, 'Florencio Sánchez', 1),
(32, 'Gaspar González', 1),
(33, 'General José Artigas', 1),
(34, 'General Manuel Oribe', 1),
(35, 'Grito de Asencio', 1),
(36, 'Hermanos Spikerman', 1),
(37, 'Hernandarias', 1),
(38, 'Idiarte Borda', 1),
(39, 'Ingeniero Carlos A. Magnone', 1),
(40, 'Isidoro Cano', 1),
(41, 'Ituzaingó', 1),
(42, 'Jacinto Trápani', 1),
(43, 'Jazmín', 1),
(44, 'Joaquín Suárez', 1),
(45, 'José Batlle y Ordóñez', 1),
(46, 'José María Iparraguirre', 1),
(47, 'José Pedro Varela', 1),
(48, 'Juan Antonio Lavalleja', 1),
(49, 'Maestra Glafira Franzia', 1),
(50, 'Maestra Isabel Rubio', 1),
(51, 'Maestra Juana Camino', 1),
(52, 'Maestra Leila Tuya', 1),
(53, 'Maestra Sara Roura', 1),
(54, 'Maestras Florez Novo', 1),
(55, 'Manuel Freire', 1),
(56, 'Manuel Meléndez', 1),
(57, 'Mario H. Segurola', 1),
(58, 'Wilson Ferreira Aldunate', 1),
(59, '18 de Julio', 1),
(60, '19 de Abril', 1),
(61, '1924 Colombes', 1),
(62, '1928 Amsterdam', 1),
(63, '1930 Centenario', 1),
(64, '1950 Maracaná', 1),
(65, '21 de Setiembre', 1),
(66, '28 de Febrero', 1),
(67, '8 de Octubre', 1),
(68, 'Alegría', 1),
(69, 'Alfredo Magliacca', 1),
(70, 'Andrés Cheveste', 1),
(71, 'Ansina', 1),
(72, 'Atanasio Sierra', 1),
(73, 'Avenida Agraciada', 1),
(74, 'Ángela Erba', 1),
(75, 'Basilio Araújo', 1),
(76, 'Bernabé Comes', 1),
(77, 'Bulevar Viera y Benavídez', 1),
(78, 'Calle 1', 1),
(79, 'Calle 2', 1),
(80, 'Calle 3', 1),
(81, 'Calle 4', 1),
(82, 'Calle 5', 1),
(83, 'Calle 6', 1),
(84, 'Camino Cantoni', 1),
(85, 'Camino de los Argentinos', 1),
(86, 'Eusebio Giménez', 1),
(87, 'Facundo Alzola', 1),
(88, 'Felipe Carapé', 1),
(89, 'Florencio Sánchez', 1),
(90, 'Gaspar González', 1),
(91, 'General José Artigas', 1),
(92, 'General Manuel Oribe', 1),
(93, 'Grito de Asencio', 1),
(94, 'Hermanos Spikerman', 1),
(95, 'Hernandarias', 1),
(96, 'Idiarte Borda', 1),
(97, 'Ingeniero Carlos A. Magnone', 1),
(98, 'Isidoro Cano', 1),
(99, 'Ituzaingó', 1),
(100, 'Jacinto Trápani', 1),
(101, 'Jazmín', 1),
(102, 'Joaquín Suárez', 1),
(103, 'José Batlle y Ordóñez', 1),
(104, 'José María Iparraguirre', 1),
(105, 'José Pedro Varela', 1),
(106, 'Juan Antonio Lavalleja', 1),
(107, 'Maestra Glafira Franzia', 1),
(108, 'Maestra Isabel Rubio', 1),
(109, 'Maestra Juana Camino', 1),
(110, 'Maestra Leila Tuya', 1),
(111, 'Maestra Sara Roura', 1),
(112, 'Maestras Florez Novo', 1),
(113, 'Manuel Freire', 1),
(114, 'Manuel Meléndez', 1),
(115, 'Mario H. Segurola', 1),
(116, 'Wilson Ferreira Aldunate', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciudad`
--

CREATE TABLE `ciudad` (
  `Codigo` int(11) NOT NULL,
  `Nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ciudad`
--

INSERT INTO `ciudad` (`Codigo`, `Nombre`) VALUES
(1, 'Mercedes'),
(2, 'Dolores'),
(3, 'Cardona'),
(4, 'Palmitas'),
(5, 'José Enrique Rodó'),
(6, 'Chacras de Dolores'),
(7, 'Villa Soriano'),
(8, 'Santa Catalina'),
(9, 'Egaña'),
(10, 'Agraciada'),
(11, 'Risso'),
(12, 'Sacachispas'),
(13, 'Cañada Nieto'),
(14, 'Palmar'),
(15, 'Palo Solo'),
(16, 'Castillos'),
(17, 'Perseverano'),
(18, 'La Loma'),
(19, 'Lares'),
(20, 'La Concordia'),
(21, 'El Tala'),
(22, 'Colonia Concordia'),
(23, 'Cuchilla del Perdido');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `disponibilidad`
--

CREATE TABLE `disponibilidad` (
  `CI_deUsuario` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `Dia` date NOT NULL,
  `id_alerta` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado`
--

CREATE TABLE `estado` (
  `ID_Estado` int(11) NOT NULL,
  `Nombre_Estado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado`
--

INSERT INTO `estado` (`ID_Estado`, `Nombre_Estado`) VALUES
(1, 'Pendiente'),
(2, 'En Proceso'),
(3, 'Resuelta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gestiona`
--

CREATE TABLE `gestiona` (
  `CI_deAdministrador` int(11) NOT NULL,
  `Num_deAlerta` int(11) NOT NULL,
  `Fecha` date NOT NULL,
  `Hora` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plan_vereda`
--

CREATE TABLE `plan_vereda` (
  `id_usuario` int(11) NOT NULL,
  `CI_pv` int(11) DEFAULT NULL,
  `Direccion` varchar(100) NOT NULL,
  `ID_Barrio` int(11) DEFAULT NULL,
  `Banner` varchar(255) DEFAULT 'linear-gradient(135deg, #3498db, #2ecc71)',
  `FotoPerfil` varchar(255) DEFAULT 'assets/media/perfil.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `plan_vereda`
--
DELIMITER $$
CREATE TRIGGER `planvereda_bi` BEFORE INSERT ON `plan_vereda` FOR EACH ROW BEGIN
  IF NEW.id_usuario IS NOT NULL THEN
    SELECT CI INTO @tmpCI3 FROM usuario WHERE id_usuario = NEW.id_usuario LIMIT 1;
    SET NEW.CI_pv = @tmpCI3;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `planvereda_bu` BEFORE UPDATE ON `plan_vereda` FOR EACH ROW BEGIN
  IF NEW.id_usuario IS NOT NULL AND NEW.id_usuario <> OLD.id_usuario THEN
    SELECT CI INTO @tmpCI4 FROM usuario WHERE id_usuario = NEW.id_usuario LIMIT 1;
    SET NEW.CI_pv = @tmpCI4;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propietario`
--

CREATE TABLE `propietario` (
  `id_propietario` int(11) NOT NULL,
  `CI` int(11) DEFAULT NULL,
  `Nombre_Completo` varchar(120) NOT NULL,
  `Telefono` int(11) DEFAULT NULL,
  `Mail` varchar(120) DEFAULT NULL,
  `Contrasena` varchar(255) NOT NULL,
  `Banner` varchar(255) DEFAULT 'linear-gradient(135deg, #3498db, #2ecc71)',
  `FotoPerfil` varchar(255) DEFAULT 'assets/media/perfil.png',
  `Codigo_deCiudad` int(11) DEFAULT NULL,
  `ID_Barrio` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_alerta`
--

CREATE TABLE `tipo_alerta` (
  `ID_Tipo` int(11) NOT NULL,
  `Nombre_Tipo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_alerta`
--

INSERT INTO `tipo_alerta` (`ID_Tipo`, `Nombre_Tipo`) VALUES
(0, 'Calle');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicacion`
--

CREATE TABLE `ubicacion` (
  `Numero` int(11) NOT NULL,
  `id_barrio` int(11) DEFAULT NULL,
  `Codigo_deCiudad` int(11) NOT NULL,
  `id_Calle` int(11) NOT NULL,
  `Numero_dePuerta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ubicacion`
--

INSERT INTO `ubicacion` (`Numero`, `id_barrio`, `Codigo_deCiudad`, `id_Calle`, `Numero_dePuerta`) VALUES
(2, 1, 1, 24, 1200);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `CI` int(11) DEFAULT NULL,
  `Nombre_Completo` varchar(120) NOT NULL,
  `Telefono` int(11) NOT NULL,
  `Mail` varchar(120) NOT NULL,
  `Contraseña` varchar(255) NOT NULL,
  `Banner` varchar(255) DEFAULT 'linear-gradient(135deg, #3498db, #2ecc71)',
  `FotoPerfil` varchar(255) DEFAULT 'assets/media/perfil.png',
  `Tipo` enum('usuario','voluntario','propietario','admin') NOT NULL DEFAULT 'usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `CI`, `Nombre_Completo`, `Telefono`, `Mail`, `Contraseña`, `Banner`, `FotoPerfil`, `Tipo`) VALUES
(1, NULL, 'fa', 98776432, 'f@gmail.com', '$2y$10$VcdQxydClvQUa5qSHr9MVe44A2kOV.FbrUCI6qu05.rTUV4NBpzsS', 'linear-gradient(135deg, #3498db, #2ecc71)', 'assets/media/perfil.png', 'usuario'),
(2, NULL, 'gf', 91235333, 'Fake@gmail.com', '$2y$10$UOzt8riKF65ZWfdGWpJhku7LZZtY4c/RQll6L8IfWXGR1PltnuT4.', 'linear-gradient(135deg, #3498db, #2ecc71)', 'assets/media/perfil.png', 'usuario'),
(3, 99999999, 'Usuario Eliminado', 0, 'deleted@example.com', '', 'linear-gradient(135deg, #3498db, #2ecc71)', 'assets/media/perfil.png', 'usuario'),
(4, NULL, 'gg', 98777653, 'g@gmail.com', '$2y$10$JdfSATcA6vTJOcknlM1bEeyMBsOKmQNmhfL3ctmTN5BfByhexztmi', NULL, NULL, 'usuario'),
(16, 12345678, 'fabiana cano', 98654333, '12345678@example.com', '', 'linear-gradient(135deg, #3498db, #2ecc71)', 'assets/media/perfil.png', 'usuario'),
(27, 54226995, 'fabianacano', 98666951, '54226995@example.com', '', 'linear-gradient(135deg, #3498db, #2ecc71)', 'assets/media/perfil.png', 'usuario'),
(28, 23533060, 'Priscila Jamen Filippa', 98511319, 'jamenpris@gmail.com', '$2y$10$wEGTlJBv3mmtV3XXpBn4v.VPq6wOQSWT.BnYzQdDbRf6arvQgPYUW', 'linear-gradient(135deg, #3498db, #2ecc71)', 'assets/media/perfil.png', 'usuario');

--
-- Disparadores `usuario`
--
DELIMITER $$
CREATE TRIGGER `usuario_au` AFTER UPDATE ON `usuario` FOR EACH ROW BEGIN
  IF NEW.CI IS NOT NULL AND NEW.CI <> OLD.CI THEN
    UPDATE alerta SET CI_deUsuario = NEW.CI WHERE id_usuario = NEW.id_usuario;
    UPDATE plan_vereda SET CI_pv = NEW.CI WHERE id_usuario = NEW.id_usuario;
  END IF;
END
$$
DELIMITER ;

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
  ADD KEY `ID_Estado` (`ID_Estado`),
  ADD KEY `Codigo_deCiudad` (`Codigo_deCiudad`,`Numero_deUbi`),
  ADD KEY `idx_alerta_id_usuario` (`id_usuario`),
  ADD KEY `idx_alerta_id_barrio` (`ID_Barrio`),
  ADD KEY `idx_alerta_id_barrios` (`ID_Barrio`),
  ADD KEY `idx_alerta_id_usuarios` (`id_usuario`),
  ADD KEY `fk_alerta_nombre_tipo` (`Nombre_Tipo`);

--
-- Indices de la tabla `barrio`
--
ALTER TABLE `barrio`
  ADD PRIMARY KEY (`ID_Barrio`);

--
-- Indices de la tabla `calles`
--
ALTER TABLE `calles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_calle_ciudad` (`id`,`Codigo_deCiudad`),
  ADD KEY `Codigo_deCiudad` (`Codigo_deCiudad`);

--
-- Indices de la tabla `ciudad`
--
ALTER TABLE `ciudad`
  ADD PRIMARY KEY (`Codigo`);

--
-- Indices de la tabla `disponibilidad`
--
ALTER TABLE `disponibilidad`
  ADD PRIMARY KEY (`CI_deUsuario`,`Dia`),
  ADD KEY `idx_disp_id_usuario` (`id_usuario`),
  ADD KEY `fk_disponibilidad_alerta` (`id_alerta`);

--
-- Indices de la tabla `estado`
--
ALTER TABLE `estado`
  ADD PRIMARY KEY (`ID_Estado`);

--
-- Indices de la tabla `gestiona`
--
ALTER TABLE `gestiona`
  ADD PRIMARY KEY (`CI_deAdministrador`,`Num_deAlerta`),
  ADD KEY `Num_deAlerta` (`Num_deAlerta`);

--
-- Indices de la tabla `plan_vereda`
--
ALTER TABLE `plan_vereda`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `CI_pv` (`CI_pv`),
  ADD KEY `idx_plan_id_barrios` (`ID_Barrio`);

--
-- Indices de la tabla `propietario`
--
ALTER TABLE `propietario`
  ADD PRIMARY KEY (`id_propietario`),
  ADD UNIQUE KEY `uk_propietario_CI` (`CI`),
  ADD UNIQUE KEY `uk_propietario_mail` (`Mail`),
  ADD KEY `idx_prop_ciudad` (`Codigo_deCiudad`),
  ADD KEY `idx_prop_barrio` (`ID_Barrio`),
  ADD KEY `idx_propietario_id_usuario` (`id_propietario`),
  ADD KEY `idx_prop_id_usuario` (`id_propietario`);

--
-- Indices de la tabla `tipo_alerta`
--
ALTER TABLE `tipo_alerta`
  ADD PRIMARY KEY (`ID_Tipo`),
  ADD UNIQUE KEY `Nombre_Tipo` (`Nombre_Tipo`),
  ADD UNIQUE KEY `Nombre_Tipo_2` (`Nombre_Tipo`);

--
-- Indices de la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  ADD PRIMARY KEY (`Codigo_deCiudad`,`Numero`),
  ADD UNIQUE KEY `Nombre_Calle` (`id_Calle`),
  ADD UNIQUE KEY `id_Calle` (`id_Calle`),
  ADD KEY `idx_ubic_id_barrios` (`id_barrio`),
  ADD KEY `fk_ubicacion_calle_ciudad` (`id_Calle`,`Codigo_deCiudad`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `idx_usuario_CI` (`CI`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alerta`
--
ALTER TABLE `alerta`
  MODIFY `Numero_Alerta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `calles`
--
ALTER TABLE `calles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT de la tabla `plan_vereda`
--
ALTER TABLE `plan_vereda`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `propietario`
--
ALTER TABLE `propietario`
  MODIFY `id_propietario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alerta`
--
ALTER TABLE `alerta`
  ADD CONSTRAINT `alerta_ibfk_4` FOREIGN KEY (`Codigo_deCiudad`,`Numero_deUbi`) REFERENCES `ubicacion` (`Codigo_deCiudad`, `Numero`),
  ADD CONSTRAINT `fk_alerta_barrio` FOREIGN KEY (`ID_Barrio`) REFERENCES `barrio` (`ID_Barrio`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_alerta_ci_usuario` FOREIGN KEY (`CI_deUsuario`) REFERENCES `usuario` (`CI`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_alerta_nombre_tipo` FOREIGN KEY (`Nombre_Tipo`) REFERENCES `tipo_alerta` (`Nombre_Tipo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_alerta_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_alerta_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `disponibilidad`
--
ALTER TABLE `disponibilidad`
  ADD CONSTRAINT `disponibilidad_ibfk_1` FOREIGN KEY (`CI_deUsuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `fk_disp_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_disponibilidad_alerta` FOREIGN KEY (`id_alerta`) REFERENCES `alerta` (`Numero_Alerta`) ON DELETE SET NULL ON UPDATE CASCADE;

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
  ADD CONSTRAINT `fk_plan_barrio` FOREIGN KEY (`ID_Barrio`) REFERENCES `barrio` (`ID_Barrio`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `plan_vereda_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `propietario`
--
ALTER TABLE `propietario`
  ADD CONSTRAINT `fk_propietario` FOREIGN KEY (`id_propietario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_propietario_barrio` FOREIGN KEY (`ID_Barrio`) REFERENCES `barrio` (`ID_Barrio`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_propietario_ciudad` FOREIGN KEY (`Codigo_deCiudad`) REFERENCES `ciudad` (`Codigo`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  ADD CONSTRAINT `fk_ubicacion_barrio` FOREIGN KEY (`id_barrio`) REFERENCES `barrio` (`ID_Barrio`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ubicacion_calle` FOREIGN KEY (`id_Calle`) REFERENCES `calles` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ubicacion_calle_ciudad` FOREIGN KEY (`id_Calle`,`Codigo_deCiudad`) REFERENCES `calles` (`id`, `Codigo_deCiudad`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ubicacion_ibfk_1` FOREIGN KEY (`Codigo_deCiudad`) REFERENCES `ciudad` (`Codigo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
