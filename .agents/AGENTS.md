# Project Rules & Style Guidelines

## Core Stack & Infrastructure
- **Stack**: PHP 8.4+, Laravel 13, MySQL database.
- **Docker Ports**:
  - Web Server (Nginx): Mapped to host port `8080`.
  - Database (MySQL): Mapped to host port `33060`.
- **API-Only**: The project is strictly a backend REST API. Do not introduce frontend assets, templates, Node packages (`package.json`), or Vite configurations. The root route `/` must return a JSON response.
- **Gitignore**: Only `.agents/AGENTS.md` should be tracked in Git; the rest of the `.agents/` directory must be ignored.

## Code Standards
- **Strict Types**: Every PHP file must begin with `declare(strict_types=1);` right after the opening php tag.
- **Closed Classes**: Make controllers, models, resources, request classes, and services `final` to prevent accidental inheritance.
- **Pint Linter**: Always format code using Laravel Pint by running `./vendor/bin/pint` after modifying files.

## Eloquent Models & IDE Integration
- **IDE Helpers**: Always document Eloquent properties and static magic methods in the model's PHPDoc block to prevent IDE warnings.
  - E.g., add `@method static \App\Models\Product create(array $attributes = [])` and `@method static \App\Models\Product updateOrCreate(array $attributes, array $values = [])`.
- **Local Scopes**: 
  - Extract query filtering logic from controllers into Eloquent Local Scopes.
  - Follow the `scopeOf...` naming convention (e.g. `scopeOfBrand`) so they read naturally as `Product::ofBrand($brand)`.
  - Document all local scopes in the model's PHPDoc block as static methods (e.g. `@method static \Illuminate\Database\Eloquent\Builder|Product ofBrand(?string $brand)`).

## API Architecture
- **Routing**:
  - Define static/collection routes (like `/products/seed`) BEFORE dynamic wildcard routes (like `/products/{product}`) to prevent routing collisions.
  - Group and place wildcard routes at the bottom.
  - Enforce route constraints on numeric wildcards using `->whereNumber('product')` to prevent non-numeric clashes and save server resources by returning immediate 404s.
- **Validation**: Use dedicated Laravel `FormRequest` classes for request validation instead of in-controller validation.
- **Service Layer**: Extract external API integrations and data-syncing scripts into dedicated Service classes (e.g., `DummyJsonService`).

## Testing Best Practices
- **TDD (Test-First)**: Always write tests before implementing endpoint logic.
- **Feature Tests**: Test database operations and model casting/behavior inside Feature tests using `RefreshDatabase` and database factories instead of asserting model configurations inside Unit tests.
- **HTTP Mocking**: 
  - Never call real external APIs during test execution.
  - Always mock external HTTP requests using `Http::fake()`.
  - Verify that the correct URL and method were used by asserting with `Http::assertSent()`.
