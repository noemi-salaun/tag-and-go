.DEFAULT_GOAL := help

.PHONY: start
start: erase build up db ## clean current environment, recreate dependencies and spin up again

.PHONY: stop
stop: ## stop environment
		docker-compose stop

.PHONY: rebuild
rebuild: start ## same as start

.PHONY: erase
erase: ## stop and delete containers, clean volumes.
		docker-compose stop
		docker-compose rm -v -f

.PHONY: build
build: ## build environment and initialize composer and project dependencies
		docker-compose build
		docker-compose run --rm php bash -lc 'composer install'

.PHONY: up
up: ## spin up environment
		docker-compose up -d

.PHONY: phpunit
phpunit: db ## execute project unit tests
		docker-compose exec php bash -lc './bin/phpunit'

.PHONY: db
db: wait-for-db ## recreate database
		docker-compose exec php bash -lc './bin/console doctrine:database:drop --force'
		docker-compose exec php bash -lc './bin/console doctrine:database:create'
		docker-compose exec php bash -lc './bin/console doctrine:migrations:migrate -n'
		docker-compose exec php bash -lc './bin/console doctrine:fixtures:load -n'

.PHONY: wait-for-db
wait-for-db: ## wait for MariaDB initialization
		docker-compose exec php php -r "set_time_limit(60);for(;;){if(@fsockopen('mariadb',3306)){break;}echo \"Waiting for MariaDB\n\";sleep(1);}"

.PHONY: bash
bash: ## gets inside a container, use 's' variable to select a service. make s=php ba
		docker-compose exec $(s) bash -l

.PHONY: logs
logs: ## look for 's' service logs, make s=php logs
		docker-compose logs -f $(s)

.PHONY: help
help: ## Display this help message
	@cat $(MAKEFILE_LIST) | grep -e "^[a-zA-Z_\-]*: *.*## *" | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
