.PHONY: mysql
mysql: systemctl start mysql

.PHONY: cs-fix
cs-fix: php vendor/bin/php-cs-fixer fix tests
        php vendor/bin/php-cs-fixer fix src

.PHONY: coverage
coverage: php bin/phpunit --coverage-text