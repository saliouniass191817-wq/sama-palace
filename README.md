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

## Sessions

Production uses database-backed sessions (`SESSION_DRIVER=database`). The app schedules `sessions:prune-expired` daily; configure a Render Cron Job to run `php artisan schedule:run` every minute for Laravel's scheduled tasks to execute.

As an alternative, set `SESSION_DRIVER=cookie` to keep the encrypted session payload in the browser rather than a database row. Cookie sessions avoid session-table writes but are limited by browser cookie size and are harder to revoke server-side.

## Deployment

The multi-stage Dockerfile builds frontend assets and Composer dependencies separately, then serves Laravel's `public` directory from PHP 8.4/Apache without Node in the final image. At startup, the entrypoint runs isolated migrations and optimizes the application; it exits without starting Apache if either step fails.

See [docs/deployment-render-supabase.md](docs/deployment-render-supabase.md) for the Render and Supabase deployment notes.

### Render and Supabase variables

Required Render environment variables:

- `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`
- `DB_CONNECTION=pgsql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DB_SSLMODE`
- `SESSION_DRIVER=database`, `SESSION_LIFETIME`, `SESSION_SECURE_COOKIE`
- `FILESYSTEM_DISK=s3`, `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`, `AWS_ENDPOINT`, `AWS_USE_PATH_STYLE_ENDPOINT`
- `AWS_PUBLIC_URL` or the configured Supabase public storage URL
- `TRUSTED_PROXIES`
- `LOG_CHANNEL`
- Optional administrator variables: `ADMIN_PHONE`, `ADMIN_PASSWORD`, `ADMIN_FIRST_NAME`, `ADMIN_LAST_NAME`, `ADMIN_ADDRESS`

Enter secret values only in the Render environment settings or Supabase dashboard. Do not commit credentials or put real values in this README or `.env.example`.
