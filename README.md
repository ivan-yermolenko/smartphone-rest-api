# Smartphone Inventory REST API

This project is a test task implementing a RESTful API for managing smartphones. It is built using PHP 8.4+ and Laravel 11, strictly designed as a backend REST API (API-only) with no frontend assets, Node packages, or Blade templates.

## What the project does and how it is structured

The API provides endpoints to perform CRUD (Create, Read, Update, Delete) operations on smartphone records. It also includes an idempotent seeding mechanism to fetch and synchronize products from an external API (DummyJSON).

**Core Features:**
- Full REST CRUD operations for products.
- External API synchronization.
- JSON-only responses (including 404/405 errors).
- Strict typing and isolated service layers.

## The database schema

The database schema is optimized for simplicity, performance, and compatibility with the external data source. Instead of listing every column, here are the key architectural decisions regarding the `products` table:

- **`external_id` for Synchronization**: We use an `external_id` column to store the ID from the DummyJSON API. This allows the `/api/products/seed` endpoint to be idempotent—it checks for existing records by `external_id` and updates them rather than creating duplicates during repeated syncs.
- **`reviews` as a JSON Column**: The test task data provides reviews as an array of objects within each product. Since the API only needs to return these reviews along with the product and does not require complex relational queries (like filtering all products by a specific review author), storing `reviews` as a `JSON` column avoids unnecessary relational overhead and `JOIN` operations. This perfectly fits the NoSQL-like nested structure while still leveraging MySQL's robust JSON capabilities.
- **Other JSON Fields (`tags`, `dimensions`, `meta`, `images`)**: Similar to `reviews`, these fields are stored natively as JSON to preserve the nested structure of the external API without overcomplicating the schema.
- **Fast Filtering & Lookups**: Columns like `brand` and `title` are indexed since the API may require filtering by these fields (e.g., `?brand=Apple`). The `sku` column is marked unique to ensure data integrity during product creation and updates.

## Organizing the Laravel application

The application is structured following clean architecture principles, keeping controllers thin and strictly typed:

- **Strict Types & Final Classes**: Every class (Controllers, Models, Requests, Services) is declared `final` and enforces `declare(strict_types=1);` to prevent inheritance and ensure type safety.
- **Service Layer**: External API calls and data mapping logic are decoupled from controllers into a dedicated `DummyJsonService`. This prevents the `ProductController` from being bloated with third-party integration logic.
- **Form Requests**: Request validation is handled via dedicated `FormRequest` classes (e.g., `StoreProductRequest`, `UpdateProductRequest`), keeping validation out of the controllers.
- **Eloquent Local Scopes**: Query filtering (like `?brand=Apple`) is encapsulated in the model (`scopeOfBrand`) instead of cluttering the controller logic.
- **Route Constraints**: API routes use strict constraints (e.g., `->whereNumber('product')`) to ensure valid parameters and prevent routing collisions.

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
