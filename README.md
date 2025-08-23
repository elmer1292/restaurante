# Sistema de Gestión de Restaurante y Bar

Este es un sistema completo de gestión para restaurantes y bares desarrollado en PHP puro, diseñado para manejar pedidos, inventario, personal y ventas de manera eficiente.

## Características Principales

- **Gestión de Usuarios y Roles**
  - Inicio de sesión seguro
  - Roles diferenciados (Administrador, Mesero, Cajero, Cocina, Barra)
  - Control de acceso basado en roles

- **Gestión de Productos**
  - Catálogo de productos
  - Categorización (Alimentos/Bebidas)
  - Control de inventario
  - Precios y costos

- **Gestión de Mesas**
  - Estado de mesas en tiempo real
  - Asignación de mesas
  - Seguimiento de ocupación

- **Sistema de Comandas**
  - Toma de pedidos digital
  - Envío a cocina/barra
  - Seguimiento de estado de pedidos

- **Panel de Cocina y Barra**
  - Vista en tiempo real de pedidos pendientes
  - Organización por prioridad
  - Actualización automática

- **Gestión de Ventas**
  - Registro de ventas
  - Historial detallado
  - Reportes diarios

- **Dashboard**
  - Estadísticas en tiempo real
  - Indicadores clave de rendimiento
  - Resumen de ventas diarias

## Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web Apache con mod_rewrite habilitado
- Extensiones PHP requeridas:
  - PDO
  - PDO_MySQL
  - JSON
  - Session

## Instalación

1. **Configuración de la Base de Datos**
   ```sql
   -- Importar el archivo SQL proporcionado en phpMyAdmin o cliente MySQL
   -- El archivo contiene la estructura y procedimientos almacenados necesarios
   ```

2. **Configuración del Proyecto**
   - Clonar o descargar el repositorio en el directorio web:
     ```bash
     git clone [url-repositorio] /path/to/xampp/htdocs/restaurante
     ```
   - Configurar los parámetros de la base de datos en `config/config.php`:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'rest_bar');
     define('DB_USER', 'tu_usuario');
     define('DB_PASS', 'tu_contraseña');
     ```

3. **Configuración del Servidor Web**
   - Asegurarse que mod_rewrite está habilitado en Apache
   - Verificar que el archivo .htaccess está presente en la raíz del proyecto

4. **Permisos de Archivos**
   ```bash
   chmod 755 -R /path/to/xampp/htdocs/restaurante
   chmod 777 -R /path/to/xampp/htdocs/restaurante/assets/uploads
   ```

## Estructura del Proyecto

```
restaurante/
├── assets/
│   ├── css/
│   ├── js/
│   └── uploads/
├── config/
│   ├── config.php
│   ├── database.php
│   └── Session.php
├── models/
│   ├── UserModel.php
│   ├── ProductModel.php
│   ├── MesaModel.php
│   └── VentaModel.php
├── views/
│   ├── comandas/
│   ├── mesas/
│   ├── productos/
│   └── shared/
├── .htaccess
├── index.php
├── login.php
└── README.md
```

## Usuarios por Defecto

```
Administrador:
Usuario: admin
Contraseña: admin123

Mesero:
Usuario: mesero
Contraseña: mesero123

Cajero:
Usuario: cajero
Contraseña: cajero123
```

## Uso del Sistema

1. **Inicio de Sesión**
   - Acceder a `http://localhost/restaurante`
   - Ingresar credenciales según el rol

2. **Panel de Control**
   - Navegación intuitiva según el rol del usuario
   - Acceso a funciones específicas por rol

3. **Gestión de Pedidos**
   - Los meseros pueden crear nuevas comandas
   - Cocina y barra ven sus pedidos respectivos
   - Actualización en tiempo real

## Seguridad

- Autenticación segura de usuarios
- Protección contra SQL Injection
- Validación de datos en formularios
- Control de acceso basado en roles
- Sesiones seguras

## Mantenimiento

- Realizar respaldos regulares de la base de datos
- Mantener actualizado el sistema operativo y PHP
- Revisar logs de errores periódicamente
- Actualizar contraseñas regularmente

## Soporte

Para reportar problemas o solicitar soporte, por favor crear un issue en el repositorio del proyecto.

## Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo LICENSE para más detalles.