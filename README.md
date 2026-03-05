# Student Management System (v2student)

A comprehensive Laravel-based Student Management System for educational institutions. Manage students, courses, enrollments, payments, assessments, and certificates all in one platform.

## 🚀 Features

- **Student Management**: Registration, approval workflow, profile management, document uploads
- **Course & Batch Management**: Create and manage courses with batch scheduling
- **Enrollment System**: Enroll students in courses/batches with tracking
- **Payment Management**: Payment recording, approval workflow, receipt generation
- **Fee Latest Build 08-12-2025 Management**: Payment recording, approval workflow, receipt generation
- **Assessment System**: Question bank, online assessments, automatic grading
- **Certificate Management**: Generate and manage course completion certificates
- **Notifications**: Email and WhatsApp integration
- **Multi-role Access**: Admin, Reception, and Student portals

## 📋 Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18.x and npm
- MySQL/MariaDB or SQLite
- Web server (Apache/Nginx)

## 🛠️ Installation

### Local Development

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/v2student.git
   cd v2student
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database in `.env`**
   
   **For Local Development (SQLite - Current):**
   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=/path/to/database/database.sqlite
   ```
   *Note: SQLite is already configured and working locally*
   
   **For Production (MySQL):**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```
   *See [DATABASE_SETUP.md](./DATABASE_SETUP.md) for MySQL setup guide*

6. **Run migrations**
   ```bash
   php artisan migrate
   php artisan db:seed --class=AdminUserSeeder
   ```

7. **Create storage link**
   ```bash
   php artisan storage:link
   ```

8. **Build assets**
   ```bash
   npm run build
   # Or for development: npm run dev
   ```

9. **Start development server**
   ```bash
   php artisan serve
   ```

## 🚀 Production Deployment

For detailed deployment instructions to Hostinger, see **[DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md)**

For quick reference, see **[QUICK_DEPLOY.md](./QUICK_DEPLOY.md)**

### Quick Deployment Steps

1. **Create GitHub repository** and push code
2. **Create subdomain** in Hostinger: `v2insurance.softpromis.com`
3. **Clone repository** on server
4. **Configure `.env`** with production settings
5. **Run deployment script**:
   ```bash
   chmod +x deploy.sh
   ./deploy.sh
   ```

## 📁 Project Structure

```
v2student/
├── app/
│   ├── Http/Controllers/    # Application controllers
│   ├── Models/               # Eloquent models
│   ├── Services/             # Business logic services
│   └── Mail/                 # Email classes
├── database/
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── resources/
│   ├── views/                # Blade templates
│   ├── css/                  # Stylesheets
│   └── js/                   # JavaScript files
├── routes/
│   └── web.php               # Web routes
└── public/                   # Public assets
```

## 🔐 Default Admin Credentials

After running `AdminUserSeeder`, check the seeder file for default admin credentials.

**⚠️ Change default credentials immediately after first login!**

## 📝 Environment Configuration

Key environment variables:

```env
APP_NAME="Student Management System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://v2insurance.softpromis.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=your_email@softpromis.com
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=ssl
```

## 🧪 Testing

```bash
php artisan test
```

## 📦 Building Assets

**Development:**
```bash
npm run dev
```

**Production:**
```bash
npm run build
```

## 🔄 Updating the Application

1. Pull latest changes: `git pull origin main`
2. Install dependencies: `composer install --no-dev --optimize-autoloader`
3. Build assets: `npm run build`
4. Run migrations: `php artisan migrate --force`
5. Clear cache: `php artisan config:cache && php artisan route:cache`

## 📚 Documentation

- [Deployment Guide](./DEPLOYMENT_GUIDE.md) - Complete Hostinger deployment guide
- [Quick Deploy Reference](./QUICK_DEPLOY.md) - Quick deployment steps
- [Database Setup Guide](./DATABASE_SETUP.md) - SQLite (local) vs MySQL (production)
- [Email Setup](./EMAIL_SETUP.md) - Email configuration instructions

## 🐛 Troubleshooting

### Common Issues

**500 Internal Server Error**
- Check `.env` file exists and is configured
- Verify file permissions: `chmod -R 755 storage bootstrap/cache`
- Check error logs: `storage/logs/laravel.log`

**Assets Not Loading**
- Run `npm run build`
- Clear cache: `php artisan cache:clear`

**Database Connection Error**
- Verify database credentials in `.env`
- Ensure database exists and user has permissions

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👥 Support

For issues and questions:
1. Check [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md) for deployment help
2. Review error logs: `storage/logs/laravel.log`
3. Check Laravel documentation: https://laravel.com/docs

---

**Built with [Laravel](https://laravel.com)**
