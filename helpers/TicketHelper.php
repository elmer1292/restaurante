<?php
// Helper para generar el contenido de tickets y comandas
class TicketHelper {
    public static function generarComandaCocina($mesa, $hora, $productos, $restaurante = 'RESTAURANTE') {
        $out = "====== $restaurante - COMANDA COCINA ======\n";
        $out .= "Mesa: $mesa\n";
        $out .= "Hora: $hora\n";
        $out .= str_repeat('-', 26) . "\n";
        foreach ($productos as $prod) {
            $out .= $prod['cantidad'] . "x " . strtoupper($prod['nombre']) . "\n";
        }
        $out .= str_repeat('-', 26) . "\n";
        return $out;
    }

    public static function generarTicketVenta($restaurante, $mesa, $fechaHora, $detalles, $total, $empleado, $ticketId, $moneda, $metodoPago, $cambio, $servicio, $prefactura = false) {
        // Ancho máximo de línea para ticket térmico estándar 80mm
        $maxWidth = 35;
        $out  = str_pad($restaurante, $maxWidth, ' ', STR_PAD_BOTH) . "\n";
        if ($prefactura) {
            $out .= str_pad('*** PRE-FACTURA ***', $maxWidth, ' ', STR_PAD_BOTH) . "\n";
        }
        $out .= "RUC: 0810509961001U\n";
        $out .= "Tel: (123) 456-7890\n";
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
}
