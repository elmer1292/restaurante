<?php
// Configuración global
// Cambia el valor de BASE_URL si renombrarás la carpeta
if (!defined('BASE_URL')) {
    define('BASE_URL', '/' . basename(dirname(__DIR__)) . '/'); // Detecta el nombre de la carpeta actual
}
