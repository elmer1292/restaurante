<?php
// Helper para gestión de impresoras en Windows
class ImpresoraHelper {
    // Buscar impresoras instaladas en el sistema (Windows)
    public static function buscarImpresoras() {
        $impresoras = [];
        // Usar PowerShell para obtener impresoras instaladas
        @exec('powershell "Get-Printer | Select-Object -ExpandProperty Name"', $output, $resultCode);
        if ($resultCode !== 0 || empty($output)) {
            return [];
        }
        foreach ($output as $line) {
            $line = trim($line);
            if ($line) {
                $impresoras[] = $line;
            }
        }
        return $impresoras;
    }

    // Guardar impresora seleccionada en la tabla config
    public static function guardarImpresora($clave, $nombre) {
        require_once dirname(__DIR__, 1) . '/models/ConfigModel.php';
        $configModel = new ConfigModel();
        return $configModel->set($clave, $nombre);
    }

    // Obtener impresora configurada
    public static function obtenerImpresora($clave) {
        require_once dirname(__DIR__, 1) . '/models/ConfigModel.php';
        $configModel = new ConfigModel();
        return $configModel->get($clave);
    }

    // Enviar texto a la impresora (ejemplo básico)
    public static function imprimir($clave, $contenido) {
        $impresora = self::obtenerImpresora($clave);
        if (!$impresora) return false;
        // Guardar el contenido en un archivo temporal
        $tmpFile = tempnam(sys_get_temp_dir(), 'print_');
        file_put_contents($tmpFile, $contenido);
        // Comando para imprimir en Windows
        $cmd = 'print /D:"' . $impresora . '" "' . $tmpFile . '"';
        exec($cmd);
        unlink($tmpFile);
        return true;
    }
}
