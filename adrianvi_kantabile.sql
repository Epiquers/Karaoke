-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 12-05-2026 a las 18:20:12
-- Versión del servidor: 10.11.11-MariaDB-cll-lve
-- Versión de PHP: 8.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `adrianvi_kantabile`
--
CREATE DATABASE IF NOT EXISTS `adrianvi_kantabile` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;
USE `adrianvi_kantabile`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `canciones`
--

DROP TABLE IF EXISTS `canciones`;
CREATE TABLE `canciones` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `artista` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `estilo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `voz` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `instrumental` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `letra` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `canciones`
--

INSERT INTO `canciones` (`id`, `titulo`, `artista`, `estilo`, `voz`, `instrumental`, `letra`) VALUES
(18, 'Un plan mejor', 'Vetusta Morla', 'Indie', 'uploads/canciones/Vetusta_Morla/Un_plan_mejor_(voz)_32.mp3', 'uploads/canciones/Vetusta_Morla/Un_plan_mejor_(instrumental)_32.mp3', 'uploads/letras/Vetusta_Morla/Un_plan_mejor_32.lrc'),
(19, 'Profetas de la mañana', 'Vetusta Morla', 'Indie', 'uploads/canciones/Vetusta_Morla/Profetas_de_la_maana_(voz)_05.mp3', 'uploads/canciones/Vetusta_Morla/Profetas_de_la_maana_(instrumental)_05.mp3', 'uploads/letras/Vetusta_Morla/Profetas_de_la_maana_05.lrc'),
(20, 'Mercaderes de salud', 'Sok', 'Rock', 'uploads/canciones/Sok/Mercaderes_de_salud_(voz)_53.mp3', 'uploads/canciones/Sok/Mercaderes_de_salud_(instrumental)_53.mp3', 'uploads/letras/Sok/Mercaderes_de_salud_53.lrc'),
(21, 'Viva la vida', 'Coldplay', 'Rock', 'uploads/canciones/Coldplay/Viva_la_vida_(voz)_41.mp3', 'uploads/canciones/Coldplay/Viva_la_vida_(instrumental)_41.mp3', 'uploads/letras/Coldplay/Viva_la_vida_41.lrc'),
(22, 'All of me', 'John Legend', 'Jazz', 'uploads/canciones/John_Legend/All_of_me_(voz)_21.mp3', 'uploads/canciones/John_Legend/All_of_me_(instrumental)_21.mp3', 'uploads/letras/John_Legend/All_of_me_21.lrc'),
(23, 'La Vereda de la Puerta de Atrás', 'Extremoduro', 'Rock', 'uploads/canciones/Extremoduro/La_Vereda_de_la_Puerta_de_Atrs_(voz)_26.mp3', 'uploads/canciones/Extremoduro/La_Vereda_de_la_Puerta_de_Atrs_(instrumental)_26.mp3', 'uploads/letras/Extremoduro/La_Vereda_de_la_Puerta_de_Atrs_26.lrc'),
(24, '19 días y 500 noches', 'Joaquín Sabina', 'Rock', 'uploads/canciones/Joaqun_Sabina/19_das_y_500_noches_(voz)_59.mp3', 'uploads/canciones/Joaqun_Sabina/19_das_y_500_noches_(instrumental)_59.mp3', 'uploads/letras/Joaqun_Sabina/19_das_y_500_noches_59.lrc'),
(25, 'Zafar', 'La Vela Puerca', 'Rock', 'uploads/canciones/La_Vela_Puerca/Zafar_(voz)_19.mp3', 'uploads/canciones/La_Vela_Puerca/Zafar_(instrumental)_19.mp3', 'uploads/letras/La_Vela_Puerca/Zafar_19.lrc'),
(26, 'Vale la Pena', 'Juan Luis Guerra', 'Pop', 'uploads/canciones/Juan_Luis_Guerra/Vale_la_Pena_(voz)_29.mp3', 'uploads/canciones/Juan_Luis_Guerra/Vale_la_Pena_(instrumental)_29.mp3', 'uploads/letras/Juan_Luis_Guerra/Vale_la_Pena_29.lrc');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cola`
--

DROP TABLE IF EXISTS `cola`;
CREATE TABLE `cola` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_cancion` int(11) DEFAULT NULL,
  `cantante` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `peticiones`
--

DROP TABLE IF EXISTS `peticiones`;
CREATE TABLE `peticiones` (
  `id_peticion` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `artista` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `titulo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - pendiente\r\n1 - realizado',
  `fechaHora` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `peticiones`
--

INSERT INTO `peticiones` (`id_peticion`, `usuario`, `artista`, `titulo`, `estado`, `fechaHora`) VALUES
(4, 13, 'Midnite', 'I am a Bushman', 0, '2026-05-12 15:55:24'),
(5, 14, 'Jarabe de palo', 'La flaca', 0, '2026-05-12 15:55:53'),
(6, 15, 'Dulce Pontes', 'Cancão Do Mar', 0, '2026-05-12 15:56:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `passwd` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `rol` int(11) NOT NULL DEFAULT 1 COMMENT '1-Usuario\r\n2-Administrador',
  `estado` tinyint(1) NOT NULL COMMENT '0 - activo\r\n1 - bloqueado',
  `registrado` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `passwd`, `rol`, `estado`, `registrado`) VALUES
(10, 'Eduardo Piquer', 'EPiquer@kantabile.com', '1234', 2, 0, '2026-05-11 23:01:49'),
(11, 'Adrian Vincent', 'AVincent@kantabile.com', '1234', 2, 0, '2026-05-11 23:02:04'),
(13, 'Victor Verdú', 'VVerdu@kantabile.com', '1234', 1, 0, '2026-05-11 23:02:37'),
(14, 'Fernando Ureña', 'FUrena@kantabile.com', '1234', 1, 0, '2026-05-11 23:03:19'),
(15, 'Invitado Especial', 'Invitado@kantabile.com', '1234', 1, 0, '2026-05-11 23:17:00');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `cola`
--
ALTER TABLE `cola`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT de la tabla `peticiones`
--
ALTER TABLE `peticiones`
  MODIFY `id_peticion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
