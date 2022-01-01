UID=1000
DOCKER_ARGS=--log-level=ERROR
WEB_SERVICE_NAME=todo-nginx
CLI_SERVICE_NAME=todo-php-cli
FPM_SERVICE_NAME=todo-php-fpm

up: docker-up
down: docker-down
init: docker-down-clear todo-clear docker-pull docker-build docker-up todo-init post-install
test: todo-test
ps: docker-ps
restart: down up
cli-shell: todo-cli-shell
fpm-shell: todo-fpm-shell
node-shell: todo-node-shell
watch-logs: node-watch-logs

shell:
	@docker-compose exec $(WEB_SERVICE_NAME) /bin/sh
	@$(MAKE) -s chown

docker-up:
	docker-compose up -d

docker-ps:
	docker-compose ps

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

todo-clear:
	docker run --rm -v ${PWD}:/app --workdir=/app alpine rm -f .ready

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

todo-init: todo-composer-install todo-assets-install todo-wait-db todo-migrations todo-fixtures todo-ready

todo-composer-install:
	docker-compose run --rm todo-php-cli composer install

todo-assets-install:
	docker-compose run --rm todo-node yarn install

node-watch-logs:
	docker-compose logs todo-node-watch

todo-assets-dev:
	docker-compose run --rm todo-node npm run dev

todo-wait-db:
	until docker-compose exec -T todo-postgres pg_isready --timeout=0 --dbname=app ; do sleep 1 ; done

post-install: chown

todo-migrations:
	docker-compose run --rm todo-php-cli php bin/console doctrine:migrations:migrate --no-interaction

todo-fixtures:
	docker-compose run --rm todo-php-cli php bin/console doctrine:fixtures:load --no-interaction

todo-test:
	docker-compose run --rm todo-php-cli php bin/phpunit

todo-cli-shell:
	@docker-compose run todo-php-cli bash

todo-node-shell:
	@docker-compose run todo-node sh

todo-ready:
	docker run --rm -v ${PWD}:/app --workdir=/app alpine touch .ready

chown:
	@docker-compose $(DOCKER_ARGS) exec $(WEB_SERVICE_NAME) chown -R $(UID):$(UID) ./

todo-fpm-shell:
	@docker-compose run --rm $(FPM_SERVICE_NAME) /bin/bash