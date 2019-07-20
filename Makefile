mysql: systemctl start mysql

cs-fix: php vendor/bin/php-cs-fixer fix tests
php vendor/bin/php-cs-fixer fix src

Create image:
docker build -t "php-planing-travels" .

Run image
docker run -rm -p 8000:80 -it "php-planing-travels"
Run image with volumen
docker run -p 8000:80 -v $PWD:/var/www/html -it "php-planing-travels"
