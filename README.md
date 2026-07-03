# Appflame Smartphone REST API

This project is a test task implementing a RESTful API for managing smartphones using PHP 8.2+ and Laravel 11. It is designed strictly as a backend REST API (API-only) with no frontend assets, Node packages, or Blade templates.

## Main Stack
- **PHP**: 8.2+ (Strict types enforced with `declare(strict_types=1)`)
- **Laravel**: 11.x
- **Database**: MySQL 8.0
- **Containerization**: Docker & Docker Compose
- **Code Style**: Laravel Pint

---

## Quick Start (Automation Scripts)

Automation scripts are included to deploy the project quickly across different operating systems.

> [!NOTE]
> *If you prefer to configure everything manually, step-by-step instructions can be found at the **[end of this file](#manual-installation-alternative)**.*

###  macOS / Linux / Windows (WSL2)
Use the included `Makefile`:
```bash
make setup
```

This command automatically:
1. Copies `.env.example` to `.env` (if it does not exist yet).
2. Starts Docker containers in the background (`docker compose up -d`).
3. Installs Composer dependencies inside the app container.
4. Generates a unique application key (`key:generate`).
5. Runs database migrations.

Once completed, you can sync/seed products from the DummyJSON API:
```bash
make seed
```

#### Other Makefile Commands:
- **`make start`** — Start Docker containers.
- **`make stop`** — Stop Docker containers (`docker compose down`).
- **`make test`** — Run all feature/unit tests.
- **`make pint`** — Run Laravel Pint code style formatter.

### ❖ Windows (Native CMD / PowerShell)
Run the batch script directly in your terminal:
```cmd
setup.bat
```

---

## Database Connection from Host (PhpStorm / DBeaver)
To inspect the database using an IDE or external tool, use the following credentials:
- **Host**: `localhost` (or `127.0.0.1`)
- **Port**: `33060` (the mapped external port)
- **Database**: `appflame`
- **Username**: `appflame`
- **Password**: `secret`

---

## API Endpoints

All requests return structured JSON payloads by default.

| Method | Path | Description |
| :--- | :--- | :--- |
| **POST** | `/api/products/seed` | Import and synchronize smartphones from the DummyJSON API (Idempotent). |
| **GET** | `/api/products` | Retrieve a list of products (Paginated, filterable by `?brand=Apple`). |
| **GET** | `/api/products/{id}` | Retrieve detailed information for a product by its numeric ID. |
| **POST** | `/api/products` | Create a new product (with full request validation). |
| **PATCH** | `/api/products/{id}` | Partially update a product by ID (with unique SKU check ignoring self). |
| **DELETE** | `/api/products/{id}` | Delete a product by ID (returns 204 No Content). |

---

## Testing & Code Quality

### Running Tests
The project features integration tests (Feature tests) verifying all API actions, request validations, and external API mocking via `Http::fake()`:
```bash
docker compose exec app php artisan test
```

### Code Formatting (Linter)
All PHP files comply with PSR-12 and Laravel style guidelines. You can check or format your files with Pint:
```bash
docker compose exec app ./vendor/bin/pint
```

---

## Architectural Highlights
- **Closed Classes**: All key classes (controllers, models, resources, request classes, and services) are declared `final` to prevent accidental inheritance.
- **Service Layer**: Third-party API integration and data mapping are decoupled from controllers into a dedicated `DummyJsonService` class.
- **Route Constraints**: Numeric route parameters are strictly validated with `->whereNumber('product')` to prevent clashes (e.g. with `/products/seed`) and return fast 404s.
- **Eloquent Scopes**: Brand filtering query logic is encapsulated inside the model using a local scope `scopeOfBrand` (`Product::ofBrand()`).
- **IDE Helpers**: Model fields and magic static methods (such as `create()`, `updateOrCreate()`, and scopes) are documented in the model's PHPDoc block.
- **Error Handling**: API exceptions like `NotFoundHttpException` (404) and `MethodNotAllowedHttpException` (405) are captured globally in `bootstrap/app.php` and returned in a unified JSON format.

---

## Manual Installation (Alternative)

If you prefer to run commands manually, follow these steps:

### 1. Environment Setup
Clone the repository and copy the environment configuration file:
```bash
cp .env.example .env
```

### 2. Start Docker Containers
Bring up the containers in detached mode:
```bash
docker compose up -d
```
This starts:
- Nginx Web Server mapped to host port **`8080`**.
- MySQL Database mapped to host port **`33060`**.

### 3. Install Dependencies & Generate Key
Install PHP dependencies inside the container:
```bash
docker compose exec app composer install
```

Generate the application key:
```bash
docker compose exec app php artisan key:generate
```

### 4. Adjust Directory Permissions (If needed)
If you encounter permission errors with cache or logs, run:
```bash
chmod -R 777 storage bootstrap/cache
```

### 5. Run Database Migrations
Create the database tables:
```bash
docker compose exec app php artisan migrate
```
