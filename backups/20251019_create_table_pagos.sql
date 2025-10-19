-- Backup / migration: Create table pagos
-- Run this in your MySQL/MariaDB to add the pagos table

CREATE TABLE IF NOT EXISTS `pagos` (
  `ID_Pago` int(11) NOT NULL AUTO_INCREMENT,
  `ID_Venta` int(11) NOT NULL,
  `Metodo` varchar(255) DEFAULT NULL,
  `Monto` decimal(12,2) NOT NULL DEFAULT '0.00',
  `Es_Cambio` tinyint(1) NOT NULL DEFAULT '0',
  `Fecha_Hora` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID_Pago`),
  KEY `idx_pago_venta` (`ID_Venta`),
  CONSTRAINT `fk_pagos_venta` FOREIGN KEY (`ID_Venta`) REFERENCES `ventas` (`ID_Venta`) ON DELETE CASCADE
);
