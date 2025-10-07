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

    // Enviar texto a la impresora usando escpos-php (soporte ESC/POS)
    public static function imprimir($clave, $contenido) {
        $impresora = self::obtenerImpresora($clave);
        if (!$impresora) return false;
        try {
            require_once __DIR__ . '/escpos-php/src/Mike42/Escpos/Printer.php';
            require_once __DIR__ . '/escpos-php/src/Mike42/Escpos/PrintConnectors/WindowsPrintConnector.php';
            require_once __DIR__ . '/escpos-php/src/Mike42/Escpos/CapabilityProfile.php';
            
            $connector = new \Mike42\Escpos\PrintConnectors\WindowsPrintConnector($impresora);
            $profile = \Mike42\Escpos\CapabilityProfile::load("simple");
            $printer = new \Mike42\Escpos\Printer($connector, $profile);
            $printer->text($contenido);
            $printer->feed(3);
            $printer->cut();
            $printer->close();
            return true;
        } catch (\Exception $e) {
            // Puedes loguear el error si lo deseas
            return false;
        }
    }
}
