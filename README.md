# GPS - Laravel 12 Application

A Laravel 12-based offline-capable application with background job scheduling, Redis queues, Horizon monitoring, and Filament admin panel.

---

## 📀 Requirements

| Tool     | Minimum Version        |
| -------- | ---------------------- |
| PHP      | 8.2+                   |
| Composer | 2.x                    |
| Node.js  | 18+                    |
| npm      | 9+                     |
| MySQL    | 8.x                    |
| Redis    | For queues and Horizon |

---

## 👥 OS-Specific Stack Suggestions

* **Linux/macOS:** Laravel Herd, Nginx, MySQL, Redis, Supervisor
* **Windows:** Laravel Herd (preferred), or XAMPP/Laragon + Redis via WSL/Memurai

---

## 🚀 Project Setup

### 1. Clone the Project

```bash
git clone https://github.com/your-username/gps.git
cd gps
```

### 2. Install Dependencies

```bash
composer install
npm install && npm run build
```

### 3. Create and Configure `.env`

```bash
cp .env.example .env
php artisan key:generate
```

Update your `.env` file:

```dotenv
APP_NAME=GPS
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gps
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=redis
```

### 4. Create the Database

```sql
CREATE DATABASE gps;
```

### 5. Run Migrations

```bash
php artisan migrate --seed
```

---

## 🎨 Filament Admin Panel

```bash
composer require filament/filament:"^3.0"
php artisan filament:install
php artisan make:filament-user
```

---

## ⚡ Queue Worker Setup

### Install Redis (Linux)

```bash
sudo apt update
sudo apt install redis-server
sudo systemctl enable redis
sudo systemctl start redis
redis-cli ping # returns "PONG"
```

### Run Worker (Windows/Linux)

```bash
php artisan queue:work
```

---

## 🚀 Laravel Horizon Setup

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan migrate
php artisan horizon
```

Access Horizon at: `http://localhost/horizon`

---

## ⚙️ Supervisor (Linux Only)

### Create config:

```bash
sudo nano /etc/supervisor/conf.d/laravel-queue.conf
```

Paste:

```ini
[program:laravel-queue]
process_name=%(program_name)s_%(process_num)02d
command=/usr/bin/php /home/youruser/sites/gps/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/home/youruser/sites/gps/storage/logs/laravel-queue.log
stopwaitsecs=3600
```

### Then run:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-queue:*
```

---

## 🗕 Scheduler Setup (Linux)

Edit crontab:

```bash
crontab -e
```

Add:

```bash
* * * * * cd /home/youruser/sites/gps && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

---

## 📂 Permissions (Linux)

```bash
mkdir -p storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:www-data storage bootstrap/cache
```

---

## 🔢 Useful Artisan Commands

```bash
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
php artisan queue:restart
php artisan horizon
```

---

## 💻 Windows Notes

If using Laravel Herd or Laragon:

* Start Redis via Memurai or WSL.
* Queue workers must be run manually:

```bash
php artisan queue:work
```

---

## ✅ Troubleshooting

### Permission Denied (Linux)

```bash
chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:www-data storage bootstrap/cache
```

### Queue Not Working?

* Ensure `redis-server` is running
* Ensure `queue:work` or `horizon` is active
* Check logs: `storage/logs/laravel.log`

---

## 🔐 Security Tips

* NEVER commit `.env` to version control.
* Set proper file permissions.
* Use HTTPS in production.

---

## 📄 License

MIT © \[QuickApps]
