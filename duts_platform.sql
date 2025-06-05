-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-06-2025 a las 06:03:57
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
-- Base de datos: `duts_platform`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_duts`
--

CREATE TABLE `cuentas_duts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `saldo_actual` decimal(12,2) DEFAULT 0.00,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuentas_duts`
--

INSERT INTO `cuentas_duts` (`id`, `user_id`, `saldo_actual`, `fecha_creacion`) VALUES
(1, 1, 0.00, '2025-06-05 02:46:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadisticas_duts`
--

CREATE TABLE `estadisticas_duts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `duts_ganados` decimal(10,2) DEFAULT 0.00,
  `duts_gastados` decimal(10,2) DEFAULT 0.00,
  `saldo_final` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `lugar` varchar(100) DEFAULT NULL,
  `costo_duts` decimal(8,2) DEFAULT 0.00,
  `cupo_maximo` int(11) DEFAULT 0,
  `tipo_evento` enum('UTSmart','CIINATIC','Grados UTS','Expobienestar','Otro') NOT NULL,
  `organizador_id` int(11) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id`, `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `lugar`, `costo_duts`, `cupo_maximo`, `tipo_evento`, `organizador_id`, `activo`, `fecha_creacion`) VALUES
(1, 'CIINATIC 2025', 'Congreso de Innovación', '2025-06-10 09:00:00', '2025-06-10 18:00:00', 'Auditorio UTS', 50.00, 100, 'CIINATIC', 1, 1, '2025-06-05 03:38:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones_eventos`
--

CREATE TABLE `inscripciones_eventos` (
  `id` int(11) NOT NULL,
  `evento_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fecha_inscripcion` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('inscrito','asistio','no_asistio','cancelado') DEFAULT 'inscrito'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inscripciones_eventos`
--

INSERT INTO `inscripciones_eventos` (`id`, `evento_id`, `user_id`, `fecha_inscripcion`, `estado`) VALUES
(1, 1, 1, '2025-06-05 03:39:11', 'inscrito');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transacciones_duts`
--

CREATE TABLE `transacciones_duts` (
  `id` int(11) NOT NULL,
  `id_origen` int(11) NOT NULL,
  `id_destino` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `concepto` varchar(200) DEFAULT NULL,
  `fecha_transaccion` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo_transaccion` enum('transferencia','pago_evento','beca','premio') NOT NULL,
  `estado` enum('completada','pendiente','cancelada') DEFAULT 'completada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `ciudad` varchar(50) NOT NULL,
  `pais` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `lista_intereses` text DEFAULT NULL,
  `programa` varchar(100) NOT NULL,
  `semestre` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `tipo_usuario` enum('estudiante','docente','administrativo') NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `nombres`, `apellidos`, `email`, `ciudad`, `pais`, `descripcion`, `lista_intereses`, `programa`, `semestre`, `username`, `password`, `tipo_usuario`, `fecha_registro`, `activo`) VALUES
(1, 'Juan', 'Pérez', 'juan@example.com', 'Bogotá', 'Colombia', 'Estudiante de ingeniería', 'Finanzas, Tecnología', 'Ingeniería de Sistemas', 5, 'juan123', '$2y$10$nC65aNencrl2apCAoNDqXeTpFm1UWhEdirShsTAd2meyUc71nUoae', 'estudiante', '2025-06-05 02:46:19', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cuentas_duts`
--
ALTER TABLE `cuentas_duts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `estadisticas_duts`
--
ALTER TABLE `estadisticas_duts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_date` (`user_id`,`fecha`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `organizador_id` (`organizador_id`);

--
-- Indices de la tabla `inscripciones_eventos`
--
ALTER TABLE `inscripciones_eventos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_inscripcion` (`evento_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `transacciones_duts`
--
ALTER TABLE `transacciones_duts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_origen` (`id_origen`),
  ADD KEY `id_destino` (`id_destino`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cuentas_duts`
--
ALTER TABLE `cuentas_duts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `estadisticas_duts`
--
ALTER TABLE `estadisticas_duts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `inscripciones_eventos`
--
ALTER TABLE `inscripciones_eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `transacciones_duts`
--
ALTER TABLE `transacciones_duts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cuentas_duts`
--
ALTER TABLE `cuentas_duts`
  ADD CONSTRAINT `cuentas_duts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `estadisticas_duts`
--
ALTER TABLE `estadisticas_duts`
  ADD CONSTRAINT `estadisticas_duts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `eventos_ibfk_1` FOREIGN KEY (`organizador_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `inscripciones_eventos`
--
ALTER TABLE `inscripciones_eventos`
  ADD CONSTRAINT `inscripciones_eventos_ibfk_1` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscripciones_eventos_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `transacciones_duts`
--
ALTER TABLE `transacciones_duts`
  ADD CONSTRAINT `transacciones_duts_ibfk_1` FOREIGN KEY (`id_origen`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transacciones_duts_ibfk_2` FOREIGN KEY (`id_destino`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
