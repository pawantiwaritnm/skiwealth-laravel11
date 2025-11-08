@echo off
echo ========================================
echo Export XAMPP Database to Docker
echo ========================================
echo.

set BACKUP_FILE=database-backup-%date:~-4,4%%date:~-10,2%%date:~-7,2%-%time:~0,2%%time:~3,2%%time:~6,2%.sql
set BACKUP_FILE=%BACKUP_FILE: =0%

echo Exporting database: wealthDBski
echo Backup file: %BACKUP_FILE%
echo.

C:\xampp\mysql\bin\mysqldump.exe -u root wealthDBski > %BACKUP_FILE%

if %ERRORLEVEL% EQU 0 (
    echo ✓ Database exported successfully!
    echo.
    echo File saved as: %BACKUP_FILE%
    echo.
    echo To import into Docker:
    echo 1. Start Docker: docker-start.bat
    echo 2. Go to: http://localhost:8081
    echo 3. Select 'wealthDBski' database
    echo 4. Click 'Import'
    echo 5. Choose file: %BACKUP_FILE%
    echo.
) else (
    echo ✗ Export failed!
    echo.
    echo Make sure XAMPP MySQL is running.
    echo.
)

pause
