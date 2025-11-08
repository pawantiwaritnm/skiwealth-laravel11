# SKI Wealth Laravel Application - Docker Setup

Complete Docker configuration for running the SKI Wealth Laravel application.

## 🚀 Quick Start

### Prerequisites
- **Docker Desktop** installed and running ([Download here](https://www.docker.com/products/docker-desktop))
- **Git** (optional, for version control)

### Installation Steps

1. **Stop XAMPP MySQL** (if running)
   - Open XAMPP Control Panel
   - Click "Stop" next to MySQL

2. **Start the Application**
   ```batch
   # Simply double-click or run:
   docker-start.bat
   ```

3. **Access the Application**
   - **Website:** http://localhost:8080
   - **phpMyAdmin:** http://localhost:8081
     - Username: `root`
     - Password: `root`

## 📦 What's Included

The Docker setup includes:

- **PHP 8.2-FPM** - Application server
- **Nginx** - Web server
- **MySQL 8.0** - Database
- **Redis** - Cache and sessions
- **phpMyAdmin** - Database management

## 🔧 Helper Scripts

### `docker-start.bat`
Starts the entire application (first time setup)

### `docker-stop.bat`
Stops all Docker containers

### `docker-artisan.bat`
Run Laravel Artisan commands
```batch
docker-artisan.bat migrate
docker-artisan.bat make:controller TestController
docker-artisan.bat db:seed
docker-artisan.bat cache:clear
```

### `docker-composer.bat`
Run Composer commands
```batch
docker-composer.bat install
docker-composer.bat update
docker-composer.bat require vendor/package
```

## 🗄️ Database Access

### From Your Host Machine
- **Host:** `localhost`
- **Port:** `3307`
- **Database:** `wealthDBski`
- **Username:** `root`
- **Password:** `root`

### From Laravel (Inside Docker)
- **Host:** `mysql`
- **Port:** `3306`
- **Database:** `wealthDBski`
- **Username:** `root`
- **Password:** `root`

## 🛠️ Common Commands

### View Logs
```batch
docker-compose logs -f
docker-compose logs -f app
docker-compose logs -f mysql
docker-compose logs -f nginx
```

### Restart Services
```batch
docker-compose restart
docker-compose restart app
docker-compose restart mysql
```

### Stop Services
```batch
docker-compose down
```

### Start Services (After Initial Setup)
```batch
docker-compose up -d
```

### Execute Commands in Container
```batch
# Access bash in app container
docker-compose exec app bash

# Run artisan commands
docker-compose exec app php artisan migrate

# Run composer commands
docker-compose exec app composer install
```

### Database Operations
```batch
# Run migrations
docker-artisan.bat migrate

# Rollback migrations
docker-artisan.bat migrate:rollback

# Fresh migration (WARNING: Deletes all data)
docker-artisan.bat migrate:fresh

# Seed database
docker-artisan.bat db:seed
```

### Clear Cache
```batch
docker-artisan.bat config:clear
docker-artisan.bat cache:clear
docker-artisan.bat route:clear
docker-artisan.bat view:clear
```

## 📁 File Structure

```
skiwealth-laravel11/
├── docker/
│   ├── nginx/
│   │   └── conf.d/
│   │       └── default.conf      # Nginx configuration
│   └── mysql/
│       └── my.cnf                # MySQL configuration
├── docker-compose.yml            # Docker services configuration
├── Dockerfile                    # PHP application Docker image
├── .env.docker                   # Docker environment variables
├── .dockerignore                 # Files to exclude from Docker
├── docker-start.bat              # Start script
├── docker-stop.bat               # Stop script
├── docker-artisan.bat            # Artisan helper
└── docker-composer.bat           # Composer helper
```

## 🔍 Troubleshooting

### Port Already in Use
If you get "port already in use" errors:

1. **For port 8080 (Website):**
   ```batch
   # Find process using port 8080
   netstat -ano | findstr :8080
   # Kill the process
   taskkill /PID [process_id] /F
   ```

2. **For port 3307 (MySQL):**
   - Stop XAMPP MySQL from XAMPP Control Panel

### MySQL Connection Issues
- Make sure you're using the correct host:
  - `mysql` - from inside Docker containers
  - `localhost` or `127.0.0.1` - from your Windows machine (port 3307)

### Permission Issues
```batch
# Fix storage permissions
docker-compose exec app chmod -R 777 storage bootstrap/cache
```

### Rebuild Containers
If things aren't working, rebuild:
```batch
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Reset Everything
Complete reset (WARNING: Deletes all data):
```batch
docker-compose down -v
docker-start.bat
```

## 🔄 Import Existing Database

If you have an existing database dump:

1. **Via phpMyAdmin:**
   - Go to http://localhost:8081
   - Select `wealthDBski` database
   - Click "Import"
   - Choose your `.sql` file

2. **Via Command Line:**
   ```batch
   # Copy SQL file to container
   docker cp your-database.sql skiwealth-mysql:/tmp/

   # Import
   docker-compose exec mysql mysql -uroot -proot wealthDBski < /tmp/your-database.sql
   ```

## 🌐 Environment Variables

The `.env.docker` file is automatically copied to `.env` when you run `docker-start.bat`.

Key differences from XAMPP:
- `DB_HOST=mysql` (instead of 127.0.0.1)
- `DB_PASSWORD=root` (instead of empty)
- `REDIS_HOST=redis` (instead of 127.0.0.1)
- `APP_URL=http://localhost:8080`

## 📊 Performance

For better performance, you can allocate more resources to Docker Desktop:
1. Right-click Docker Desktop icon → Settings
2. Resources → Advanced
3. Increase CPU and Memory (recommended: 4 CPUs, 4 GB RAM)

## 🔐 Security Notes

**Development Environment Only:**
- Root password is set to `root` for ease of development
- For production, use strong passwords and secure configurations

## 📝 Notes

- Storage folder is automatically created and permissions set
- Public storage link is created automatically
- All Laravel caches are cleared on startup
- Database migrations run automatically on first start

## 🆘 Getting Help

If you encounter issues:
1. Check the logs: `docker-compose logs -f`
2. Verify all containers are running: `docker-compose ps`
3. Restart the containers: `docker-compose restart`
4. Rebuild if needed: `docker-compose build --no-cache`

## 🎉 Success!

If everything is working, you should see:
- ✅ Website at http://localhost:8080
- ✅ phpMyAdmin at http://localhost:8081
- ✅ No errors in logs
- ✅ Database connected

Happy coding! 🚀
