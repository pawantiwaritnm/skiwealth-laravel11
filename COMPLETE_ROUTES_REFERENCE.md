# Complete Routes Reference for SKI Capital Laravel 11

This document contains all routes for the SKI Capital KYC application. Copy the appropriate sections to your `routes/web.php` file.

---

## Full routes/web.php File

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\KYC\KycController;
use App\Http\Controllers\KYC\RegulatoryInfoController;
use App\Http\Controllers\KYC\NominationController;
use App\Http\Controllers\KYC\DocumentController;
use App\Http\Controllers\IPV\IpvController;
use App\Http\Controllers\AccountClosure\AccountClosureController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing/Home Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Authentication Routes (OTP-based)
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->name('auth.')->group(function () {
    // Registration
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::post('/verify-registration-otp', [AuthController::class, 'verifyRegistrationOtp'])->name('verifyRegistrationOtp');

    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/send-login-otp', [AuthController::class, 'sendLoginOtp'])->name('sendLoginOtp');
    Route::post('/verify-login-otp', [AuthController::class, 'verifyLoginOtp'])->name('verifyLoginOtp');

    // Resend OTP
    Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('resendOtp');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth.user');
});

/*
|--------------------------------------------------------------------------
| KYC Routes (Protected by auth.user middleware)
|--------------------------------------------------------------------------
*/

Route::prefix('kyc')->name('kyc.')->middleware('auth.user')->group(function () {
    // Main KYC Form
    Route::get('/form', [KycController::class, 'showForm'])->name('form');

    // Step 1: Personal Information
    Route::post('/personal-info', [KycController::class, 'submitPersonalInfo'])
        ->name('submitPersonalInfo')
        ->middleware('kyc.step:1');

    // Step 2: Address
    Route::post('/address', [KycController::class, 'submitAddress'])
        ->name('submitAddress')
        ->middleware('kyc.step:2');

    // Step 3: Bank Details
    Route::post('/verify-ifsc', [KycController::class, 'verifyIfsc'])->name('verifyIfsc');
    Route::post('/bank-details', [KycController::class, 'submitBankDetails'])
        ->name('submitBankDetails')
        ->middleware('kyc.step:3');

    // Step 4: Market Segments
    Route::post('/market-segments', [KycController::class, 'submitMarketSegments'])
        ->name('submitMarketSegments')
        ->middleware('kyc.step:4');

    // Step 5: Regulatory Information
    Route::post('/regulatory-info', [RegulatoryInfoController::class, 'submitRegulatoryInfo'])
        ->name('submitRegulatoryInfo')
        ->middleware('kyc.step:5');

    // Step 6: Nomination
    Route::post('/nomination', [NominationController::class, 'submitNomination'])
        ->name('submitNomination')
        ->middleware('kyc.step:6');

    // Document Upload
    Route::post('/upload-documents', [DocumentController::class, 'uploadDocuments'])
        ->name('uploadDocuments')
        ->middleware('kyc.step:6');

    // Get Progress
    Route::get('/progress', [KycController::class, 'getProgress'])->name('progress');
});

/*
|--------------------------------------------------------------------------
| IPV (In-Person Verification) Routes
|--------------------------------------------------------------------------
*/

Route::prefix('ipv')->name('ipv.')->group(function () {
    // Public routes (before verification)
    Route::get('/permission', [IpvController::class, 'showPermissionPage'])->name('permission');
    Route::post('/check-user', [IpvController::class, 'checkUser'])->name('checkUser');
    Route::post('/verify-otp', [IpvController::class, 'verifyOtp'])->name('verifyOtp');

    // Protected routes (after verification)
    Route::middleware(['web', 'session'])->group(function () {
        Route::get('/camera', [IpvController::class, 'showCameraPage'])->name('camera');
        Route::post('/record', [IpvController::class, 'recordVideo'])->name('record');
        Route::post('/upload-base64', [IpvController::class, 'uploadBase64Video'])->name('uploadBase64');
    });

    // API routes (for checking history)
    Route::post('/history', [IpvController::class, 'getHistory'])->name('history');
});

/*
|--------------------------------------------------------------------------
| Account Closure Routes
|--------------------------------------------------------------------------
*/

Route::prefix('account-closure')->name('account.closure.')->group(function () {
    // Public routes (before verification)
    Route::get('/login', [AccountClosureController::class, 'showLoginPage'])->name('login');
    Route::post('/check-user', [AccountClosureController::class, 'checkUser'])->name('checkUser');
    Route::post('/verify-otp', [AccountClosureController::class, 'verifyOtp'])->name('verifyOtp');

    // Protected routes (after verification)
    Route::middleware(['web', 'session'])->group(function () {
        Route::get('/form', [AccountClosureController::class, 'showClosureForm'])->name('form');
        Route::post('/submit', [AccountClosureController::class, 'submitClosure'])->name('submit');
        Route::post('/verify-closure-otp', [AccountClosureController::class, 'verifyClosureOtp'])->name('verifyClosureOtp');
    });

    // API routes
    Route::post('/history', [AccountClosureController::class, 'getHistory'])->name('history');
});

/*
|--------------------------------------------------------------------------
| Dashboard Route
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth.user')->name('dashboard');

/*
|--------------------------------------------------------------------------
| Admin Routes (To be added)
|--------------------------------------------------------------------------
| These will be added when admin panel is migrated
*/

// Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {
//     // Admin routes go here
// });
```

---

## Route Groups by Module

### 1. Authentication Routes

```php
use App\Http\Controllers\Auth\AuthController;

Route::prefix('auth')->name('auth.')->group(function () {
    // Registration
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::post('/verify-registration-otp', [AuthController::class, 'verifyRegistrationOtp'])->name('verifyRegistrationOtp');

    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/send-login-otp', [AuthController::class, 'sendLoginOtp'])->name('sendLoginOtp');
    Route::post('/verify-login-otp', [AuthController::class, 'verifyLoginOtp'])->name('verifyLoginOtp');

    // Resend OTP
    Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('resendOtp');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth.user');
});
```

**Route Names:**
- `auth.register` (GET)
- `auth.register.post` (POST)
- `auth.verifyRegistrationOtp` (POST)
- `auth.login` (GET)
- `auth.sendLoginOtp` (POST)
- `auth.verifyLoginOtp` (POST)
- `auth.resendOtp` (POST)
- `auth.logout` (POST)

---

### 2. KYC Routes

```php
use App\Http\Controllers\KYC\KycController;
use App\Http\Controllers\KYC\RegulatoryInfoController;
use App\Http\Controllers\KYC\NominationController;
use App\Http\Controllers\KYC\DocumentController;

Route::prefix('kyc')->name('kyc.')->middleware('auth.user')->group(function () {
    // Main KYC Form
    Route::get('/form', [KycController::class, 'showForm'])->name('form');

    // Step 1: Personal Information
    Route::post('/personal-info', [KycController::class, 'submitPersonalInfo'])
        ->name('submitPersonalInfo')
        ->middleware('kyc.step:1');

    // Step 2: Address
    Route::post('/address', [KycController::class, 'submitAddress'])
        ->name('submitAddress')
        ->middleware('kyc.step:2');

    // Step 3: Bank Details
    Route::post('/verify-ifsc', [KycController::class, 'verifyIfsc'])->name('verifyIfsc');
    Route::post('/bank-details', [KycController::class, 'submitBankDetails'])
        ->name('submitBankDetails')
        ->middleware('kyc.step:3');

    // Step 4: Market Segments
    Route::post('/market-segments', [KycController::class, 'submitMarketSegments'])
        ->name('submitMarketSegments')
        ->middleware('kyc.step:4');

    // Step 5: Regulatory Information
    Route::post('/regulatory-info', [RegulatoryInfoController::class, 'submitRegulatoryInfo'])
        ->name('submitRegulatoryInfo')
        ->middleware('kyc.step:5');

    // Step 6: Nomination
    Route::post('/nomination', [NominationController::class, 'submitNomination'])
        ->name('submitNomination')
        ->middleware('kyc.step:6');

    // Document Upload
    Route::post('/upload-documents', [DocumentController::class, 'uploadDocuments'])
        ->name('uploadDocuments')
        ->middleware('kyc.step:6');

    // Get Progress
    Route::get('/progress', [KycController::class, 'getProgress'])->name('progress');
});
```

**Route Names:**
- `kyc.form` (GET)
- `kyc.submitPersonalInfo` (POST)
- `kyc.submitAddress` (POST)
- `kyc.verifyIfsc` (POST)
- `kyc.submitBankDetails` (POST)
- `kyc.submitMarketSegments` (POST)
- `kyc.submitRegulatoryInfo` (POST)
- `kyc.submitNomination` (POST)
- `kyc.uploadDocuments` (POST)
- `kyc.progress` (GET)

---

### 3. IPV Routes

```php
use App\Http\Controllers\IPV\IpvController;

Route::prefix('ipv')->name('ipv.')->group(function () {
    // Public routes (before verification)
    Route::get('/permission', [IpvController::class, 'showPermissionPage'])->name('permission');
    Route::post('/check-user', [IpvController::class, 'checkUser'])->name('checkUser');
    Route::post('/verify-otp', [IpvController::class, 'verifyOtp'])->name('verifyOtp');

    // Protected routes (after verification)
    Route::middleware(['web', 'session'])->group(function () {
        Route::get('/camera', [IpvController::class, 'showCameraPage'])->name('camera');
        Route::post('/record', [IpvController::class, 'recordVideo'])->name('record');
        Route::post('/upload-base64', [IpvController::class, 'uploadBase64Video'])->name('uploadBase64');
    });

    // API routes
    Route::post('/history', [IpvController::class, 'getHistory'])->name('history');
});
```

**Route Names:**
- `ipv.permission` (GET)
- `ipv.checkUser` (POST)
- `ipv.verifyOtp` (POST)
- `ipv.camera` (GET)
- `ipv.record` (POST)
- `ipv.uploadBase64` (POST)
- `ipv.history` (POST)

---

### 4. Account Closure Routes

```php
use App\Http\Controllers\AccountClosure\AccountClosureController;

Route::prefix('account-closure')->name('account.closure.')->group(function () {
    // Public routes (before verification)
    Route::get('/login', [AccountClosureController::class, 'showLoginPage'])->name('login');
    Route::post('/check-user', [AccountClosureController::class, 'checkUser'])->name('checkUser');
    Route::post('/verify-otp', [AccountClosureController::class, 'verifyOtp'])->name('verifyOtp');

    // Protected routes (after verification)
    Route::middleware(['web', 'session'])->group(function () {
        Route::get('/form', [AccountClosureController::class, 'showClosureForm'])->name('form');
        Route::post('/submit', [AccountClosureController::class, 'submitClosure'])->name('submit');
        Route::post('/verify-closure-otp', [AccountClosureController::class, 'verifyClosureOtp'])->name('verifyClosureOtp');
    });

    // API routes
    Route::post('/history', [AccountClosureController::class, 'getHistory'])->name('history');
});
```

**Route Names:**
- `account.closure.login` (GET)
- `account.closure.checkUser` (POST)
- `account.closure.verifyOtp` (POST)
- `account.closure.form` (GET)
- `account.closure.submit` (POST)
- `account.closure.verifyClosureOtp` (POST)
- `account.closure.history` (POST)

---

## Middleware Reference

### Custom Middleware Used

1. **auth.user** - Check if user is authenticated
   - Location: `app/Http/Middleware/CheckUserAuth.php`
   - Usage: Protects KYC and dashboard routes

2. **kyc.step:{step}** - Check if user is on correct KYC step
   - Location: `app/Http/Middleware/CheckKycStep.php`
   - Usage: Ensures sequential KYC form completion
   - Example: `->middleware('kyc.step:2')`

3. **admin.auth** - Check if admin is authenticated (to be implemented)
   - Location: `app/Http/Middleware/CheckAdminAuth.php`
   - Usage: Protects admin routes

4. **admin.role:{role}** - Check admin role (to be implemented)
   - Location: `app/Http/Middleware/CheckAdminRole.php`
   - Usage: Role-based access control for admin
   - Example: `->middleware('admin.role:super_admin')`

---

## Register Middleware in bootstrap/app.php

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.user' => \App\Http\Middleware\CheckUserAuth::class,
            'kyc.step' => \App\Http\Middleware\CheckKycStep::class,
            'admin.auth' => \App\Http\Middleware\CheckAdminAuth::class,
            'admin.role' => \App\Http\Middleware\CheckAdminRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

---

## Route Testing Commands

### Test All Routes

```bash
# List all routes
php artisan route:list

# List routes for specific module
php artisan route:list --name=auth
php artisan route:list --name=kyc
php artisan route:list --name=ipv
php artisan route:list --name=account.closure

# Clear route cache
php artisan route:clear

# Cache routes for production
php artisan route:cache
```

---

## URL Examples

### Authentication
- Registration: `http://localhost/auth/register`
- Login: `http://localhost/auth/login`

### KYC
- KYC Form: `http://localhost/kyc/form`
- KYC Progress: `http://localhost/kyc/progress`

### IPV
- IPV Permission: `http://localhost/ipv/permission`
- IPV Camera: `http://localhost/ipv/camera`

### Account Closure
- Closure Login: `http://localhost/account-closure/login`
- Closure Form: `http://localhost/account-closure/form`

### Dashboard
- User Dashboard: `http://localhost/dashboard`

---

## Blade Route Helper Usage

### In Blade Templates

```blade
<!-- Authentication -->
<a href="{{ route('auth.register') }}">Register</a>
<a href="{{ route('auth.login') }}">Login</a>

<!-- KYC -->
<a href="{{ route('kyc.form') }}">Start KYC</a>
<form action="{{ route('kyc.submitPersonalInfo') }}" method="POST">
    @csrf
    <!-- form fields -->
</form>

<!-- IPV -->
<a href="{{ route('ipv.permission') }}">Start IPV</a>
<form action="{{ route('ipv.checkUser') }}" method="POST">
    @csrf
    <!-- form fields -->
</form>

<!-- Account Closure -->
<a href="{{ route('account.closure.login') }}">Close Account</a>

<!-- Logout -->
<form action="{{ route('auth.logout') }}" method="POST">
    @csrf
    <button type="submit">Logout</button>
</form>
```

---

## JavaScript Fetch Examples

### Authentication

```javascript
// Register
const response = await fetch('{{ route("auth.register.post") }}', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        name: 'John Doe',
        mobile: '9876543210',
        email: 'john@example.com'
    })
});
```

### KYC

```javascript
// Submit Personal Info
const response = await fetch('{{ route("kyc.submitPersonalInfo") }}', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        father_name: 'Father Name',
        mother_name: 'Mother Name',
        dob: '1990-01-01',
        // ... other fields
    })
});
```

### IPV

```javascript
// Upload video
const formData = new FormData();
formData.append('video', videoBlob);
formData.append('image', imageBlob);

const response = await fetch('{{ route("ipv.record") }}', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: formData
});
```

---

## Route Protection Summary

### Public Routes (No Authentication)
- `/` (Home)
- `/auth/*` (All auth routes)
- `/ipv/permission`
- `/ipv/check-user`
- `/ipv/verify-otp`
- `/account-closure/login`
- `/account-closure/check-user`
- `/account-closure/verify-otp`

### User-Authenticated Routes
- `/dashboard`
- `/kyc/*` (All KYC routes)
- `/auth/logout`

### Session-Verified Routes
- `/ipv/camera`
- `/ipv/record`
- `/ipv/upload-base64`
- `/account-closure/form`
- `/account-closure/submit`
- `/account-closure/verify-closure-otp`

### Step-Protected Routes
- `/kyc/personal-info` (Step 1)
- `/kyc/address` (Step 2)
- `/kyc/bank-details` (Step 3)
- `/kyc/market-segments` (Step 4)
- `/kyc/regulatory-info` (Step 5)
- `/kyc/nomination` (Step 6)
- `/kyc/upload-documents` (Step 6)

---

## Next Steps

1. Copy the complete routes to `routes/web.php`
2. Verify middleware is registered in `bootstrap/app.php`
3. Test each route with `php artisan route:list`
4. Create missing Blade views
5. Test complete user flow
6. Add admin routes when admin panel is ready
7. Configure CSRF token in meta tags for all views

---

## Notes

- All POST routes require CSRF token
- File upload routes use `multipart/form-data`
- API routes return JSON responses
- Protected routes redirect to login if unauthenticated
- Step protection ensures KYC form is filled sequentially
- Session-based protection for IPV and account closure
