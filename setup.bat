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
echo [2/7] Configurando variables de entorno...
if not exist .env (
    copy .env.example .env >nul
    echo ✓ Archivo .env creado
) else (
    echo ✓ Archivo .env ya existe
)

echo.
echo [3/7] Instalando dependencias PHP...
docker run --rm -v %cd%:/app composer install --no-interaction --optimize-autoloader
if %errorlevel% neq 0 (
    echo ERROR: Fallo al instalar dependencias
    pause
    exit /b 1
)
echo ✓ Dependencias instaladas

echo.
echo [4/7] Construyendo contenedores Docker...
docker-compose up -d --build
if %errorlevel% neq 0 (
    echo ERROR: Fallo al construir contenedores
    pause
    exit /b 1
)
echo ✓ Contenedores construidos exitosamente

echo.
echo [5/9] Esperando que MySQL este listo...
echo Verificando conexion a base de datos...
:wait_mysql
docker-compose exec -T mysql mysql -u root -ppassword -e "SELECT 1" >nul 2>&1
if %errorlevel% neq 0 (
    echo Esperando MySQL...
    timeout /t 5 /nobreak >nul
    goto wait_mysql
)
echo ✓ MySQL esta listo

echo.
echo [6/9] Generando clave de aplicacion...
docker-compose exec -T laravel.test php artisan key:generate --force
echo ✓ Clave de aplicacion generada

echo.
echo [7/9] Configurando base de datos...
docker-compose exec -T laravel.test php artisan migrate --force
docker-compose exec -T laravel.test php artisan db:seed --force
if %errorlevel% neq 0 (
    echo ERROR: Fallo al configurar base de datos
    pause
    exit /b 1
)
echo ✓ Base de datos configurada

echo.
echo [8/9] Configurando permisos...
docker-compose exec -T laravel.test chmod -R 777 storage
docker-compose exec -T laravel.test chown -R www-data:www-data storage
docker-compose exec -T laravel.test chmod -R 775 bootstrap/cache
echo ✓ Permisos configurados

echo.
echo [9/9] Limpiando cache y optimizando...
docker-compose exec -T laravel.test php artisan config:clear
docker-compose exec -T laravel.test php artisan view:clear
docker-compose exec -T laravel.test php artisan cache:clear
docker-compose exec -T laravel.test php artisan optimize
echo ✓ Cache limpiado y aplicacion optimizada

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