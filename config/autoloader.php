<?php
spl_autoload_register(function ($className) {
    $paths = [
        __DIR__ . '/../models/',
        __DIR__ . '/../config/',
        __DIR__ . '/../controllers/',
        __DIR__ . '/../helpers/',
        __DIR__ . '/../helpers/escpos-php/src/', // Soporte para escpos-php
    ];
    // Convertir namespace a ruta de archivo
    $classFile = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $className) . '.php';
    foreach ($paths as $path) {
        $file = $path . $classFile;
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
