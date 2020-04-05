# TRAVEL SHARE

## Execution
```
$make up
```
```
http://localhost:8000/public/index.php
```
## Docker

#### Make commands 

Up application
```
$make up
```
Down application
```
$make down
```
Exec comamnd in the container
```
$make exec CDM='ls'
```

#### Container bash
```
$make bash
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
http://localhost:8080/?server=mysql&username=root
``
```
server: mysql
user: root
password: root
```

Server: mysql (service name in the docker-compose)

## Elasticsearch

Check if is alive
``
http://elasticsearch:9201
``