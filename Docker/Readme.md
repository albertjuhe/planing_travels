# Docker

#### Run application with docker

```
$docker-compose up
```

#### Container bash
```
$docker-compose exec app bash
```

## MySQL 

### connection

```
DATABASE_URL=mysql://root:root@mysql:3306/travelGuuid
```

### mysql admin

```
 adminer:
    image: adminer
    ports:
      - 8080:8080
    depends_on:
      - mysql
```

is mapped in 8080 port to acces it with:

``
localhost:8080
``
Server: mysql (service name in the docker-compose)

## Elasticsearch

Check if is alive

``
http://elasticsearch:9200
``