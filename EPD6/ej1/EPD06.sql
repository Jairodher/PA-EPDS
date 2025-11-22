-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-11-2025 a las 16:27:05
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
-- Base de datos: `epd06`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `tipo_accion` varchar(50) NOT NULL,
  `entidad` varchar(255) NOT NULL,
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `logs`
--

INSERT INTO `logs` (`id`, `id_usuario`, `tipo_accion`, `entidad`, `fecha`) VALUES
(1, 15, 'LEER', 'lista usuarios', '2025-11-22 15:24:18'),
(2, 15, 'CREAR', 'usuario admin@almacen.com', '2025-11-22 15:29:22'),
(3, 15, 'LEER', 'lista usuarios', '2025-11-22 15:29:22'),
(4, 15, 'LEER', 'lista usuarios', '2025-11-22 15:30:24'),
(5, 15, 'BORRAR', 'usuario 16', '2025-11-22 15:30:33'),
(6, 15, 'LEER', 'lista usuarios', '2025-11-22 15:30:33'),
(7, 15, 'CREAR', 'usuario adminprincipal@almacen.com', '2025-11-22 15:31:53'),
(8, 15, 'BORRAR', 'usuario 16', '2025-11-22 15:31:53'),
(9, 15, 'LEER', 'lista usuarios', '2025-11-22 15:31:53'),
(10, 15, 'CREAR', 'usuario pedroadministrativo@almacen.com', '2025-11-22 15:32:34'),
(11, 15, 'BORRAR', 'usuario 16', '2025-11-22 15:32:34'),
(12, 15, 'LEER', 'lista usuarios', '2025-11-22 15:32:34'),
(13, 15, 'LEER', 'lista usuarios', '2025-11-22 15:32:42'),
(14, 15, 'ACTUALIZAR', 'usuario 18', '2025-11-22 15:32:45'),
(15, 15, 'LEER', 'lista usuarios', '2025-11-22 15:32:45'),
(16, 15, 'CREAR', 'usuario juanoperario@almacen.com', '2025-11-22 15:33:11'),
(17, 15, 'LEER', 'lista usuarios', '2025-11-22 15:33:11'),
(18, 17, 'LEER', 'lista usuarios', '2025-11-22 15:34:57');

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
(14, 'prueba1@almacen.com', '$2y$10$.KAc/GmFJiBU3BBDBNa4ueJyBuscnFRo/LhzX05kjhviPBihKK1z6', 'prueba', 'prueba1', 3),
(15, 'admin@almacen.com', '$2y$10$FQtuvs1R1LcYGeL6bbCH9ephVj/aoiCzIxbye0GgZRtErqBGS5yBq', 'Admin', 'Admin', 1),
(17, 'adminprincipal@almacen.com', '$2y$10$6Y1WpcCkSgaqZ7PE5mUXu.JGBWR5PeVXnt1uP3UPoZs2JGMj70DHC', 'Admin', 'Principal', 1),
(18, 'pedroadministrativo@almacen.com', '$2y$10$.sHEQXru5Vdth5hkhz2EVe2N5RMDS0MwHyCv16yYUOCW6sJzjW3de', 'Pedro', 'Administrativo', 2),
(19, 'juanoperario@almacen.com', '$2y$10$5.t6y1wiae3AA7bUAaglQ.n508I1u/pamNqe1HI3VLJ.z4id5X076', 'Juan', 'Operario', 3);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `sku` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `id_rol` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
