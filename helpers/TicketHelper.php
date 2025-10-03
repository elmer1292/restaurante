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

    public static function generarTicketVenta($restaurante, $mesa, $fechaHora, $detalles, $total, $empleado, $ticketId, $moneda = '$') {
        $out = "====== $restaurante ======\n";
        $out .= "Mesa: $mesa\n";
        $out .= "Fecha: $fechaHora\n";
        $out .= str_repeat('-', 34) . "\n";
        $out .= "Cant  Producto           Subtotal\n";
        $out .= str_repeat('-', 34) . "\n";
        foreach ($detalles as $item) {
            $cant = str_pad($item['cantidad'] . 'x', 4);
            $nombre = str_pad(substr($item['nombre'], 0, 16), 16);
            $subtotal = str_pad($moneda . number_format($item['subtotal'], 2), 8, ' ', STR_PAD_LEFT);
            $out .= "$cant $nombre $subtotal\n";
        }
        $out .= str_repeat('-', 34) . "\n";
        $out .= "TOTAL:" . str_pad($moneda . number_format($total, 2), 25, ' ', STR_PAD_LEFT) . "\n";
        $out .= str_repeat('-', 34) . "\n";
        $out .= "¡Gracias por su visita!\n";
        $out .= "Atendido por: $empleado\n";
        $out .= "Ticket: #" . str_pad($ticketId, 6, '0', STR_PAD_LEFT) . "\n";
        return $out;
    }
}
