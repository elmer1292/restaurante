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

    /**
     * Envía texto a una impresora compatible con ESC/POS utilizando la librería escpos-php.
     *
     * @param string $clave Clave identificadora de la impresora.
     * @param string $contenido Texto que se desea imprimir.
     * @return bool Retorna true si la impresión fue exitosa, false en caso contrario.
     *
     * El método obtiene la impresora correspondiente a la clave proporcionada, verifica si está habilitada
     * en la configuración, y luego intenta imprimir el contenido especificado. Utiliza el perfil "simple"
     * de capacidad para la impresora. En caso de error durante el proceso de impresión, retorna false.
     */
    // Enviar texto a la impresora usando escpos-php (soporte ESC/POS)
    public static function imprimir($clave, $contenido) {
        $impresora = self::obtenerImpresora($clave);
// Verifica si la impresora está habilitada en la configuración (usar_impresora_cocina o usar_impresora_barra)
        $usarClave = 'usar_' . $clave;
        require_once dirname(__DIR__, 1) . '/models/ConfigModel.php';
        $configModel = new ConfigModel();
        $usarImpresora = $configModel->get($usarClave);
        if ($usarImpresora === '0' || $usarImpresora === 0 || $usarImpresora === false || $usarImpresora === null) {
            return false;
        }
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
        } catch (Exception $e) {
            // Puedes loguear el error si lo deseas
            return false;
        }
    }
        // Imprimir directamente en una impresora por nombre (sin validar clave de config)
    public static function imprimir_directo($nombre, $contenido) {
        if (!$nombre) return false;
        try {
            require_once __DIR__ . '/escpos-php/src/Mike42/Escpos/Printer.php';
            require_once __DIR__ . '/escpos-php/src/Mike42/Escpos/PrintConnectors/WindowsPrintConnector.php';
            require_once __DIR__ . '/escpos-php/src/Mike42/Escpos/CapabilityProfile.php';
            $connector = new \Mike42\Escpos\PrintConnectors\WindowsPrintConnector($nombre);
            $profile = \Mike42\Escpos\CapabilityProfile::load("simple");
            $printer = new \Mike42\Escpos\Printer($connector, $profile);
            $printer->text($contenido);
            $printer->feed(2);
            $printer->cut();
            $printer->close();
            return true;
        } catch (Exception $e) {
            // Puedes loguear el error si lo deseas
            return false;
        }
    }
}
