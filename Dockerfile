# Usar uma imagem base oficial do PHP 8.2 (ou 8.3) com Apache
FROM php:8.2-apache

# Define o diretório de trabalho
WORKDIR /var/www/html

# Instala extensões PHP comuns necessárias para o Laravel e git
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pdo_pgsql bcmath

# Copia o 'composer' para o container
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Copia o código (exceto o que está no .dockerignore)
COPY . .

# Instala as dependências e otimiza o Laravel (Passos de "Build")
RUN composer install --optimize-autoloader --no-dev
RUN php artisan config:cache
RUN php artisan route:cache

# Configura o Apache para apontar para a pasta /public do Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Define as permissões corretas para o Laravel
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Expõe a porta que o Apache usa
EXPOSE 80