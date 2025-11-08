@echo off
echo ========================================
echo Stopping SKI Wealth Laravel Application
echo ========================================
echo.

docker-compose down

echo.
echo ========================================
echo Application stopped successfully!
echo ========================================
echo.
echo To start again, run: docker-start.bat
echo.
pause
