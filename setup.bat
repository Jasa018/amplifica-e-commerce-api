@echo off
echo ========================================
echo   Amplifica E-commerce API Setup
echo ========================================
echo.

echo [1/7] Verificando Docker Desktop...
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Docker Desktop no esta instalado o no esta ejecutandose
    echo Por favor instala Docker Desktop y asegurate de que este ejecutandose
    pause
    exit /b 1
)
echo ✓ Docker Desktop detectado

echo.
echo [2/7] Construyendo contenedores Docker...
docker-compose up -d --build
if %errorlevel% neq 0 (
    echo ERROR: Fallo al construir contenedores
    pause
    exit /b 1
)
echo ✓ Contenedores construidos exitosamente

echo.
echo [3/7] Esperando que los servicios esten listos...
timeout /t 10 /nobreak >nul
echo ✓ Servicios iniciados

echo.
echo [4/7] Instalando dependencias PHP...
docker-compose exec -T app composer install --no-interaction --optimize-autoloader
if %errorlevel% neq 0 (
    echo ERROR: Fallo al instalar dependencias
    pause
    exit /b 1
)
echo ✓ Dependencias instaladas

echo.
echo [5/7] Configurando aplicacion...
docker-compose exec -T app cp .env.example .env
docker-compose exec -T app php artisan key:generate --force
echo ✓ Configuracion completada

echo.
echo [6/7] Configurando base de datos...
docker-compose exec -T app php artisan migrate --force
docker-compose exec -T app php artisan db:seed --force
if %errorlevel% neq 0 (
    echo ERROR: Fallo al configurar base de datos
    pause
    exit /b 1
)
echo ✓ Base de datos configurada

echo.
echo [7/7] Configurando permisos...
docker-compose exec -T app chmod -R 775 storage bootstrap/cache
docker-compose exec -T app chown -R www-data:www-data storage bootstrap/cache
echo ✓ Permisos configurados

echo.
echo ========================================
echo          INSTALACION COMPLETADA
echo ========================================
echo.
echo La aplicacion esta lista para usar:
echo.
echo 🌐 Aplicacion Web: http://localhost
echo 📚 Documentacion API: http://localhost/api/documentation
echo.
echo Credenciales de acceso:
echo 👤 Usuario: admin@example.com
echo 🔑 Contraseña: password
echo.
echo ========================================
pause