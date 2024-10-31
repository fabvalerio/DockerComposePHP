# DockerComposePHP

## Iniciar o docker

```
docker-compose up -d --build
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
