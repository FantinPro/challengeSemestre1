.PHONY: up stop restart init-jwt-keys backend-install frontend-install schema-update fixtures start resetdb setup

up:
	docker-compose up --detach

stop:
	docker-compose down --remove-orphans --volumes --timeout 0

restart: stop start

init-jwt-keys:
	cd ./back && php bin/console lexik:jwt:generate-keypair --skip-if-exists

backend-install:
	cd ./back && composer install

frontend-install:
	docker-compose exec frontend yarn install

resetdb:
	cd ./back && php bin/console doctrine:database:drop --force && php bin/console doctrine:database:create && php bin/console doctrine:schema:update --force && php bin/console doctrine:fixtures:load

schema-update:
	cd ./back && php bin/console doctrine:schema:update --force

fixtures:
	cd ./back && php bin/console doctrine:fixtures:load

start: up
	cd ./back && php bin/console cache:clear && symfony serve

clear:
	cd ./back && php bin/console cache:clear

setup: clear backend-install frontend-install init-jwt-keys schema-update fixtures start
