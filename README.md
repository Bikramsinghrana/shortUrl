# Short URL Service (Laravel)

This project is a multi-company short URL service built with Laravel, using:
- Role-based access (SuperAdmin, Admin, Member, Manager)
- Invitation flow (SuperAdmin/Admin)
- Public short URL resolution and redirect
- Service + Repository architecture with reusable Enums

## Tech Stack

- Laravel 10+
- PHP 8.1+
- MySQL or SQLite
- Spatie Laravel Permission

## Clone & Install

```bash
git clone https://github.com/Bikramsinghrana/shortUrl.git
cd shortUrl
composer install
npm install
cp .env.example .env
php artisan key:generate
```

## Database Setup

### Option A: MySQL
Set these in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=short_url
DB_USERNAME=root
DB_PASSWORD=
```

Then run:

```bash
php artisan migrate --seed
```

### Option B: SQLite
Set these in `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

Then run:

```bash
type nul > database/database.sqlite
php artisan migrate --seed
```

## SuperAdmin Seeder (Raw SQL)

The SuperAdmin account is created using raw SQL in `database/seeders/SuperAdminSeeder.php`.

Credentials:
- Email: `superadmin@gmail.com`
- Password: `password`

## Run Project

```bash
php artisan serve
npm run dev
```

Open: `http://127.0.0.1:8000`

## Run Tests

```bash
php artisan test
```

Targeted assignment test file:

```bash
php artisan test --filter=ShortUrlAuthorizationTest
```

## Assignment Rules Covered

- Admin and Member can create short URLs
- SuperAdmin cannot create short URLs
- SuperAdmin sees short URLs from all companies
- Admin sees short URLs created in their own company
- Member sees short URLs created by themselves
- Short URLs are publicly resolvable and redirect to the original URL
- SuperAdmin can only invite Admin into a new company
- Admin can invite Admin/Member only inside their own company

## Architecture Notes

- `app/Enums`: reusable domain enums (`RoleEnum`, `InvitationStatusEnum`, `CompanyOptionEnum`)
- `app/Repositories`: reusable data access layer (Eloquent ORM)
- `app/Services`: business rules and orchestration
- Controllers are thin and delegate logic to services

## GitHub Repository

https://github.com/Bikramsinghrana/shortUrl
