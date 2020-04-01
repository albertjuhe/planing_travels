up:
	# start docker
	docker-compose up

down:
	# stop docker
	docker-compose down

cs-fix:
	#clean code
	php vendor/bin/php-cs-fixer fix src

unit-test:
	#run tests
	php vendor/bin/phpunit

compile-react:
	#compile react with yarn
	yarn encore dev

compose-bash:
	#connect to bash container
	docker-compose exec app bash

CMD?=echo CMD var with the command is expected
exec:
	docker-compose exec app sh -c '$(CMD)'

check-el:
	make exec CMD='bin/console app:check-travel-elasticsearch'

populate-el:
	make exec CMD='bin/console app:populate-travel-elasticsearch'
