@echo off
echo 1. Copying environment file...
if not exist .env (
    copy .env.example .env
) else (
    echo .env already exists, skipping copy.
)

echo 2. Starting Docker containers...
docker compose up -d

echo 3. Installing Composer dependencies...
docker compose exec app composer install

echo 4. Generating Application Key...
docker compose exec app php artisan key:generate

echo 5. Running Database Migrations...
docker compose exec app php artisan migrate

echo --------------------------------------------------
echo Setup completed successfully!
echo Visit: http://localhost:8080
echo To seed products, run: docker compose exec app curl -s -X POST http://localhost/api/products/seed
pause
