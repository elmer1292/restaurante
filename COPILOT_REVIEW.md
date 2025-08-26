# Auditoría del Proyecto - WebApp Restaurante (PHP Puro)

Este proyecto es una **aplicación web de restaurante en PHP puro**.  
Quiero que actúes como un **auditor senior de software** y revises **todo el proyecto completo** siguiendo estos criterios:

---

## 1. Buenas Prácticas de Programación
- Revisa el uso de variables, funciones y clases.
- Sugiere refactorización si hay duplicación de código (DRY).
- Verifica que exista separación clara entre lógica de negocio, vistas y controladores.
- Identifica si el proyecto tiene una arquitectura modular (MVC).

---

## 2. Seguridad
- Asegúrate de que todas las consultas SQL usen **sentencias preparadas** (PDO o MySQLi).
- Revisa si hay validación y sanitización de entradas (inputs, formularios, parámetros GET/POST).
- Identifica posibles riesgos de **XSS** (Cross-Site Scripting).
- Detecta ausencia de protección contra **CSRF** (Cross-Site Request Forgery).
- Revisa cómo se manejan las contraseñas (usar `password_hash()` y `password_verify()`).
- Verifica que las sesiones se manejen de forma segura.

---

## 3. Estructura del Proyecto
- Evalúa la organización de carpetas (`models/`, `views/`, `controllers/`).
- Verifica que haya un archivo central de configuración (`config.php`) para conexión DB y constantes.
- Sugiere mejoras en modularidad y organización de archivos.

---

## 4. Optimización
- Revisa la eficiencia de las consultas SQL.
- Busca código duplicado o innecesario.
- Sugiere mejoras para escalabilidad y mantenibilidad.
- Señala dónde se pueden usar funciones/helpers para evitar redundancia.

---

## 5. Estándares y Documentación
- Verifica si se usan comentarios cuando realmente son necesarios.
- Revisa consistencia en nombres de variables, funciones y clases.
- Sugiere mejoras en legibilidad del código.

---

# Instrucciones para Copilot
1. Revisa el proyecto **carpeta por carpeta y archivo por archivo**.
2. Identifica problemas y vulnerabilidades **con ejemplos concretos del código**.
3. Da sugerencias claras de cómo corregirlos.
4. Si el código ya cumple con buenas prácticas, confírmalo.
5. No inventes código que no existe: sugiere correcciones sobre lo que ya está escrito.
