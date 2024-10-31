# Dockerfile

FROM php:8.2.12-apache

# Instala as dependências necessárias para o PDO MySQL e o MySQL Client
RUN apt-get update && \
    apt-get install -y default-mysql-client && \
    docker-php-ext-install pdo pdo_mysql


##rodar para executar: docker-compose up -d --build

# Define variáveis de ambiente para login no MySQL
ENV MYSQL_DATABASE=iaprova
ENV MYSQL_USER=root
ENV MYSQL_PASSWORD=root
ENV MYSQL_ROOT_PASSWORD=root

# Cria um diretório para armazenar backups
RUN mkdir -p /backup

# Concede permissão de execução ao script
RUN chmod +x /usr/local/bin/backup.sh

    ##rodar para executar: docker-compose up -d --build

