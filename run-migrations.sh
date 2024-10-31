#!/bin/bash

# Caminho da pasta com as migrations
MIGRATIONS_DIR="./migrations"

# Função para verificar se a migration já foi executada
migration_executada() {
    local filename="$1"
    local resultado=$(echo "SELECT COUNT(*) FROM migrations_log WHERE filename='$filename';" | mysql -h$DB_HOST -P$DB_PORT -u$DB_USER -p$DB_PASS $DB_NAME -s -N)
    [ "$resultado" -gt 0 ]
}

# Executa cada arquivo SQL na pasta migrations, se ainda não foi executado
for FILE in $MIGRATIONS_DIR/*.sql; do
    FILENAME=$(basename "$FILE")
    
    if migration_executada "$FILENAME"; then
        echo "Migration $FILENAME já foi executada, pulando."
    else
        echo "Executando migration: $FILENAME"
        cat "$FILE" | mysql -h$DB_HOST -P$DB_PORT -u$DB_USER -p$DB_PASS $DB_NAME
        
        # Insere o nome da migration na tabela de controle
        echo "INSERT INTO migrations_log (filename) VALUES ('$FILENAME');" | mysql -h$DB_HOST -P$DB_PORT -u$DB_USER -p$DB_PASS $DB_NAME
    fi
done

#tabela de migration log
# CREATE TABLE IF NOT EXISTS migrations_log (
#     id INT AUTO_INCREMENT PRIMARY KEY,
#     filename VARCHAR(255) NOT NULL UNIQUE,
#     executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
# );


# comando para rodar os migrations
# docker exec -it php_app bash -c "/var/www/html/run-migrations.sh"

