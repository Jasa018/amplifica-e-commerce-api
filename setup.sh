#!/bin/bash

echo "========================================"
echo "   Amplifica E-commerce API Setup"
echo "========================================"
echo

echo "[1/7] Verificando Docker Desktop..."
if ! command -v docker &> /dev/null; then
    echo "ERROR: Docker Desktop no está instalado o no está ejecutándose"
    echo "Por favor instala Docker Desktop y asegúrate de que esté ejecutándose"
    exit 1
fi
echo "✓ Docker Desktop detectado"

echo
echo "[2/7] Construyendo contenedores Docker..."
if ! docker-compose up -d --build; then
    echo "ERROR: Falló al construir contenedores"
    exit 1
fi
echo "✓ Contenedores construidos exitosamente"

echo
echo "[3/7] Esperando que los servicios estén listos..."
sleep 10
echo "✓ Servicios iniciados"

echo
echo "[4/7] Instalando dependencias PHP..."
if ! docker-compose exec -T app composer install --no-interaction --optimize-autoloader; then
    echo "ERROR: Falló al instalar dependencias"
    exit 1
fi
echo "✓ Dependencias instaladas"

echo
echo "[5/7] Configurando aplicación..."
docker-compose exec -T app cp .env.example .env
docker-compose exec -T app php artisan key:generate --force
echo "✓ Configuración completada"

echo
echo "[6/7] Configurando base de datos..."
if ! docker-compose exec -T app php artisan migrate --force; then
    echo "ERROR: Falló al configurar base de datos"
    exit 1
fi
if ! docker-compose exec -T app php artisan db:seed --force; then
    echo "ERROR: Falló al insertar datos de prueba"
    exit 1
fi
echo "✓ Base de datos configurada"

echo
echo "[7/7] Configurando permisos..."
docker-compose exec -T app chmod -R 775 storage bootstrap/cache
docker-compose exec -T app chown -R www-data:www-data storage bootstrap/cache
echo "✓ Permisos configurados"

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