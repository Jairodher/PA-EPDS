-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 19-11-2025 a las 16:43:56
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `EPD06`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `descripcion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `sku` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `num_pasillo` int(11) NOT NULL,
  `num_estanteria` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`sku`, `descripcion`, `num_pasillo`, `num_estanteria`, `cantidad`) VALUES
(1, 'Raton', 3, 4, 15),
(2, 'Teclado', 3, 2, 10),
(3, 'Monitor', 3, 1, 7),
(4, 'Auriculares', 2, 5, 20),
(5, 'Lampara', 1, 3, 14);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `nombre`) VALUES
(1, 'Administrador'),
(2, 'Administrativo'),
(3, 'Operario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `email`, `password`, `nombre`, `apellidos`, `id_rol`) VALUES
(6, 'juliaestevez@almacen.com', '$2y$10$eIMI1.s2d.3d/4f5g6h7j8k9l0m1n2o3p4q5r6s7t8u9v0w1x2', 'Julia', 'Estevez Sanmartin', 1),
(7, 'pedrogomez@almacen.com', '$2y$10$eIMI1.s2d.3d/4f5g6h7j8k9l0m1n2o3p4q5r6s7t8u9v0w1x2', 'Pedro', 'Gomez Perez', 2),
(8, 'marinacuenca@almacen.com', '$2y$10$eIMI1.s2d.3d/4f5g6h7j8k9l0m1n2o3p4q5r6s7t8u9v0w1x2', 'Marina', 'Cuenca Sales', 2),
(9, 'lolaflores@almacen.com', '$2y$10$eIMI1.s2d.3d/4f5g6h7j8k9l0m1n2o3p4q5r6s7t8u9v0w1x2', 'Lola', 'Flores Ruiz', 3),
(10, 'carlosromero@almacen.com', '$2y$10$eIMI1.s2d.3d/4f5g6h7j8k9l0m1n2o3p4q5r6s7t8u9v0w1x2', 'Carlos', 'Romero Tomillo', 3),
(11, 'test509@almacen.com', '$2y$10$9EPctBB2VuOyRU0tltbfoOQYNfPxs.lfRUmDtjtLYsreRiQl6OuJK', 'UsuarioTest_36', 'ApellidoTest', 3),
(12, 'juanmartin@almacen.com', '$2y$10$ycAyNRQ96bHoDHb/4S5r5eJGjgKlqXrRyL9Jp0RPiFY3OTHI9i1u6', 'juan', 'martin perez', 3),
(13, 'juanjopinoypilon@almacen.com', '$2y$10$mKxgHN5OlHux3dgADG9SM.WisMAxAfxZfAdHPQVp4K0aOJTzwIPSi', 'Juan José', 'Rodríguez Marín', 3),
(14, 'prueba1@almacen.com', '$2y$10$.KAc/GmFJiBU3BBDBNa4ueJyBuscnFRo/LhzX05kjhviPBihKK1z6', 'prueba', 'prueba1', 3);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`sku`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `sku` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `id_rol` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
