.PHONY: help up down restart build logs shell tinker migrate migrate-fresh test install clean

# Default target
.DEFAULT_GOAL := help

# Colors for output
BLUE := \033[0;34m
GREEN := \033[0;32m
YELLOW := \033[0;33m
RED := \033[0;31m
NC := \033[0m # No Color

help: ## Show this help message
	@echo "$(GREEN)Laravel LLM Platform - Docker Commands$(NC)"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "$(BLUE)%-20s$(NC) %s\n", $$1, $$2}'

up: ## Start all services
	@echo "$(GREEN)Starting services...$(NC)"
	docker compose up -d
	@echo "$(GREEN)Services started!$(NC)"
	@echo "Application: http://localhost"
	@echo "Ollama API: http://localhost:11434"

up-dev: ## Start all services including Vite dev server
	@echo "$(GREEN)Starting services with dev profile...$(NC)"
	docker compose --profile dev up -d
	@echo "$(GREEN)Services started!$(NC)"
	@echo "Application: http://localhost"
	@echo "Vite Dev Server: http://localhost:5173"
	@echo "Ollama API: http://localhost:11434"

down: ## Stop all services
	@echo "$(YELLOW)Stopping services...$(NC)"
	docker compose down
	@echo "$(GREEN)Services stopped$(NC)"

down-volumes: ## Stop services and remove volumes
	@echo "$(RED)Stopping services and removing volumes...$(NC)"
	docker compose down -v
	@echo "$(GREEN)Services stopped and volumes removed$(NC)"

restart: down up ## Restart all services

build: ## Build Docker images
	@echo "$(GREEN)Building Docker images...$(NC)"
	docker compose build
	@echo "$(GREEN)Build complete!$(NC)"

build-no-cache: ## Build Docker images without cache
	@echo "$(GREEN)Building Docker images without cache...$(NC)"
	docker compose build --no-cache
	@echo "$(GREEN)Build complete!$(NC)"

logs: ## Show logs from all services
	docker compose logs -f

logs-app: ## Show logs from app service
	docker compose logs -f app

logs-nginx: ## Show logs from nginx service
	docker compose logs -f nginx

logs-postgres: ## Show logs from postgres service
	docker compose logs -f postgres

logs-ollama: ## Show logs from ollama service
	docker compose logs -f ollama

shell: ## Open shell in app container
	docker compose exec app sh

shell-root: ## Open shell in app container as root
	docker compose exec -u root app sh

tinker: ## Run Laravel Tinker
	docker compose exec app php artisan tinker

migrate: ## Run database migrations
	@echo "$(GREEN)Running migrations...$(NC)"
	docker compose exec app php artisan migrate
	@echo "$(GREEN)Migrations complete!$(NC)"

migrate-fresh: ## Fresh migration with seeding
	@echo "$(RED)Running fresh migration (this will drop all tables)...$(NC)"
	docker compose exec app php artisan migrate:fresh --seed
	@echo "$(GREEN)Fresh migration complete!$(NC)"

seed: ## Seed the database
	@echo "$(GREEN)Seeding database...$(NC)"
	docker compose exec app php artisan db:seed
	@echo "$(GREEN)Seeding complete!$(NC)"

test: ## Run tests
	@echo "$(GREEN)Running tests...$(NC)"
	docker compose exec app php artisan test

pint: ## Run Laravel Pint (code formatting)
	@echo "$(GREEN)Running Pint...$(NC)"
	docker compose exec app ./vendor/bin/pint

install: ## Install/setup the application
	@echo "$(GREEN)Installing application...$(NC)"
	@if [ ! -f .env ]; then cp .env.example .env; echo "$(GREEN).env file created$(NC)"; fi
	docker compose up -d
	@echo "$(YELLOW)Waiting for services to be ready...$(NC)"
	sleep 5
	docker compose exec app composer install
	docker compose exec app php artisan key:generate
	docker compose exec app php artisan migrate
	docker compose run --rm node npm install
	docker compose run --rm node npm run build
	@echo "$(GREEN)Installation complete!$(NC)"
	@echo "$(GREEN)Access your application at http://localhost$(NC)"

composer-install: ## Install Composer dependencies
	docker compose exec app composer install

composer-update: ## Update Composer dependencies
	docker compose exec app composer update

npm-install: ## Install NPM dependencies
	docker compose run --rm node npm install

npm-build: ## Build frontend assets
	docker compose run --rm node npm run build

npm-dev: ## Run Vite dev server
	docker compose --profile dev up -d node

cache-clear: ## Clear all Laravel caches
	@echo "$(GREEN)Clearing caches...$(NC)"
	docker compose exec app php artisan cache:clear
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan route:clear
	docker compose exec app php artisan view:clear
	@echo "$(GREEN)Caches cleared!$(NC)"

optimize: ## Optimize Laravel for production
	@echo "$(GREEN)Optimizing application...$(NC)"
	docker compose exec app php artisan config:cache
	docker compose exec app php artisan route:cache
	docker compose exec app php artisan view:cache
	docker compose exec app php artisan optimize
	@echo "$(GREEN)Optimization complete!$(NC)"

db-shell: ## Open PostgreSQL shell
	docker compose exec postgres psql -U laravel -d laravel

db-backup: ## Backup database to backup.sql
	@echo "$(GREEN)Backing up database...$(NC)"
	docker compose exec postgres pg_dump -U laravel laravel > backup.sql
	@echo "$(GREEN)Database backed up to backup.sql$(NC)"

db-restore: ## Restore database from backup.sql
	@echo "$(YELLOW)Restoring database from backup.sql...$(NC)"
	docker compose exec -T postgres psql -U laravel laravel < backup.sql
	@echo "$(GREEN)Database restored!$(NC)"

ollama-pull: ## Pull Ollama model (usage: make ollama-pull MODEL=llama2)
	@if [ -z "$(MODEL)" ]; then \
		echo "$(RED)Please specify MODEL. Example: make ollama-pull MODEL=llama2$(NC)"; \
	else \
		echo "$(GREEN)Pulling Ollama model: $(MODEL)...$(NC)"; \
		docker compose exec ollama ollama pull $(MODEL); \
		echo "$(GREEN)Model pulled!$(NC)"; \
	fi

ollama-list: ## List installed Ollama models
	docker compose exec ollama ollama list

ollama-run: ## Run Ollama model interactively (usage: make ollama-run MODEL=llama2)
	@if [ -z "$(MODEL)" ]; then \
		echo "$(RED)Please specify MODEL. Example: make ollama-run MODEL=llama2$(NC)"; \
	else \
		docker compose exec ollama ollama run $(MODEL); \
	fi

clean: ## Remove all containers, volumes, and images
	@echo "$(RED)Removing all containers, volumes, and images...$(NC)"
	docker compose down -v --rmi all
	@echo "$(GREEN)Cleanup complete!$(NC)"

ps: ## Show running containers
	docker compose ps

stats: ## Show container resource usage
	docker stats

fix-permissions: ## Fix storage and cache permissions
	@echo "$(GREEN)Fixing permissions...$(NC)"
	docker compose exec -u root app chown -R www:www /var/www/storage /var/www/bootstrap/cache
	docker compose exec -u root app chmod -R 775 /var/www/storage /var/www/bootstrap/cache
	@echo "$(GREEN)Permissions fixed!$(NC)"

