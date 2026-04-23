# Pakistan Grammar School Management System (PGS)

A Laravel-based school management system focused on student admissions, fee workflows, vouchers/challans, defaulter tracking, expenses, payroll, product/invoice sales, and reports.

## Modules

- Dashboard with KPIs and recent activity
- Student registration and roster management
- Student admission print and profile views
- Fee management (class-wise fee configuration)
- Fee voucher generation, print, payment tracking
- Pending, paid, and defaulter voucher lists
- Automated fee logic (fine, arrears carry forward, defaulter flags)
- Products management (CRUD + print)
- Sales invoices with student/product lookup
- Workers and salaries (advances, overtime, wage payments)
- Expenses management
- Reports and report print views
- Alerts/notifications

## Tech Stack

- PHP `^8.3`
- Laravel `^13`
- Blade templating + Vite frontend pipeline
- MySQL/SQLite compatible via Laravel database config
- DomPDF (`barryvdh/laravel-dompdf`) for PDF exports

## Requirements

- PHP 8.3+
- Composer
- Node.js 18+ and npm
- A database (SQLite works out of the box; MySQL supported)

## Quick Start

```bash
git clone <your-repo-url>
cd pgs
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run dev
php artisan serve
```

Open: [http://127.0.0.1:8000](http://127.0.0.1:8000)

## One-Command Local Setup

This project includes a helper script:

```bash
composer run setup
```

It installs PHP/JS dependencies, generates key, runs migrations, and builds frontend assets.

## Default Login (Seeder)

Run:

```bash
php artisan db:seed
```

Default seeded admin user:

- Email: `admin@pgs.local`
- Password: `Admin@12345`

Important: change this password immediately in non-local environments.

## Fee Automation Commands

The project includes:

```bash
php artisan fees:sync-automation
```

It recalculates:

- daily fines (Rs. 100/day after due date)
- voucher payable totals
- student defaulter flags

For production, run this command daily via cron/task scheduler.

## Demo Data for Fee Automation

To seed 20 students and fee scenarios (paid, arrears, fine, defaulters, sibling cases):

```bash
php artisan db:seed --class=DemoFeeAutomationSeeder
```

## Useful Development Commands

- Start Vite dev server: `npm run dev`
- Build assets: `npm run build`
- Run tests: `php artisan test`
- Clear config cache: `php artisan config:clear`

## Main Routes (High Level)

- `/dashboard`
- `/students/registration`
- `/students/roster`
- `/fee-management`
- `/fee-vouchers`
- `/fee-vouchers/lists/pending`
- `/fee-vouchers/lists/paid`
- `/fee-vouchers/lists/defaulters`
- `/products`
- `/invoices-sales`
- `/staff-salaries`
- `/expenses`
- `/reports`

## Project Structure

- `app/Http/Controllers` - module controllers
- `app/Models` - Eloquent models
- `app/Support` - shared business logic (fee engines/presenters)
- `resources/views` - Blade UI templates
- `database/migrations` - schema changes
- `database/seeders` - baseline/demo seeders
- `routes/web.php` - web routes
- `routes/console.php` - artisan commands

## Deployment Notes

- Set `APP_ENV=production` and `APP_DEBUG=false`
- Configure proper DB credentials in `.env`
- Run `php artisan migrate --force`
- Build frontend assets with `npm run build`
- Configure web server document root to `public/`
- Schedule `php artisan fees:sync-automation` daily

## License

This project is distributed under the MIT License unless your organization defines otherwise.
