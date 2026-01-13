-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-11-2025 a las 18:08:05
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tiendaonline_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(1, 'Cascos'),
(2, 'Chaquetas'),
(3, 'Botas'),
(4, 'Guantes'),
(5, 'Pantalones');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id` int(11) NOT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio_unitario` decimal(7,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `detalle_pedido`
--

INSERT INTO `detalle_pedido` (`id`, `id_pedido`, `id_producto`, `cantidad`, `precio_unitario`) VALUES
(1, 3, 5, 2, 129.99),
(2, 4, 10, 3, 69.99),
(3, 5, 10, 2, 69.99),
(4, 5, 7, 1, 99.99),
(5, 5, 12, 1, 109.99),
(6, 6, 8, 1, 89.99),
(7, 6, 10, 1, 69.99),
(8, 7, 7, 1, 99.99),
(9, 10, 8, 4, 89.99),
(10, 11, 10, 2, 69.99),
(11, 11, 7, 1, 99.99),
(12, 12, 2, 1, 249.99),
(13, 13, 4, 2, 139.99),
(14, 13, 10, 1, 69.99),
(15, 14, 5, 1, 129.99),
(16, 15, 4, 1, 139.99),
(17, 15, 8, 1, 89.99),
(18, 15, 10, 1, 69.99),
(19, 15, 13, 1, 149.99),
(20, 16, 10, 1, 69.99),
(21, 17, 5, 1, 129.99),
(22, 18, 8, 1, 89.99),
(23, 19, 7, 1, 99.99),
(24, 20, 7, 1, 99.99),
(25, 21, 7, 1, 99.99),
(26, 22, 7, 1, 99.99);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `total` decimal(7,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `id_usuario`, `fecha`, `total`) VALUES
(3, 7, '2025-11-17 15:24:26', 259.98),
(4, 8, '2025-11-17 15:25:38', 209.97),
(5, 9, '2025-11-17 15:28:54', 349.96),
(6, 10, '2025-11-17 15:58:04', 159.98),
(7, 11, '2025-11-17 15:59:00', 99.99),
(10, 14, '2025-11-18 21:00:33', 359.96),
(11, 15, '2025-11-18 21:03:06', 239.97),
(12, 16, '2025-11-18 21:06:20', 249.99),
(13, 17, '2025-11-20 20:57:55', 349.97),
(14, 18, '2025-11-20 21:17:29', 129.99),
(15, 19, '2025-11-24 11:11:28', 449.96),
(16, 20, '2025-11-24 18:52:14', 69.99),
(17, 21, '2025-11-24 19:22:16', 129.99),
(18, 22, '2025-11-24 19:37:24', 89.99),
(19, 11, '2025-11-24 19:46:50', 99.99),
(20, 11, '2025-11-24 19:47:31', 99.99),
(21, 23, '2025-11-24 19:49:09', 99.99),
(22, 24, '2025-11-24 19:52:29', 99.99);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `imagen`, `id_categoria`) VALUES
(2, 'Casco integral Chark Danger', 'Casco integral con doble homologación y sistema de cierre micrométrico.', 249.99, 'img/casco1.jpg', 1),
(3, 'Casco integral Enezeta-E II', 'Casco integral con ventilación avanzada y visor solar integrado.', 129.00, 'img/casco2.jpg', 1),
(4, 'Chaqueta deportiva SBR700 Alpunistors', 'Chaqueta textil deportiva con protecciones en hombros y codos.', 139.99, 'img/chaqueta1.jpg', 2),
(5, 'Chaqueta touring TR734', 'Chaqueta de invierno ideal para rutas largas, con forro desmontable.', 129.99, 'img/chaqueta2.jpg', 2),
(6, 'Chaqueta cuero SS80', 'Chaqueta en cuero sintético de corte deportivo/custom.', 79.99, 'img/chaqueta3.jpg', 2),
(7, 'Botas deportivas R900 Alpunistors', 'Botas ideales para uso dentro de circuito.', 99.99, 'img/botas1.png', 3),
(8, 'Botas de cuero ST390 Alpunistors', 'Clásica bota de cuero marrón, ideal para uso en carretera abierta.', 89.99, 'img/botas2.jpg', 3),
(9, 'Guantes verano Armaryo BMX-5 Air', 'Guantes ventilados con refuerzos en palma y cierre de muñeca', 54.99, 'img/guantes1.jpg', 4),
(10, 'Guantes invierno Daymiel 50 Ventodry', 'Guantes impermeables y térmicos con protección en palma y nudillos, con membrana reforzada.', 69.99, 'img/guantes2.png', 4),
(11, 'Pantalón textil Erazer Vega', 'Pantalón textil perforado con protecciones CE nivel 2.', 129.99, 'img/pantalones1.jpg', 5),
(12, 'Pantalón reforzado Daymiel Spyro Con Air', 'Pantalón reforzado con protecciones CE nivel 1, ideal para uso en carretera.', 109.99, 'img/pantalones2.jpg', 5),
(13, 'Pantalón touring Sport T-20 V3', 'Pantalón estilo touring con inserciones elásticas y cremallera de unión.', 149.99, 'img/pantalones3.jpg', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `is_guest` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password_hash`, `direccion`, `is_guest`) VALUES
(7, NULL, 'jeje@jeje.com', NULL, 'jeje, 32', 1),
(8, 'Juan Palomo', 'juanpa@gmail.com', '$2y$10$DAHqlAbpPhdJ.wA7sZMOB.JWaoJsa5/mjR/8zPksaOposKvPP.IRK', 'El Greco, 98', 0),
(9, NULL, 'invitado@invi.com', NULL, 'InvitadoLandia', 1),
(10, NULL, 'test1@test.com', NULL, 'testlandia, 45', 1),
(11, 'testman', 'test2@test.com', '$2y$10$wiyYvjXmx99PivIhdEThY.w89Yo0l1h4nWrolAUMmXw/SKDbWgFXS', 'Testigos, 48', 0),
(14, NULL, 'juanpa@gmail.com', NULL, 'madrid, 77', 1),
(15, 'Pepe Rito', 'peperito@gmail.com', '$2y$10$aHV4X1l3aRoUgtEhmN63fet8bxyVYENaLiSHvA4dwfMYL/D0gPqvO', 'Casa Pepe Gastrobar', 0),
(16, NULL, 'milindri@gmail.com', NULL, 'milindri, 22', 1),
(17, NULL, 'juanpalomo@gmail.com', NULL, 'Palomo, 45', 1),
(18, 'Julipi Vélez', 'jvelez@gmail.com', '$2y$10$KRQwipIuHhoe6Iyqitzlgeuu7uRrdZu/8.Qk//jE7SEcrXIcKp22i', 'Juan Eusebio Fuentes Hurtado, 44', 0),
(19, NULL, 'juanpalomo@gmail.com', NULL, 'Carrer Muntaner, 25', 1),
(20, 'Julián Caderrana', 'jcaderrana@gmail.com', '$2y$10$t9CDNEqM.hjN6Sgq7vngtezUPJ9EFJPrNGtHYra7ppdHuc5BIlTtq', 'Casa Pepe Gastrobar', 0),
(21, NULL, 'jeje@gmail.com', NULL, 'Muybuenas, 22', 1),
(22, NULL, 'test@test.com', NULL, 'Testlandia, 76', 1),
(23, 'Juanito Test', 'jtest@gmail.com', '$2y$10$LwhC9lvf8ciy/P0wFU.io.HFXtPJuNwaFEHGLi9kceoeT5O3r7TwG', 'Calle Logitech, 20', 0),
(24, 'Testarudo', 'testarud0@gmail.com', '$2y$10$kQA1oK68tHOwn0CQcExVWebGtafS2nYFGWQHJ1osyuw5SohM/nFHm', 'Testigos de Jehova, 99', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `detalle_pedido_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
