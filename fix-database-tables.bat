@echo off
echo ========================================
echo Fix Database Tables
echo ========================================
echo.
echo This script will create all required database tables
echo.

REM Check if Docker is being used
findstr /C:"DB_HOST=mysql" .env >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo Detected Docker configuration
    echo.
    echo Running migrations in Docker...
    docker-compose exec app php artisan migrate --force
) else (
    echo Detected XAMPP configuration
    echo.
    echo Running migrations...
    php artisan migrate --force
)

echo.
echo ========================================
echo Done!
echo ========================================
echo.
echo Tables created:
echo - sessions
echo - cache
echo - cache_locks
echo - jobs
echo - job_batches
echo - failed_jobs
echo - All your KYC tables
echo.
pause
