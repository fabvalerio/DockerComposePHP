﻿# DockerComposePHP

## Iniciar o docker

```
docker-compose up -d --build
docker-compose up -d
```

## Rodando SQL Migration

```
docker exec -it php_app bash -c "/var/www/html/run-migrations.sh"
```

## Encerrar

```
docker-compose down
```

## Visualizar Volumes

```
docker volume ls
```

## docker-migrate
```
function docker-migrate {
    docker exec -it php_app bash /var/www/html/run-migrations.sh
}
```
