UID=1000
DOCKER_ARGS=--log-level=ERROR
PHP_SERVICE_NAME=todo-nginx

up: docker-up
down: docker-down
init: docker-down-clear todo-clear docker-pull docker-build docker-up todo-init post-install
test: todo-test

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

todo-clear:
	docker run --rm -v ${PWD}:/app --workdir=/app alpine rm -f .ready

docker-pull:
	docker-compose pull

shell:
	@docker-compose exec $(PHP_SERVICE_NAME) /bin/sh
	@$(MAKE) -s chown

docker-build:
	docker-compose build

todo-init: todo-composer-install todo-assets-install todo-wait-db todo-migrations todo-fixtures todo-ready

todo-composer-install:
	docker-compose run --rm todo-php-cli composer install

todo-assets-install:
	docker-compose run --rm todo-node yarn install

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

todo-ready:
	docker run --rm -v ${PWD}:/app --workdir=/app alpine touch .ready

chown:
	@docker-compose $(DOCKER_ARGS) exec $(PHP_SERVICE_NAME) chown -R $(UID):$(UID) ./