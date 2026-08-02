# InvoicePro

InvoicePro is a portable, offline-first invoice report generator built with Laravel 12, Livewire 3, NativePHP, and SQLite.

## Development

Requirements: PHP 8.4+, Composer, Node.js 20+, and the PHP SQLite and ZIP extensions.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm run build
php artisan serve
```

The desktop runtime is a development dependency. On a fresh checkout, `composer install` downloads its bundled PHP runtime and can take several minutes.

```bash
php artisan native:install
php artisan native:serve
php artisan native:build
```

Use NativePHP's platform build options to create Windows, macOS, and Linux artifacts. Build each target on its native operating system for reliable signing and packaging.

## Data and exports

- SQLite file: `database/database.sqlite`
- Report formats: PDF, Excel (`.xlsx`), and CSV
- Database backup/restore: Settings
- Report filters are synchronized to the URL and optimized with composite indexes.

## Quality checks

```bash
vendor/bin/pint
php artisan test
npm run build
```
# invoice
