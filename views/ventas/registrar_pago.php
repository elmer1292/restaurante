<?php
// Archivo migrado, la lógica ahora está en VentaController::registrarPago
http_response_code(410);
echo json_encode(['success' => false, 'error' => 'Este endpoint ha sido migrado al controlador.']);
