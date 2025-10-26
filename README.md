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
- Configuración de variables de entorno
- Instalación de dependencias PHP
- Construcción de contenedores Docker
- Configuración de base de datos
- Ejecución de migraciones y seeders
- Configuración de permisos
- Limpieza de cache y optimización

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

### 2. Configurar Variables de Entorno
```bash
# Copiar archivo de configuración
cp .env.example .env

# Editar .env con tu configuración si es necesario
```

### 3. Instalar Dependencias PHP
```bash
# Instalar dependencias con Composer (requiere PHP local)
composer install

# O usar Docker para instalar dependencias
docker run --rm -v $(pwd):/app composer install
```

### 4. Construir Contenedores Docker
```bash
# Construir y levantar contenedores
docker-compose up -d --build

# Verificar que los contenedores estén ejecutándose
docker-compose ps
```

### 5. Esperar que MySQL esté listo
```bash
# Verificar que MySQL esté funcionando
docker-compose exec mysql mysql -u root -ppassword -e "SELECT 1"
```

### 6. Generar Clave de Aplicación
```bash
# Generar clave de aplicación Laravel
docker-compose exec laravel.test php artisan key:generate
```

### 7. Configurar Base de Datos
```bash
# Ejecutar migraciones
docker-compose exec laravel.test php artisan migrate

# Ejecutar seeders (datos de prueba)
docker-compose exec laravel.test php artisan db:seed
```

### 8. Configurar Permisos
```bash
# Dar permisos completos a storage y bootstrap/cache
docker-compose exec laravel.test chmod -R 777 storage
docker-compose exec laravel.test chown -R www-data:www-data storage
docker-compose exec laravel.test chmod -R 775 bootstrap/cache
```

### 9. Limpiar Cache y Optimizar
```bash
# Limpiar cache y optimizar aplicación
docker-compose exec laravel.test php artisan config:clear
docker-compose exec laravel.test php artisan view:clear
docker-compose exec laravel.test php artisan cache:clear
docker-compose exec laravel.test php artisan optimize
```

### 10. Verificar Instalación
- Acceder a: http://localhost
- Login con: admin@example.com / password

**Nota:** Si encuentras errores de permisos en views, ejecuta:
```bash
# Solución completa para errores de permisos
docker-compose exec laravel.test chmod -R 777 storage
docker-compose exec laravel.test chown -R www-data:www-data storage
docker-compose exec laravel.test php artisan view:clear
docker-compose exec laravel.test php artisan config:clear
```

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

#### Usuarios
- `GET /api/users` - Listar usuarios
- `POST /api/users` - Crear usuario
- `GET /api/users/{id}` - Obtener usuario
- `PUT /api/users/{id}` - Actualizar usuario
- `DELETE /api/users/{id}` - Eliminar usuario

#### Cotizaciones
- `POST /api/cotizar-envio` - Cotizar envío
- `GET /api/historial-cotizaciones` - Obtener historial de cotizaciones
- `GET /api/historial-cotizaciones/{id}` - Obtener detalle de cotización
- `DELETE /api/historial-cotizaciones/{id}` - Eliminar cotización del historial

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
- **Filtros avanzados** por nombre, precio y stock
- **Paginación** de 10 elementos por página

### Gestión de Pedidos
- **Crear pedidos** seleccionando productos del catálogo
- **Cálculo automático** de totales
- **Editar pedidos** existentes con productos dinámicos
- **Eliminar pedidos** del sistema
- **Visualización detallada** de cada pedido
- **Filtros avanzados** por cliente, fechas y total
- **Paginación** de 10 elementos por página

### Gestión de Usuarios
- **Crear usuarios** con información completa (nombre, email, contraseña)
- **Editar usuarios** existentes con actualización opcional de contraseña
- **Eliminar usuarios** del sistema (con protección contra auto-eliminación)
- **Visualizar lista** de todos los usuarios registrados
- **Ver detalles** de usuarios individuales
- **Filtros avanzados** por nombre, email y fecha de creación
- **Paginación** de 10 elementos por página
- **API completa** con Resource Collections y tests automatizados

### Sistema de Cotizaciones
- **Selección de región y comuna** en cascada
- **Selección de productos** con cantidades
- **Cálculo automático de peso total** (peso unitario × cantidad)
- **Integración con API externa** de Amplifica
- **Visualización de tarifas** disponibles (Express, Estándar, Económica)
- **Persistencia del historial** de cotizaciones por usuario
- **Gestión del historial** (ver, eliminar cotizaciones anteriores)
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
- **Logs estructurados de API externa** con métricas de rendimiento

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
- **Resource Collections** para estructurar respuestas de API
- **Logs estructurados de API externa** con métricas detalladas

---

## 📋 Persistencia del Historial de Cotizaciones

### Funcionalidad Implementada
El sistema guarda automáticamente todas las cotizaciones realizadas por usuarios autenticados:

#### Características del Historial
- **Persistencia automática** de cada cotización realizada
- **Asociación por usuario** - cada usuario ve solo su historial
- **Información completa** guardada: origen, destino, productos, tarifas, peso total
- **Gestión del historial** via API y interfaz web

#### Endpoints del Historial
```bash
# Obtener historial del usuario (últimas 10 por defecto)
GET /api/historial-cotizaciones?limit=20

# Ver detalle de cotización específica
GET /api/historial-cotizaciones/{id}

# Eliminar cotización del historial
DELETE /api/historial-cotizaciones/{id}
```

#### Estructura de Datos Guardados
```json
{
  "id": 1,
  "user_id": 1,
  "region_origen": "Metropolitana",
  "comuna_origen": "Santiago",
  "region_destino": "Valparaíso",
  "comuna_destino": "Viña del Mar",
  "peso_total": 3.50,
  "productos": [
    {"weight": 1.5, "quantity": 2},
    {"weight": 0.5, "quantity": 1}
  ],
  "tarifas": [
    {"name": "Express", "price": 5000},
    {"name": "Estándar", "price": 3000}
  ],
  "created_at": "2024-01-01 12:00:00"
}
```

---

## 📈 Logs Estructurados de API Externa

### Implementación de Logging
El sistema implementa logs estructurados completos para todas las peticiones a la API externa de Amplifica:

#### Información Registrada
- **Request Logs**: Método HTTP, endpoint, datos de solicitud, intentos
- **Response Logs**: Código de estado, duración en milisegundos, tamaño de respuesta
- **Error Logs**: Detalles de errores de conexión, autenticación y validación
- **Performance Metrics**: Tiempo de respuesta, reintentos, tamaño de datos

#### Tipos de Logs Generados
```bash
# Logs de autenticación
[INFO] API Request - Token Authentication
[INFO] API Response - Token Authentication
[INFO] API Success - Token obtained successfully

# Logs de peticiones autenticadas
[INFO] API Request - Authenticated
[INFO] API Response - Authenticated
[WARNING] API Token Expired - Refreshing
[INFO] API Success - Request completed

# Logs de errores
[ERROR] API Connection Error - Authenticated Request
[ERROR] API Error - Max retries exceeded
[ERROR] API Error - Custom Credentials Auth Failed
```

#### Estructura de Logs
```json
{
  "endpoint": "/cotizar",
  "method": "POST",
  "status_code": 200,
  "duration_ms": 1250.75,
  "attempt": 1,
  "success": true,
  "response_size": 2048,
  "request_data": {...}
}
```

#### Ubicación de Logs
- **Archivo**: `storage/logs/laravel.log`
- **Formato**: JSON estructurado con contexto completo
- **Rotación**: Automática según configuración de Laravel

---

## 📊 Resource Collections y Transformadores

### Estructura de Respuestas API
Todas las respuestas de la API utilizan **Resource Collections** para estructurar y transformar los datos:

#### ProductResource
```json
{
  "id": 1,
  "name": "Producto ejemplo",
  "price": 99.99,
  "weight": 1.5,
  "dimensions": {
    "width": 10,
    "height": 5,
    "length": 15
  },
  "stock": 100,
  "created_at": "2024-01-01 12:00:00",
  "updated_at": "2024-01-01 12:00:00"
}
```

#### ProductCollection (con metadatos)
```json
{
  "data": [...],
  "meta": {
    "total_products": 50,
    "total_stock": 2500,
    "average_price": 75.50
  }
}
```

#### OrderResource (con relaciones)
```json
{
  "id": 1,
  "cliente_nombre": "Juan Pérez",
  "fecha": "2024-01-01",
  "total": 199.98,
  "order_details": [
    {
      "id": 1,
      "quantity": 2,
      "unit_price": 99.99,
      "subtotal": 199.98,
      "product": {...}
    }
  ],
  "created_at": "2024-01-01 12:00:00"
}
```

#### UserResource
```json
{
  "id": 1,
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "email_verified_at": "2024-01-01 12:00:00",
  "created_at": "2024-01-01 12:00:00",
  "updated_at": "2024-01-01 12:00:00"
}
```

## 🧪 Tests Automatizados con PHPUnit

### Cobertura de Tests
El proyecto incluye una suite completa de tests automatizados que cubren:

#### Tests Unitarios (`tests/Unit/`) - ✅ 11/11 Pasando
- **ProductTest**: Validación de modelos y atributos de productos
- **OrderTest**: Relaciones y cálculos de pedidos
- **AmplificaApiServiceTest**: Integración con API externa y cache de tokens
- **HelperTest**: Configuración del entorno de testing

#### Tests de Feature (`tests/Feature/`) - ✅ 45/45 Pasando
- **ProductApiTest**: ✅ CRUD completo de productos via API
- **OrderApiTest**: ✅ Gestión de pedidos 
- **UserApiTest**: ✅ CRUD completo de usuarios con autenticación y validaciones
- **AuthApiTest**: ✅ Autenticación JWT y manejo de sesiones
- **CotizacionApiTest**: ✅ Cotizaciones de envío
- **HistorialCotizacionApiTest**: ✅ Historial de cotizaciones con Resource Collections
- **WebRoutesTest**: ✅ Rutas web y autenticación

### Ejecutar Tests

```bash
# Ejecutar todos los tests
docker-compose exec laravel.test php artisan test

# Ejecutar tests con cobertura
docker-compose exec laravel.test php artisan test --coverage

# Ejecutar solo tests unitarios
docker-compose exec laravel.test php artisan test --testsuite=Unit

# Ejecutar solo tests de feature
docker-compose exec laravel.test php artisan test --testsuite=Feature

# Ejecutar test específico
docker-compose exec laravel.test php artisan test --filter=ProductTest
```

### Configuración de Testing
- **Base de datos**: SQLite en memoria para tests rápidos
- **HTTP Mocking**: Simulación de APIs externas con Laravel HTTP
- **Factory Pattern**: Generación de datos de prueba consistentes
- **RefreshDatabase**: Limpieza automática entre tests
- **Sanctum Testing**: Autenticación simulada para APIs

### Características de los Tests
- **Validación de datos**: Verificación de reglas de negocio
- **Manejo de errores**: Tests de casos de fallo y excepciones
- **Integración API**: Tests end-to-end de endpoints
- **Mocking externo**: Simulación de servicios de terceros
- **Autenticación**: Tests de seguridad y autorización

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
- **Tests automatizados** con PHPUnit y cobertura completa

---

## 🔧 Solución de Problemas

### Error de Permisos en Views
Si encuentras el error:
```
file_put_contents(/var/www/html/storage/framework/views/...): Failed to open stream: Permission denied
```

**Solución:**
```bash
# Corregir permisos de directorios Laravel
docker-compose exec laravel.test chmod -R 775 storage bootstrap/cache
docker-compose exec laravel.test chown -R www-data:www-data storage bootstrap/cache

# Limpiar cache de views
docker-compose exec laravel.test php artisan view:clear
docker-compose exec laravel.test php artisan config:clear
```

### Otros Problemas Comunes

**Contenedores no inician:**
```bash
# Verificar estado
docker-compose ps

# Ver logs
docker-compose logs laravel.test
docker-compose logs mysql

# Reiniciar servicios
docker-compose restart
```

**Base de datos no conecta:**
```bash
# Esperar a que MySQL esté listo
docker-compose exec mysql mysql -u root -p -e "SHOW DATABASES;"

# Recrear base de datos
docker-compose exec laravel.test php artisan migrate:fresh --seed
```

**Puerto 80 ocupado:**
```bash
# Cambiar puerto en docker-compose.yml
# Modificar: "80:80" por "8080:80"
# Acceder en: http://localhost:8080
```

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

# Ejecutar tests
docker-compose exec laravel.test php artisan test

# Ejecutar tests con cobertura
docker-compose exec laravel.test php artisan test --coverage

# Reiniciar todos los servicios
docker-compose restart

# Detener todos los servicios
docker-compose down

# Reconstruir contenedores
docker-compose up -d --build
```