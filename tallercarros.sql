-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-12-2024 a las 19:58:24
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
-- Base de datos: `tallercarros`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `aplicar_garantia` (IN `p_reparacion_id` INT)   BEGIN
    UPDATE reparaciones
    SET estado = 'En progreso', fecha_ingreso = CURDATE(), fecha_salida = NULL
    WHERE id = p_reparacion_id AND fecha_salida >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `iniciar_reparacion` (IN `p_vehiculo_id` INT, IN `p_descripcion` TEXT)   BEGIN
    INSERT INTO reparaciones (vehiculo_id, fecha_ingreso, descripcion)
    VALUES (p_vehiculo_id, CURDATE(), p_descripcion);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `programar_servicio` (IN `p_cliente_id` INT, IN `p_vehiculo_id` INT, IN `p_fecha` DATETIME, IN `p_motivo` TEXT)   BEGIN
    INSERT INTO citas (cliente_id, vehiculo_id, fecha, motivo)
    VALUES (p_cliente_id, p_vehiculo_id, p_fecha, p_motivo);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `registrar_vehiculo` (IN `p_cliente_id` INT, IN `p_marca` VARCHAR(50), IN `p_modelo` VARCHAR(50), IN `p_anio` INT, IN `p_placa` VARCHAR(20))   BEGIN
    INSERT INTO vehiculos (cliente_id, marca, modelo, anio, placa)
    VALUES (p_cliente_id, p_marca, p_modelo, p_anio, p_placa);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alertas_mantenimiento`
--

CREATE TABLE `alertas_mantenimiento` (
  `id` int(11) NOT NULL,
  `vehiculo_id` int(11) DEFAULT NULL,
  `fecha_alerta` datetime DEFAULT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `modelo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alertas_mantenimiento`
--

INSERT INTO `alertas_mantenimiento` (`id`, `vehiculo_id`, `fecha_alerta`, `marca`, `modelo`) VALUES
(10, 2, '2024-12-11 00:00:00', 'mazda', 'Mx5');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `vehiculo_id` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `motivo` text DEFAULT NULL,
  `estado` enum('Programada','Completada','Cancelada') DEFAULT 'Programada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `citas`
--

INSERT INTO `citas` (`id`, `cliente_id`, `vehiculo_id`, `fecha`, `motivo`, `estado`) VALUES
(3, 4, 2, '2024-12-11 13:52:00', 'Recibe vehiculo arreglado', 'Programada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `apellido`, `telefono`, `email`) VALUES
(4, 'juan andres ', 'garcia', '3104324798', 'juanandresgarciavalezuela@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_servicios`
--

CREATE TABLE `historial_servicios` (
  `id` int(11) NOT NULL,
  `servicio_id` int(11) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_servicios`
--

INSERT INTO `historial_servicios` (`id`, `servicio_id`, `nombre`, `descripcion`, `precio`, `fecha`) VALUES
(2, 4, 'Reparacion de vehiculo', 'Cambio de diferencial trasero', 100000.00, '2024-12-10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reparaciones`
--

CREATE TABLE `reparaciones` (
  `id` int(11) NOT NULL,
  `vehiculo_id` int(11) DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `fecha_salida` date DEFAULT NULL,
  `estado` enum('En progreso','Completado','Cancelado') DEFAULT 'En progreso',
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reparaciones`
--

INSERT INTO `reparaciones` (`id`, `vehiculo_id`, `fecha_ingreso`, `fecha_salida`, `estado`, `descripcion`) VALUES
(6, 2, '2024-12-10', '2024-12-11', 'En progreso', 'Cambio de Diferencia Trasero');

--
-- Disparadores `reparaciones`
--
DELIMITER $$
CREATE TRIGGER `alertar_mantenimientos_programados` AFTER UPDATE ON `reparaciones` FOR EACH ROW BEGIN
    IF NEW.estado = 'En progreso' THEN
        -- Eliminar cualquier alerta existente para evitar duplicados
        DELETE FROM alertas_mantenimiento WHERE vehiculo_id = NEW.vehiculo_id;
        -- Insertar nueva alerta con la fecha de salida
        INSERT INTO alertas_mantenimiento (vehiculo_id, marca, modelo, fecha_alerta)
        VALUES (
            NEW.vehiculo_id,
            (SELECT v.marca FROM vehiculos v WHERE v.id = NEW.vehiculo_id),
            (SELECT v.modelo FROM vehiculos v WHERE v.id = NEW.vehiculo_id),
            NEW.fecha_salida
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `eliminar_alertas_mantenimiento` AFTER DELETE ON `reparaciones` FOR EACH ROW BEGIN
    DELETE FROM alertas_mantenimiento WHERE vehiculo_id = OLD.vehiculo_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reparaciones_repuestos`
--

CREATE TABLE `reparaciones_repuestos` (
  `reparacion_id` int(11) NOT NULL,
  `repuesto_id` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `reparaciones_repuestos`
--
DELIMITER $$
CREATE TRIGGER `actualizar_stock_repuestos` AFTER INSERT ON `reparaciones_repuestos` FOR EACH ROW BEGIN
    UPDATE repuestos
    SET stock = stock - NEW.cantidad
    WHERE id = NEW.repuesto_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reparaciones_servicios`
--

CREATE TABLE `reparaciones_servicios` (
  `reparacion_id` int(11) NOT NULL,
  `servicio_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `repuestos`
--

CREATE TABLE `repuestos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `repuestos`
--

INSERT INTO `repuestos` (`id`, `nombre`, `descripcion`, `precio`, `stock`) VALUES
(3, 'Diferencial trasero Autoblocante', 'Diferencial con bloqueo automático para camionetas', 700000.00, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id`, `nombre`, `descripcion`, `precio`) VALUES
(4, 'Reparacion de vehiculo', 'Cambio de diferencial trasero', 100000.00);

--
-- Disparadores `servicios`
--
DELIMITER $$
CREATE TRIGGER `registrar_historial_servicios` AFTER INSERT ON `servicios` FOR EACH ROW BEGIN
    INSERT INTO historial_servicios (servicio_id, nombre, descripcion, precio, fecha)
    VALUES (NEW.id, NEW.nombre, NEW.descripcion, NEW.precio, CURDATE());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

CREATE TABLE `vehiculos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `marca` varchar(50) NOT NULL,
  `modelo` varchar(50) NOT NULL,
  `anio` int(11) DEFAULT NULL,
  `placa` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`id`, `cliente_id`, `marca`, `modelo`, `anio`, `placa`) VALUES
(2, 4, 'mazda', 'Mx5', 1991, 'lil 666');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alertas_mantenimiento`
--
ALTER TABLE `alertas_mantenimiento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alertas_mantenimiento_ibfk_1` (`vehiculo_id`);

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `vehiculo_id` (`vehiculo_id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `historial_servicios`
--
ALTER TABLE `historial_servicios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `historial_servicios_ibfk_1` (`servicio_id`);

--
-- Indices de la tabla `reparaciones`
--
ALTER TABLE `reparaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehiculo_id` (`vehiculo_id`);

--
-- Indices de la tabla `reparaciones_repuestos`
--
ALTER TABLE `reparaciones_repuestos`
  ADD PRIMARY KEY (`reparacion_id`,`repuesto_id`),
  ADD KEY `repuesto_id` (`repuesto_id`);

--
-- Indices de la tabla `reparaciones_servicios`
--
ALTER TABLE `reparaciones_servicios`
  ADD PRIMARY KEY (`reparacion_id`,`servicio_id`),
  ADD KEY `servicio_id` (`servicio_id`);

--
-- Indices de la tabla `repuestos`
--
ALTER TABLE `repuestos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `placa` (`placa`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alertas_mantenimiento`
--
ALTER TABLE `alertas_mantenimiento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `historial_servicios`
--
ALTER TABLE `historial_servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `reparaciones`
--
ALTER TABLE `reparaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `repuestos`
--
ALTER TABLE `repuestos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alertas_mantenimiento`
--
ALTER TABLE `alertas_mantenimiento`
  ADD CONSTRAINT `alertas_mantenimiento_ibfk_1` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `citas_ibfk_2` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculos` (`id`);

--
-- Filtros para la tabla `historial_servicios`
--
ALTER TABLE `historial_servicios`
  ADD CONSTRAINT `historial_servicios_ibfk_1` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `reparaciones`
--
ALTER TABLE `reparaciones`
  ADD CONSTRAINT `reparaciones_ibfk_1` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculos` (`id`);

--
-- Filtros para la tabla `reparaciones_repuestos`
--
ALTER TABLE `reparaciones_repuestos`
  ADD CONSTRAINT `reparaciones_repuestos_ibfk_1` FOREIGN KEY (`reparacion_id`) REFERENCES `reparaciones` (`id`),
  ADD CONSTRAINT `reparaciones_repuestos_ibfk_2` FOREIGN KEY (`repuesto_id`) REFERENCES `repuestos` (`id`);

--
-- Filtros para la tabla `reparaciones_servicios`
--
ALTER TABLE `reparaciones_servicios`
  ADD CONSTRAINT `reparaciones_servicios_ibfk_1` FOREIGN KEY (`reparacion_id`) REFERENCES `reparaciones` (`id`),
  ADD CONSTRAINT `reparaciones_servicios_ibfk_2` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`id`);

--
-- Filtros para la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD CONSTRAINT `vehiculos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`);

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`root`@`localhost` EVENT `recordatorios_mantenimiento` ON SCHEDULE EVERY 30 SECOND STARTS '2024-12-10 04:05:20' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    INSERT INTO notificaciones (cliente_id, mensaje)
    SELECT c.id, CONCAT('Recordatorio: Su vehículo necesita mantenimiento el ', DATE_FORMAT(am.fecha_alerta, '%d/%m/%Y'))
    FROM alertas_mantenimiento am
    JOIN vehiculos v ON am.vehiculo_id = v.id
    JOIN clientes c ON v.cliente_id = c.id
    WHERE am.fecha_alerta = DATE_ADD(CURDATE(), INTERVAL 7 DAY);
END$$

CREATE DEFINER=`root`@`localhost` EVENT `actualizar_estado_reparaciones` ON SCHEDULE EVERY 1 MINUTE STARTS '2024-12-10 04:05:22' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    UPDATE reparaciones
    SET estado = 'Completado'
    WHERE fecha_salida <= CURDATE() AND estado = 'En progreso';
END$$

CREATE DEFINER=`root`@`localhost` EVENT `reporte_diario_servicios` ON SCHEDULE EVERY 1 MINUTE STARTS '2024-12-10 04:06:22' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    INSERT INTO reportes_diarios (fecha, total_servicios, total_ingresos)
    SELECT 
        CURDATE() - INTERVAL 1 MINUTE,
        COUNT(*),
        SUM(s.precio)
    FROM reparaciones_servicios rs
    JOIN servicios s ON rs.servicio_id = s.id
    JOIN reparaciones r ON rs.reparacion_id = r.id
    WHERE DATE(r.fecha_salida) = CURDATE() - INTERVAL 1 DAY;
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
