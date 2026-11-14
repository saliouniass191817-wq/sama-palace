# Micro Commerce

Micro Commerce is a Laravel storefront for shops and products. It uses PostgreSQL in deployment and builds its frontend assets with Vite.

## Requirements

- PHP 8.4 or newer with the extensions required by Laravel and PostgreSQL (`pdo_pgsql`)
- Composer
- Node.js 20 or newer and npm
- PostgreSQL with the `citext` extension available

## Local setup

```sh
composer install
cp .env.example .env
php artisan key:generate
npm ci
npm run build
```

Configure the database connection in `.env`, then create the database and apply the Laravel migrations. On PostgreSQL, the migrations create the `extensions` schema and enable `citext` for case-insensitive phone and shop-slug uniqueness; the database role therefore needs permission to create that extension (or an administrator must enable it once):

```sh
php artisan migrate
php artisan serve
```

For frontend development with Vite, run `npm run dev` in a second terminal.

## Checks

Run the PHP test suite with `composer test`. The test configuration uses an in-memory SQLite database. Build production frontend assets with `npm run build`.

GitHub Actions runs the PHP tests against PostgreSQL, checks Pint formatting and Composer advisories, builds the Vite assets, and applies all migrations against a temporary PostgreSQL service.

## Deployment

The multi-stage Dockerfile builds frontend assets and Composer dependencies separately, then serves Laravel's `public` directory from PHP 8.4/Apache without Node in the final image. At startup, the entrypoint runs isolated migrations and optimizes the application; it exits without starting Apache if either step fails.

See [docs/deployment-render-supabase.md](docs/deployment-render-supabase.md) for the Render and Supabase deployment notes.
