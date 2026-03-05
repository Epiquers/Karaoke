-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-03-2026 a las 21:45:21
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
-- Base de datos: `karaoke`
--
CREATE DATABASE IF NOT EXISTS `karaoke` DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci;
USE `karaoke`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `canciones`
--

DROP TABLE IF EXISTS `canciones`;
CREATE TABLE `canciones` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `artista` varchar(100) NOT NULL,
  `archivo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `canciones`
--

INSERT INTO `canciones` (`id`, `titulo`, `artista`, `archivo`) VALUES
(1, 'Bohemian Rhapsody', 'Queen', 'videos/bohemian.mp4'),
(2, 'Imagine', 'John Lennon', 'videos/imagine.mp4'),
(3, 'Sweet Child O Mine', 'Guns N Roses', 'videos/sweet.mp4'),
(4, 'Hotel California', 'Eagles', 'videos/hotel.mp4');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cola`
--

DROP TABLE IF EXISTS `cola`;
CREATE TABLE `cola` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_cancion` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cola`
--

INSERT INTO `cola` (`id`, `id_usuario`, `id_cancion`) VALUES
(24, 1, 1),
(28, 1, 1),
(25, 1, 2),
(3, 1, 3),
(27, 2, 2),
(15, 3, 4),
(16, 3, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `peticiones`
--

DROP TABLE IF EXISTS `peticiones`;
CREATE TABLE `peticiones` (
  `id_peticion` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `artista` varchar(50) NOT NULL,
  `titulo` varchar(50) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - pendiente\r\n1 - realizado',
  `fechaHora` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `passwd` varchar(255) NOT NULL,
  `rol` int(11) NOT NULL DEFAULT 1 COMMENT '1-Usuario\r\n2-Administrador',
  `estado` tinyint(1) NOT NULL COMMENT '0 - activo\r\n1 - bloqueado',
  `registrado` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `passwd`, `rol`, `estado`, `registrado`) VALUES
(1, 'Admin', 'admin@karaoke.com', '1234', 2, 0, '2026-03-05 20:35:27'),
(2, 'Juan', 'juan@test.com', '1234', 1, 0, '2026-03-05 18:55:19'),
(3, 'Jose Luis', 'prueba@karaoke.com', '1234', 1, 0, '2026-03-05 18:55:19');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `canciones`
--
ALTER TABLE `canciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cola`
--
ALTER TABLE `cola`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`,`id_cancion`),
  ADD KEY `id_cancion` (`id_cancion`);

--
-- Indices de la tabla `peticiones`
--
ALTER TABLE `peticiones`
  ADD PRIMARY KEY (`id_peticion`),
  ADD KEY `usuario` (`usuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `canciones`
--
ALTER TABLE `canciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `cola`
--
ALTER TABLE `cola`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `peticiones`
--
ALTER TABLE `peticiones`
  MODIFY `id_peticion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cola`
--
ALTER TABLE `cola`
  ADD CONSTRAINT `cola_ibfk_1` FOREIGN KEY (`id_cancion`) REFERENCES `canciones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cola_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `peticiones`
--
ALTER TABLE `peticiones`
  ADD CONSTRAINT `peticiones_ibfk_1` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
