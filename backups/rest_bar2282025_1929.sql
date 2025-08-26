CREATE DATABASE  IF NOT EXISTS `rest_bar`;
USE `rest_bar`;
CREATE TABLE `categorias` (
  `ID_Categoria` int NOT NULL AUTO_INCREMENT,
  `Nombre_Categoria` varchar(50) NOT NULL,
  PRIMARY KEY (`ID_Categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `categorias` VALUES (1,'Bebidas'),(2,'buffet'),(3,'Carnes'),(4,'sopas');

CREATE TABLE `clientes` (
  `ID_Cliente` int NOT NULL AUTO_INCREMENT,
  `Nombre_Cliente` varchar(100) NOT NULL,
  `RUC` varchar(100) DEFAULT NULL,
  `Telefono` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`ID_Cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
INSERT INTO `clientes` VALUES (1,'C/F','N/A','N/A');


CREATE TABLE `mesas` (
  `ID_Mesa` int NOT NULL AUTO_INCREMENT,
  `Numero_Mesa` int NOT NULL,
  `Capacidad` int DEFAULT NULL,
  `Estado` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID_Mesa`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `mesas` VALUES (1,1,4,0),(2,2,2,0),(3,5,3,0),(4,4,1,1),(5,6,8,0);

CREATE TABLE `productos` (
  `ID_Producto` int NOT NULL AUTO_INCREMENT,
  `Nombre_Producto` varchar(100) NOT NULL,
  `Precio_Costo` decimal(10,2) NOT NULL,
  `Precio_Venta` decimal(10,2) NOT NULL,
  `ID_Categoria` int DEFAULT NULL,
  `Stock` int NOT NULL,
  PRIMARY KEY (`ID_Producto`),
  KEY `ID_Categoria` (`ID_Categoria`),
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`ID_Categoria`) REFERENCES `categorias` (`ID_Categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `productos` VALUES (1,'coca 12 oz',10.00,42.00,1,45),(2,'fresca',10.00,40.00,1,20),(3,'rojita',10.00,40.00,1,45),(4,'canada club soda',15.00,40.00,1,45),(5,'Ribeye',200.00,560.00,3,11),(6,'Res con punches',100.00,270.00,4,25),(7,'chelada soda',45.00,80.00,1,10),(8,'mix chelada',40.00,40.00,1,10),(9,'sopa de gallina',100.00,250.00,4,25),(10,'Ribeye',20.00,520.00,3,30),(11,'carne de res',120.00,290.00,2,30);

CREATE TABLE `roles` (
  `ID_Rol` int NOT NULL AUTO_INCREMENT,
  `Nombre_Rol` varchar(50) NOT NULL,
  PRIMARY KEY (`ID_Rol`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `roles` VALUES (1,'Administrador'),(2,'Mesero'),(3,'Cajero');

CREATE TABLE `usuarios` (
  `ID_usuario` int NOT NULL AUTO_INCREMENT,
  `Nombre_Usuario` varchar(30) DEFAULT NULL,
  `Contrasenia` varchar(255) DEFAULT NULL,
  `ID_Rol` int DEFAULT NULL,
  `Estado` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`ID_usuario`),
  UNIQUE KEY `Nombre_Usuario_UNIQUE` (`Nombre_Usuario`),
  KEY `ID_Rol` (`ID_Rol`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`ID_Rol`) REFERENCES `roles` (`ID_Rol`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `usuarios` VALUES (1,'superadmin','$2y$10$t/mD.Wc7Fo3Ot.nU0Q9XxuLMDHzF4bYM0QZXgBUM0OPITQzL4JSxS',1,1),
(2,'elaguna','$2y$10$NiN5s3BGkBuQqwkeX1TjiOL1PBXSeLmrX6EYavDYW8B3zhCWdyg.u',3,1),
(3,'eojeda','$2y$10$6V0h8ioF855oKKRMhkJMPu/WmyAvnvokAGzvr0.EDABWLFGztU6Pe',2,1),
(4,'kramos','$2y$10$mIFjirasRfhBIZ3txBUL5OnBoF40nbMjaH1AmM79I1M8RRJ71dBza',3,1),
(5,'xchevez','$2y$10$8q/QVWblz1kOyDnDhc0yDOyg2DsOKcbTcgCwquyd/oLtbKwihqQgO',1,1),
(6,'jarredondo','$2y$10$SCPMsHuh7XqoFuFkOw1DHO/VmqiyVcSjRfMcoqscspw3t37ZQ/25m',3,1);

CREATE TABLE `empleados` (
  `ID_Empleado` int NOT NULL AUTO_INCREMENT,
  `Nombre_Completo` varchar(100) NOT NULL,
  `Correo` varchar(100) DEFAULT NULL,
  `Telefono` varchar(15) DEFAULT NULL,
  `Fecha_Contratacion` date NOT NULL,
  `ID_Usuario` int DEFAULT NULL,
  PRIMARY KEY (`ID_Empleado`),
  UNIQUE KEY `Correo` (`Correo`),
  KEY `ID_Usuario` (`ID_Usuario`),
  CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuarios` (`ID_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `empleados` VALUES (6,'Elmer Laguna','elmer@gmail.com','12345678','2025-03-18',1),(7,'Elvira Ojeda','elvira@gmail.com','12345678','2025-03-18',9),(8,'Katy Ramos','kramos@gmail.com','85987484','2025-03-18',10),(10,'Xiomara Chévez','xiomara@gmail.com','85988748','2025-03-18',12),(13,'Jose Agustin Arredondo','josearredondo@gmail.com','12346578','2025-03-18',15),(15,'elmer','elmer113.com','123456789','2025-07-14',15);
CREATE TABLE `ventas` (
  `ID_Venta` int NOT NULL AUTO_INCREMENT,
  `ID_Cliente` int DEFAULT NULL,
  `ID_Mesa` int DEFAULT NULL,
  `Fecha_Hora` datetime NOT NULL,
  `Total` decimal(10,2) DEFAULT NULL,
  `Metodo_Pago` varchar(20) DEFAULT NULL,
  `ID_Empleado` int DEFAULT NULL,
  `Estado` varchar(20) NOT NULL DEFAULT 'Pendiente',
  PRIMARY KEY (`ID_Venta`),
  KEY `ID_Cliente` (`ID_Cliente`),
  KEY `ID_Mesa` (`ID_Mesa`),
  KEY `ID_Empleado` (`ID_Empleado`),
  CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`ID_Cliente`) REFERENCES `clientes` (`ID_Cliente`),
  CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`ID_Mesa`) REFERENCES `mesas` (`ID_Mesa`),
  CONSTRAINT `ventas_ibfk_3` FOREIGN KEY (`ID_Empleado`) REFERENCES `empleados` (`ID_Empleado`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
CREATE TABLE `detalle_venta` (
  `ID_Detalle` int NOT NULL AUTO_INCREMENT,
  `ID_Venta` int DEFAULT NULL,
  `ID_Producto` int DEFAULT NULL,
  `Precio_Venta` decimal(10,2) NOT NULL,
  `Cantidad` int NOT NULL,
  `Subtotal` decimal(10,2) DEFAULT NULL,
  `Estado` enum('pendiente','preparando','preparado','entregado','cancelado') DEFAULT 'pendiente',
  PRIMARY KEY (`ID_Detalle`),
  KEY `ID_Venta` (`ID_Venta`),
  KEY `ID_Producto` (`ID_Producto`),
  CONSTRAINT `detalle_venta_ibfk_1` FOREIGN KEY (`ID_Venta`) REFERENCES `ventas` (`ID_Venta`),
  CONSTRAINT `detalle_venta_ibfk_2` FOREIGN KEY (`ID_Producto`) REFERENCES `productos` (`ID_Producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_AddProduct`(
    IN p_nombre VARCHAR(100),
    IN p_precio_costo DECIMAL(10,2),
    IN p_precio_venta DECIMAL(10,2),
    IN p_id_categoria INT,
    IN p_stock INT
)
BEGIN
    INSERT INTO productos (Nombre_Producto, Precio_Costo, Precio_Venta, ID_Categoria, Stock)
    VALUES (p_nombre, p_precio_costo, p_precio_venta, p_id_categoria, p_stock);
END ;;
DELIMITER ;

DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_AddSaleDetail`(
    IN p_idVenta INT,
    IN p_idProducto INT,
    IN p_cantidad INT,
    IN p_precioVenta DECIMAL(10,2)
)
BEGIN
    INSERT INTO detalle_venta (ID_Venta, ID_Producto, Cantidad, Precio_Venta, Subtotal)
    VALUES (p_idVenta, p_idProducto, p_cantidad, p_precioVenta, p_cantidad * p_precioVenta);
END ;;
DELIMITER ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_CheckUserExists`(
    IN p_NombreUsuario VARCHAR(30),
    OUT p_Exists BOOLEAN
)
BEGIN
    SELECT EXISTS(
        SELECT 1 FROM usuarios 
        WHERE Nombre_Usuario = p_NombreUsuario
    ) INTO p_Exists;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_CreateCategory`(in p_Nombre_Categoria varchar(50))
begin
	insert into categorias(Nombre_Categoria) values(p_Nombre_Categoria);
end ;;
DELIMITER ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_CreateSale`(
    IN p_idCliente INT,
    IN p_idMesa INT,
    IN p_metodoPago VARCHAR(50),
    IN p_idEmpleado INT
)
BEGIN
    INSERT INTO ventas (ID_Cliente, ID_Mesa, Metodo_Pago, ID_Empleado, Estado, Fecha_Hora)
    VALUES (p_idCliente, p_idMesa, p_metodoPago, p_idEmpleado, 'Pendiente', NOW());
    SELECT LAST_INSERT_ID() AS ID_Venta;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_CreateUserWithEmployee`(
    IN p_NombreUsuario VARCHAR(30),
    IN p_Contrasenia VARCHAR(255),
    IN p_IDRol INT,
    IN p_NombreCompleto VARCHAR(100),
    IN p_Correo VARCHAR(100),
    IN p_Telefono VARCHAR(15),
    IN p_FechaContratacion DATE
)
BEGIN
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
END ;;
DELIMITER ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_DeleteEmployee`(IN p_ID_Empleado INT)
BEGIN
    START TRANSACTION;
    
    DELETE u FROM usuarios u
    INNER JOIN empleados e ON u.ID_Usuario = e.ID_Usuario
    WHERE e.ID_Empleado = p_ID_Empleado;
    
    DELETE FROM empleados WHERE ID_Empleado = p_ID_Empleado;
    
    COMMIT;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_GetAllCategories`()
begin
select * from categorias;
end ;;
DELIMITER ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_GetAllEmployees`()
BEGIN
    SELECT e.ID_Empleado, e.ID_Usuario, e.Nombre_Completo, e.Telefono, e.Correo, u.Nombre_Usuario,u.ID_Rol
    FROM empleados e
    JOIN usuarios u ON e.ID_Usuario = u.ID_usuario;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_GetAllProducts`()
BEGIN
    SELECT p.*, c.Nombre_Categoria 
    FROM productos p
    INNER JOIN categorias c ON p.ID_Categoria = c.ID_Categoria
    ORDER BY p.Nombre_Producto;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_GetAllRols`()
BEGIN
	Select * from roles;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_GetAllTables`()
BEGIN
    SELECT * FROM mesas ORDER BY Numero_Mesa;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_GetDailySales`(
    IN p_fecha DATE
)
BEGIN
    SELECT v.*, c.Nombre_Cliente, e.Nombre_Completo as Empleado
    FROM ventas v
    INNER JOIN clientes c ON v.ID_Cliente = c.ID_Cliente
    INNER JOIN empleados e ON v.ID_Empleado = e.ID_Empleado
    WHERE DATE(v.Fecha_Hora) = p_fecha;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_GetDashboardStats`()
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM mesas WHERE Estado = 1) as MesasOcupadas,
        (SELECT COUNT(*) FROM mesas) as TotalMesas,
        (SELECT COALESCE(SUM(Total), 0) FROM ventas WHERE DATE(Fecha_Hora) = CURDATE()) as VentasDiarias,
        (SELECT COUNT(*) FROM ventas WHERE DATE(Fecha_Hora) = CURDATE()) as OrdenesHoy;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_GetEmployeeById`(IN p_ID_Usuario INT)
BEGIN
    SELECT e.*, u.Nombre_Usuario ,u.ID_Rol
    FROM empleados e
    JOIN usuarios u ON e.ID_Usuario = u.ID_usuario  -- Ensure column names match the schema
    WHERE e.ID_Usuario = p_ID_Usuario;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_GetSaleDetails`(
    IN p_id_venta INT
)
BEGIN
    SELECT d.*, p.Nombre_Producto
    FROM detalle_venta d
    INNER JOIN productos p ON d.ID_Producto = p.ID_Producto
    WHERE d.ID_Venta = p_id_venta;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_UpdateEmployee`(
    IN p_ID_Empleado INT,
    IN p_Nombre_Completo VARCHAR(100),
    IN p_Correo VARCHAR(100),
    IN p_Telefono VARCHAR(15),
    IN p_ID_Usuario INT,
    IN p_Nombre_Usuario VARCHAR(30),
    IN p_ID_Rol INT
)
BEGIN
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

END ;;
DELIMITER ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_UpdateProduct`(
    IN p_id_producto INT,
    IN p_nombre VARCHAR(100),
    IN p_precio_costo DECIMAL(10,2),
    IN p_precio_venta DECIMAL(10,2),
    IN p_id_categoria INT,
    IN p_stock INT
)
BEGIN
    UPDATE productos 
    SET Nombre_Producto = p_nombre,
        Precio_Costo = p_precio_costo,
        Precio_Venta = p_precio_venta,
        ID_Categoria = p_id_categoria,
        Stock = p_stock
    WHERE ID_Producto = p_id_producto;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_UpdateTableStatus`(
    IN p_id_mesa INT,
    IN p_estado BOOLEAN
)
BEGIN
    UPDATE mesas SET Estado = p_estado WHERE ID_Mesa = p_id_mesa;
END ;;
DELIMITER ;
/DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_UpdateUser`(
    IN p_user_id INT,
    IN p_username VARCHAR(30),
    IN p_password VARCHAR(255),
    IN p_role_id INT
)
BEGIN
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
END ;;
DELIMITER ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_ValidateUser`(IN p_username VARCHAR(30))
BEGIN
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
END ;;
DELIMITER ;