help: ## Show this help message
	@echo 'Usage:'
	@echo '  make <target>'
	@echo ''
	@echo 'Targets:'
	@awk 'BEGIN {FS = ":.*##"} /^[a-zA-Z_\/-]+:.*?##/ { printf "  %-20s %s\n", $$1, $$2 }' $(MAKEFILE_LIST)

dev/start-server: ## Start the Symfony server
	symfony server:start

dev/install-deps: ## Install the dependencies
	composer install

db/generate-migration: ## Generate a new migration
	php bin/console make:migration

db/create: ## Create the database
	php bin/console doctrine:database:create

db/migrate: ## Run the migrations
	php bin/console doctrine:migrations:migrate --no-interaction --all-or-nothing

test: ## Run the tests
	php bin/phpunit

lint: ## Run the linter
	vendor/bin/php-cs-fixer fix

imports/import-themes: ## Import the themes
	php bin/console ExtractService extractthemes

