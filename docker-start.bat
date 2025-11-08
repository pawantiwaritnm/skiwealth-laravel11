@echo off
echo ========================================
echo Starting SKI Wealth Laravel Application
echo ========================================
echo.

REM Stop XAMPP MySQL if running
echo Checking for XAMPP MySQL...
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo WARNING: XAMPP MySQL is running. Please stop it from XAMPP Control Panel.
    echo Press any key to continue anyway, or close this window to stop.
    pause
)

echo.
echo Step 1: Copying Docker environment file...
copy /Y .env.docker .env

echo.
echo Step 2: Building Docker containers (this may take a few minutes)...
docker-compose build

echo.
echo Step 3: Starting Docker containers...
docker-compose up -d

echo.
echo Step 4: Installing Composer dependencies...
docker-compose exec app composer install --no-interaction

echo.
echo Step 5: Setting up Laravel...
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan storage:link

echo.
echo Step 6: Running database migrations...
docker-compose exec app php artisan migrate --force

echo.
echo ========================================
echo Application is now running!
echo ========================================
echo.
echo Access your application at:
echo   - Website:     http://localhost:8080
echo   - phpMyAdmin:  http://localhost:8081
echo.
echo MySQL Connection:
echo   - Host: localhost
echo   - Port: 3307
echo   - Database: wealthDBski
echo   - Username: root
echo   - Password: root
echo.
echo Useful Commands:
echo   - View logs:   docker-compose logs -f
echo   - Stop:        docker-compose down
echo   - Restart:     docker-compose restart
echo.
pause
