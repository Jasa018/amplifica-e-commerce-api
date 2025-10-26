#!/bin/bash

echo "========================================"
echo "   Amplifica E-commerce API Setup"
echo "========================================"
echo

echo "[1/8] Verificando Docker..."
if ! command -v docker &> /dev/null; then
    echo "ERROR: Docker no está instalado"
    echo "Por favor instala Docker y asegúrate de que esté ejecutándose"
    exit 1
fi

if ! docker info &> /dev/null; then
    echo "ERROR: Docker no está ejecutándose"
    echo "Por favor inicia Docker Desktop"
    exit 1
fi
echo "✓ Docker detectado"

echo
echo "[2/8] Configurando variables de entorno..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo "✓ Archivo .env creado"
else
    echo "✓ Archivo .env ya existe"
fi

echo
echo "[3/8] Instalando dependencias PHP..."
docker run --rm -v $(pwd):/app composer install --no-interaction --optimize-autoloader
if [ $? -ne 0 ]; then
    echo "ERROR: Falló al instalar dependencias"
    exit 1
fi
echo "✓ Dependencias instaladas"

echo
echo "[4/8] Construyendo contenedores Docker..."
docker-compose up -d --build
if [ $? -ne 0 ]; then
    echo "ERROR: Falló al construir contenedores"
    exit 1
fi
echo "✓ Contenedores construidos exitosamente"

echo
echo "[5/9] Esperando que MySQL esté listo..."
echo "Verificando conexión a base de datos..."
while ! docker-compose exec -T mysql mysql -u root -ppassword -e "SELECT 1" >/dev/null 2>&1; do
    echo "Esperando MySQL..."
    sleep 5
done
echo "✓ MySQL está listo"

echo
echo "[6/9] Generando clave de aplicación..."
docker-compose exec -T laravel.test php artisan key:generate --force
echo "✓ Clave de aplicación generada"

echo
echo "[7/9] Configurando base de datos..."
docker-compose exec -T laravel.test php artisan migrate --force
docker-compose exec -T laravel.test php artisan db:seed --force
if [ $? -ne 0 ]; then
    echo "ERROR: Falló al configurar base de datos"
    exit 1
fi
echo "✓ Base de datos configurada"

echo
echo "[8/9] Configurando permisos..."
docker-compose exec -T laravel.test chmod -R 777 storage
docker-compose exec -T laravel.test chown -R www-data:www-data storage
docker-compose exec -T laravel.test chmod -R 775 bootstrap/cache
echo "✓ Permisos configurados"

echo
echo "[9/9] Limpiando cache y optimizando..."
docker-compose exec -T laravel.test php artisan config:clear
docker-compose exec -T laravel.test php artisan view:clear
docker-compose exec -T laravel.test php artisan cache:clear
docker-compose exec -T laravel.test php artisan optimize
echo "✓ Cache limpiado y aplicación optimizada"

echo
echo "========================================"
echo "          INSTALACIÓN COMPLETADA"
echo "========================================"
echo
echo "La aplicación está lista para usar:"
echo
echo "🌐 Aplicación Web: http://localhost"
echo "📚 Documentación API: http://localhost/api/documentation"
echo
echo "Credenciales de acceso:"
echo "👤 Usuario: admin@example.com"
echo "🔑 Contraseña: password"
echo
echo "========================================"