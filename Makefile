.PHONY: setup start stop test pint seed

setup:
	@echo "1. Copying environment file..."
	@cp -n .env.example .env || true
	@echo "2. Starting Docker containers..."
	@docker compose up -d
	@echo "3. Installing Composer dependencies..."
	@docker compose exec app composer install
	@echo "4. Generating Application Key..."
	@docker compose exec app php artisan key:generate
	@echo "5. Running Database Migrations..."
	@docker compose exec app php artisan migrate
	@echo "--------------------------------------------------"
	@echo "Setup completed successfully!"
	@echo "Visit: http://localhost:8080"
	@echo "To seed products, run: make seed"

start:
	@docker compose up -d

stop:
	@docker compose down

test:
	@docker compose exec app php artisan test

pint:
	@docker compose exec app ./vendor/bin/pint

seed:
	@echo "Seeding products from DummyJSON API..."
	@curl -X POST http://localhost:8080/api/products/seed
	@echo ""
