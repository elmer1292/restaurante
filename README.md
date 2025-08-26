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

---

## Seguridad CSRF Global

Desde agosto 2025, el sistema implementa protección CSRF en todos los formularios y peticiones AJAX.

### ¿Cómo funciona?
- Se genera un token único por sesión y se inyecta automáticamente en el `<head>` de todas las vistas.
- Todos los formularios HTML incluyen:
  ```html
  <input type="hidden" name="csrf_token" value="<?= Csrf::getToken() ?>">
  ```
- Todas las peticiones AJAX incluyen el token en el header:
  ```js
  headers: { 'X-CSRF-Token': csrfToken }
  ```
  Donde `csrfToken` se obtiene de:
  ```js
  window.csrfToken // o
  document.querySelector('meta[name="csrf-token"]').content
  ```

### Validación en el backend
- Los controladores validan el token antes de procesar cualquier acción que modifique datos.
- Si el token es inválido o falta, la petición se rechaza con error 403.

### Pruebas manuales
- Enviar un POST/PUT/DELETE sin token → respuesta 403.
- Enviar con token correcto → acción permitida.
- AJAX con header correcto → acción permitida.

---

## Seguridad y Validación

- Todas las rutas POST/PUT/DELETE requieren validación de CSRF.
- Validación y sanitización centralizada con el helper Validator.
- Todas las consultas SQL usan sentencias preparadas (PDO).
- Los parámetros de paginación están validados y limitados.
- Las sesiones usan cookies HttpOnly y Secure (si HTTPS).

## Cómo correr el proyecto

1. Instala XAMPP y asegúrate de tener PHP y MySQL activos.
2. Clona el repositorio en `htdocs`.
3. Configura la base de datos en `config/config.php`.
4. Importa el archivo SQL desde la carpeta `backups`.
5. Accede a `http://localhost/restaurante` en tu navegador.

## Cómo contribuir

- Haz fork del repositorio y crea una rama para tu cambio.
- Sigue la guía de nombres y estilos del proyecto.
- Agrega docblocks en funciones complejas.
- Realiza pruebas manuales antes de enviar PR.
- Describe claramente tu cambio en el PR.

---

## Mantenimiento

- Realizar respaldos regulares de la base de datos
- Mantener actualizado el sistema operativo y PHP
- Revisar logs de errores periódicamente
- Actualizar contraseñas regularmente

## Soporte

Para reportar problemas o solicitar soporte, por favor crear un issue en el repositorio del proyecto.

## Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo LICENSE para más detalles.

## Guía de Nombres Consistente

- **Idioma:** Español para todo el código y nombres.
- **Clases:** StudlyCaps (Ejemplo: VentaModel, ProductoController)
- **Métodos:** camelCase (Ejemplo: getAllVentas, crearUsuario)
- **Archivos:**
  - Modelos: NombreModel.php (Ejemplo: VentaModel.php)
  - Controladores: NombreController.php (Ejemplo: ProductoController.php)
  - Helpers: NombreHelper.php o función específica (Ejemplo: Validator.php)
- **Variables:** camelCase (Ejemplo: totalVentas, nombreProducto)
- **Constantes:** MAYÚSCULAS_CON_GUIONES (Ejemplo: DB_HOST)

> Mantener el idioma y estilo en todo el proyecto. Si se renombra una clase o método, actualizar los includes/imports/rutas correspondientes.

---