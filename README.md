# RICM — Research, Innovation and Commercialisation Management System

RICM is a Laravel web application for managing research and innovation information in one place. It provides role-protected workspaces for administrators and staff, a Super Admin overview across modules, record management, reporting, and spreadsheet import/export workflows.

## System modules

- **Super Admin dashboard:** Cross-module summaries for awards, intellectual property (IP), community/STEM/industry programs, TRL products, KPI achievements, projects, staff, competitions, and faculties. Includes year/faculty filters and workspace navigation.
- **User and access management:** Login, profile management, role-based access, user administration, and module permissions.
- **Awards and competitions:** Manage awards and exhibition/competition events, link records to projects and staff, and view dashboards, reports, and award statistics.
- **Intellectual Property (IP):** Manage IP records, researchers/staff and project associations, categories and status, renewal dates, dashboards, reports, and Excel/CSV imports and exports.
- **Products / Technology Readiness Level (TRL):** Manage product records and TRL levels, filter records, and import/export spreadsheets.
- **Community, STEM and Industry:** Manage program records, beneficiaries, evidence, duplicate review, dashboards, and MYRA/overall reports.
- **KPI:** Manage strategies, indicators, annual targets, achievements, and phases; view and export KPI data.
- **Research data and reporting:** Manage projects, staff, faculties, and shared reporting, with Excel, CSV, or PDF exports where available.

## Technology

- Laravel 10 and PHP 8.1 or later
- MySQL (production/local setup may use another Laravel-supported database)
- Composer for PHP dependencies
- Node.js and npm for compiling frontend assets with Vite
- Laravel Blade, Tailwind CSS, and Alpine.js for the web interface

## Local setup

1. Install PHP 8.1+, Composer, Node.js/npm, and configure a MySQL database.
2. Install dependencies and configure the environment:

   ```bash
   composer install
   copy .env.example .env
   php artisan key:generate
   ```

   Set the database connection values in `.env` (`DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`). Do not commit `.env` or production credentials.

3. Install frontend dependencies and build assets:

   ```bash
   npm install
   npm run build
   ```

4. Run schema migrations:

   ```bash
   php artisan migrate
   ```

5. Start the local application:

   ```bash
   php artisan serve
   ```

## Seed data

The default `DatabaseSeeder` seeds example/core data for roles, modules, users, faculties, projects, exhibition references, competitions, staff, community programs, awards, user-module permissions, and KPI data:

```bash
php artisan db:seed
```

Some seeders replace the contents of their target tables, and some depend on source files under `storage/app/import` or `database/seeders`. Review `database/seeders/DatabaseSeeder.php` and the individual seeders before running this command against a database with records you need to keep.

### IP data

IP records are stored in `ip_records`. The migration preserves legacy IP records by copying them from `intellectual_properties` into `ip_records`, archiving the old table as `legacy_intellectual_properties_archive`, and remapping staff associations.

A point-in-time snapshot of 463 records from the `dashboard-ip` Google Sheet's `DATA_IP` tab is included in `database/seeders/ip_records_seed.json`. After migrations have completed, load or refresh those records with:

```bash
php artisan db:seed --class=IpRecordsSeeder
```

The seeder upserts by Google Sheet source ID and leaves unrelated IP records intact. It does not connect to Google Sheets at runtime; update the snapshot separately when newer source data is needed.

### TRL product data

TRL product records use `trl_products`. The former `products` table is retained as `legacy_products_archive` for compatibility with old award references. To load the included TRL dataset:

```bash
php artisan db:seed --class=TrlProductsSeeder
```

The TRL seed contains 731 records from `dashboard-trl` and can be rerun safely.

### Other external seed files

- `KpiSeeder` reads `database/seeders/KPI_Data_Strategi.csv`.
- `CommunityStemProgramSeeder` reads `storage/app/import/borang_a.csv`.

Supply the relevant file before running the corresponding seeder. Missing input files mean those source records cannot be seeded.

## Deployment notes

- Point the web server document root at Laravel's `public` directory.
- Configure a production `.env` with `APP_ENV=production`, `APP_DEBUG=false`, a valid `APP_KEY`, and the correct database credentials.
- Ensure `storage` and `bootstrap/cache` are writable by the PHP/web-server user.
- Install Composer dependencies for production and build frontend assets before deploying.
- Run pending migrations using the deployment process and take a database backup first. Avoid running the full `db:seed` command in production unless its effects on existing records have been reviewed.
- If a deployed application returns HTTP 500, inspect the matching exception in `storage/logs/laravel.log` and the hosting PHP error log; do not enable public debug output on production.

## Tests and checks

Run the Laravel test suite with:

```bash
php artisan test
```

Build frontend assets with:

```bash
npm run build
```

## Repository structure

- `app/Http/Controllers` — request handling and module workflows
- `app/Models` — Eloquent models and relationships
- `database/migrations` — database schema and legacy-data migrations
- `database/seeders` — reference, sample, and source snapshot seeders
- `resources/views` — Blade pages and layouts
- `routes` — web and authentication routes
- `tests` — unit and feature tests

## License

This application uses Laravel and other open-source dependencies. See `composer.json` and the relevant package licenses for dependency licensing details.
