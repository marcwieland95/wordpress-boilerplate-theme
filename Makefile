.DEFAULT_GOAL := help

.PHONY: help
help: ## Display this message
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

.PHONY: install
install: ## Install WP, add plugins and compile assets
	if [ ! -f .env ]; then cp .env.example .env; fi;
	composer install
	make db_import
	npm install
	make build

.PHONY: watch
watch: ## Watch for changes in assets
	npm run dev

.PHONY: build
build: ## Build assets
	npm run build

.PHONY: prettier
prettier: ## Improves code formatting
	# yarn run prettier

.PHONY: coding-standard
coding-standard: ## Check coding-standard
	vendor/bin/pint --test
	# npm run lint

.PHONY: coding-standard-fix
coding-standard-fix: ## Auto fix coding-standards
	vendor/bin/pint --repair
	# npm run eslint-fix
	# npm run stylelint-fix

.PHONY: db_dump
db_dump: ## Create DB Dump
	wp db export /var/www/resources/sql/initial.sql

.PHONY: db_import
db_import: ## Import DB Dump
	wp db reset --yes
	wp db import /var/www/resources/sql/initial.sql
