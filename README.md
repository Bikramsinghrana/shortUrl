# Short URL Management System

Multi-tenant URL shortening application with role-based access control and company management.

## Features

- URL Shortening with custom codes
- Multi-tenant architecture
- Role-based permissions (SuperAdmin, Admin, Member)
- User invitation system with email notifications
- URL click tracking and analytics
- URL expiration management

## Requirements

- PHP >= 8.1
- Composer
- Node.js >= 16.x
- MySQL >= 5.7
- Apache

## Installation

```bash
# 1. Clone repository
git clone <https://github.com/Bikramsinghrana/shortUrl.git>   Or Unzip folder
cd shortUrl

# 2. Install dependencies
composer update
npm install

# 3. Configure environment
cp .env.example .env
# Edit .env with your database and mail settings

# 4. Generate application key
php artisan key:generate

# 5. Create database
mysql -u root -p -e "CREATE DATABASE short_url CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 6. Run migrations
php artisan migrate --seed

# 7. Publish vendor assets
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# 8. Create storage link
php artisan storage:link

# 9. Start development server
php artisan serve
```

## Environment Configuration

Edit `.env` file:

```env
DB_DATABASE=short_url
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="noreply@shorturl.com"
```


```php
// Create roles
foreach (['SuperAdmin', 'Admin', 'Member'] as $role) {
    Spatie\Permission\Models\Role::create(['name' => $role]);
}

// Create permissions
foreach (['invite-users', 'create-short-urls', 'edit-short-urls', 'delete-short-urls'] as $permission) {
    Spatie\Permission\Models\Permission::create(['name' => $permission]);
}

// Assign permissions
Spatie\Permission\Models\Role::findByName('SuperAdmin')->givePermissionTo(['invite-users']);
Spatie\Permission\Models\Role::findByName('Admin')->givePermissionTo(['invite-users', 'create-short-urls', 'edit-short-urls', 'delete-short-urls']);
Spatie\Permission\Models\Role::findByName('Member')->givePermissionTo(['create-short-urls', 'edit-short-urls', 'delete-short-urls']);

// Create first SuperAdmin user
$user = App\Models\User::create([
    'name' => 'Super Admin',
    'email' => 'admin@gmail.com',
    'password' => Hash::make('password'),
    'is_active' => true
]);
$user->assignRole('SuperAdmin');
```

## Access Application

Open browser: `http://localhost:8000`

Login with:
- Email: admin@gmail.com
- Password: password


> AI Tools ChatGPT Used and stack

Helped improve UI structure and Blade layout organization.
Supported in implementing permission logic using Spatie Roles & Permissions.


Here’s a single-tab, compact README.md (no sections jumping around) only for Laravel Passport implementation.
You can paste this as-is.

# Laravel Passport API Authentication

This project uses **Laravel Passport** to implement **token-based API authentication** (Register & Login).

---

## Installation  passport +++++++++++++++++++

```bash
composer require laravel/passport
php artisan migrate
php artisan passport:install

# Configuration
# User Model (app/Models/User.php)

use Laravel\Passport\HasApiTokens;
class User extends Authenticatable
{
    use HasApiTokens;
}


# Auth Service Provider (app/Providers/AuthServiceProvider.php)

use Laravel\Passport\Passport;
public function boot()
{
    $this->registerPolicies();
    Passport::routes();  // add this optional
}


# Auth Guard (config/auth.php)

'guards' => [
    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],

# API Routes (routes/api.php)
Route::post('register', [RegistrationController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);

# Route::middleware('auth:api')->get('profile', function (Request $request) {
#     return $request->user();
# });

Route::middleware(['auth:api', 'verified'])->prefix('users')->group(function() {
    Route::get('profile/index', [ProfileController::class, 'index'])->name('api.user.profile.index');
});

# Register API

# URL for postman  +++++
POST /api/register
Body (JSON)

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}

# Response
{
  "access_token": "TOKEN_VALUE",
  "token_type": "Bearer",
  "status" : "true"
}

# Login API

POST /api/login
# Body (JSON)

{
  "email": "john@example.com",
  "password": "password123"
}

# Response
{
  "access_token": "TOKEN_VALUE",
  "token_type": "Bearer"
}

# Postman Authorization api ++++++
# Add header key:

Authorization: Bearer TOKEN_VALUE
Accept: application/json
Content-Type: application/json


