# Usar uma imagem base oficial do PHP 8.2 (ou 8.3) com Apache
# O Apache já vem configurado para servir ficheiros, o que facilita
FROM php:8.2-apache

# Define o diretório de trabalho dentro do container
WORKDIR /var/www/html

# Instala extensões PHP comuns necessárias para o Laravel
# (Pode adicionar ou remover conforme a necessidade do seu projeto)
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

# Copia o seu código (exceto o que está no .dockerignore)
COPY . .

# Instala as dependências do Composer
RUN composer install --optimize-autoloader --no-dev --no-scripts

# O Laravel 12+ usa 'public_path'
# Esta linha aponta o Apache para a pasta /public do Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Define as permissões corretas para o Laravel
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Expõe a porta que o Apache usa (o Render vai gerir isto)
EXPOSE 80