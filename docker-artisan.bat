@echo off
REM Run Laravel Artisan commands inside Docker container
REM Usage: docker-artisan.bat [artisan command]
REM Example: docker-artisan.bat migrate
REM Example: docker-artisan.bat make:controller TestController

docker-compose exec app php artisan %*
