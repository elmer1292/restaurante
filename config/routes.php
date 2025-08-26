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
$router->add('productos', 'ProductoController', 'index');
$router->add('mesas', 'MesaController', 'index');
$router->add('comandas', 'ComandaController', 'index');
$router->add('ventas', 'VentaController', 'index');

// Puedes añadir más rutas aquí para otras acciones como crear, editar, eliminar, etc.
// Ruta para AJAX de agregar productos a la comanda
$router->add('detalleventa/actualizarEstado', 'DetalleVentaController', 'actualizarEstado');
$router->add('detalleventa/eliminarProducto', 'DetalleVentaController', 'eliminarProducto');
$router->add('comanda/agregarProductos', 'ComandaAjaxController', 'agregarProductos');
// Ejemplo:
// $router->add('productos/crear', 'ProductoController', 'create');
// $router->add('productos/editar', 'ProductoController', 'edit');
