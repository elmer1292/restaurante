<?php
// Definir las rutas de la aplicación
// El método add() del router toma tres argumentos:
// 1. La ruta (la URL que el usuario visitará)
// 2. El nombre del controlador (sin el sufijo 'Controller')
// 3. El método en el controlador que se ejecutará

$router->add('/', 'DashboardController', 'index');
$router->add('login', 'AuthController', 'login');
$router->add('logout', 'AuthController', 'logout');
$router->add('empleados', 'EmpleadoController', 'index');
$router->add('empleados/get', 'EmpleadoController', 'getEmpleado');
$router->add('empleados/update', 'EmpleadoController', 'updateEmpleado');
$router->add('empleados/delete', 'EmpleadoController', 'deleteEmpleado');
$router->add('productos', 'ProductoController', 'index');
$router->add('mesas', 'MesaController', 'index');
$router->add('comandas', 'ComandaController', 'index');
$router->add('comandas/imprimirComanda', 'ComandaController', 'imprimirComanda');
$router->add('mesa', 'MesaController', 'detalle');
$router->add('ventas', 'VentaController', 'index');

$router->add('ventas/registrarPago', 'VentaController', 'registrarPago');
$router->add('ventas/ticket', 'VentaController', 'ticket');

// Puedes añadir más rutas aquí para otras acciones como crear, editar, eliminar, etc.
$router->add('configuracion', 'ConfigController', 'index');
$router->add('configuracion/update', 'ConfigController', 'update');
$router->add('configuracion/buscarImpresoras', 'ConfigController', 'buscarImpresoras');
$router->add('configuracion/backup', 'ConfigController', 'backup');
$router->add('productos/procesar', 'ProductoController', 'procesar');
// Ruta para AJAX de agregar productos a la comanda
$router->add('detalleventa/actualizarEstado', 'DetalleVentaController', 'actualizarEstado');
$router->add('detalleventa/eliminarProducto', 'DetalleVentaController', 'eliminarProducto');
$router->add('comanda/agregarProductos', 'ComandaAjaxController', 'agregarProductos');
$router->add('comanda/crear', 'ComandaAjaxController', 'crearComanda');
$router->add('comanda/liberar', 'ComandaAjaxController', 'liberarMesa');
$router->add('user/perfil', 'UserController', 'perfil');
$router->add('user/update', 'UserController', 'actualizarPerfil');

$router->add('mesas/dividir_cuenta', 'MesaController', 'dividirCuenta');

$router->add('mesas/procesar_mesa', 'MesaController', 'procesarMesa');

$router->add('caja/apertura', 'CajaController', 'apertura');
$router->add('caja/cierre', 'CajaController', 'cierre');

$router->add('movimientos', 'MovimientoController', 'index');
$router->add('movimientos/registrar', 'MovimientoController', 'registrar');
// Ejemplo:
// $router->add('productos/crear', 'ProductoController', 'create');
// $router->add('productos/editar', 'ProductoController', 'edit');
