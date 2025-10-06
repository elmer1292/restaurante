CREATE DATABASE  IF NOT EXISTS `rest_bar` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `rest_bar`;
-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: localhost    Database: rest_bar
-- ------------------------------------------------------
-- Server version	8.0.43

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `ID_Categoria` int NOT NULL AUTO_INCREMENT,
  `Nombre_Categoria` varchar(50) NOT NULL,
  PRIMARY KEY (`ID_Categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Bebidas'),(2,'buffet'),(3,'Carnes'),(4,'sopas');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `ID_Cliente` int NOT NULL AUTO_INCREMENT,
  `Nombre_Cliente` varchar(100) NOT NULL,
  `RUC` varchar(100) DEFAULT NULL,
  `Telefono` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`ID_Cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,'C/F','N/A','N/A');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `config` (
  `id` int NOT NULL AUTO_INCREMENT,
  `clave` varchar(50) NOT NULL,
  `valor` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clave` (`clave`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config`
--

LOCK TABLES `config` WRITE;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
INSERT INTO `config` VALUES (1,'nombre_app','Rincon Chinandegano'),(2,'usar_impresora_cocina','1'),(3,'impresora_cocina','AON Printer'),(4,'usar_impresora_barra','0'),(5,'impresora_barra',''),(6,'moneda','C$'),(7,'IVA','12%'),(8,'impresora_ticket','AON Printer'),(9,'servicio','0.05');
/*!40000 ALTER TABLE `config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_venta`
--

DROP TABLE IF EXISTS `detalle_venta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_venta` (
  `ID_Detalle` int NOT NULL AUTO_INCREMENT,
  `ID_Venta` int DEFAULT NULL,
  `ID_Producto` int DEFAULT NULL,
  `Precio_Venta` decimal(10,2) NOT NULL,
  `Cantidad` int NOT NULL,
  `Subtotal` decimal(10,2) DEFAULT NULL,
  `ID_Parcial` int DEFAULT NULL,
  PRIMARY KEY (`ID_Detalle`),
  KEY `ID_Venta` (`ID_Venta`),
  KEY `ID_Producto` (`ID_Producto`),
  KEY `fk_detalle_parcial` (`ID_Parcial`),
  CONSTRAINT `detalle_venta_ibfk_1` FOREIGN KEY (`ID_Venta`) REFERENCES `ventas` (`ID_Venta`),
  CONSTRAINT `detalle_venta_ibfk_2` FOREIGN KEY (`ID_Producto`) REFERENCES `productos` (`ID_Producto`),
  CONSTRAINT `fk_detalle_parcial` FOREIGN KEY (`ID_Parcial`) REFERENCES `parciales_venta` (`ID_Parcial`)
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_venta`
--

LOCK TABLES `detalle_venta` WRITE;
/*!40000 ALTER TABLE `detalle_venta` DISABLE KEYS */;
INSERT INTO `detalle_venta` VALUES (1,48,1,42.00,1,126.00,NULL),(2,49,1,42.00,3,126.00,NULL),(4,48,11,290.00,1,290.00,NULL),(9,48,11,290.00,1,290.00,NULL),(10,48,5,560.00,1,560.00,NULL),(11,48,10,520.00,2,1040.00,NULL),(12,48,2,40.00,3,120.00,NULL),(13,48,1,42.00,1,42.00,NULL),(14,48,6,270.00,1,270.00,NULL),(15,48,6,270.00,1,270.00,NULL),(17,51,9,250.00,1,250.00,NULL),(18,51,4,40.00,1,40.00,NULL),(19,51,11,290.00,1,290.00,NULL),(21,50,4,40.00,4,280.00,NULL),(22,50,7,80.00,1,80.00,NULL),(23,50,6,270.00,1,270.00,NULL),(24,50,5,560.00,1,560.00,NULL),(25,52,11,290.00,1,290.00,NULL),(26,52,5,560.00,1,560.00,NULL),(32,54,1,40.00,1,40.00,NULL),(53,62,7,80.00,1,80.00,NULL),(54,62,3,40.00,1,40.00,NULL),(55,62,11,290.00,1,290.00,NULL),(56,62,6,270.00,1,270.00,NULL),(57,62,9,250.00,1,250.00,NULL),(58,63,7,80.00,1,80.00,NULL),(59,63,11,290.00,1,290.00,NULL),(60,64,2,40.00,1,40.00,NULL),(61,64,6,270.00,1,270.00,NULL),(62,65,6,270.00,4,1080.00,NULL),(63,65,11,290.00,1,290.00,NULL),(64,65,5,560.00,1,560.00,NULL),(65,66,12,30.00,1,30.00,NULL),(66,66,11,290.00,1,290.00,NULL),(67,67,11,290.00,1,290.00,NULL),(68,67,6,270.00,1,270.00,NULL),(69,67,12,30.00,1,30.00,NULL),(70,68,11,290.00,1,290.00,NULL),(71,68,6,270.00,1,270.00,NULL),(72,68,2,40.00,1,40.00,NULL),(73,69,12,30.00,1,30.00,NULL),(74,69,11,290.00,1,290.00,NULL),(75,70,3,40.00,1,40.00,NULL),(76,70,11,290.00,1,290.00,NULL),(77,71,12,30.00,1,30.00,NULL),(78,71,11,290.00,1,290.00,NULL),(79,71,6,270.00,1,270.00,NULL),(80,72,6,270.00,1,270.00,NULL),(81,72,11,290.00,1,290.00,NULL),(82,73,2,40.00,1,40.00,NULL),(83,73,11,290.00,1,290.00,NULL),(84,74,3,40.00,1,40.00,NULL),(85,74,6,270.00,1,270.00,NULL),(86,75,11,290.00,1,290.00,NULL),(87,75,6,270.00,1,270.00,NULL),(88,76,11,290.00,1,290.00,NULL),(89,76,12,30.00,1,30.00,NULL),(90,77,6,270.00,1,270.00,NULL),(91,77,3,40.00,1,40.00,NULL),(92,78,3,40.00,1,40.00,NULL),(93,78,11,290.00,1,290.00,NULL),(94,79,11,290.00,1,290.00,NULL),(95,79,6,270.00,1,270.00,NULL),(96,79,3,40.00,1,40.00,NULL),(97,80,12,30.00,1,30.00,NULL),(98,80,11,290.00,1,290.00,NULL),(99,81,11,290.00,1,290.00,NULL),(100,81,12,30.00,1,30.00,NULL),(101,83,11,290.00,1,290.00,NULL),(102,83,6,270.00,1,270.00,NULL),(103,83,12,30.00,1,30.00,NULL),(104,84,8,40.00,1,40.00,NULL),(105,84,6,270.00,1,270.00,NULL),(106,84,5,560.00,1,560.00,NULL),(107,85,3,40.00,1,40.00,NULL),(108,85,6,270.00,1,270.00,NULL),(109,86,3,40.00,1,40.00,NULL),(110,86,6,270.00,1,270.00,NULL),(111,87,3,40.00,1,40.00,NULL),(112,87,11,290.00,1,290.00,NULL),(113,88,9,250.00,1,250.00,NULL),(114,88,12,30.00,1,30.00,NULL),(115,89,11,290.00,3,870.00,NULL),(116,89,12,30.00,1,30.00,NULL),(117,89,3,40.00,1,40.00,NULL),(118,90,12,30.00,1,30.00,NULL),(119,90,10,520.00,1,520.00,NULL),(120,91,12,30.00,1,30.00,NULL),(121,91,6,270.00,1,270.00,NULL),(122,92,6,270.00,1,270.00,NULL),(123,92,5,560.00,1,560.00,NULL),(124,92,8,40.00,1,40.00,NULL),(125,93,1,40.00,1,40.00,NULL),(126,93,11,290.00,1,290.00,NULL),(127,93,9,250.00,1,250.00,NULL),(128,93,7,80.00,1,80.00,NULL),(129,94,9,250.00,3,750.00,NULL),(130,94,2,40.00,2,80.00,NULL),(131,95,11,290.00,1,290.00,NULL),(132,95,12,30.00,1,30.00,NULL),(133,95,8,40.00,1,40.00,NULL),(134,95,9,250.00,1,250.00,NULL),(135,96,9,250.00,1,250.00,NULL),(136,96,12,30.00,1,30.00,NULL),(137,97,10,520.00,1,520.00,NULL),(138,97,11,290.00,1,290.00,NULL),(139,97,12,30.00,1,30.00,NULL),(140,98,11,290.00,1,290.00,NULL),(141,98,6,270.00,1,270.00,NULL),(142,98,3,40.00,1,40.00,NULL),(143,99,11,290.00,1,290.00,NULL),(144,99,8,40.00,1,40.00,NULL),(145,99,7,80.00,1,80.00,NULL);
/*!40000 ALTER TABLE `detalle_venta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleados`
--

DROP TABLE IF EXISTS `empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleados`
--

LOCK TABLES `empleados` WRITE;
/*!40000 ALTER TABLE `empleados` DISABLE KEYS */;
INSERT INTO `empleados` VALUES (6,'Elmer Laguna Larios','elmer@gmail.com','12345678','2025-03-18',8),(7,'Elvira Ojeda Padilla','elvira@gmail.com','12345678','2025-03-18',9),(8,'Katy Ramos','kramos@gmail.com','85987484','2025-03-18',10),(10,'Xiomara Chévez','xiomara@gmail.com','85988748','2025-03-18',12),(13,'Jose Agustin Arredondo','josearredondo@gmail.com','12346578','2025-03-18',15),(16,'Administrador Principal','admin@rest_bar.com','83330589','2020-01-01',1);
/*!40000 ALTER TABLE `empleados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mesas`
--

DROP TABLE IF EXISTS `mesas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mesas` (
  `ID_Mesa` int NOT NULL AUTO_INCREMENT,
  `Numero_Mesa` int NOT NULL,
  `Capacidad` int DEFAULT NULL,
  `Estado` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID_Mesa`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mesas`
--

LOCK TABLES `mesas` WRITE;
/*!40000 ALTER TABLE `mesas` DISABLE KEYS */;
INSERT INTO `mesas` VALUES (1,1,4,0),(2,2,2,0),(3,3,3,0),(4,4,1,0),(5,5,8,0),(6,6,2,0),(7,7,2,0);
/*!40000 ALTER TABLE `mesas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movimientos`
--

DROP TABLE IF EXISTS `movimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `movimientos` (
  `ID_Movimiento` int NOT NULL AUTO_INCREMENT,
  `Fecha_Hora` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Tipo` varchar(30) NOT NULL,
  `Monto` decimal(10,2) NOT NULL,
  `Descripcion` varchar(255) DEFAULT NULL,
  `ID_Usuario` int DEFAULT NULL,
  `ID_Venta` int DEFAULT NULL,
  PRIMARY KEY (`ID_Movimiento`),
  KEY `ID_Usuario` (`ID_Usuario`),
  KEY `ID_Venta` (`ID_Venta`),
  CONSTRAINT `movimientos_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuarios` (`ID_usuario`),
  CONSTRAINT `movimientos_ibfk_2` FOREIGN KEY (`ID_Venta`) REFERENCES `ventas` (`ID_Venta`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movimientos`
--

LOCK TABLES `movimientos` WRITE;
/*!40000 ALTER TABLE `movimientos` DISABLE KEYS */;
INSERT INTO `movimientos` VALUES (1,'2025-10-02 23:08:36','Ingreso',930.00,'Pago de venta ID 62 (Efectivo:1000)',1,62),(2,'2025-10-02 23:12:11','Ingreso',370.00,'Pago de venta ID 63 (Efectivo:400)',1,63),(3,'2025-10-02 23:17:34','Ingreso',310.00,'Pago de venta ID 64 (Efectivo:350)',1,64),(4,'2025-10-02 23:19:13','Ingreso',1930.00,'Pago de venta ID 65 (Tarjeta:1930)',1,65),(5,'2025-10-02 23:36:58','Ingreso',320.00,'Pago de venta ID 66 (Efectivo:100, Transf)',1,66),(6,'2025-10-02 23:38:28','Ingreso',590.00,'Pago de venta ID 67 (Efectivo:600)',1,67),(7,'2025-10-02 23:40:37','Ingreso',600.00,'Pago de venta ID 68 (Efectivo:600)',1,68),(8,'2025-10-02 23:41:54','Ingreso',320.00,'Pago de venta ID 69 (Efectivo:400)',1,69),(9,'2025-10-02 23:44:26','Ingreso',330.00,'Pago de venta ID 70 (Efectivo:400)',1,70),(10,'2025-10-02 23:46:33','Ingreso',590.00,'Pago de venta ID 71 (Efectivo:600)',1,71),(11,'2025-10-02 23:48:22','Ingreso',560.00,'Pago de venta ID 72 (Efectivo:600)',1,72),(12,'2025-10-02 23:52:53','Ingreso',330.00,'Pago de venta ID 73 (Efectivo:400)',1,73),(13,'2025-10-02 23:54:14','Ingreso',310.00,'Pago de venta ID 74 (Efectivo:400)',1,74),(14,'2025-10-03 00:01:18','Ingreso',560.00,'Pago de venta ID 75 (Efectivo:600)',1,75),(15,'2025-10-03 00:02:58','Ingreso',320.00,'Pago de venta ID 76 (Efectivo:400)',1,76),(16,'2025-10-03 00:04:55','Ingreso',310.00,'Pago de venta ID 77 (Efectivo:400)',1,77),(17,'2025-10-03 00:05:51','Ingreso',330.00,'Pago de venta ID 78 (Efectivo:400)',1,78),(18,'2025-10-03 00:18:08','Ingreso',600.00,'Pago de venta ID 79 (Efectivo:400, Transferencia:200)',1,79),(19,'2025-10-03 00:20:59','Ingreso',320.00,'Pago de venta ID 80 (Efectivo:400)',1,80),(20,'2025-10-03 00:23:20','Ingreso',320.00,'Pago de venta ID 81 (Efectivo:400)',1,81),(21,'2025-10-03 00:24:52','Ingreso',590.00,'Pago de venta ID 83 (Efectivo:600)',1,83),(22,'2025-10-03 00:26:30','Ingreso',870.00,'Pago de venta ID 84 (Efectivo:900)',1,84),(23,'2025-10-03 00:29:13','Ingreso',310.00,'Pago de venta ID 85 (Efectivo:400)',1,85),(24,'2025-10-03 00:30:53','Ingreso',310.00,'Pago de venta ID 86 (Efectivo:350)',1,86),(25,'2025-10-03 00:35:20','Ingreso',330.00,'Pago de venta ID 87 (Efectivo:400)',1,87),(26,'2025-10-03 22:18:18','Ingreso',280.00,'Pago de venta ID 88 (Efectivo:300)',1,88),(27,'2025-10-03 22:56:35','Ingreso',940.00,'Pago de venta ID 89 (Efectivo:500, Transferencia:440)',1,89),(28,'2025-10-03 23:02:59','Ingreso',550.00,'Pago de venta ID 90 (Efectivo:300, Transferencia:250)',1,90),(29,'2025-10-03 23:11:20','Apertura',5000.00,'Apertura de caja',1,NULL),(30,'2025-10-03 23:12:10','Cierre',5000.00,'Cierre de caja',1,NULL),(31,'2025-10-03 23:12:22','Apertura',5000.00,'Apertura de caja',1,NULL),(32,'2025-10-03 23:12:35','Ingreso',300.00,'Pago de venta ID 91 (Efectivo:300)',1,91),(33,'2025-10-03 23:12:44','Cierre',5300.00,'Cierre de caja',1,NULL),(34,'2025-10-05 19:25:51','Ingreso',870.00,'Pago de venta ID 92 (Efectivo:900)',1,92),(35,'2025-10-05 19:32:26','Ingreso',660.00,'Pago de venta ID 93 (Efectivo:300, Transferencia:360)',1,93),(36,'2025-10-05 20:26:03','Ingreso',830.00,'Pago de venta ID 94 (Efectivo:900)',1,94),(37,'2025-10-05 22:44:18','Ingreso',610.00,'Pago de venta ID 95 (Efectivo:650)',1,95),(38,'2025-10-05 23:02:00','Ingreso',280.00,'Pago de venta ID 96 (Efectivo:300)',1,96),(39,'2025-10-05 23:07:13','Ingreso',840.00,'Pago de venta ID 97 (Efectivo:400, Transferencia:440)',1,97),(40,'2025-10-06 00:19:49','Ingreso',600.00,'Pago de venta ID 98 (Efectivo:600)',1,98),(41,'2025-10-06 00:23:40','Ingreso',410.00,'Pago de venta ID 99 (Efectivo:500)',1,99);
/*!40000 ALTER TABLE `movimientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parciales_venta`
--

DROP TABLE IF EXISTS `parciales_venta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parciales_venta` (
  `ID_Parcial` int NOT NULL AUTO_INCREMENT,
  `ID_Venta` int NOT NULL,
  `nombre_cliente` varchar(100) DEFAULT NULL,
  `pagado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID_Parcial`),
  KEY `ID_Venta` (`ID_Venta`),
  CONSTRAINT `parciales_venta_ibfk_1` FOREIGN KEY (`ID_Venta`) REFERENCES `ventas` (`ID_Venta`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parciales_venta`
--

LOCK TABLES `parciales_venta` WRITE;
/*!40000 ALTER TABLE `parciales_venta` DISABLE KEYS */;
/*!40000 ALTER TABLE `parciales_venta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,'COCA COLA 12 OZ',15.00,40.00,1,45),(2,'FRESCA 12 OZ',10.00,40.00,1,20),(3,'rojita',10.00,40.00,1,45),(4,'canada club soda',15.00,40.00,1,45),(5,'Ribeye',200.00,560.00,3,11),(6,'Res con punches',100.00,270.00,4,25),(7,'chelada soda',45.00,80.00,1,10),(8,'mix chelada',40.00,40.00,1,10),(9,'sopa de gallina',100.00,250.00,4,25),(10,'Ribeye',20.00,520.00,3,30),(11,'CARNE DE RES',120.00,290.00,2,30),(12,'TE LIPTON',15.00,30.00,1,10);
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `ID_Rol` int NOT NULL AUTO_INCREMENT,
  `Nombre_Rol` varchar(50) NOT NULL,
  PRIMARY KEY (`ID_Rol`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrador'),(2,'Mesero'),(3,'Cajero');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'superadmin','$2y$10$t/mD.Wc7Fo3Ot.nU0Q9XxuLMDHzF4bYM0QZXgBUM0OPITQzL4JSxS',1,1),(8,'elaguna','$2y$10$NiN5s3BGkBuQqwkeX1TjiOL1PBXSeLmrX6EYavDYW8B3zhCWdyg.u',1,1),(9,'eojeda1','$2y$10$6V0h8ioF855oKKRMhkJMPu/WmyAvnvokAGzvr0.EDABWLFGztU6Pe',2,1),(10,'kramos','$2y$10$mIFjirasRfhBIZ3txBUL5OnBoF40nbMjaH1AmM79I1M8RRJ71dBza',3,0),(12,'xchevez','$2y$10$8q/QVWblz1kOyDnDhc0yDOyg2DsOKcbTcgCwquyd/oLtbKwihqQgO',3,1),(15,'jarredondo','$2y$10$SCPMsHuh7XqoFuFkOw1DHO/VmqiyVcSjRfMcoqscspw3t37ZQ/25m',3,1);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ventas`
--

DROP TABLE IF EXISTS `ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ventas` (
  `ID_Venta` int NOT NULL AUTO_INCREMENT,
  `ID_Cliente` int DEFAULT NULL,
  `ID_Mesa` int DEFAULT NULL,
  `Fecha_Hora` datetime NOT NULL,
  `Total` decimal(10,2) DEFAULT NULL,
  `Metodo_Pago` varchar(200) DEFAULT NULL,
  `ID_Empleado` int DEFAULT NULL,
  `Estado` varchar(20) NOT NULL DEFAULT 'Pendiente',
  PRIMARY KEY (`ID_Venta`),
  KEY `ID_Cliente` (`ID_Cliente`),
  KEY `ID_Mesa` (`ID_Mesa`),
  KEY `ID_Empleado` (`ID_Empleado`),
  CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`ID_Cliente`) REFERENCES `clientes` (`ID_Cliente`),
  CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`ID_Mesa`) REFERENCES `mesas` (`ID_Mesa`),
  CONSTRAINT `ventas_ibfk_3` FOREIGN KEY (`ID_Empleado`) REFERENCES `empleados` (`ID_Empleado`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas`
--

LOCK TABLES `ventas` WRITE;
/*!40000 ALTER TABLE `ventas` DISABLE KEYS */;
INSERT INTO `ventas` VALUES (48,1,1,'2025-08-22 22:16:29',3008.00,'Efectivo:3000',6,'Pagada'),(49,1,2,'2025-08-24 00:28:17',126.00,'Efectivo:100, Efecti',6,'Pagada'),(50,1,4,'2025-08-24 01:08:25',1190.00,'Efectivo:1100, Tarjeta:90',6,'Pagada'),(51,1,3,'2025-08-26 00:29:25',580.00,'Efectivo:500, Tarjet',6,'Pagada'),(52,1,1,'2025-08-31 15:43:30',850.00,'Efectivo:400, Tarjet',6,'Pagada'),(54,1,1,'2025-08-31 16:49:46',40.00,'Efectivo:100',6,'Pagada'),(62,1,2,'2025-10-02 23:01:18',930.00,'Efectivo:1000',6,'Pagada'),(63,1,4,'2025-10-02 23:11:50',370.00,'Efectivo:400',6,'Pagada'),(64,1,1,'2025-10-02 23:17:16',310.00,'Efectivo:350',6,'Pagada'),(65,1,4,'2025-10-02 23:18:50',1930.00,'Tarjeta:1930',6,'Pagada'),(66,1,4,'2025-10-02 23:29:58',320.00,'Efectivo:100, Transf',6,'Pagada'),(67,1,4,'2025-10-02 23:38:16',590.00,'Efectivo:600',6,'Pagada'),(68,1,6,'2025-10-02 23:40:24',600.00,'Efectivo:600',6,'Pagada'),(69,1,4,'2025-10-02 23:41:33',320.00,'Efectivo:400',6,'Pagada'),(70,1,6,'2025-10-02 23:42:14',330.00,'Efectivo:400',6,'Pagada'),(71,1,2,'2025-10-02 23:46:21',590.00,'Efectivo:600',6,'Pagada'),(72,1,4,'2025-10-02 23:48:12',560.00,'Efectivo:600',6,'Pagada'),(73,1,6,'2025-10-02 23:52:41',330.00,'Efectivo:400',6,'Pagada'),(74,1,3,'2025-10-02 23:54:04',310.00,'Efectivo:400',6,'Pagada'),(75,1,4,'2025-10-03 00:01:18',560.00,'Efectivo:600',6,'Pagada'),(76,1,6,'2025-10-03 00:02:45',320.00,'Efectivo:400',6,'Pagada'),(77,1,6,'2025-10-03 00:04:44',310.00,'Efectivo:400',6,'Pagada'),(78,1,5,'2025-10-03 00:05:38',330.00,'Efectivo:400',6,'Pagada'),(79,1,5,'2025-10-03 00:11:44',600.00,'Efectivo:400, Transferencia:200',6,'Pagada'),(80,1,2,'2025-10-03 00:20:50',320.00,'Efectivo:400',6,'Pagada'),(81,1,1,'2025-10-03 00:23:05',320.00,'Efectivo:400',6,'Pagada'),(83,1,4,'2025-10-03 00:24:35',590.00,'Efectivo:600',6,'Pagada'),(84,1,1,'2025-10-03 00:26:17',870.00,'Efectivo:900',6,'Pagada'),(85,1,5,'2025-10-03 00:29:04',310.00,'Efectivo:400',6,'Pagada'),(86,1,3,'2025-10-03 00:30:43',310.00,'Efectivo:350',6,'Pagada'),(87,1,1,'2025-10-03 00:35:09',330.00,'Efectivo:400',6,'Pagada'),(88,1,2,'2025-10-03 22:18:05',280.00,'Efectivo:300',6,'Pagada'),(89,1,6,'2025-10-03 22:56:05',940.00,'Efectivo:500, Transferencia:440',16,'Pagada'),(90,1,5,'2025-10-03 23:02:33',550.00,'Efectivo:300, Transferencia:250',16,'Pagada'),(91,1,5,'2025-10-03 23:12:26',300.00,'Efectivo:300',16,'Pagada'),(92,1,7,'2025-10-05 19:25:38',870.00,'Efectivo:900',16,'Pagada'),(93,1,1,'2025-10-05 19:31:25',660.00,'Efectivo:300, Transferencia:360',16,'Pagada'),(94,1,1,'2025-10-05 20:25:52',830.00,'Efectivo:900',16,'Pagada'),(95,1,5,'2025-10-05 20:34:20',610.00,'Efectivo:650',16,'Pagada'),(96,1,2,'2025-10-05 23:01:50',280.00,'Efectivo:300',16,'Pagada'),(97,1,7,'2025-10-05 23:06:46',840.00,'Efectivo:400, Transferencia:440',16,'Pagada'),(98,1,1,'2025-10-06 00:19:41',600.00,'Efectivo:600',16,'Pagada'),(99,1,1,'2025-10-06 00:23:30',410.00,'Efectivo:500',16,'Pagada');
/*!40000 ALTER TABLE `ventas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'rest_bar'
--

--
-- Dumping routines for database 'rest_bar'
--
/*!50003 DROP PROCEDURE IF EXISTS `sp_AddProduct` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_AddSaleDetail` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`localhost` PROCEDURE `sp_AddSaleDetail`(
    IN p_id_venta INT,
    IN p_id_producto INT,
    IN p_cantidad INT,
    IN p_precio DECIMAL(10,2)
)
BEGIN
    DECLARE detalle_id INT;
    SELECT ID_Detalle INTO detalle_id
    FROM detalle_venta
    WHERE ID_Venta = p_id_venta AND ID_Producto = p_id_producto
    LIMIT 1;

    IF detalle_id IS NOT NULL THEN
        UPDATE detalle_venta
        SET Cantidad = Cantidad + p_cantidad,
            Subtotal = (Cantidad + p_cantidad) * p_precio
        WHERE ID_Detalle = detalle_id;
    ELSE
        INSERT INTO detalle_venta (ID_Venta, ID_Producto, Cantidad, Precio_Venta, Subtotal)
        VALUES (p_id_venta, p_id_producto, p_cantidad, p_precio, p_cantidad * p_precio);
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_CheckUserExists` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_CreateCategory` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_CreateCategory`(in p_Nombre_Categoria varchar(50))
begin
	insert into categorias(Nombre_Categoria) values(p_Nombre_Categoria);
end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_CreateSale` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_CreateUserWithEmployee` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_DeleteEmployee` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_GetAllCategories` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_GetAllCategories`()
begin
select * from categorias;
end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_GetAllEmployees` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_GetAllEmployees`()
BEGIN
    SELECT e.ID_Empleado, e.ID_Usuario, e.Nombre_Completo, e.Telefono, e.Correo, u.Nombre_Usuario,u.ID_Rol
    FROM empleados e
    JOIN usuarios u ON e.ID_Usuario = u.ID_usuario;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_GetAllProducts` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_GetAllProducts`()
BEGIN
    SELECT p.*, c.Nombre_Categoria 
    FROM productos p
    INNER JOIN categorias c ON p.ID_Categoria = c.ID_Categoria
    ORDER BY p.Nombre_Producto;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_GetAllRols` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_GetAllRols`()
BEGIN
	Select * from roles;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_GetAllTables` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_GetAllTables`()
BEGIN
    SELECT * FROM mesas ORDER BY Numero_Mesa;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_GetDailySales` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_GetDashboardStats` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_GetEmployeeById` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_GetEmployeeById`(IN p_ID_Usuario INT)
BEGIN
    SELECT e.*, u.Nombre_Usuario ,u.ID_Rol
    FROM empleados e
    JOIN usuarios u ON e.ID_Usuario = u.ID_usuario  -- Ensure column names match the schema
    WHERE e.ID_Usuario = p_ID_Usuario;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_GetSaleDetails` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_UpdateEmployee` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_UpdateProduct` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_UpdateTableStatus` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`baruser`@`%` PROCEDURE `sp_UpdateTableStatus`(
    IN p_id_mesa INT,
    IN p_estado BOOLEAN
)
BEGIN
    UPDATE mesas SET Estado = p_estado WHERE ID_Mesa = p_id_mesa;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_UpdateUser` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
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
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_ValidateUser` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-06  0:32:44
