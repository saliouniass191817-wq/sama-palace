# Render and Supabase deployment

## Supabase database connection

Use the shared **Session pooler** connection string shown in the Supabase dashboard under **Connect**. Copy its host exactly; do not derive the pooler hostname from the region. The Session pooler uses port `5432`, database `postgres`, and a username in the form `postgres.<project-ref>`.

Do not use the Transaction pooler on port `6543` for this Laravel/PDO connection. Supabase documents that transaction mode does not support prepared statements.

Configure these variables in the Render service environment. Enter secret values directly in Render; never commit them or put them in this document.

| Variable | Value |
| --- | --- |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://sama-palace.onrender.com` |
| `SESSION_SECURE_COOKIE` | `true` |
| `DB_CONNECTION` | `pgsql` |
| `DB_HOST` | Exact Session pooler host copied from Supabase **Connect** |
| `DB_PORT` | `5432` |
| `DB_DATABASE` | `postgres` |
| `DB_USERNAME` | `postgres.<project-ref>` from the copied connection string |
| `DB_PASSWORD` | Supabase database password, entered as a Render secret |
| `DB_SSLMODE` | `require` |

Keep the current `APP_KEY` unchanged if Render already has one. If it has never been configured, generate it once and store it as a Render secret before deploying; do not rotate it as part of ordinary deployments.

Laravel uses the `public` schema. The application also uses Supabase Storage through the existing `AWS_*` variables; keep those values unchanged during the database cutover.

## Data transfer from MySQL

Do not begin this step until the old database has been backed up and the owner confirms its data should be retained. Keep the old database read-only and available for rollback. Import data into the schema created by Laravel migrations; do not import the old `migrations` table or run `migrate:fresh`.

Use a one-time `pgloader` data-only import into a separate staging Supabase project first, or export/import individual tables with CSV and PostgreSQL `COPY`. Load parent tables before dependents: `users`, then `shops`, then `products`, then `clicks`; load `sessions` after `users`. Other cache and queue tables can be imported only if their transient contents matter. Convert date/time values consistently to UTC and retain decimal product prices exactly.

After import, reset each auto-increment sequence (`users`, `shops`, `products`, `clicks`, `jobs`, and `failed_jobs`) with `setval(pg_get_serial_sequence(...), MAX(id), true)`; use `setval(..., 1, false)` for an empty table. Compare source and target row counts table by table, check foreign-key orphans, and test sign-in and seller flows before switching traffic. Keep an export and the old database until this verification is complete.

## Deployment order

1. Keep the current MySQL database untouched. Take a backup/export and record row counts before cutover.
2. Apply Laravel migrations to the empty Supabase schema and import the MySQL data. Pause writes during the final export/import window so no records are lost between the snapshot and the switch.
3. Verify row counts, sequences, foreign keys, RLS, and the main application flows while the old database remains available.
4. Add the production variables in this document to Render using **Save only**. This avoids restarting the current image before the replacement image includes `pdo_pgsql`.
5. Deploy the reviewed code that installs `pdo_pgsql` and listens on Render's `PORT` (default `10000`). On container start, it gives each PostgreSQL connection up to five seconds and retries `php artisan migrate --force` up to three times, then starts Apache even if the database is temporarily unreachable. Do not run the demo seeder in production.
6. Verify deployment logs and the application flows, then reopen writes. Keep the previous MySQL database and export available through the rollback window.

Render starts a new deployment when the code is deployed; its environment-variable page also offers **Save only**, which applies saved values on a later deploy. Schedule the cutover with the Render service settings and the availability of the old database in mind.

## Rollback

To roll back the application, deploy the previous image/commit and restore the prior MySQL environment variables. The old MySQL database is not modified by this migration. Keep both databases available until the rollback window is closed. Never use `migrate:fresh`, `DROP`, or `TRUNCATE` as a rollback procedure.

## Row-level security

The PostgreSQL migration enables RLS on the application's tables in `public` without adding policies. Supabase REST/API roles therefore receive no table access through RLS. Laravel connects from the server using its database credentials; keep those credentials private and never expose them in the browser.
