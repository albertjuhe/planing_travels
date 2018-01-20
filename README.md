# Symfony 4

## Install PHP 7.2. in Wamp64
https://dhali.com/wordpress/upgrade-wamp-server-php-7/

## Install Symfony:
> php composer.phar create-project symfony/skeleton travel

Test: http://localhost/Travel/public

## Install packets

Annotations
> php composer.phar require annotations

Templates in wig

> php composer.phar require twig

Security

> php composer.phar require security

config/packages/secutiry.yaml

Database doctrine

> php composer.phar require doctrine maker

Validator entities

> php composer.phar require validator

Translations

> php composer.phar require translator

config/packages/translation.yaml
http://symfony.com/doc/current/translation.html

Forms
php composer.phar require form

## Modify

Edit .env
Add DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/travel
https://symfony.com/doc/current/doctrine.html

Create database

> php bin/console doctrine:database:create

## Entity

> php  bin/console make:entity User

Edit src/Entity/User.php add properties and methods
Add to database
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate

## Assets

Working with assets in templates
php composer.phar require symfony/asset

Create folder assets
