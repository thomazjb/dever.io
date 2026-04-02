# =============================================
# Dockerfile PHP 8.3-FPM para Yii2
# Inclui extensões: pdo_mysql, mbstring, intl, gd, zip
# Composer instalado globalmente
# =============================================
FROM php:8.3-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libicu-dev \
    libzip-dev \
    libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# Configurar e instalar extensões PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mbstring \
        intl \
        gd \
        zip \
        opcache

# Instalar Composer globalmente
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Diretório de trabalho
WORKDIR /var/www/html

# Copiar entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expor porta FPM
EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
