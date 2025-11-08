<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\KYC\KycController;
use App\Http\Controllers\KYC\RegulatoryInfoController;
use App\Http\Controllers\KYC\NominationController;
use App\Http\Controllers\KYC\DocumentController;
use App\Http\Controllers\IPV\IpvController;
use App\Http\Controllers\AccountClosure\AccountClosureController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KycReviewController;
use App\Http\Controllers\Admin\IpvReviewController;
use App\Http\Controllers\Admin\ClosureReviewController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ReportController;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/

// Landing/Home Page - Redirect to Register
Route::get('/', [AuthController::class, 'showRegistrationForm'])->name('home');

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
    Route::post('/personal-info', [KycController::class, 'submitPersonalInfo'])->name('personalInfo');

    // Step 2: Address
    Route::post('/address', [KycController::class, 'submitAddress'])->name('address');

    // Step 3: Bank Details
    Route::post('/verify-ifsc', [KycController::class, 'verifyIfsc'])->name('verifyIfsc');
    Route::post('/bank-details', [KycController::class, 'submitBankDetails'])->name('bankDetails');

    // Step 4: Market Segments
    Route::post('/market-segments', [KycController::class, 'submitMarketSegments'])->name('marketSegments');

    // Step 5: Regulatory Information
    Route::post('/regulatory-info', [KycController::class, 'submitRegulatoryInfo'])->name('regulatoryInfo');

    // Step 6: Disclosures
    Route::post('/disclosures', [KycController::class, 'submitDisclosures'])->name('disclosures');

    // Nomination
    Route::get('/nomination', [NominationController::class, 'showForm'])->name('nomination');
    Route::post('/nomination', [NominationController::class, 'submitNomination'])->name('nomination.submit');
    Route::post('/nomination/remove', [NominationController::class, 'deleteNominee'])->name('nomination.remove');

    // Document Upload
    Route::get('/documents', [DocumentController::class, 'showForm'])->name('documents');
    Route::post('/upload-documents', [DocumentController::class, 'uploadDocuments'])->name('uploadDocuments');

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
| Thank You Page Route
|--------------------------------------------------------------------------
*/
Route::get('/thank-you', function () {
    $userId = session('user_id');
    $registration = \App\Models\Registration::findOrFail($userId);
    return view('thank-you', compact('registration'));
})->middleware('auth.user')->name('thank-you');

/*
|--------------------------------------------------------------------------
| Admin Routes (Protected by admin.auth middleware)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Login (Public)
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');

    // Protected Admin Routes
    Route::middleware('admin.auth')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Profile
        Route::get('/profile', [AdminAuthController::class, 'showProfile'])->name('profile');
        Route::post('/profile', [AdminAuthController::class, 'updateProfile'])->name('profile.update');

        // Logout
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // KYC Management
        Route::prefix('kyc')->name('kyc.')->group(function () {
            Route::get('/', [KycReviewController::class, 'list'])->name('list');
            Route::get('/{id}', [KycReviewController::class, 'view'])->name('view');
            Route::post('/{id}/approve', [KycReviewController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [KycReviewController::class, 'reject'])->name('reject');
            Route::post('/{id}/request-changes', [KycReviewController::class, 'requestChanges'])->name('requestChanges');
            Route::get('/{id}/download-pdf', [KycReviewController::class, 'downloadPdf'])->name('downloadPdf');
        });

        // IPV Management
        Route::prefix('ipv')->name('ipv.')->group(function () {
            Route::get('/', [IpvReviewController::class, 'list'])->name('list');
            Route::get('/{id}', [IpvReviewController::class, 'view'])->name('view');
            Route::post('/{id}/approve', [IpvReviewController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [IpvReviewController::class, 'reject'])->name('reject');
        });

        // Account Closure Management
        Route::prefix('closure')->name('closure.')->group(function () {
            Route::get('/', [ClosureReviewController::class, 'list'])->name('list');
            Route::get('/{id}', [ClosureReviewController::class, 'view'])->name('view');
            Route::post('/{id}/approve', [ClosureReviewController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [ClosureReviewController::class, 'reject'])->name('reject');
        });

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserManagementController::class, 'list'])->name('list');
            Route::get('/{id}', [UserManagementController::class, 'view'])->name('view');
            Route::post('/{id}/disable', [UserManagementController::class, 'disable'])->name('disable');
            Route::post('/{id}/enable', [UserManagementController::class, 'enable'])->name('enable');
            Route::get('/{id}/activity-log', [UserManagementController::class, 'activityLog'])->name('activityLog');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/kyc', [ReportController::class, 'kycReport'])->name('kyc');
            Route::get('/ipv', [ReportController::class, 'ipvReport'])->name('ipv');
            Route::get('/closures', [ReportController::class, 'closureReport'])->name('closures');
            Route::post('/export', [ReportController::class, 'export'])->name('export');
        });

        // Settings
        Route::get('/settings', [AdminAuthController::class, 'showSettings'])->name('settings');
        Route::post('/settings', [AdminAuthController::class, 'updateSettings'])->name('settings.update');

        // Role Management (Super Admin only)
        Route::middleware('admin.role:super_admin')->group(function () {
            Route::get('/roles', [AdminAuthController::class, 'listRoles'])->name('roles.list');
            Route::get('/roles/create', [AdminAuthController::class, 'createRole'])->name('roles.create');
            Route::post('/roles', [AdminAuthController::class, 'storeRole'])->name('roles.store');
            Route::get('/roles/{id}/edit', [AdminAuthController::class, 'editRole'])->name('roles.edit');
            Route::put('/roles/{id}', [AdminAuthController::class, 'updateRole'])->name('roles.update');
            Route::delete('/roles/{id}', [AdminAuthController::class, 'deleteRole'])->name('roles.delete');
        });
    });
});
