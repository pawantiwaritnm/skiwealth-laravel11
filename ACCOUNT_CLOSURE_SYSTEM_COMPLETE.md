# Account Closure System - Complete Documentation

## Overview

The Account Closure system allows registered users to submit requests for closing their trading and demat accounts. It includes:
- Mobile number verification with OTP
- Secure form submission with file upload
- Two-step OTP verification (initial login + final confirmation)
- One submission per user limit
- Complete audit trail with IP tracking

---

## Architecture

### Service Classes
- `OtpService`: OTP generation and verification for account closure
- `SmsService`: SMS delivery via Onex Gateway

### Controller
- `AccountClosureController`: Handles all account closure operations

### Model
- `AccountClosure`: Stores account closure request records

---

## Routes

Add these routes to `routes/web.php`:

```php
// Account Closure Routes
Route::prefix('account-closure')->name('account.closure.')->group(function () {
    // Public routes (before verification)
    Route::get('/login', [App\Http\Controllers\AccountClosure\AccountClosureController::class, 'showLoginPage'])->name('login');
    Route::post('/check-user', [App\Http\Controllers\AccountClosure\AccountClosureController::class, 'checkUser'])->name('checkUser');
    Route::post('/verify-otp', [App\Http\Controllers\AccountClosure\AccountClosureController::class, 'verifyOtp'])->name('verifyOtp');

    // Protected routes (after verification)
    Route::middleware(['web', 'session'])->group(function () {
        Route::get('/form', [App\Http\Controllers\AccountClosure\AccountClosureController::class, 'showClosureForm'])->name('form');
        Route::post('/submit', [App\Http\Controllers\AccountClosure\AccountClosureController::class, 'submitClosure'])->name('submit');
        Route::post('/verify-closure-otp', [App\Http\Controllers\AccountClosure\AccountClosureController::class, 'verifyClosureOtp'])->name('verifyClosureOtp');
    });

    // API routes
    Route::post('/history', [App\Http\Controllers\AccountClosure\AccountClosureController::class, 'getHistory'])->name('history');
});
```

---

## Database Schema

The `account_closure_tbl` table stores:

```sql
- id (primary key)
- registration_id (foreign key to registration table)
- name (user's name)
- email (user's email)
- dp_id (Depository Participant ID)
- client_master_file (uploaded document path)
- reason_for_closure (reason for account closure)
- mobile_number (contact mobile)
- target_dp_id (target DP ID for transfer)
- client_id (client ID)
- trading_code (trading code/UCC)
- ip (IP address)
- verify_otp (1 if final OTP verified)
- status (1 = active, 0 = inactive)
- added_on (created timestamp)
- updated_on (updated timestamp)
```

---

## Blade Views

### 1. Login Page (`resources/views/account_closure/login.blade.php`)

```blade
<!DOCTYPE html>
<html>
<head>
    <title>Account Closure - Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        <h2>Account Closure Request</h2>
        <p class="warning">Account closure is permanent and irreversible.</p>

        <form id="accountClosureLoginForm">
            @csrf
            <div class="form-group">
                <label>Registered Mobile Number</label>
                <input type="text" name="mobile" id="mobile" maxlength="10" required>
                <span class="error" id="mobileError"></span>
            </div>

            <button type="submit">Send OTP</button>
        </form>

        <!-- OTP Verification Form (initially hidden) -->
        <div id="otpSection" style="display: none;">
            <h3>Enter OTP</h3>
            <form id="otpForm">
                @csrf
                <input type="hidden" name="mobile" id="otpMobile">
                <div class="form-group">
                    <input type="text" name="otp" id="otp" maxlength="6" required>
                    <span class="error" id="otpError"></span>
                </div>
                <button type="submit">Verify OTP</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('accountClosureLoginForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);

            try {
                const response = await fetch('{{ route("account.closure.checkUser") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Show OTP section
                    document.getElementById('otpSection').style.display = 'block';
                    document.getElementById('otpMobile').value = formData.get('mobile');
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        });

        document.getElementById('otpForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);

            try {
                const response = await fetch('{{ route("account.closure.verifyOtp") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Redirect to closure form
                    window.location.href = data.redirect;
                } else {
                    alert(data.message);
                    if (data.remaining_attempts !== undefined) {
                        alert(`Remaining attempts: ${data.remaining_attempts}`);
                    }
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        });
    </script>
</body>
</html>
```

### 2. Closure Form (`resources/views/account_closure/form.blade.php`)

```blade
<!DOCTYPE html>
<html>
<head>
    <title>Account Closure - Form</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        <h2>Account Closure Request</h2>
        <p class="warning"><strong>Account closure is permanent and irreversible.</strong></p>

        <div class="alert alert-info">
            <h4>Important Notice:</h4>
            <p>Please download your trade book, ledger, contract notes, tax P&L and any other statements before proceeding with account closure. You may need these for tax filing and compliance. This will not be available for download once the account is closed.</p>
        </div>

        <form id="accountClosureForm" enctype="multipart/form-data">
            @csrf

            <h3>Personal Information</h3>
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" required value="{{ $userName }}">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required value="{{ $userEmail }}">
            </div>

            <div class="form-group">
                <label>DP ID</label>
                <input type="text" name="dp_id" required>
            </div>

            <div class="form-group">
                <label>Client Master File (Optional)</label>
                <input type="file" name="client_master_file" accept=".pdf,.jpg,.jpeg,.png">
                <small>Max size: 2MB. Accepted formats: PDF, JPG, PNG</small>
            </div>

            <div class="form-group">
                <label>Reason for Closure</label>
                <select name="reason_for_closure" required>
                    <option value="">Select Reason</option>
                    <option value="Financial Constraints">Financial Constraints</option>
                    <option value="Service issue">Service issue</option>
                    <option value="Others">Others</option>
                </select>
            </div>

            <div class="form-group">
                <label>Mobile Number</label>
                <input type="text" name="mobile_number" maxlength="10" required>
            </div>

            <h3>Target Account Details</h3>
            <p>Please provide details of the account where you want to transfer your holdings.</p>

            <div class="form-group">
                <label>Target DP ID</label>
                <input type="text" name="target_dp_id" required>
            </div>

            <div class="form-group">
                <label>Client ID</label>
                <input type="text" name="client_id" required>
            </div>

            <div class="form-group">
                <label>Trading Code/UCC</label>
                <input type="text" name="trading_code" required>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="terms" required>
                    I understand that account closure is permanent and irreversible
                </label>
            </div>

            <button type="submit">Submit Request</button>
        </form>

        <!-- Final OTP Verification Form (initially hidden) -->
        <div id="finalOtpSection" style="display: none;">
            <h3>Verify OTP</h3>
            <p>An OTP has been sent to your registered mobile number for final verification.</p>
            <form id="finalOtpForm">
                @csrf
                <div class="form-group">
                    <label>Enter OTP</label>
                    <input type="text" name="otp" id="finalOtp" maxlength="6" required>
                    <span class="error" id="finalOtpError"></span>
                </div>
                <button type="submit">Verify & Complete</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('accountClosureForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);

            try {
                const response = await fetch('{{ route("account.closure.submit") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    if (data.requires_otp) {
                        // Show final OTP section
                        document.getElementById('accountClosureForm').style.display = 'none';
                        document.getElementById('finalOtpSection').style.display = 'block';
                        alert(data.message);
                    } else {
                        alert(data.message);
                        window.location.href = '/dashboard';
                    }
                } else {
                    if (data.errors) {
                        // Display validation errors
                        Object.keys(data.errors).forEach(key => {
                            alert(data.errors[key][0]);
                        });
                    } else {
                        alert(data.message);
                    }
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        });

        document.getElementById('finalOtpForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);

            try {
                const response = await fetch('{{ route("account.closure.verifyClosureOtp") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    window.location.href = '/dashboard';
                } else {
                    alert(data.message);
                    if (data.remaining_attempts !== undefined) {
                        alert(`Remaining attempts: ${data.remaining_attempts}`);
                    }
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        });
    </script>
</body>
</html>
```

---

## API Endpoints

### 1. Check User

**POST** `/account-closure/check-user`

**Request:**
```json
{
    "mobile": "9876543210"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "OTP sent successfully"
}
```

**Error Responses:**

User Not Found (422):
```json
{
    "success": false,
    "message": "Mobile number not registered"
}
```

Already Submitted (422):
```json
{
    "success": false,
    "message": "Account closure request already submitted"
}
```

---

### 2. Verify Login OTP

**POST** `/account-closure/verify-otp`

**Request:**
```json
{
    "mobile": "9876543210",
    "otp": "123456"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "OTP verified successfully",
    "redirect": "https://yoursite.com/account-closure/form"
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Invalid OTP",
    "remaining_attempts": 2
}
```

---

### 3. Submit Closure Request

**POST** `/account-closure/submit`

**Headers:**
```
Content-Type: multipart/form-data
```

**Request (multipart form-data):**
```
name: "John Doe"
email: "john@example.com"
dp_id: "DP001"
client_master_file: [file] (optional, max 2MB)
reason_for_closure: "Financial Constraints"
mobile_number: "9876543210"
target_dp_id: "DP002"
client_id: "CLIENT001"
trading_code: "UCC001"
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Account closure request submitted. Please verify OTP sent to your mobile.",
    "requires_otp": true,
    "otp_sent": true
}
```

**Error Responses:**

Session Expired (401):
```json
{
    "success": false,
    "message": "Session expired. Please verify again."
}
```

Already Submitted (422):
```json
{
    "success": false,
    "message": "Account closure request already submitted"
}
```

Validation Error (422):
```json
{
    "success": false,
    "errors": {
        "name": ["The name field is required."],
        "email": ["The email must be a valid email address."]
    }
}
```

---

### 4. Verify Final OTP

**POST** `/account-closure/verify-closure-otp`

**Request:**
```json
{
    "otp": "123456"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Account closure request verified successfully. Our team will process your request shortly."
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Invalid OTP",
    "remaining_attempts": 2
}
```

---

### 5. Get Closure History

**POST** `/account-closure/history`

**Request:**
```json
{
    "mobile": "9876543210"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "closures": [
        {
            "id": 123,
            "name": "John Doe",
            "email": "john@example.com",
            "dp_id": "DP001",
            "reason": "Financial Constraints",
            "mobile": "9876543210",
            "otp_verified": true,
            "submitted_at": "04 Nov 2025, 10:30 AM",
            "status": "Verified & Submitted"
        }
    ]
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "User not found"
}
```

---

## Testing Guide

### 1. Test User Login

```bash
# Check if user exists and send OTP
curl -X POST http://localhost/account-closure/check-user \
  -H "Content-Type: application/json" \
  -d '{
    "mobile": "9876543210"
  }'
```

### 2. Test OTP Verification

```bash
# Verify login OTP
curl -X POST http://localhost/account-closure/verify-otp \
  -H "Content-Type: application/json" \
  -d '{
    "mobile": "9876543210",
    "otp": "123456"
  }'
```

### 3. Test Closure Submission

```bash
# Submit closure request with file
curl -X POST http://localhost/account-closure/submit \
  -F "name=John Doe" \
  -F "email=john@example.com" \
  -F "dp_id=DP001" \
  -F "client_master_file=@/path/to/file.pdf" \
  -F "reason_for_closure=Financial Constraints" \
  -F "mobile_number=9876543210" \
  -F "target_dp_id=DP002" \
  -F "client_id=CLIENT001" \
  -F "trading_code=UCC001"
```

### 4. Test Final OTP

```bash
# Verify final OTP
curl -X POST http://localhost/account-closure/verify-closure-otp \
  -H "Content-Type: application/json" \
  -d '{
    "otp": "123456"
  }'
```

### 5. Test History

```bash
# Get closure history
curl -X POST http://localhost/account-closure/history \
  -H "Content-Type: application/json" \
  -d '{
    "mobile": "9876543210"
  }'
```

---

## Security Features

1. **Two-Step OTP Verification**: Initial login OTP + Final confirmation OTP
2. **Session Management**: Secure session storage for verification
3. **One Submission Limit**: Users can only submit one closure request
4. **File Validation**: Strict file type and size validation
5. **IP Logging**: Records IP address for each submission
6. **CSRF Protection**: Laravel CSRF token validation
7. **Input Sanitization**: All inputs validated and sanitized

---

## Workflow

1. User visits `/account-closure/login`
2. User enters registered mobile number
3. System checks if user exists and hasn't already submitted
4. OTP is sent to mobile number via SMS
5. User enters login OTP for verification
6. User is redirected to closure form
7. User fills out account closure form with:
   - Personal information
   - Reason for closure
   - Target account details for transfer
   - Optional client master file
8. User submits form
9. System sends final confirmation OTP
10. User enters final OTP to complete request
11. System marks OTP as verified
12. System clears all sessions
13. Admin can review and process request in admin panel

---

## File Storage Configuration

Ensure `config/filesystems.php` has:

```php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

Create storage link:
```bash
php artisan storage:link
```

---

## Common Issues and Solutions

### Issue 1: File Upload Fails
**Solution**: Adjust `php.ini` settings:
```ini
upload_max_filesize = 5M
post_max_size = 10M
```

### Issue 2: OTP Not Received
**Solution**: Check SMS service configuration in `.env` and ensure Onex SMS Gateway credentials are correct

### Issue 3: Session Expired Error
**Solution**: Ensure session middleware is active and session lifetime is sufficient

### Issue 4: Already Submitted Error
**Solution**: This is by design - users can only submit one request. Admin must process or reject existing request first.

---

## AccountClosure Model Helper Methods

The model includes these helper methods:

```php
// Get status text
$closure->getStatusText(); // "Verified & Submitted", "Pending Verification", etc.

// Check if OTP verified
$closure->isOtpVerified(); // true/false

// Get formatted date
$closure->getFormattedDate(); // "04 Nov 2025, 10:30 AM"

// Get file URL
$closure->getFileUrl(); // Full URL to uploaded file

// Check if has file
$closure->hasClientMasterFile(); // true/false
```

---

## Admin Panel Integration

**Recommended Admin Features:**

1. **List All Closure Requests**
   - Filter by status (pending, verified, processed)
   - Search by mobile/email/name
   - Sort by submission date

2. **View Request Details**
   - Display all submitted information
   - Show uploaded client master file
   - View submission IP and timestamp

3. **Process Request**
   - Approve/Reject closure
   - Add admin notes
   - Send email notification to user

4. **Export Reports**
   - Generate CSV/PDF of closures
   - Monthly/Yearly reports
   - Audit trail

---

## Email Notifications (To Be Implemented)

Recommended email notifications:

1. **Request Submitted**: Email to user confirming submission
2. **Request Approved**: Email to user when admin approves
3. **Request Rejected**: Email to user with rejection reason
4. **Account Closed**: Final confirmation email

---

## Files Created

1. `app/Http/Controllers/AccountClosure/AccountClosureController.php` - Complete controller
2. `app/Models/AccountClosure.php` - Model with relationships and helper methods
3. `app/Services/OtpService.php` - Already includes `sendAccountClosureOtp()` method

**Required Views** (to be created):
- `resources/views/account_closure/login.blade.php`
- `resources/views/account_closure/form.blade.php`

---

## Configuration Checklist

- [ ] Add account closure routes to `routes/web.php`
- [ ] Run `php artisan storage:link`
- [ ] Create account closure views
- [ ] Test OTP delivery
- [ ] Test file upload
- [ ] Set up admin panel for review
- [ ] Implement email notifications
- [ ] Test complete workflow end-to-end
- [ ] Add logging and monitoring

---

## Next Steps

1. Create Blade views for login and closure form
2. Add admin panel to review and process requests
3. Implement email notifications
4. Add user dashboard to track closure status
5. Create reports for closures
6. Add bulk processing for admin

---

## Important Notes

- Account closure is permanent and cannot be undone
- Users must download all statements before closure
- Holdings will be transferred to target account specified
- One closure request per user (prevents duplicate submissions)
- Two-step OTP verification ensures security
- Complete audit trail maintained
