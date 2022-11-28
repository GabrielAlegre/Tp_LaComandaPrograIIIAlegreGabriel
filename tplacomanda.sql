-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-11-2022 a las 19:37:16
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tplacomandaprograiii`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `nickNameUser` varchar(50) NOT NULL,
  `clave` varchar(120) NOT NULL,
  `fechaAlta` date NOT NULL,
  `tipoDeEmpleado` varchar(40) NOT NULL,
  `fechaBaja` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `nombre`, `nickNameUser`, `clave`, `fechaAlta`, `tipoDeEmpleado`, `fechaBaja`) VALUES
(1, 'jorge perez', 'jorgito23', '$2y$10$Zvvp0LXJQd.EjvBwlCJjwu9j4icBIz6e8IAkn8G0pK3XoDgv4iww6', '2022-11-25', 'mozo', NULL),
(2, 'emiliano rodriguez', 'emicabj', '$2y$10$aPa61wn161/ZIOF6A8jqwOXJrOZHxjOtqYSg3upnVbP63zimBOD/W', '2022-11-25', 'cocinero', NULL),
(3, 'gabriel alegre', '_gabyalegre', '$2y$10$YBuOf/aouCcRWHGaM1UVuOn4O0oaBQedzb3XnXO6UiqlYIoHZB28S', '2022-11-25', 'mozo', NULL),
(4, 'roberto', 'robert12', '$2y$10$m4MIrjT/ktJUDoRSQPYp1.mpy3a..KF9KBnE6siGaIzASbunek9C6', '2022-11-26', 'cervecero', NULL),
(5, 'matias', 'matinoge12', '$2y$10$6399XzML28qaRaa8orNwX.oBpfdwtBxHtJtwy9y8dEF6CyvWMz8Je', '2022-11-26', 'bartender', NULL),
(6, 'franco', 'flippi', '$2y$10$J3vPBRAGDUotNRuBUBBHXe.XJLq2MPmYDGJjslC52bukxCFQJzd9C', '2022-11-26', 'socio', NULL),
(7, 'damian', 'dami_perez', '$2y$10$cvSI708mJ3L/eU0D0/Ban./r0x6C8LJMApUy2dtDgC3t4oWV8ma1.', '2022-11-27', 'cocinero', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuestas`
--

CREATE TABLE `encuestas` (
  `id` int(11) NOT NULL,
  `puntajeMesa` float NOT NULL,
  `puntajeResto` float NOT NULL,
  `puntajeMozo` float NOT NULL,
  `puntajeCocinero` float NOT NULL,
  `promedio` float NOT NULL,
  `descripcion` varchar(66) NOT NULL,
  `numeroDelPedido` varchar(15) NOT NULL,
  `codigoDeLaMesaUtilizada` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `encuestas`
--

INSERT INTO `encuestas` (`id`, `puntajeMesa`, `puntajeResto`, `puntajeMozo`, `puntajeCocinero`, `promedio`, `descripcion`, `numeroDelPedido`, `codigoDeLaMesaUtilizada`) VALUES
(1, 7, 10, 8, 10, 8.75, 'Todo correcto', 'KR9ZN', 'FUX2O');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `id` int(11) NOT NULL,
  `codigoDeMesa` varchar(50) NOT NULL,
  `estado` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`id`, `codigoDeMesa`, `estado`) VALUES
(1, '9KGA7', 'cerrada'),
(2, 'FUX2O', 'cerrada'),
(3, 'BZ5I4', 'cerrada'),
(4, 'WGJ50', 'cerrada'),
(5, 'MOHUR', 'cerrada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes_productos`
--

CREATE TABLE `ordenes_productos` (
  `id` int(11) NOT NULL,
  `idDelProductoElegido` int(11) NOT NULL,
  `nroDePedidoAlQueCorrespondeLaOrden` varchar(10) NOT NULL,
  `estado` varchar(70) NOT NULL,
  `idEmpleadoQuePrepararaLaOrden` int(11) NOT NULL,
  `tiempoEstimadoDePreparacion` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `ordenes_productos`
--

INSERT INTO `ordenes_productos` (`id`, `idDelProductoElegido`, `nroDePedidoAlQueCorrespondeLaOrden`, `estado`, `idEmpleadoQuePrepararaLaOrden`, `tiempoEstimadoDePreparacion`) VALUES
(1, 14, 'KR9ZN', 'entregado', 4, 3),
(2, 9, 'KR9ZN', 'entregado', 7, 47);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `nombreDelCliente` varchar(50) NOT NULL,
  `nroDePedido` varchar(50) NOT NULL,
  `estado` varchar(100) NOT NULL,
  `codigoDeMesaAsociada` varchar(100) NOT NULL,
  `idMozoEncargado` int(11) NOT NULL,
  `tiempoDeFinalizacionEstimado` int(11) DEFAULT NULL,
  `precioTotal` int(11) DEFAULT NULL,
  `pathFoto` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `nombreDelCliente`, `nroDePedido`, `estado`, `codigoDeMesaAsociada`, `idMozoEncargado`, `tiempoDeFinalizacionEstimado`, `precioTotal`, `pathFoto`) VALUES
(1, 'Alberto', 'KR9ZN', 'entregado', 'FUX2O', 3, 47, 1500, '../app/archivos/fotosPedido/KR9ZN.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `precio` float NOT NULL,
  `sectorEncargado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `precio`, `sectorEncargado`) VALUES
(1, 'Milanesa con fritas', 1500, 'cocinero'),
(2, 'Cerveza negra', 500, 'cervecero'),
(3, 'Hamburguesa clasica', 1200, 'cocinero'),
(4, 'Daikiri', 850, 'bartender'),
(5, 'Pizza', 1600, 'cocinero'),
(6, 'Empanada', 1200, 'cocinero'),
(7, 'Mojito', 500, 'bartender'),
(8, 'Cerveza ipa', 550, 'cervecero'),
(9, 'Ravioles a la boloñesa', 950, 'cocinero'),
(10, 'Sandwich de mila', 990, 'cocinero'),
(11, 'Ron con coca', 450, 'bartender'),
(12, 'Parrillada completa', 4300, 'cocina'),
(13, 'Destornillado', 400, 'bartender'),
(14, 'Cerveza Roja', 550, 'cervecero'),
(15, 'Papas con chedar', 850, 'cocinero'),
(16, 'Campari', 790, 'bartender');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ordenes_productos`
--
ALTER TABLE `ordenes_productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ordenes_productos`
--
ALTER TABLE `ordenes_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
