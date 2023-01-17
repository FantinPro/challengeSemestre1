.PHONY: up stop restart init-jwt-keys backend-install frontend-install schema-update fixtures start

up:
	docker-compose up --detach

stop:
	docker-compose down --remove-orphans --volumes --timeout 0

restart: stop start

init-jwt-keys:
	cd ./back && php bin/console lexik:jwt:generate-keypair

backend-install:
	cd ./back && composer install

frontend-install:
	docker-compose exec frontend yarn install

schema-update:
	cd ./back && php bin/console doctrine:schema:update --force

fixtures:
	cd ./back && php bin/console doctrine:fixtures:load

start: up
	cd ./back && symfony serve