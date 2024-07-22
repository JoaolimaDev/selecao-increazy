# Imagem oficial do PHP
FROM php:8.1-cli

WORKDIR /app

# Instalar dependências
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql

# Copia os arquivos do aplicativo Laravel
COPY . .

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install --no-plugins --no-scripts

RUN php artisan migrate --force

EXPOSE 3000

RUN php artisan db:seed --class=UserSeeder --force

# Inicia o servidor de desenvolvimento PHP
CMD php artisan serve --host=0.0.0.0 --port=3000
