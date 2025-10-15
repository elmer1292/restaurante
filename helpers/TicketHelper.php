<?php
// Helper para generar el contenido de tickets y comandas
class TicketHelper {
    public static function generarTicketVenta($restaurante, $mesa, $fechaHora, $detalles, $total, $empleado, $ticketId, $moneda, $metodoPago, $cambio, $servicio, $prefactura = false) {
        // Ancho máximo de línea para ticket térmico estándar 80mm
        $maxWidth = 35;
        $out  = str_pad($restaurante, $maxWidth, ' ', STR_PAD_BOTH) . "\n";
        if ($prefactura) {
            $out .= str_pad('*** PRE-FACTURA ***', $maxWidth, ' ', STR_PAD_BOTH) . "\n";
        }
        $out .= "RUC: 0810509961001U\n";
        $out .= "Tel: 8882-3804\n";
        $out .= "Dirección: Frente al nuevo \nhospital HEODRA\n";
        $out .= str_repeat('=', $maxWidth) . "\n";
        $out .= "Mesa: $mesa\n";
        $out .= "Fecha: $fechaHora\n";
        $out .= str_repeat('-', $maxWidth) . "\n";
        $out .= str_pad('Cant', 4) . str_pad('Producto', 22) . str_pad('Subtotal', 8, ' ', STR_PAD_LEFT) . "\n";
        $out .= str_repeat('-', $maxWidth) . "\n";
        foreach ($detalles as $item) {
            $cant = str_pad($item['cantidad'] . 'x', 4);
            $nombre = mb_strimwidth(strtoupper($item['nombre']), 0, 22, '');
            $nombre = str_pad($nombre, 22);
            $subtotal = str_pad($moneda . number_format($item['subtotal'], 2), 8, ' ', STR_PAD_LEFT);
            $out .= "$cant$nombre$subtotal\n";
        }
        $out .= str_repeat('-', $maxWidth) . "\n";
        $out .= str_pad('SUBTOTAL:', 26) . str_pad($moneda . number_format($total, 2), 8, ' ', STR_PAD_LEFT) . "\n";
        if($servicio>0){
            $out .= str_pad('Servicio:', 26) . str_pad($moneda . number_format($servicio, 2), 8, ' ', STR_PAD_LEFT) . "\n";
        }
        $out .= str_repeat('-', $maxWidth) . "\n";
        $out .= str_pad('TOTAL:', 26) . str_pad($moneda . number_format($total+$servicio, 2), 8, ' ', STR_PAD_LEFT) . "\n";
        if (!$prefactura) {
            // Método de pago: dividir si es muy largo
            $mpago = "$moneda $metodoPago";
            if (mb_strlen($mpago) > $maxWidth - 15) {
                $out .= "Método de pago:\n";
                $out .= wordwrap($mpago, $maxWidth, "\n", true) . "\n";
            } else {
                $out .= "Método de pago: $mpago\n";
            }
            if ($cambio > 0) {
                $out .= "Cambio: $moneda $cambio\n";
            }
        }
        $out .= "¡Gracias por su visita!\n";
        $emp = "$empleado";
        if (mb_strlen($emp) > $maxWidth - 15) {
            $out .= "Atendido por:\n";
            $out .= wordwrap($emp, $maxWidth, "\n", true) . "\n";
        } else {
            $out .= "Atendido por: $emp\n";
        }
        $out .= "Ticket: #" . str_pad($ticketId, 6, '0', STR_PAD_LEFT) . "\n";
        //despues de todo esto guarda el $out en un archivo .txt
        /* $filePath = __DIR__ . '/../tickets/ticket_' . $ticketId . '.txt';
         file_put_contents($filePath, $out); */
        return $out;
    }
    // Generar ticket de cierre de caja - ventas diarias
    public static function generarTicketCierreCaja($restaurante, $fechaInicio, $fechaFin, $totalEfectivo, $totalTarjeta, $totalVentas, $totalServicio, $totalGeneral, $empleado, $ticketId, $moneda) {
        $maxWidth = 35;
        $out  = str_pad($restaurante, $maxWidth, ' ', STR_PAD_BOTH) . "\n";
        $out .= str_pad('*** CIERRE DE CAJA ***', $maxWidth, ' ', STR_PAD_BOTH) . "\n";
        $out .= "RUC: 0810509961001U\n";
        $out .= "Tel: 8882-3804\n";
        $out .= "Dirección: Frente al nuevo \nhospital HEODRA\n";
        $out .= str_repeat('=', $maxWidth) . "\n";
        $out .= "Desde: $fechaInicio\n";
        $out .= "Hasta: $fechaFin\n";
        $out .= str_repeat('-', $maxWidth) . "\n";
        $out .= str_pad('Total Efectivo:', 26) . str_pad($moneda . number_format($totalEfectivo, 2), 8, ' ', STR_PAD_LEFT) . "\n";
        $out .= str_pad('Total Tarjeta:', 26) . str_pad($moneda . number_format($totalTarjeta, 2), 8, ' ', STR_PAD_LEFT) . "\n";
        if($totalServicio>0){
            $out .= str_pad('Total Servicio:', 26) . str_pad($moneda . number_format($totalServicio, 2), 8, ' ', STR_PAD_LEFT) . "\n";
        }
        $out .= str_repeat('-', $maxWidth) . "\n";
        $out .= str_pad('Total Ventas:', 26) . str_pad($moneda . number_format($totalVentas, 2), 8, ' ', STR_PAD_LEFT) . "\n";
        $out .= str_repeat('=', $maxWidth) . "\n";
        $out .= str_pad('TOTAL GENERAL:', 26) . str_pad($moneda . number_format($totalGeneral, 2), 8, ' ', STR_PAD_LEFT) . "\n";
        $out .= str_repeat('-', $maxWidth) . "\n";
        $emp = "$empleado";
        if (mb_strlen($emp) > $maxWidth - 15) {
            $out .= "Cerrado por:\n";
            $out .= wordwrap($emp, $maxWidth, "\n", true) . "\n";
        } else {
            $out .= "Cerrado por: $emp\n";
        }
        $out .= "Ticket: #" . str_pad($ticketId, 6, '0', STR_PAD_LEFT) . "\n";
        $out .= "Fecha: " . date('Y-m-d H:i:s') . "\n";
        $out .= "¡Gracias por su visita!\n";
        $out .= str_repeat('-', $maxWidth) . "\n";
        return $out;
    }

        /**
     * Genera el texto de un ticket para reporte de productos vendidos por fecha.
     * @param string $restaurante Nombre del restaurante
     * @param string $fecha Fecha del reporte (formato d/m/Y)
     * @param array $productos Array de productos: [ ['nombre'=>..., 'cantidad'=>..., 'total'=>...] ]
     * @param float $granTotal Suma total de todos los productos
     * @param string $moneda Símbolo de moneda
     * @param string $empleado Nombre del usuario/empleado que imprime
     * @return string Texto listo para impresión térmica
     */
    public static function generarTicketProductosVendidos($restaurante, $fecha, $productos, $granTotal, $moneda, $empleado) {
        $maxWidth = 35;
        $out  = str_pad($restaurante, $maxWidth, ' ', STR_PAD_BOTH) . "\n";
        $out .= str_pad('*** REPORTE DE PRODUCTOS ***', $maxWidth, ' ', STR_PAD_BOTH) . "\n";
        $out .= "Fecha: $fecha\n";
        $out .= str_repeat('=', $maxWidth) . "\n";

        // Agrupar productos por categoría
        $porCategoria = [];
        foreach ($productos as $item) {
            $cat = isset($item['categoria']) ? $item['categoria'] : 'SIN CATEGORIA';
            if (!isset($porCategoria[$cat])) {
                $porCategoria[$cat] = [];
            }
            $porCategoria[$cat][] = $item;
        }

        foreach ($porCategoria as $categoria => $items) {
            $out .= str_pad(strtoupper($categoria), $maxWidth, ' ', STR_PAD_BOTH) . "\n";
            $out .= str_pad('Cant', 4) . str_pad('Producto', 22) . str_pad('Total', 8, ' ', STR_PAD_LEFT) . "\n";
            $out .= str_repeat('-', $maxWidth) . "\n";
            $subtotal = 0;
            foreach ($items as $item) {
                $cant = str_pad($item['cantidad'], 4);
                $nombre = mb_strimwidth(strtoupper($item['nombre']), 0, 22, '');
                $nombre = str_pad($nombre, 22);
                $total = str_pad($moneda . number_format($item['total'], 2), 8, ' ', STR_PAD_LEFT);
                $out .= "$cant$nombre$total\n";
                $subtotal += $item['total'];
            }
            $out .= str_repeat('-', $maxWidth) . "\n";
            $out .= str_pad('Subtotal:', 26) . str_pad($moneda . number_format($subtotal, 2), 8, ' ', STR_PAD_LEFT) . "\n";
            $out .= str_repeat('=', $maxWidth) . "\n";
        }

        $out .= str_pad('GRAN TOTAL:', 26) . str_pad($moneda . number_format($granTotal, 2), 8, ' ', STR_PAD_LEFT) . "\n";
        $out .= str_repeat('=', $maxWidth) . "\n";
        $emp = "$empleado";
        if (mb_strlen($emp) > $maxWidth - 15) {
            $out .= "Generado por:\n";
            $out .= wordwrap($emp, $maxWidth, "\n", true) . "\n";
        } else {
            $out .= "Generado por: $emp\n";
        }
        $out .= "Fecha/Hora: " . date('d/m/Y H:i') . "\n";
        $out .= "\n";
        $out .= str_pad('FIN DEL REPORTE', $maxWidth, ' ', STR_PAD_BOTH) . "\n";
        return $out;
    }
}
