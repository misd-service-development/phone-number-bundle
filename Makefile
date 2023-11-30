.PHONY: ${TARGETS}
.DEFAULT_GOAL := help

DIR := ${CURDIR}
QA_IMAGE := jakzal/phpqa:latest

help:
	@echo "\033[33mUsage:\033[0m"
	@echo "  make [command]"
	@echo ""
	@echo "\033[33mAvailable commands:\033[0m"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[32m%s\033[0m___%s\n", $$1, $$2}' | column -ts___

cs-lint: ## Verify check styles
	composer install --working-dir=tools/php-cs-fixer
	tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --dry-run --diff

cs-fix: ## Apply Check styles
	composer install --working-dir=tools/php-cs-fixer
	tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --diff

phpstan: ## Run PHPStan
	composer install --working-dir=tools/phpstan
	tools/phpstan/vendor/bin/phpstan analyze

phpunit: ## Run phpunit
	-./vendor/bin/phpunit

test: phpunit cs-lint phpstan ## Run tests
