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
