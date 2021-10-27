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
	@docker run --rm -v $(DIR):/project -w /project $(QA_IMAGE) php-cs-fixer fix --allow-risky=yes --dry-run -vvv

cs-fix: ## Apply Check styles
	@docker run --rm -v $(DIR):/project -w /project $(QA_IMAGE) php-cs-fixer fix --allow-risky=yes -vvv

phpstan: ## Run PHPStan
	@docker run --rm -v $(DIR):/project -w /project $(QA_IMAGE) phpstan analyse

phpunit: ## Run phpunit
	-./vendor/bin/phpunit

test: phpunit cs-lint phpstan ## Run tests
