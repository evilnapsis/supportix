# Supportix v2.0 (MVC)

Supportix es un sistema moderno de Gestión de Tickets de Soporte desarrollado en **PHP** y **MySQL**, con arquitectura limpia **MVC**, capa de servicios, motor de plantillas **Twig** y enrutamiento con **FastRoute**.

---

## 🚀 Novedades y Arquitectura (v2.0)

- **Arquitectura MVC con Enrutamiento Limpio**: Implementación de **FastRoute** para URLs amigables (`/home`, `/tickets`, `/projects`, `/categories`, `/reports`, `/users`, `/profile`).
- **Motor de Plantillas Twig**: Vistas desacopladas, seguras y reutilizables con soporte de herencia en layouts.
- **Seguridad Integrada**:
  - Protección contra ataques **CSRF** en todos los formularios mediante tokens de sesión únicos.
  - Almacenamiento seguro de contraseñas con hash criptográfico.
  - Verificación de sesión y roles (`Administrador` y `Usuario normal`).
- **Capa de Servicios**: Desacoplamiento de la lógica de negocio en clases de servicio dedicadas:
  - `AuthService`: Verificación y autenticación de usuarios.
  - `HomeService`: Métricas en tiempo real para el panel de control.
  - `TicketService`: Creación, actualización, filtrado y eliminación de tickets de soporte.
  - `ProjectService`: Gestión integral de proyectos.
  - `CategoryService`: Catálogo de categorías para tickets.
  - `ReportService`: Reportes dinámicos por proyecto, prioridad, estado y fechas.
  - `UserService`: Administración de usuarios, permisos y credenciales.
- **Interfaz de Usuario Moderna**: Plantilla integrada con **CoreUI v4**, componentes **Bootstrap 5**, **Bootstrap Icons**, **DataTables** en español y notificaciones interactivas con **SweetAlert2**.

---

## 📦 Módulos Principales

- **Dashboard / Inicio**: Métricas de resumen (tickets pendientes, proyectos, categorías, usuarios) y accesos directos.
- **Tickets**: Gestión completa de tickets con filtros combinados (palabra clave, proyecto, categoría, fecha), estados (Pendiente, En Desarrollo, Terminado, Cancelado), prioridades (Alta, Media, Baja) y tipos (Ticket, Bug, Sugerencia, Característica).
- **Proyectos**: Catálogo y mantenimiento de proyectos.
- **Categorías**: Catálogo y clasificación temática de tickets.
- **Reportes**: Generador de reportes de tickets con filtros avanzados.
- **Usuarios**: Control de cuentas de usuario, roles de administrador y estado activo/inactivo.
- **Mi Perfil**: Información de la cuenta conectada y cambio seguro de contraseña.

---

## 🛠️ Requisitos del Sistema

- **Servidor Web**: Apache con módulo `mod_rewrite` habilitado.
- **PHP**: 7.4 o superior (recomendado PHP 8.x) con extensiones `pdo_mysql` y `mysqli`.
- **Base de Datos**: MySQL 5.7+ o MariaDB 10.3+.
- **Composer**: Para instalación de dependencias Twig y FastRoute.

---

## ⚙️ Instalación y Configuración

1. **Colocar el proyecto en el servidor**:
   Ubicar la carpeta `supportix2` dentro del directorio web (por ejemplo, `htdocs` en XAMPP).

2. **Instalar dependencias**:
   ```bash
   composer install
   ```

3. **Base de Datos**:
   Importar el archivo `schema.sql` en MySQL/MariaDB:
   ```sql
   CREATE DATABASE IF NOT EXISTS supportix;
   USE supportix;
   SOURCE schema.sql;
   ```

4. **Configuración de Conexión**:
   Editar los parámetros de conexión en [`core/controller/Database.php`](file:///Applications/XAMPP/xamppfiles/htdocs/supportix2/supportix2/core/controller/Database.php):
   ```php
   $this->user = "root";
   $this->pass = "";
   $this->host = "localhost";
   $this->ddbb = "supportix";
   ```

5. **Acceso al Sistema**:
   Navegar a `http://localhost/supportix/`.

6. **Credenciales por defecto**:
   - **Usuario**: `admin`
   - **Contraseña**: `admin`

---

## 📄 Créditos
Desarrollado por [Evilnapsis](https://evilnapsis.com/).