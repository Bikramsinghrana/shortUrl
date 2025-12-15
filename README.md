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
- Apache/Nginx

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

# 10. Seeder 
php artisan db:seed

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
    'email' => 'superadmin@gmail.com',
    'password' => Hash::make('password'),
    'is_active' => true
]);
$user->assignRole('SuperAdmin');
```

## Access Application

Open browser: `http://localhost:8000`

Login with:
- Email: superadmin@gmail.com
- Password: password


> AI Tools ChatGPT Used and stack

Helped improve UI structure and Blade layout organization.
Supported in implementing permission logic using Spatie Roles & Permissions.



