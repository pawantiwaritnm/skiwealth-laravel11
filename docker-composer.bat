@echo off
REM Run Composer commands inside Docker container
REM Usage: docker-composer.bat [composer command]
REM Example: docker-composer.bat install
REM Example: docker-composer.bat require package/name

docker-compose exec app composer %*
