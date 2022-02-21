UID=1000
DOCKER_ARGS=--log-level=ERROR
NGINX_SERVICE=backend-todo-nginx
CLI_SERVICE=backend-todo-cli
FPM_SERVICE=backend-todo-fpm

init: docker-down-clear docker-pull docker-build docker-up todo-init post-install

up-and-serve: docker-up f-serve
up: docker-up
down: docker-down
restart: down up
test: todo-test
ps: docker-ps

logs-btn: logs-backend-todo-node
logs-ftn: logs-frontend-todo-node

cli-shell: todo-cli-shell
fpm-shell: todo-fpm-shell
b-node-shell: todo-node-shell
f-shell:
	@docker exec -it ft-node sh
	@$(MAKE) -s f-chown
b-shell:
	@docker-compose exec $(NGINX_SERVICE) /bin/sh
	@$(MAKE) -s b-chown

docker-up:
	docker-compose up -d

f-serve:
	@docker exec -it ft-node yarn serve

docker-ps:
	docker-compose ps

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

todo-init: todo-composer-install todo-assets-install todo-wait-db todo-migrations todo-fixtures

todo-composer-install:
	docker-compose run --rm $(CLI_SERVICE) composer install

todo-assets-install:
	docker-compose run --rm backend-todo-node yarn install

logs-backend-todo-node:
	docker-compose logs backend-todo-node

logs-frontend-todo-node:
	@docker-compose logs -- frontend-todo-node

todo-assets-dev:
	docker-compose run --rm todo-node npm run dev

todo-wait-db:
	until docker-compose exec -T backend-todo-postgres pg_isready --timeout=0 --dbname=app ; do sleep 1 ; done

post-install: b-chown

todo-migrations:
	docker-compose run --rm $(CLI_SERVICE) php bin/console doctrine:migrations:migrate --no-interaction

todo-fixtures:
	docker-compose run --rm $(CLI_SERVICE) php bin/console doctrine:fixtures:load --no-interaction

todo-test:
	docker-compose run --rm $(CLI_SERVICE) php bin/phpunit

todo-cli-shell:
	@docker-compose run $(CLI_SERVICE) bash

todo-node-shell:
	@docker-compose run todo-node sh

f-chown:
	@docker exec ft-node chown -R $(UID):$(UID) ./

b-chown:
	@docker-compose $(DOCKER_ARGS) exec $(NGINX_SERVICE) chown -R $(UID):$(UID) ./

todo-fpm-shell:
	@docker-compose run --rm $(FPM_SERVICE) /bin/bash