$(shell cp -n .env.example .env 2>/dev/null)

include .env
export

.PHONY: help start test

help: ## Display this help message
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n"} /^[a-zA-Z_0-9-]+:.*?##/ { printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

start: ## Build the Docker image
	docker build -t payment-gateway .

test: ## Run all tests inside Docker
	docker run --rm payment-gateway ./vendor/bin/phpunit
