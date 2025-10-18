-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-10-2025 a las 02:40:35
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
-- Base de datos: `rest_bar`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_AddProduct` (IN `p_nombre` VARCHAR(100), IN `p_precio_costo` DECIMAL(10,2), IN `p_precio_venta` DECIMAL(10,2), IN `p_id_categoria` INT, IN `p_stock` INT)   BEGIN
    INSERT INTO productos (Nombre_Producto, Precio_Costo, Precio_Venta, ID_Categoria, Stock)
    VALUES (p_nombre, p_precio_costo, p_precio_venta, p_id_categoria, p_stock);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_AddSaleDetail` (IN `p_id_venta` INT, IN `p_id_producto` INT, IN `p_cantidad` INT, IN `p_precio` DECIMAL(10,2))   BEGIN
    DECLARE detalle_id INT;
    SELECT ID_Detalle INTO detalle_id
    FROM detalle_venta
    WHERE ID_Venta = p_id_venta AND ID_Producto = p_id_producto
    LIMIT 1;

    IF detalle_id IS NOT NULL THEN
        UPDATE detalle_venta
        SET Cantidad = Cantidad + p_cantidad
        WHERE ID_Detalle = detalle_id;
        -- Corregido: ahora el subtotal es la cantidad actualizada * precio
        UPDATE detalle_venta
        SET Subtotal = Cantidad * p_precio
        WHERE ID_Detalle = detalle_id;
    ELSE
        INSERT INTO detalle_venta (ID_Venta, ID_Producto, Cantidad, Precio_Venta, Subtotal)
        VALUES (p_id_venta, p_id_producto, p_cantidad, p_precio, p_cantidad * p_precio);
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_CheckUserExists` (IN `p_NombreUsuario` VARCHAR(30), OUT `p_Exists` BOOLEAN)   BEGIN
    SELECT EXISTS(
        SELECT 1 FROM usuarios 
        WHERE Nombre_Usuario = p_NombreUsuario
    ) INTO p_Exists;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_CreateCategory` (IN `p_Nombre_Categoria` VARCHAR(50))   begin
	insert into categorias(Nombre_Categoria) values(p_Nombre_Categoria);
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_CreateSale` (IN `p_idCliente` INT, IN `p_idMesa` INT, IN `p_metodoPago` VARCHAR(50), IN `p_idEmpleado` INT)   BEGIN
    INSERT INTO ventas (ID_Cliente, ID_Mesa, Metodo_Pago, ID_Empleado, Estado, Fecha_Hora)
    VALUES (p_idCliente, p_idMesa, p_metodoPago, p_idEmpleado, 'Pendiente', NOW());
    SELECT LAST_INSERT_ID() AS ID_Venta;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_CreateUserWithEmployee` (IN `p_NombreUsuario` VARCHAR(30), IN `p_Contrasenia` VARCHAR(255), IN `p_IDRol` INT, IN `p_NombreCompleto` VARCHAR(100), IN `p_Correo` VARCHAR(100), IN `p_Telefono` VARCHAR(15), IN `p_FechaContratacion` DATE)   BEGIN
    DECLARE v_IDUsuario INT;
    
    START TRANSACTION;
    
    INSERT INTO usuarios (Nombre_Usuario, Contrasenia, ID_Rol)
    VALUES (p_NombreUsuario, p_Contrasenia, p_IDRol);
    
    SET v_IDUsuario = LAST_INSERT_ID();
        
    INSERT INTO empleados (
        Nombre_Completo,
        Correo,
        Telefono,
        Fecha_Contratacion,
        ID_Usuario
    )
    VALUES (
        p_NombreCompleto,
        p_Correo,
        p_Telefono,
        p_FechaContratacion,
        v_IDUsuario
    );
    
    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_DeleteEmployee` (IN `p_ID_Empleado` INT)   BEGIN
    START TRANSACTION;
    
    DELETE u FROM usuarios u
    INNER JOIN empleados e ON u.ID_Usuario = e.ID_Usuario
    WHERE e.ID_Empleado = p_ID_Empleado;
    
    DELETE FROM empleados WHERE ID_Empleado = p_ID_Empleado;
    
    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_GetAllCategories` ()   begin
select * from categorias;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_GetAllEmployees` ()   BEGIN
    SELECT e.ID_Empleado, e.ID_Usuario, e.Nombre_Completo, e.Telefono, e.Correo, u.Nombre_Usuario,u.ID_Rol
    FROM empleados e
    JOIN usuarios u ON e.ID_Usuario = u.ID_usuario;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_GetAllProducts` ()   BEGIN
    SELECT p.*, c.Nombre_Categoria 
    FROM productos p
    INNER JOIN categorias c ON p.ID_Categoria = c.ID_Categoria
    ORDER BY p.Nombre_Producto;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_GetAllRols` ()   BEGIN
	Select * from roles;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_GetAllTables` ()   BEGIN
    SELECT * FROM mesas ORDER BY Numero_Mesa;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_GetDailySales` (IN `p_fecha` DATE)   BEGIN
    SELECT v.*, c.Nombre_Cliente, e.Nombre_Completo as Empleado
    FROM ventas v
    INNER JOIN clientes c ON v.ID_Cliente = c.ID_Cliente
    INNER JOIN empleados e ON v.ID_Empleado = e.ID_Empleado
    WHERE DATE(v.Fecha_Hora) = p_fecha;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_GetDashboardStats` ()   BEGIN
    SELECT 
        (SELECT COUNT(*) FROM mesas WHERE Estado = 1) as MesasOcupadas,
        (SELECT COUNT(*) FROM mesas) as TotalMesas,
        (SELECT COALESCE(SUM(Total), 0) FROM ventas WHERE DATE(Fecha_Hora) = CURDATE()) as VentasDiarias,
        (SELECT COUNT(*) FROM ventas WHERE DATE(Fecha_Hora) = CURDATE()) as OrdenesHoy;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_GetEmployeeById` (IN `p_ID_Usuario` INT)   BEGIN
    SELECT e.*, u.Nombre_Usuario ,u.ID_Rol
    FROM empleados e
    JOIN usuarios u ON e.ID_Usuario = u.ID_usuario  -- Ensure column names match the schema
    WHERE e.ID_Usuario = p_ID_Usuario;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_GetSaleDetails` (IN `p_id_venta` INT)   BEGIN
    SELECT d.*, p.Nombre_Producto
    FROM detalle_venta d
    INNER JOIN productos p ON d.ID_Producto = p.ID_Producto
    WHERE d.ID_Venta = p_id_venta;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_UpdateEmployee` (IN `p_ID_Empleado` INT, IN `p_Nombre_Completo` VARCHAR(100), IN `p_Correo` VARCHAR(100), IN `p_Telefono` VARCHAR(15), IN `p_ID_Usuario` INT, IN `p_Nombre_Usuario` VARCHAR(30), IN `p_ID_Rol` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error al actualizar empleado y usuario';
    END;

    START TRANSACTION;

    -- Actualizar tabla empleados
    UPDATE empleados
    SET
        Nombre_Completo = p_Nombre_Completo,
        Correo = p_Correo,
        Telefono = p_Telefono,
        ID_Usuario = p_ID_Usuario
    WHERE
        ID_Empleado = p_ID_Empleado;

    -- Actualizar tabla usuarios
    UPDATE usuarios
    SET
        Nombre_Usuario = p_Nombre_Usuario,
        ID_Rol = p_ID_Rol
    WHERE
        ID_usuario = p_ID_Usuario;

    COMMIT;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_UpdateProduct` (IN `p_id_producto` INT, IN `p_nombre` VARCHAR(100), IN `p_precio_costo` DECIMAL(10,2), IN `p_precio_venta` DECIMAL(10,2), IN `p_id_categoria` INT, IN `p_stock` INT)   BEGIN
    UPDATE productos 
    SET Nombre_Producto = p_nombre,
        Precio_Costo = p_precio_costo,
        Precio_Venta = p_precio_venta,
        ID_Categoria = p_id_categoria,
        Stock = p_stock
    WHERE ID_Producto = p_id_producto;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_UpdateTableStatus` (IN `p_id_mesa` INT, IN `p_estado` BOOLEAN)   BEGIN
    UPDATE mesas SET Estado = p_estado WHERE ID_Mesa = p_id_mesa;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_UpdateUser` (IN `p_user_id` INT, IN `p_username` VARCHAR(30), IN `p_password` VARCHAR(255), IN `p_role_id` INT)   BEGIN
    IF p_password IS NOT NULL THEN
        UPDATE usuarios 
        SET Nombre_Usuario = p_username,
            Contrasenia = p_password,
            ID_Rol = p_role_id
        WHERE ID_Usuario = p_user_id;
    ELSE
        UPDATE usuarios 
        SET Nombre_Usuario = p_username,
            ID_Rol = p_role_id
        WHERE ID_Usuario = p_user_id;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ValidateUser` (IN `p_username` VARCHAR(30))   BEGIN
    SELECT 
        u.ID_Usuario,
        u.Nombre_Usuario,
        u.Contrasenia,
        r.Nombre_Rol,
        e.Nombre_Completo
    FROM usuarios u
    INNER JOIN roles r ON u.ID_Rol = r.ID_Rol
    LEFT JOIN empleados e ON u.ID_Usuario = e.ID_Usuario
    WHERE u.Nombre_Usuario = p_username;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `ID_Categoria` int(11) NOT NULL,
  `Nombre_Categoria` varchar(50) NOT NULL,
  `is_food` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`ID_Categoria`, `Nombre_Categoria`, `is_food`) VALUES
(1, 'Bebidas', 0),
(2, 'Cockteles', 1),
(3, 'Licores', 0),
(4, 'Cervezas', 0),
(5, 'Menù a la Carta', 1),
(6, 'Combos Familiares', 1),
(7, 'Hamburguesas', 1),
(8, 'Nachos', 1),
(9, 'Tacos', 1),
(10, 'Alitas', 1),
(11, 'Papas', 1),
(12, 'Extras', 0),
(13, 'Cortes Especiales', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `ID_Cliente` int(11) NOT NULL,
  `Nombre_Cliente` varchar(100) NOT NULL,
  `RUC` varchar(100) DEFAULT NULL,
  `Telefono` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`ID_Cliente`, `Nombre_Cliente`, `RUC`, `Telefono`) VALUES
(1, 'C/F', 'N/A', 'N/A');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config`
--

CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `clave` varchar(50) NOT NULL,
  `valor` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `config`
--

INSERT INTO `config` (`id`, `clave`, `valor`) VALUES
(1, 'nombre_app', 'Uy Que Rico'),
(2, 'usar_impresora_cocina', '1'),
(3, 'impresora_cocina', 'POSPrinter POS-80'),
(4, 'usar_impresora_barra', '1'),
(5, 'impresora_barra', 'AON Printer'),
(6, 'moneda', 'C$'),
(7, 'IVA', '0'),
(8, 'impresora_ticket', 'AON Printer'),
(9, 'servicio', '0.05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `ID_Detalle` int(11) NOT NULL,
  `ID_Venta` int(11) DEFAULT NULL,
  `ID_Producto` int(11) DEFAULT NULL,
  `Precio_Venta` decimal(10,2) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Subtotal` decimal(10,2) DEFAULT NULL,
  `ID_Parcial` int(11) DEFAULT NULL,
  `Preparacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `ID_Empleado` int(11) NOT NULL,
  `Nombre_Completo` varchar(100) NOT NULL,
  `Correo` varchar(100) DEFAULT NULL,
  `Telefono` varchar(15) DEFAULT NULL,
  `Fecha_Contratacion` date NOT NULL,
  `ID_Usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`ID_Empleado`, `Nombre_Completo`, `Correo`, `Telefono`, `Fecha_Contratacion`, `ID_Usuario`) VALUES
(1, 'Elmer Laguna', '', '', '2025-10-14', 1),
(11, 'Ana Paula', 'apaucenteno@gmail.com', '58366451', '2025-10-15', 11),
(12, 'Lucy Uriarte', 'lucyuriarte93@gmail.es', '58654398', '2025-10-13', 12),
(13, 'Maria Cristina', 'mariacristinamoreirab19@gmail.com', '58803593', '2025-10-13', 13),
(14, 'Yessy Vargas', 'yessyvargas54@gmail.com', '77231151', '2025-10-13', 14),
(15, 'Daniel Aragon', 'jdaa220401@gmail.com', '84511880', '2025-10-13', 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `liberaciones_mesa`
--

CREATE TABLE `liberaciones_mesa` (
  `ID_Liberacion` int(11) NOT NULL,
  `ID_Mesa` int(11) NOT NULL,
  `ID_Usuario` int(11) NOT NULL,
  `Motivo` varchar(100) NOT NULL,
  `Descripcion` text DEFAULT NULL,
  `Fecha_Hora` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `ID_Mesa` int(11) NOT NULL,
  `Numero_Mesa` int(11) NOT NULL,
  `Capacidad` int(11) DEFAULT NULL,
  `Estado` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`ID_Mesa`, `Numero_Mesa`, `Capacidad`, `Estado`) VALUES
(1, 1, 1, 0),
(2, 2, 2, 0),
(3, 3, 2, 0),
(4, 4, 3, 0),
(5, 5, 1, 0),
(6, 6, 1, 0),
(7, 7, 1, 0),
(8, 19, 3, 0),
(9, 8, 4, 0),
(10, 9, 4, 0),
(11, 16, 6, 0),
(12, 17, 6, 0),
(13, 18, 6, 0),
(14, 10, 2, 0),
(15, 11, 2, 0),
(16, 12, 2, 0),
(17, 13, 2, 0),
(18, 14, 6, 0),
(19, 15, 5, 0),
(20, 20, 3, 0),
(21, 21, 2, 0),
(22, 22, 3, 0),
(23, 23, 2, 0),
(24, 24, 4, 0),
(25, 25, 5, 0),
(26, 26, 7, 0),
(27, 27, 4, 0),
(28, 28, 5, 0),
(29, 29, 7, 0),
(30, 30, 2, 0),
(31, 31, 2, 0),
(32, 32, 3, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

CREATE TABLE `movimientos` (
  `ID_Movimiento` int(11) NOT NULL,
  `Fecha_Hora` datetime NOT NULL DEFAULT current_timestamp(),
  `Tipo` varchar(30) NOT NULL,
  `Monto` decimal(10,2) NOT NULL,
  `Descripcion` varchar(255) DEFAULT NULL,
  `ID_Usuario` int(11) DEFAULT NULL,
  `ID_Venta` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parciales_venta`
--

CREATE TABLE `parciales_venta` (
  `ID_Parcial` int(11) NOT NULL,
  `ID_Venta` int(11) NOT NULL,
  `nombre_cliente` varchar(100) DEFAULT NULL,
  `pagado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `ID_Producto` int(11) NOT NULL,
  `Nombre_Producto` varchar(100) NOT NULL,
  `Precio_Costo` decimal(10,2) NOT NULL,
  `Precio_Venta` decimal(10,2) NOT NULL,
  `ID_Categoria` int(11) DEFAULT NULL,
  `Stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`ID_Producto`, `Nombre_Producto`, `Precio_Costo`, `Precio_Venta`, `ID_Categoria`, `Stock`) VALUES
(1, 'Toña', 33.66, 60.00, 4, 70),
(2, 'Fanta Roja', 12.00, 40.00, 1, 50),
(3, 'coca 12 oz', 12.00, 40.00, 1, 24),
(5, 'Hamburguesas clásicas +1 tender de pollo', 1.00, 880.00, 6, 1),
(6, '4 hamburguesas paro cardíaco o the monster', 1.00, 1100.00, 6, 1),
(7, 'Combo Huy Que Rico', 1.00, 820.00, 6, 1),
(8, '16 piezas de alitas', 1.00, 850.00, 6, 1),
(9, '24 piezas de alitas', 1.00, 970.00, 6, 1),
(10, '1 bandeja de nachos gigantes', 1.00, 950.00, 6, 1),
(11, 'Combo chicken wings', 1.00, 750.00, 6, 1),
(12, 'Combo especial', 1.00, 1050.00, 6, 1),
(13, 'Combo the paradise', 1.00, 1250.00, 6, 1),
(14, 'Combo Super Big', 1.00, 1150.00, 6, 1),
(15, 'Papas Fritas', 1.00, 90.00, 12, 1),
(16, 'Salsa BBQ', 1.00, 40.00, 12, 1),
(17, 'Salsa Búfalo', 1.00, 40.00, 12, 1),
(18, 'Salsa Cheddar', 1.00, 40.00, 12, 1),
(19, 'Mayonesa', 1.00, 40.00, 12, 1),
(20, 'Aderezo Ranch', 1.00, 40.00, 12, 1),
(21, 'Salsa de Tomate', 1.00, 40.00, 12, 1),
(22, 'Papas Campesinas', 1.00, 110.00, 12, 1),
(23, 'Classic Burguer', 1.00, 240.00, 7, 1),
(24, 'La Diabla', 1.00, 260.00, 7, 1),
(25, 'Chicken Burguer', 1.00, 240.00, 7, 1),
(26, 'Porky\'s Delicius', 1.00, 300.00, 7, 1),
(27, 'La pecadora', 1.00, 310.00, 7, 1),
(28, 'The Monster Burguer', 1.00, 300.00, 7, 1),
(29, 'Full Bacon', 1.00, 300.00, 7, 1),
(30, 'Paro Cardíaco', 1.00, 330.00, 7, 1),
(31, 'Amazing Burguer', 1.00, 310.00, 7, 1),
(32, 'Bread Chicken Burguer', 1.00, 310.00, 7, 1),
(33, 'Chicken BBQ Bufalo', 1.00, 330.00, 7, 1),
(34, 'La Traviesa', 1.00, 330.00, 7, 1),
(35, 'Triple Chesse', 1.00, 340.00, 7, 1),
(36, 'Super Paro Cardíaco', 1.00, 460.00, 7, 1),
(37, 'Super Monster', 1.00, 340.00, 7, 1),
(38, 'Super Crispy', 1.00, 340.00, 7, 1),
(39, 'Praw Burguer', 1.00, 320.00, 7, 1),
(40, 'Diosa Burguer', 1.00, 430.00, 7, 1),
(41, 'MushRoom Burguer', 1.00, 290.00, 7, 1),
(42, 'Tostón Burguer Mix', 1.00, 330.00, 7, 1),
(43, 'El patron (hot-dog)', 1.00, 250.00, 7, 1),
(44, 'La Chinandegana', 1.00, 430.00, 7, 1),
(45, 'Nachos De Birria', 1.00, 320.00, 8, 1),
(46, 'Alas Virhenes', 1.00, 320.00, 10, 1),
(47, 'Chicken BBQ', 1.00, 300.00, 8, 1),
(48, 'Pullet Pork', 1.00, 310.00, 8, 1),
(49, 'Nachos Mixtos', 1.00, 330.00, 8, 1),
(50, 'Tacos de pollo 3 unidades', 1.00, 270.00, 9, 1),
(51, 'Tacos mixtos 3 unidades', 1.00, 310.00, 9, 1),
(52, 'Birria De Res', 1.00, 290.00, 9, 1),
(53, 'Tacos Al Pastor 3 unidades', 1.00, 350.00, 9, 1),
(54, 'Tacos De Camaron', 1.00, 350.00, 9, 1),
(55, '6 piezas empanizadas Ranch', 1.00, 310.00, 10, 1),
(56, '6 piezas con salsa Búfalo', 1.00, 310.00, 10, 1),
(57, '6 piezas con salsa BBQ', 1.00, 330.00, 10, 1),
(58, '6 piezas con Cheddar', 1.00, 310.00, 10, 1),
(59, '6 piezas con salsas combinadas(BBQ-BUFALO)', 1.00, 380.00, 10, 1),
(60, 'Tender de Pollo', 1.00, 250.00, 10, 1),
(61, 'Tender de Pollo Agrandado', 1.00, 370.00, 10, 1),
(62, 'Papas Gourmets', 1.00, 200.00, 11, 1),
(63, 'Bacon Chesse fries', 1.00, 250.00, 11, 1),
(64, 'Papas Mixtas', 1.00, 310.00, 11, 1),
(65, 'Filete de pollo Plancha o Grill', 1.00, 480.00, 5, 1),
(66, 'Cordon Blue', 1.00, 450.00, 5, 1),
(67, 'Churrasco', 1.00, 520.00, 5, 1),
(68, 'Filete de Res', 1.00, 520.00, 5, 1),
(69, 'Filete de Cerdo', 1.00, 490.00, 5, 1),
(70, 'Costillas de cerdo BBQ', 1.00, 460.00, 5, 1),
(71, 'Costillas de Cerdo frita', 1.00, 450.00, 5, 1),
(72, 'Camarones (Ajillo, Empanizados,Diabla)', 1.00, 460.00, 5, 1),
(73, 'Pescado frito(salsa Tipitapa opcionla)', 1.00, 450.00, 5, 1),
(74, 'Ensalada Cesar', 1.00, 360.00, 5, 1),
(75, 'Pasta Alfredo de Pollo', 1.00, 390.00, 5, 1),
(76, 'Pasta Alfredo de Camarones', 1.00, 420.00, 5, 1),
(77, 'Mar y tierra', 1.00, 670.00, 5, 1),
(78, 'Tomahakw 2.5 libras', 1.00, 970.00, 13, 1),
(79, 'New York Steak', 1.00, 620.00, 13, 1),
(80, 'Puyaso', 1.00, 550.00, 13, 1),
(81, 'Rib Eye', 1.00, 580.00, 13, 1),
(82, 'Canada Dry', 12.00, 40.00, 1, 1),
(83, 'Club Soda', 12.00, 40.00, 1, 1),
(84, 'Coca Zero', 12.00, 40.00, 1, 1),
(85, 'Fanta Naranja', 12.00, 40.00, 1, 1),
(86, 'Fanta Uva', 12.00, 40.00, 1, 1),
(87, 'Rojia', 12.00, 40.00, 1, 1),
(88, 'Jugo del Valle', 12.00, 40.00, 1, 1),
(89, 'Hi-c te', 12.00, 40.00, 1, 1),
(90, 'Hi-c manzana', 12.00, 40.00, 1, 1),
(91, 'Ensa', 12.00, 40.00, 1, 1),
(92, 'Agua purificada', 12.00, 40.00, 1, 1),
(93, 'Agua Gasificada', 15.00, 50.00, 1, 1),
(94, 'Limonada Frozen', 1.00, 120.00, 1, 1),
(95, 'Limonada Natural', 1.00, 100.00, 1, 1),
(96, 'Limonada con Hierba buena', 1.00, 130.00, 1, 1),
(97, 'Limonada con Fresa', 1.00, 130.00, 1, 1),
(98, 'Jamaica Frozen', 1.00, 130.00, 1, 1),
(99, 'Jugos Naturales', 15.00, 90.00, 1, 1),
(100, 'Margarita', 40.00, 160.00, 2, 1),
(101, 'Piña Colada', 1.00, 150.00, 2, 1),
(102, 'Daiquiri', 1.00, 150.00, 2, 1),
(103, 'Sangria', 1.00, 180.00, 2, 1),
(104, 'Arroz', 1.00, 30.00, 12, 1),
(105, 'Tostones', 1.00, 50.00, 12, 1),
(106, 'Papas Campesinas', 1.00, 80.00, 11, 1),
(107, 'Empaques', 1.00, 20.00, 12, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `ID_Rol` int(11) NOT NULL,
  `Nombre_Rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`ID_Rol`, `Nombre_Rol`) VALUES
(1, 'Administrador'),
(2, 'Mesero'),
(3, 'Cajero');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `ID_usuario` int(11) NOT NULL,
  `Nombre_Usuario` varchar(30) DEFAULT NULL,
  `Contrasenia` varchar(255) DEFAULT NULL,
  `ID_Rol` int(11) DEFAULT NULL,
  `Estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`ID_usuario`, `Nombre_Usuario`, `Contrasenia`, `ID_Rol`, `Estado`) VALUES
(1, 'superadmin', '$2y$10$t/mD.Wc7Fo3Ot.nU0Q9XxuLMDHzF4bYM0QZXgBUM0OPITQzL4JSxS', 1, 1),
(11, 'Ana', '$2y$10$.0g00gLwEQL/On6essTfGOPi/EJD4Fod9K066bXcC/a/9Ideeq5r6', 3, 1),
(12, 'Lucy', '$2y$10$kRVWp.BX3gSSJFyMXEidS.AaBFs1A/anYZxwBZgY0SMCtfswNTSoW', 3, 1),
(13, 'cristina', '$2y$10$12tEfh8QCJQvXmxpMGE5m.AmOim0mhobBcJIDngib2RCHip4TrvWC', 2, 1),
(14, 'yessy', '$2y$10$kUdGy53EL4wQ8KFs9XB/oO1rVlWcXeDY9rvaSf5PFoMSmuJaYhIj.', 2, 1),
(15, 'Daniel', '$2y$10$hgAkaYW4jOkw6OuoymBdqu7BD/vWjusLNxbsD1ZSZUQKJtudLQZsi', 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `ID_Venta` int(11) NOT NULL,
  `ID_Cliente` int(11) DEFAULT NULL,
  `ID_Mesa` int(11) DEFAULT NULL,
  `Fecha_Hora` datetime NOT NULL,
  `Total` decimal(10,2) DEFAULT NULL,
  `Metodo_Pago` varchar(200) DEFAULT NULL,
  `ID_Empleado` int(11) DEFAULT NULL,
  `Estado` varchar(20) NOT NULL DEFAULT 'Pendiente',
  `Servicio` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`ID_Categoria`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`ID_Cliente`);

--
-- Indices de la tabla `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`);

--
-- Indices de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`ID_Detalle`),
  ADD KEY `ID_Venta` (`ID_Venta`),
  ADD KEY `ID_Producto` (`ID_Producto`),
  ADD KEY `fk_detalle_parcial` (`ID_Parcial`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`ID_Empleado`),
  ADD UNIQUE KEY `Correo` (`Correo`),
  ADD KEY `ID_Usuario` (`ID_Usuario`);

--
-- Indices de la tabla `liberaciones_mesa`
--
ALTER TABLE `liberaciones_mesa`
  ADD PRIMARY KEY (`ID_Liberacion`),
  ADD KEY `ID_Mesa` (`ID_Mesa`),
  ADD KEY `ID_Usuario` (`ID_Usuario`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`ID_Mesa`);

--
-- Indices de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`ID_Movimiento`),
  ADD KEY `ID_Usuario` (`ID_Usuario`),
  ADD KEY `ID_Venta` (`ID_Venta`);

--
-- Indices de la tabla `parciales_venta`
--
ALTER TABLE `parciales_venta`
  ADD PRIMARY KEY (`ID_Parcial`),
  ADD KEY `ID_Venta` (`ID_Venta`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`ID_Producto`),
  ADD KEY `ID_Categoria` (`ID_Categoria`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`ID_Rol`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`ID_usuario`),
  ADD UNIQUE KEY `Nombre_Usuario_UNIQUE` (`Nombre_Usuario`),
  ADD KEY `ID_Rol` (`ID_Rol`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`ID_Venta`),
  ADD KEY `ID_Cliente` (`ID_Cliente`),
  ADD KEY `ID_Mesa` (`ID_Mesa`),
  ADD KEY `ID_Empleado` (`ID_Empleado`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `ID_Categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `ID_Cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `ID_Detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `ID_Empleado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `liberaciones_mesa`
--
ALTER TABLE `liberaciones_mesa`
  MODIFY `ID_Liberacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `ID_Mesa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `ID_Movimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `parciales_venta`
--
ALTER TABLE `parciales_venta`
  MODIFY `ID_Parcial` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `ID_Producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `ID_Rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `ID_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `ID_Venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD CONSTRAINT `detalle_venta_ibfk_1` FOREIGN KEY (`ID_Venta`) REFERENCES `ventas` (`ID_Venta`),
  ADD CONSTRAINT `detalle_venta_ibfk_2` FOREIGN KEY (`ID_Producto`) REFERENCES `productos` (`ID_Producto`),
  ADD CONSTRAINT `fk_detalle_parcial` FOREIGN KEY (`ID_Parcial`) REFERENCES `parciales_venta` (`ID_Parcial`);

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuarios` (`ID_usuario`);

--
-- Filtros para la tabla `liberaciones_mesa`
--
ALTER TABLE `liberaciones_mesa`
  ADD CONSTRAINT `liberaciones_mesa_ibfk_1` FOREIGN KEY (`ID_Mesa`) REFERENCES `mesas` (`ID_Mesa`),
  ADD CONSTRAINT `liberaciones_mesa_ibfk_2` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuarios` (`ID_usuario`);

--
-- Filtros para la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD CONSTRAINT `movimientos_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuarios` (`ID_usuario`),
  ADD CONSTRAINT `movimientos_ibfk_2` FOREIGN KEY (`ID_Venta`) REFERENCES `ventas` (`ID_Venta`);

--
-- Filtros para la tabla `parciales_venta`
--
ALTER TABLE `parciales_venta`
  ADD CONSTRAINT `parciales_venta_ibfk_1` FOREIGN KEY (`ID_Venta`) REFERENCES `ventas` (`ID_Venta`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`ID_Categoria`) REFERENCES `categorias` (`ID_Categoria`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`ID_Rol`) REFERENCES `roles` (`ID_Rol`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`ID_Cliente`) REFERENCES `clientes` (`ID_Cliente`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`ID_Mesa`) REFERENCES `mesas` (`ID_Mesa`),
  ADD CONSTRAINT `ventas_ibfk_3` FOREIGN KEY (`ID_Empleado`) REFERENCES `empleados` (`ID_Empleado`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
