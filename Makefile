up:
	# start docker
	docker-compose up -d

down:
	# stop docker
	docker-compose down

cs-fix:
	#clean code
	# tools/php-cs-fixer/vendor/bin/php-cs-fixer fix src
	php-cs-fixer fix src

cs-fix-tests:
	#clean code
	# tools/php-cs-fixer/vendor/bin/php-cs-fixer fix tests
	php-cs-fixer fix tests

behat:
	make exec CMD='./vendor/bin/behat'

unit-test:
	#run tests
	php vendor/bin/phpunit

compile-react:
	#compile react with yarn
	yarn encore dev

bash:
	#connect to bash container
	docker-compose exec app bash

CMD?=echo CMD var with the command is expected
exec:
	docker-compose exec app sh -c '$(CMD)'

check-el:
	make exec CMD='bin/console app:check-travel-elasticsearch'

populate-el:
	make exec CMD='bin/console app:populate-travel-elasticsearch'

build-image:
	docker build Docker

docker-bash:
	docker-compose exec app  bash

SERVICE?=echo SERVICE to log
log:
	docker-compose logs -f '$(SERVICE)'

