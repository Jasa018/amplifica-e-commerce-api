# Amplifica E-commerce API

Sistema de gestión de productos, pedidos y cotizaciones de envío desarrollado con Laravel y Docker.

## 📋 Requisitos Previos

- **Docker Desktop** instalado y ejecutándose
- Puerto 80 disponible en tu sistema

## 🚀 Instalación Automática (Recomendada)

### Windows
```bash
# Ejecutar el script de instalación automática
.\setup.bat
```

### Linux/Mac
```bash
# Dar permisos de ejecución y ejecutar
chmod +x setup.sh
./setup.sh
```

El script automático realizará:
- Construcción de contenedores Docker
- Instalación de dependencias PHP
- Configuración de base de datos
- Ejecución de migraciones y seeders
- Configuración del servidor web

**Acceso después de la instalación:**
- **Aplicación Web**: http://localhost
- **Usuario**: admin@example.com
- **Contraseña**: password

---

## 🔧 Instalación Manual

### 1. Clonar el Repositorio
```bash
git clone <repository-url>
cd amplifica-e-commerce-api
```

### 2. Construir Contenedores Docker
```bash
# Construir y levantar contenedores
docker-compose up -d --build

# Verificar que los contenedores estén ejecutándose
docker-compose ps
```

### 3. Instalar Dependencias PHP
```bash
# Ejecutar composer dentro del contenedor
docker-compose exec app composer install

# O alternativamente
docker exec -it amplifica-app composer install
```

### 4. Configurar Variables de Entorno
```bash
# Copiar archivo de configuración
cp .env.example .env

# Generar clave de aplicación
docker-compose exec app php artisan key:generate
```

### 5. Configurar Base de Datos
```bash
# Ejecutar migraciones
docker-compose exec app php artisan migrate

# Ejecutar seeders (datos de prueba)
docker-compose exec app php artisan db:seed
```

### 6. Configurar Permisos
```bash
# Dar permisos a directorios de Laravel
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### 7. Verificar Instalación
- Acceder a: http://localhost
- Login con: admin@example.com / password

---

## 📚 Documentación de la API

### Swagger UI
La documentación interactiva de la API está disponible en:
- **URL**: http://localhost/api/documentation
- **Acceso**: Enlace "API Docs" en el header de navegación

### Endpoints Principales

#### Autenticación
- `POST /api/auth/login` - Iniciar sesión
- `POST /api/auth/logout` - Cerrar sesión
- `GET /api/auth/user` - Obtener usuario autenticado

#### Productos
- `GET /api/products` - Listar productos
- `POST /api/products` - Crear producto
- `GET /api/products/{id}` - Obtener producto
- `PUT /api/products/{id}` - Actualizar producto
- `DELETE /api/products/{id}` - Eliminar producto

#### Pedidos
- `GET /api/orders` - Listar pedidos
- `POST /api/orders` - Crear pedido
- `GET /api/orders/{id}` - Obtener pedido
- `PUT /api/orders/{id}` - Actualizar pedido
- `DELETE /api/orders/{id}` - Eliminar pedido

#### Cotizaciones
- `POST /api/cotizar` - Cotizar envío

---

## 🎯 Flujo del Proyecto

### Dashboard Principal
El dashboard muestra un resumen del sistema con:
- **Contador de productos** registrados
- **Contador de pedidos** creados
- **Contador de usuarios** del sistema
- **Acceso rápido a cotizaciones** de envío

### Gestión de Productos
- **Crear productos** con información completa (nombre, precio, peso, dimensiones, stock)
- **Editar productos** existentes
- **Eliminar productos** del catálogo
- **Visualizar lista** de todos los productos

### Gestión de Pedidos
- **Crear pedidos** seleccionando productos del catálogo
- **Cálculo automático** de totales
- **Editar pedidos** existentes con productos dinámicos
- **Eliminar pedidos** del sistema
- **Visualización detallada** de cada pedido

### Sistema de Cotizaciones
- **Selección de región y comuna** en cascada
- **Selección de productos** con cantidades
- **Cálculo automático de peso total** (peso unitario × cantidad)
- **Integración con API externa** de Amplifica
- **Visualización de tarifas** disponibles (Express, Estándar, Económica)
- **Manejo de errores** de conexión y autenticación

---

## 🔐 Autenticación y Seguridad

### Autenticación Web
- Sistema de login tradicional con sesiones
- Middleware de autenticación en todas las rutas protegidas
- Logout seguro con invalidación de sesión

### Autenticación API
- **Bearer Token** JWT para endpoints de API
- Tokens con expiración automática
- Renovación automática de tokens cuando expiran

### Manejo de Errores
- **Logging estructurado** de todas las operaciones
- **Manejo específico** por tipo de error (conexión, validación, autenticación)
- **Respuestas consistentes** con códigos HTTP apropiados
- **Reintentos automáticos** para errores de token expirado

---

## 🛠 Arquitectura Técnica

### Backend
- **Laravel 10** - Framework PHP
- **MySQL 8.0** - Base de datos
- **Docker** - Containerización
- **Nginx** - Servidor web

### Frontend
- **Blade Templates** - Motor de plantillas
- **Alpine.js** - Interactividad JavaScript
- **Tailwind CSS** - Estilos

### Integraciones
- **API Externa Amplifica** - Cotizaciones de envío
- **Swagger/OpenAPI** - Documentación de API
- **JWT Authentication** - Tokens de acceso

### Características Avanzadas
- **Manejo robusto de errores** con logging
- **Validación exhaustiva** de datos
- **Transacciones de base de datos** para operaciones críticas
- **Cache de tokens** con renovación automática
- **Documentación interactiva** con Swagger UI

---

## 🤖 Desarrollo con IA

Este proyecto fue desarrollado utilizando **Geminis**, **Copilot** y **Amazon Q** un asistente de IA que proporcionó:

### Capacidades Utilizadas
- **Generación de código** Laravel siguiendo mejores prácticas
- **Implementación de patrones** de diseño apropiados
- **Manejo robusto de errores** y logging estructurado
- **Integración con APIs externas** con reintentos automáticos
- **Documentación Swagger** completa y detallada
- **Validaciones exhaustivas** de datos de entrada
- **Arquitectura escalable** y mantenible

---

## 📞 Soporte

Para problemas o consultas:
1. Verificar logs en `storage/logs/laravel.log`
2. Revisar estado de contenedores: `docker-compose ps`
3. Reiniciar servicios: `docker-compose restart`

## 🔄 Comandos Útiles

```bash
# Ver logs de la aplicación
docker-compose logs -f app

# Acceder al contenedor de la aplicación
docker-compose exec app bash

# Reiniciar todos los servicios
docker-compose restart

# Detener todos los servicios
docker-compose down

# Reconstruir contenedores
docker-compose up -d --build
```