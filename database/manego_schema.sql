-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-11-2025 a las 23:38:07
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
-- Base de datos: `manego`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `idEvento` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `paquete_id` int(11) NOT NULL,
  `fecha_evento` date NOT NULL,
  `hora_evento` time NOT NULL,
  `ubicacion` varchar(255) NOT NULL,
  `notas` text DEFAULT NULL,
  `media_link` varchar(255) DEFAULT NULL,
  `estado` enum('pendiente','confirmado','reprogramado','cancelado','aceptado','rechazado','finalizado') NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquetes`
--

CREATE TABLE `paquetes` (
  `idPack` int(11) NOT NULL,
  `nombrePack` varchar(50) DEFAULT NULL,
  `descripcion` varchar(250) DEFAULT NULL,
  `precio` int(5) DEFAULT NULL,
  `detalles` varchar(300) DEFAULT NULL,
  `imagen_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `paquetes`
--

INSERT INTO `paquetes` (`idPack`, `nombrePack`, `descripcion`, `precio`, `detalles`, `imagen_url`) VALUES
(1, 'Sesión de comida', 'Especializado para potencializar tu negocio, muestra tus mejores platillos', 2000, 'Incluye 20 fotografías de alta calidad ya editadas y en formato listo para subir a tus redes sociales.', 'uploads/paquetes/1762818997_WhatsApp_Image_2025-09-30_at_11.22.54_PM__2_.jpeg'),
(3, 'Sesión para autos', 'Especializado en resaltar los mejores ángulos de tu auto', 4000, 'Entrega 30 fotos de tu vehículo desde los mejores ángulos, todas las fotos en la mejor calidad y listas para redes sociales', 'uploads/paquetes/1762818954_WhatsApp_Image_2025-09-30_at_11.22.54_PM.jpeg'),
(5, 'Sesión personal', 'Da a conocer tu persona con una sesión', 1500, 'Retrátate en la mejor calidad y con la ayuda de profesionales, entrega lista para redes sociales 15 fotos editadas', 'uploads/paquetes/1763742065_WhatsApp_Image_2025-11-21_at_10.19.42_AM.jpeg'),
(9, 'Sesión para mascotas', 'Retrata a tus amigos de cuatro patas', 2500, 'Entrega de 30 fotografías de la mejor calidad vía Google Drive listas para descargar', 'uploads/paquetes/1764194581_WhatsApp_Image_2025-11-21_at_10.19.43_AM.jpeg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidoPaterno` varchar(100) NOT NULL,
  `apellidoMaterno` varchar(100) NOT NULL,
  `fechaNac` date NOT NULL,
  `email` varchar(150) NOT NULL,
  `numTelefono` varchar(20) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `role` enum('admin','cliente','fotografo') NOT NULL DEFAULT 'cliente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellidoPaterno`, `apellidoMaterno`, `fechaNac`, `email`, `numTelefono`, `pass`, `role`, `created_at`) VALUES
(3, 'Diego', 'Tejada', 'Nuñez', '2005-01-11', 'diegoignaciotejadanunez@gmail.com', '7771932662', '$2y$10$0ImCWAn3gFsPltOXK7mEAeGchxBO8/DhEeH0wrsxSeZhWqqgyhsjK', 'admin', '2025-11-09 06:17:13');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`idEvento`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_paquete` (`paquete_id`);

--
-- Indices de la tabla `paquetes`
--
ALTER TABLE `paquetes`
  ADD PRIMARY KEY (`idPack`);

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
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `idEvento` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `paquetes`
--
ALTER TABLE `paquetes`
  MODIFY `idPack` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `fk_eventos_paquete` FOREIGN KEY (`paquete_id`) REFERENCES `paquetes` (`idPack`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_eventos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
