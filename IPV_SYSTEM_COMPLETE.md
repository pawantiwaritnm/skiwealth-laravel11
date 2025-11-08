# IPV (In-Person Verification) System - Complete Documentation

## Overview

The IPV system allows users to complete video-based identity verification after finishing their KYC application. It includes:
- Mobile number verification with reCAPTCHA
- OTP verification via SMS
- Video and image capture with geolocation
- Maximum 3 submission attempts per user
- Support for both file upload and base64 encoding

---

## Architecture

### Service Classes
- `RecaptchaService`: Google reCAPTCHA v2 verification
- `OtpService`: OTP generation and verification for IPV
- `SmsService`: SMS delivery via Onex Gateway

### Controller
- `IpvController`: Handles all IPV operations

### Model
- `UserCaptureVideo`: Stores IPV submission records

---

## Routes

Add these routes to `routes/web.php`:

```php
// IPV Routes (In-Person Verification)
Route::prefix('ipv')->name('ipv.')->group(function () {
    // Public routes (before verification)
    Route::get('/permission', [App\Http\Controllers\IPV\IpvController::class, 'showPermissionPage'])->name('permission');
    Route::post('/check-user', [App\Http\Controllers\IPV\IpvController::class, 'checkUser'])->name('checkUser');
    Route::post('/verify-otp', [App\Http\Controllers\IPV\IpvController::class, 'verifyOtp'])->name('verifyOtp');

    // Protected routes (after verification)
    Route::middleware(['web', 'session'])->group(function () {
        Route::get('/camera', [App\Http\Controllers\IPV\IpvController::class, 'showCameraPage'])->name('camera');
        Route::post('/record', [App\Http\Controllers\IPV\IpvController::class, 'recordVideo'])->name('record');
        Route::post('/upload-base64', [App\Http\Controllers\IPV\IpvController::class, 'uploadBase64Video'])->name('uploadBase64');
    });

    // API routes (for checking history)
    Route::post('/history', [App\Http\Controllers\IPV\IpvController::class, 'getHistory'])->name('history');
});
```

---

## Environment Variables

Add to `.env`:

```env
# Google reCAPTCHA for IPV
RECAPTCHA_SITE_KEY_IPV=your_recaptcha_site_key_here
RECAPTCHA_SECRET_KEY_IPV=your_recaptcha_secret_key_here

# For nomination (if needed later)
RECAPTCHA_SITE_KEY_NOMINATION=your_nomination_recaptcha_site_key
RECAPTCHA_SECRET_KEY_NOMINATION=your_nomination_recaptcha_secret_key
```

---

## Blade Views

### 1. Permission/Login Page (`resources/views/ipv/permission.blade.php`)

```blade
<!DOCTYPE html>
<html>
<head>
    <title>IPV - Identity Verification</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        <h2>In-Person Verification (IPV)</h2>
        <p>Please enter your registered mobile number to continue with video verification.</p>

        <form id="ipvForm">
            @csrf
            <div class="form-group">
                <label>Mobile Number</label>
                <input type="text" name="mobile" id="mobile" maxlength="10" required>
                <span class="error" id="mobileError"></span>
            </div>

            <div class="form-group">
                <div class="g-recaptcha" data-sitekey="{{ $siteKey }}"></div>
                <span class="error" id="recaptchaError"></span>
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
                <button type="button" id="resendOtp">Resend OTP</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('ipvForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);

            try {
                const response = await fetch('{{ route("ipv.checkUser") }}', {
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
                    if (data.errors) {
                        // Display validation errors
                        Object.keys(data.errors).forEach(key => {
                            document.getElementById(key + 'Error').textContent = data.errors[key][0];
                        });
                    } else {
                        alert(data.message);
                    }
                    // Reset reCAPTCHA
                    grecaptcha.reset();
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
                grecaptcha.reset();
            }
        });

        document.getElementById('otpForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);

            try {
                const response = await fetch('{{ route("ipv.verifyOtp") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Redirect to camera page
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

### 2. Camera Page (`resources/views/ipv/camera.blade.php`)

```blade
<!DOCTYPE html>
<html>
<head>
    <title>IPV - Video Recording</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        video {
            width: 100%;
            max-width: 640px;
            height: auto;
        }
        canvas {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Video Verification</h2>
        <p>Welcome, <strong>{{ $userName }}</strong></p>

        <div class="instructions">
            <h3>Instructions:</h3>
            <ul>
                <li>Allow camera and microphone access</li>
                <li>Ensure good lighting and clear background</li>
                <li>Look directly at the camera</li>
                <li>Recording will be 5-10 seconds</li>
                <li>Location access is required</li>
            </ul>
        </div>

        <div id="cameraSection">
            <video id="video" autoplay playsinline></video>
            <canvas id="canvas"></canvas>

            <div class="controls">
                <button id="startRecord" disabled>Start Recording</button>
                <button id="stopRecord" disabled style="display: none;">Stop Recording</button>
                <button id="uploadBtn" disabled style="display: none;">Upload Video</button>
            </div>

            <div id="status"></div>
        </div>
    </div>

    <script>
        let mediaStream = null;
        let mediaRecorder = null;
        let recordedChunks = [];
        let capturedImage = null;
        let locationData = {};

        // Get geolocation
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    locationData = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    };

                    // Reverse geocoding (you can use Google Maps API or similar)
                    // For now, we'll just store lat/lng
                    console.log('Location captured:', locationData);
                },
                (error) => {
                    console.error('Location error:', error);
                    alert('Location access is required for IPV verification.');
                }
            );
        }

        // Request camera and microphone access
        async function initCamera() {
            try {
                mediaStream = await navigator.mediaDevices.getUserMedia({
                    video: { width: 1280, height: 720 },
                    audio: true
                });

                const video = document.getElementById('video');
                video.srcObject = mediaStream;

                document.getElementById('startRecord').disabled = false;
                document.getElementById('status').textContent = 'Camera ready. Click "Start Recording" when ready.';
            } catch (error) {
                console.error('Camera error:', error);
                alert('Camera and microphone access is required for IPV verification.');
            }
        }

        // Start recording
        document.getElementById('startRecord').addEventListener('click', () => {
            recordedChunks = [];

            mediaRecorder = new MediaRecorder(mediaStream, {
                mimeType: 'video/webm;codecs=vp9'
            });

            mediaRecorder.ondataavailable = (event) => {
                if (event.data.size > 0) {
                    recordedChunks.push(event.data);
                }
            };

            mediaRecorder.onstop = () => {
                // Capture screenshot
                captureScreenshot();

                document.getElementById('startRecord').style.display = 'none';
                document.getElementById('stopRecord').style.display = 'none';
                document.getElementById('uploadBtn').disabled = false;
                document.getElementById('uploadBtn').style.display = 'inline-block';
                document.getElementById('status').textContent = 'Recording complete. Click "Upload Video" to submit.';
            };

            mediaRecorder.start();

            document.getElementById('startRecord').style.display = 'none';
            document.getElementById('stopRecord').disabled = false;
            document.getElementById('stopRecord').style.display = 'inline-block';
            document.getElementById('status').textContent = 'Recording... Please look at the camera.';
        });

        // Stop recording
        document.getElementById('stopRecord').addEventListener('click', () => {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                mediaRecorder.stop();
            }
        });

        // Capture screenshot from video
        function captureScreenshot() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            canvas.toBlob((blob) => {
                capturedImage = blob;
            }, 'image/jpeg', 0.9);
        }

        // Upload video (file upload method)
        document.getElementById('uploadBtn').addEventListener('click', async () => {
            if (recordedChunks.length === 0) {
                alert('No video recorded.');
                return;
            }

            if (!capturedImage) {
                alert('No screenshot captured.');
                return;
            }

            const videoBlob = new Blob(recordedChunks, { type: 'video/webm' });

            const formData = new FormData();
            formData.append('video', videoBlob, 'ipv_video.webm');
            formData.append('image', capturedImage, 'ipv_screenshot.jpg');
            formData.append('latitude', locationData.latitude || '');
            formData.append('longitude', locationData.longitude || '');
            formData.append('city', locationData.city || '');
            formData.append('state', locationData.state || '');

            document.getElementById('uploadBtn').disabled = true;
            document.getElementById('status').textContent = 'Uploading... Please wait.';

            try {
                const response = await fetch('{{ route("ipv.record") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    // Redirect to success page or dashboard
                    window.location.href = '/dashboard';
                } else {
                    alert(data.message);
                    document.getElementById('uploadBtn').disabled = false;
                    document.getElementById('status').textContent = 'Upload failed. Please try again.';
                }
            } catch (error) {
                console.error('Upload error:', error);
                alert('An error occurred during upload. Please try again.');
                document.getElementById('uploadBtn').disabled = false;
                document.getElementById('status').textContent = 'Upload failed. Please try again.';
            }
        });

        // Initialize camera on page load
        initCamera();
    </script>
</body>
</html>
```

---

## API Endpoints

### 1. Check User and Send OTP

**POST** `/ipv/check-user`

**Request:**
```json
{
    "mobile": "9876543210",
    "g-recaptcha-response": "recaptcha_token_here"
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

Validation Error (422):
```json
{
    "success": false,
    "errors": {
        "mobile": ["The mobile field is required."],
        "g-recaptcha-response": ["Please complete the reCAPTCHA verification"]
    }
}
```

reCAPTCHA Failed (422):
```json
{
    "success": false,
    "message": "reCAPTCHA verification failed. Please try again."
}
```

User Not Found (422):
```json
{
    "success": false,
    "message": "Mobile number not registered"
}
```

KYC Incomplete (422):
```json
{
    "success": false,
    "message": "Please complete your KYC application first"
}
```

Max Attempts Reached (422):
```json
{
    "success": false,
    "message": "You have reached the maximum number of IPV attempts"
}
```

---

### 2. Verify OTP

**POST** `/ipv/verify-otp`

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
    "redirect": "https://yoursite.com/ipv/camera"
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

### 3. Record Video (File Upload)

**POST** `/ipv/record`

**Headers:**
```
Content-Type: multipart/form-data
```

**Request (multipart form-data):**
```
video: [file] (max 10MB, mp4/webm/mov)
image: [file] (max 2MB, jpg/jpeg/png)
latitude: 28.7041
longitude: 77.1025
city: "New Delhi"
state: "Delhi"
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "IPV video recorded successfully",
    "ipv_id": 123
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

Validation Error (422):
```json
{
    "success": false,
    "errors": {
        "video": ["The video field is required."],
        "image": ["The image must be a file of type: jpg, jpeg, png."]
    }
}
```

Max Attempts (422):
```json
{
    "success": false,
    "message": "Maximum IPV attempts reached"
}
```

Server Error (500):
```json
{
    "success": false,
    "message": "An error occurred while recording. Please try again."
}
```

---

### 4. Upload Base64 Video (Alternative Method)

**POST** `/ipv/upload-base64`

**Request:**
```json
{
    "video_data": "data:video/webm;base64,GkXfo59ChoEBQveBA...",
    "image_data": "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAA...",
    "latitude": 28.7041,
    "longitude": 77.1025,
    "city": "New Delhi",
    "state": "Delhi"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "IPV video uploaded successfully",
    "ipv_id": 124
}
```

**Error Responses:** Same as file upload method

---

### 5. Get IPV History

**POST** `/ipv/history`

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
    "count": 2,
    "max_attempts": 3,
    "remaining_attempts": 1,
    "records": [
        {
            "id": 123,
            "recorded_at": "2025-11-04 10:30:00",
            "location": "New Delhi, Delhi",
            "has_location": true,
            "video_url": "https://yoursite.com/storage/ipv_videos/video.webm",
            "image_url": "https://yoursite.com/storage/ipv_images/screenshot.jpg"
        },
        {
            "id": 122,
            "recorded_at": "2025-11-03 14:20:00",
            "location": "Location not available",
            "has_location": false,
            "video_url": "https://yoursite.com/storage/ipv_videos/video2.webm",
            "image_url": "https://yoursite.com/storage/ipv_images/screenshot2.jpg"
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

## UserCaptureVideo Model Helper Methods

Add these methods to `app/Models/UserCaptureVideo.php`:

```php
/**
 * Get location string.
 */
public function getLocationString(): string
{
    if ($this->city && $this->state) {
        return $this->city . ', ' . $this->state;
    }

    if ($this->city) {
        return $this->city;
    }

    if ($this->state) {
        return $this->state;
    }

    return 'Location not available';
}

/**
 * Check if location is available.
 */
public function hasLocation(): bool
{
    return !empty($this->lat) && !empty($this->lng);
}

/**
 * Get video URL.
 */
public function getVideoUrl(): string
{
    return $this->user_video ? asset('storage/' . $this->user_video) : '';
}

/**
 * Get image URL.
 */
public function getImageUrl(): string
{
    return $this->image ? asset('storage/' . $this->image) : '';
}

/**
 * Get formatted date.
 */
public function getFormattedDate(): string
{
    return $this->added_on ? $this->added_on->format('d M Y, h:i A') : '';
}
```

---

## Testing Guide

### 1. Test reCAPTCHA Verification

```bash
# With valid reCAPTCHA
curl -X POST http://localhost/ipv/check-user \
  -H "Content-Type: application/json" \
  -d '{
    "mobile": "9876543210",
    "g-recaptcha-response": "valid_recaptcha_token"
  }'
```

### 2. Test OTP Verification

```bash
# Verify OTP
curl -X POST http://localhost/ipv/verify-otp \
  -H "Content-Type: application/json" \
  -d '{
    "mobile": "9876543210",
    "otp": "123456"
  }'
```

### 3. Test Video Upload

```bash
# Upload video and image
curl -X POST http://localhost/ipv/record \
  -F "video=@/path/to/video.webm" \
  -F "image=@/path/to/screenshot.jpg" \
  -F "latitude=28.7041" \
  -F "longitude=77.1025" \
  -F "city=New Delhi" \
  -F "state=Delhi"
```

### 4. Test IPV History

```bash
# Get history
curl -X POST http://localhost/ipv/history \
  -H "Content-Type: application/json" \
  -d '{
    "mobile": "9876543210"
  }'
```

---

## Security Features

1. **reCAPTCHA Protection**: Prevents bot submissions
2. **OTP Verification**: Ensures user owns the mobile number
3. **Session Management**: Secure session storage for IPV verification
4. **Attempt Limiting**: Maximum 3 IPV submissions per user
5. **File Validation**: Strict file type and size validation
6. **IP Logging**: Records IP address for each submission
7. **Geolocation Tracking**: Captures user location for verification

---

## Browser Compatibility

- Chrome 52+
- Firefox 47+
- Safari 11+
- Edge 79+

**Required Browser Features:**
- MediaRecorder API
- getUserMedia API
- Geolocation API
- Canvas API
- Blob API
- FormData API

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

### Issue 1: Camera Not Accessible
**Solution**: Ensure HTTPS is enabled (required for getUserMedia API in production)

### Issue 2: File Size Too Large
**Solution**: Adjust `php.ini` settings:
```ini
upload_max_filesize = 20M
post_max_size = 25M
```

### Issue 3: reCAPTCHA Not Loading
**Solution**: Check if site key is correct and domain is whitelisted in Google reCAPTCHA console

### Issue 4: Geolocation Permission Denied
**Solution**: Browsers require HTTPS for geolocation API. Use HTTPS in production.

---

## Performance Optimization

1. **Video Compression**: Consider compressing videos on client-side before upload
2. **Lazy Loading**: Load camera only when user reaches camera page
3. **Caching**: Cache reCAPTCHA site keys
4. **CDN**: Serve uploaded videos via CDN for faster playback
5. **Thumbnail Generation**: Generate thumbnails from screenshots for admin panel

---

## Next Steps

1. Add admin panel to review IPV submissions
2. Implement video playback with controls
3. Add face detection/matching (optional)
4. Create email notifications for IPV completion
5. Add IPV status tracking in user dashboard
6. Implement video quality checks

---

## Complete Workflow

1. User completes KYC application (steps 1-6)
2. User visits `/ipv/permission`
3. User enters mobile number and completes reCAPTCHA
4. System verifies user exists and KYC is complete
5. System checks IPV attempt count (max 3)
6. OTP is sent via SMS
7. User enters OTP for verification
8. User is redirected to `/ipv/camera`
9. User allows camera/microphone/location access
10. User records 5-10 second video
11. System captures screenshot from video
12. System collects geolocation data
13. Video, image, and location are uploaded
14. System stores IPV record in database
15. User session is cleared
16. Admin can review submission in admin panel

---

## Files Created

1. `app/Services/RecaptchaService.php` - reCAPTCHA verification service
2. `app/Http/Controllers/IPV/IpvController.php` - IPV controller with all methods
3. `app/Models/UserCaptureVideo.php` - IPV model (already created in migrations)

**Required Views** (to be created):
- `resources/views/ipv/permission.blade.php`
- `resources/views/ipv/camera.blade.php`

---

## Configuration Checklist

- [ ] Add reCAPTCHA keys to `.env`
- [ ] Add IPV routes to `routes/web.php`
- [ ] Run `php artisan storage:link`
- [ ] Create IPV views
- [ ] Test camera access on HTTPS
- [ ] Configure reverse geocoding API (optional)
- [ ] Set up admin panel for IPV review
- [ ] Test complete workflow end-to-end
