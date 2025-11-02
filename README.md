# Travel experience
The goal of this application is to have a place to investigate and research development/infrastructure technics, all inside Symfony:
- SOLID, Hexagonal, DDD
- CQRS
- Docker
- Github Actions
- FrontEnd: Jquery, Reactjs,...
- ElastichSearch
- Redis
- Diferent Testing Approaches
- Websockets (golang)

## Start application with docker
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

## Symfony 4

wiki: https://github.com/albertjuhe/planing_travels/wiki

Building Travel Experience: Plan travels colaborative, sharing travel content, upload photos, comment travels and vote the best travel.
Create a map and add Locations, Routes, GPS tracks.

## Reactjs

### Install
Symfony 4 install https://www.cloudways.com/blog/symfony-react-using-webpack-encore/

```
composer require symfony/webpack-encore-pack
```

### Yarn Install

```
curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
apt-get update
apt-get install yarn
```

Manage react with yarn

```
yarn add react react-dom prop-types babel-preset-react --dev
yarn add babel-polyfill babel-preset-env --dev
```

Symfony 4 integration https://www.thinktocode.com/2018/06/21/symfony-4-and-reactjs/

### Problems

React Native Error: ENOSPC: System limit for number of file watchers reached
https://stackoverflow.com/questions/55763428/react-native-error-enospc-system-limit-for-number-of-file-watchers-reached

Manifest.json creation
https://stackoverflow.com/questions/51393459/symfony-error-an-exception-has-been-thrown-during-the-rendering-of-a-template

### Comands

```
yarn encore dev
```

## DDD articles
* Don't Use Entities in Symfony Forms. Use Custom Data Objects Instead (https://blog.martinhujer.cz/symfony-forms-with-request-objects/)
* RigorTalks (https://carlosbuenosvinos.com/category/rigor-talks/)
* Emmbedables objects https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/tutorials/embeddables.html
* Mapping types https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/cookbook/advanced-field-value-conversion-using-custom-mapping-types.html
* Command Bus: https://matthiasnoback.nl/2015/01/responsibilities-of-the-command-bus/
* Transactional https://tactician.thephpleague.com/plugins/doctrine/
* Redis Cache https://www.digitalocean.com/community/tutorials/how-to-install-and-secure-redis-on-ubuntu-18-04
* ElasticSearch and Kibana https://www.admintome.com/blog/install-elasticsearch-on-ubuntu-18-04-1/
* DDD Sample Cargo Eric Evans https://github.com/codeliner/php-ddd-cargo-sample
* Protobuf php https://mattallan.me/posts/protobuf-php-services/

## Demo
[Travel Planing](http://35.167.24.186/travelexperience/web/app.php/)


## TODO
1) Redis
1) Comments travels and descriptions with markdown
1) OERPUB Blob travel
1) GraphQL Integration
1) JWT JAson web tockens
1) Protobuf Services in PHP



