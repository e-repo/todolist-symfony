-include .env

init: docker-clear-down docker-build docker-up
up: docker-up
down: docker-down
restart: docker-down docker-up
shell: docker-shell
serve: run-serve

ps:
	@docker-compose ps

symfony-init:
	@docker-compose run --rm -u $$(id -u) php-cli symfony new --no-git .

docker-build:
	@docker-compose build

docker-up:
	@docker-compose up -d

docker-down:
	@docker-compose down --remove-orphans

docker-clear-down:
	@docker-compose down -v --remove-orphans

docker-shell:
	@docker-compose run --rm -u $$(id -u) php-cli /bin/bash

run-serve:
	@docker-compose run -p ${FRONTEND_EXTERNAL_PORT}:${FRONTEND_DOKER_PORT} \
	--rm  -u $$(id -u) node-js npm run serve

y-install-packages:
	@docker-compose run --rm node-js yarn install

y-add:
	@docker-compose run --rm node-js yarn add $(package)