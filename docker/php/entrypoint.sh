#!/bin/bash
set -e

# =============================================
# Entrypoint: instala dependências, roda
# migrations e inicia PHP-FPM
# =============================================

echo "==> Instalando dependências Composer..."
if [ -f /var/www/html/composer.json ]; then
    cd /var/www/html
    composer install --no-interaction --optimize-autoloader --no-dev 2>/dev/null || \
    composer install --no-interaction --optimize-autoloader
fi

echo "==> Aguardando MySQL ficar disponível..."
MAX_RETRIES=30
RETRY=0
until php -r "try { new PDO('mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_NAME'), getenv('DB_USER'), getenv('DB_PASSWORD')); echo 'OK'; } catch(Exception \$e) { exit(1); }" 2>/dev/null; do
    RETRY=$((RETRY+1))
    if [ $RETRY -ge $MAX_RETRIES ]; then
        echo "==> MySQL não respondeu após $MAX_RETRIES tentativas. Continuando..."
        break
    fi
    echo "==> MySQL não disponível, tentativa $RETRY/$MAX_RETRIES..."
    sleep 2
done

echo "==> Executando migrations..."
if [ -f /var/www/html/yii ]; then
    cd /var/www/html
    php yii migrate --interactive=0 || echo "==> Migrations falharam ou já aplicadas."
fi

echo "==> Ajustando permissões..."
mkdir -p /var/www/html/web/assets /var/www/html/runtime
chown -R www-data:www-data /var/www/html/runtime /var/www/html/web/assets 2>/dev/null || true

echo "==> Iniciando PHP-FPM..."
exec php-fpm
