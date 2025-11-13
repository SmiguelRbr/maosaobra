# --- Stage 1: Build (Instala dependências com Composer) ---
FROM composer:2.7 as build

# Define o diretório de trabalho e copia os arquivos
WORKDIR /app
COPY . .

# Instala as dependências APENAS de produção
RUN composer install --no-dev --optimize-autoloader

# --- Stage 2: Production (Imagem Final) ---
# Usa PHP 8.3 FPM (Alpine é leve)
FROM php:8.3-fpm-alpine

# Instala ferramentas do sistema (Nginx, Supervisor) e extensões PHP
RUN apk update && apk add --no-cache \
    nginx \
    supervisor \
    # Instale A EXTENSÃO DO SEU BANCO DE DADOS AQUI (pgsql, mysql, etc.)
    libpq-dev \
    libzip-dev \
    # Instala extensões PHP
    && docker-php-ext-install pdo_pgsql zip

# Define o diretório da aplicação
WORKDIR /var/www/html

# Copia os arquivos do projeto e dependências do estágio de build
COPY --from=build /app .

# Copia as configurações do servidor e do supervisor
COPY docker/nginx/nginx-site.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Ajusta as permissões de cache e storage (CRUCIAL para Laravel)
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Copia e torna executável o script de inicialização
COPY scripts/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Porta que o Nginx escuta
EXPOSE 80

# Comando para iniciar o contêiner
CMD ["/usr/local/bin/start.sh"]