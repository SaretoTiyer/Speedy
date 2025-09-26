-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 15-10-2024 a las 03:46:33
-- Versión del servidor: 8.0.31
-- Versión de PHP: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bdspeedy`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificacion`
--

DROP TABLE IF EXISTS `calificacion`;
CREATE TABLE IF NOT EXISTS `calificacion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_pedido` int NOT NULL,
  `id_usuario` int NOT NULL,
  `id_tienda` int NOT NULL,
  `id_domiciliario` int DEFAULT NULL,
  `puntuacion_tienda` tinyint UNSIGNED NOT NULL,
  `puntuacion_domiciliario` tinyint UNSIGNED NOT NULL,
  `comentario` text,
  `fecha_calificacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_domiciliario` (`id_domiciliario`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_pedido` (`id_pedido`),
  KEY `id_tienda` (`id_tienda`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `calificacion`
--

INSERT INTO `calificacion` (`id`, `id_pedido`, `id_usuario`, `id_tienda`, `id_domiciliario`, `puntuacion_tienda`, `puntuacion_domiciliario`, `comentario`, `fecha_calificacion`) VALUES
(8, 1, 3, 24, 10, 4, 4, 'puede ser mejor ', '2024-10-14 07:22:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `imagen` varchar(255) DEFAULT NULL,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `imagen`, `fecha_registro`) VALUES
(1, 'Herramientas', 'Productos y equipos para construcción y reparación', 'fas fa-tools', '2024-10-10 15:19:02'),
(2, 'Servicios', 'Oferta de servicios profesionales y técnicos', 'fas fa-concierge-bell', '2024-10-10 15:19:02'),
(3, 'Comida', 'Alimentos preparados y productos comestibles', 'fas fa-utensils', '2024-10-10 15:19:02'),
(4, 'Limpieza', 'Productos y servicios de limpieza y mantenimiento', 'fas fa-broom', '2024-10-10 15:19:02'),
(5, 'Accesorios', 'Complementos y adornos personales', 'fas fa-glasses', '2024-10-10 15:19:02'),
(6, 'Ropa', 'Prendas de vestir y calzado', 'fas fa-tshirt', '2024-10-10 15:19:02'),
(7, 'Electrónica', 'Dispositivos y accesorios electrónicos', 'fas fa-laptop', '2024-10-10 15:19:02'),
(8, 'Hogar', 'Artículos para el hogar y decoración', 'fas fa-home', '2024-10-10 15:19:02'),
(9, 'Salud y Belleza', 'Productos de cuidado personal y bienestar', 'fas fa-heartbeat', '2024-10-10 15:19:02'),
(10, 'Deportes', 'Equipamiento y ropa deportiva', 'fas fa-football-ball', '2024-10-10 15:19:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

DROP TABLE IF EXISTS `detalle_pedido`;
CREATE TABLE IF NOT EXISTS `detalle_pedido` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_pedido` int NOT NULL,
  `id_producto` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_pedido` (`id_pedido`),
  KEY `id_producto` (`id_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `detalle_pedido`
--

INSERT INTO `detalle_pedido` (`id`, `id_pedido`, `id_producto`, `cantidad`, `precio_unitario`) VALUES
(1, 1, 18, 2, '29860.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `domiciliario`
--

DROP TABLE IF EXISTS `domiciliario`;
CREATE TABLE IF NOT EXISTS `domiciliario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `correo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `dom_foto` varchar(535) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `vehiculo` varchar(50) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `domiciliario`
--

INSERT INTO `domiciliario` (`id`, `nombre`, `apellido`, `telefono`, `correo`, `password`, `dom_foto`, `direccion`, `fecha_registro`, `vehiculo`, `activo`) VALUES
(10, 'thiago', 'Reyes', '3186745211', 'd@gmail.com', '$2y$10$uXAH41qXjdbCJnzs7dn52uZaWl.ZmdxUfhO7X8H/V86cCcSQDIleG', './vista/recursos/img/domiciliarios/67087f5d7bdb3_santi.jpg', 'cr108#56 c18', '2024-10-11 01:29:01', 'Moto', 1),
(13, 'Cristiano', 'ronaldo', '3186745211', 'cr7@gmail.com', '$2y$10$qZgxEidBhqQaYtpAaKiS8efNsnjoM./ZE/5s7vXn72T7vzuIsZq5W', './vista/recursos/img/domiciliarios/670b623f5a266_logo.png', 'cr108#56 c18', '2024-10-13 06:01:35', 'Moto', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
CREATE TABLE IF NOT EXISTS `notificaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipo` enum('solicitud','en proceso','pedido','informacion','inventario','entrega','entregado','cancelado') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `mensaje` text NOT NULL,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('no_leida','leida') DEFAULT 'no_leida',
  `usuario_id` int DEFAULT NULL,
  `es_admin` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `tipo`, `mensaje`, `fecha`, `estado`, `usuario_id`, `es_admin`) VALUES
(1, 'pedido', 'Su pedido #1 ha sido creado con éxito.', '2024-10-13 23:47:01', 'leida', 3, 0),
(2, 'pedido', 'Nuevo pedido #1 recibido.', '2024-10-13 23:47:01', 'no_leida', 28, 0),
(3, 'pedido', 'Has aceptado el pedido #1.', '2024-10-13 23:47:16', 'no_leida', 28, 0),
(4, 'pedido', 'Tu pedido #1 ha sido aceptado por la tienda.', '2024-10-13 23:47:16', 'leida', 3, 0),
(5, 'pedido', 'Has comenzado a preparar el pedido #1.', '2024-10-13 23:47:21', 'no_leida', 28, 0),
(6, 'pedido', 'Tu pedido #1 está siendo preparado.', '2024-10-13 23:47:21', 'leida', 3, 0),
(7, 'pedido', 'Has asignado un domiciliario al pedido #1.', '2024-10-14 00:20:44', 'no_leida', 28, 0),
(8, 'pedido', 'Tu pedido #1 está en camino.', '2024-10-14 00:20:44', 'leida', 3, 0),
(10, 'pedido', 'Tu pedido #1 ha sido entregado.', '2024-10-14 04:57:39', 'leida', 3, 0),
(16, 'pedido', 'El usuario Adrian ha calificado tu tienda con 4 estrellas.', '2024-10-14 07:22:27', 'no_leida', 28, 0),
(17, 'pedido', 'El usuario Adrian ha calificado tu servicio con 4 estrellas.', '2024-10-14 07:22:27', 'leida', 29, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

DROP TABLE IF EXISTS `pedido`;
CREATE TABLE IF NOT EXISTS `pedido` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `fecha_hora_pedido` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('pendiente','aceptado','en_preparacion','listo_para_entrega','en_camino','entregado','cancelado') NOT NULL DEFAULT 'pendiente',
  `valor_pedido` decimal(10,2) NOT NULL,
  `direccion_entrega` varchar(255) DEFAULT NULL,
  `id_domiciliario` int DEFAULT NULL,
  `comentarios` text,
  `fecha_entrega` timestamp NULL DEFAULT NULL,
  `calificacion` decimal(3,2) DEFAULT NULL,
  `fecha_hora_entrega` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_domiciliario` (`id_domiciliario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `pedido`
--

INSERT INTO `pedido` (`id`, `id_usuario`, `fecha_hora_pedido`, `estado`, `valor_pedido`, `direccion_entrega`, `id_domiciliario`, `comentarios`, `fecha_entrega`, `calificacion`, `fecha_hora_entrega`) VALUES
(1, 3, '2024-10-13 23:47:01', 'entregado', '59720.00', 'Conexión vial Guillermo Gaviria Correa, Olaya Herrera, Comuna 7 - Robledo, Perímetro Urbano Medellín, Medellín, Valle de Aburrá, Antioquia, RAP del Agua y la Montaña, 050036, Colombia', 10, 'mas especifico cr107 60 c 10 ', NULL, NULL, '2024-10-14 04:57:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

DROP TABLE IF EXISTS `productos`;
CREATE TABLE IF NOT EXISTS `productos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `precio` decimal(10,2) NOT NULL,
  `cantidad` int NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `id_tienda` int DEFAULT NULL,
  `id_categoria` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_tienda` (`id_tienda`),
  KEY `id_categoria` (`id_categoria`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `cantidad`, `imagen`, `fecha_registro`, `id_tienda`, `id_categoria`) VALUES
(16, 'Brownie y Cookies', 'helado de 5L', '43500.00', 10, 'vista/recursos/img/productos/670b774c07367_Brownie y Cookies.png', '2024-10-13 07:31:24', 24, 3),
(17, 'Vainilla', 'Helado de 5L', '43500.00', 15, 'vista/recursos/img/productos/670b7a144059d_vainilla.png', '2024-10-13 07:43:16', 24, 3),
(18, 'Cookies cream', 'helado de 1l ', '29860.00', 7, 'vista/recursos/img/productos/670b7ae20e38b_Cookies Cream.png', '2024-10-13 07:46:42', 24, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

DROP TABLE IF EXISTS `rol`;
CREATE TABLE IF NOT EXISTS `rol` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` enum('usuario','administrador','tienda','domiciliario') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'usuario',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizada` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id`, `nombre`, `fecha_registro`, `fecha_actualizada`) VALUES
(1, 'administrador', '2024-08-10 16:04:06', NULL),
(2, 'usuario', '2024-08-10 16:04:19', NULL),
(3, 'tienda', '2024-08-10 16:04:27', NULL),
(4, 'domiciliario', '2024-08-10 16:04:33', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tienda`
--

DROP TABLE IF EXISTS `tienda`;
CREATE TABLE IF NOT EXISTS `tienda` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `correo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `tienda_foto` varchar(535) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `horario_apertura` time DEFAULT NULL,
  `horario_cierre` time DEFAULT NULL,
  `fecha_apertura` varchar(255) NOT NULL,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tienda`
--

INSERT INTO `tienda` (`id`, `nombre`, `direccion`, `telefono`, `correo`, `password`, `tienda_foto`, `horario_apertura`, `horario_cierre`, `fecha_apertura`, `fecha_registro`, `activo`) VALUES
(24, 'HeladosCampini', 'cr108#56 c18', '3186745211', 'tienda@gmail.com', '$2y$10$PBkzghWOgzwdW9JGjZ3MUO62CRcMneDFdhIfkm3rc/Srun3l7Tt52', '/vista/recursos/img/tiendas/670b82547b5eb_haunted-by-memories-jb-1366x768.jpg', '08:49:00', '20:49:00', '2024-10-01', '2024-10-10 05:00:00', 1),
(32, 'ParcheCristian', 'cr108#56 c18', '3186745211', 'parche1@gmail.com', '$2y$10$B6J5TnoiDF2YeFCMwCKjzOR3cOq0kqySEa1/QPmFF5wfhdJQAZNbK', './vista/recursos/img/tiendas/spider-man-sobre-la-ciudad_3840x2160_xtrafondos.com.jpg', '00:19:00', '12:19:00', '2024-10-01', '2024-10-13 05:00:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `id_rol` int DEFAULT '2',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `id_rol` (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `direccion`, `telefono`, `id_rol`) VALUES
(1, 'Santiago', 'SaAdmin@gmail.com', '$2y$10$co3wzfNMsi4KBLammrUvVuMJvX6E25Ina2xUnCFsd4vCdTTnSYL26', 'Crr10760c10', '3157222620', 1),
(3, 'Adrian', 'presiga@gmail.com', '$2y$10$izcn6KFTm/W3WwpMa0czxOIrFoyI2khvC4yj0vgpCcwfxlD/zaadS', 'Crr10264c13', '3157243620', 2),
(28, 'HeladosCampini', 'tienda@gmail.com', '$2y$10$PBkzghWOgzwdW9JGjZ3MUO62CRcMneDFdhIfkm3rc/Srun3l7Tt52', 'cr108#56 c18', '3186745211', 3),
(29, 'thiago', 'd@gmail.com', '$2y$10$uXAH41qXjdbCJnzs7dn52uZaWl.ZmdxUfhO7X8H/V86cCcSQDIleG', 'cr108#56 c18', '3186745211', 4),
(30, 'Cristian', 'cristian@gmail.com', '$2y$10$ZB9aii24iVs/0vxYN0S5fu1vHdaoyz5PrvvNAb/j7DWHcKe3Z2jsq', 'cr108#56 c18', '318674554255', 2),
(39, 'ParcheCristian', 'parche1@gmail.com', '$2y$10$B6J5TnoiDF2YeFCMwCKjzOR3cOq0kqySEa1/QPmFF5wfhdJQAZNbK', 'cr108#56 c18', '3186745211', 3),
(44, 'Cristiano', 'cr7@gmail.com', '$2y$10$qZgxEidBhqQaYtpAaKiS8efNsnjoM./ZE/5s7vXn72T7vzuIsZq5W', 'cr108#56 c18', '3186745211', 4);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `calificacion`
--
ALTER TABLE `calificacion`
  ADD CONSTRAINT `calificacion_ibfk_3` FOREIGN KEY (`id_domiciliario`) REFERENCES `domiciliario` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `calificacion_ibfk_5` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `calificacion_ibfk_6` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `calificacion_ibfk_7` FOREIGN KEY (`id_tienda`) REFERENCES `tienda` (`id`);

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_pedido_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`) ON DELETE RESTRICT;

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedido_ibfk_2` FOREIGN KEY (`id_domiciliario`) REFERENCES `domiciliario` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_productos_categorias` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_tienda`) REFERENCES `tienda` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
