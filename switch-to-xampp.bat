@echo off
echo ========================================
echo Switch to XAMPP Configuration
echo ========================================
echo.

echo Creating backup of current .env...
copy /Y .env .env.backup

echo.
echo Updating .env for XAMPP...

REM Create temporary file with XAMPP settings
(
echo APP_NAME="SKI Capital"
echo APP_ENV=local
echo APP_KEY=base64:jHL8AlnOGVVwrEIB6+Bnv4L97xSzvzVwlVv/l6NDoUA=
echo APP_DEBUG=true
echo APP_TIMEZONE=UTC
echo APP_URL=http://localhost
echo.
echo APP_LOCALE=en
echo APP_FALLBACK_LOCALE=en
echo APP_FAKER_LOCALE=en_US
echo.
echo APP_MAINTENANCE_DRIVER=file
echo.
echo PHP_CLI_SERVER_WORKERS=4
echo.
echo BCRYPT_ROUNDS=12
echo.
echo LOG_CHANNEL=stack
echo LOG_STACK=single
echo LOG_DEPRECATIONS_CHANNEL=null
echo LOG_LEVEL=debug
echo.
echo DB_CONNECTION=mysql
echo DB_HOST=127.0.0.1
echo DB_PORT=3306
echo DB_DATABASE=wealthDBski
echo DB_USERNAME=root
echo DB_PASSWORD=
echo.
echo SESSION_DRIVER=file
echo SESSION_LIFETIME=120
echo SESSION_ENCRYPT=false
echo SESSION_PATH=/
echo SESSION_DOMAIN=null
echo.
echo BROADCAST_CONNECTION=log
echo FILESYSTEM_DISK=local
echo QUEUE_CONNECTION=sync
echo.
echo CACHE_STORE=file
echo CACHE_PREFIX=
echo.
echo MEMCACHED_HOST=127.0.0.1
echo.
echo REDIS_CLIENT=phpredis
echo REDIS_HOST=127.0.0.1
echo REDIS_PASSWORD=null
echo REDIS_PORT=6379
echo.
echo MAIL_MAILER=log
echo MAIL_SCHEME=null
echo MAIL_HOST=127.0.0.1
echo MAIL_PORT=2525
echo MAIL_USERNAME=null
echo MAIL_PASSWORD=null
echo MAIL_FROM_ADDRESS="hello@example.com"
echo MAIL_FROM_NAME="${APP_NAME}"
echo.
echo AWS_ACCESS_KEY_ID=
echo AWS_SECRET_ACCESS_KEY=
echo AWS_DEFAULT_REGION=us-east-1
echo AWS_BUCKET=
echo AWS_USE_PATH_STYLE_ENDPOINT=false
echo.
echo VITE_APP_NAME="${APP_NAME}"
echo.
echo # SMS Service ^(Onex Gateway^)
echo ONEX_SMS_URL=https://api.onex-aura.com/api/sms
echo ONEX_SMS_API_KEY=
echo ONEX_SMS_SENDER=SKICAP
echo.
echo # Sandbox API ^(KYC Verification^)
echo SANDBOX_API_URL=https://api.sandbox.co.in
echo SANDBOX_API_KEY=key_live_443ZOVlWrFDzaiKYVKG4V0rymRdKR6NU
echo SANDBOX_SECRET=secret_live_gekpWDcOcUBezLnCFk61WYGbpuep4ePM
echo.
echo # Razorpay IFSC API
echo RAZORPAY_IFSC_URL=https://ifsc.razorpay.com
echo.
echo # Google reCAPTCHA
echo RECAPTCHA_SITE_KEY_IPV=
echo RECAPTCHA_SECRET_KEY_IPV=
echo RECAPTCHA_SITE_KEY_NOMINATION=
echo RECAPTCHA_SECRET_KEY_NOMINATION=
) > .env

echo.
echo Clearing Laravel cache...
php artisan config:clear
php artisan cache:clear

echo.
echo ========================================
echo Switched to XAMPP configuration!
echo ========================================
echo.
echo Changes made:
echo - DB_HOST changed to 127.0.0.1
echo - DB_PASSWORD removed (empty for XAMPP)
echo - SESSION_DRIVER changed to file
echo - CACHE_STORE changed to file
echo - QUEUE_CONNECTION changed to sync
echo.
echo IMPORTANT: You still need to fix MySQL permissions!
echo Run: fix_mysql.bat
echo.
echo Old .env backed up to: .env.backup
echo.
pause
