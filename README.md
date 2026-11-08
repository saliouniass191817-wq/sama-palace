# Micro Commerce

Micro Commerce is a Laravel storefront for shops and products. It uses PostgreSQL in deployment and builds its frontend assets with Vite.

## Requirements

- PHP 8.2 or newer with the extensions required by Laravel and PostgreSQL (`pdo_pgsql`)
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

GitHub Actions runs the PHP tests, builds the Vite assets, and applies all migrations against a temporary PostgreSQL service.

## Deployment

The repository includes a Dockerfile for an Apache/PHP container. Build it with `docker build -t micro-commerce .`; the image installs PHP dependencies, runs `npm ci` and `npm run build`, and serves Laravel's `public` directory. Provide production environment variables at runtime, including `APP_KEY` and PostgreSQL connection settings. Run database migrations during deployment with `php artisan migrate --force`.

See [docs/deployment-render-supabase.md](docs/deployment-render-supabase.md) for the Render and Supabase deployment notes.
