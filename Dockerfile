FROM php:8.3-cli

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    libxml2-dev \
    libsqlite3-dev \
    # git \
    unzip \
    curl \
    && docker-php-ext-install pdo pdo_sqlite simplexml \
    && apt-get clean

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /app

# Copiar archivos de la aplicación
COPY . .

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader

# Crear directorio de base de datos
RUN mkdir -p database && chmod 755 database

# Exponer puerto
EXPOSE 8080

# Comando para iniciar la aplicación
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]