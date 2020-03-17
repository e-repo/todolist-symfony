init: docker-clear-down docker-build docker-up
up: docker-up
down: docker-down
restart: docker-down docker-up
shell: docker-shell

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