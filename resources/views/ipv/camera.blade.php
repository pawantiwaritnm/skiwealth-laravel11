@extends('layouts.app')

@section('title', 'IPV Camera Recording - SKI Capital')

@section('content')
<style>
    .ipv-otp {
        font-size: 36px;
        color: #5b6b3d;
        font-weight: bold;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        display: inline-block;
    }
    .code_img {
        position: relative;
        text-align: center;
        margin: 20px 0;
    }
    .code_img img {
        width: 100%;
        border-radius: 8px;
    }
    .code_img h5 {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 48px;
        font-weight: bold;
        color: #000;
    }
    video {
        border: 2px solid #5b6b3d;
        border-radius: 8px;
        background: #000;
    }
    .vid2 {
        display: none;
    }
    .forward {
        padding: 10px 30px;
        margin: 10px 5px;
    }
    .loader {
        border: 5px solid #f3f3f3;
        border-top: 5px solid #5b6b3d;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        display: inline-block;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    #canvas {
        display: none;
    }
    .output {
        margin-top: 20px;
    }
    .output img {
        max-width: 100%;
        border: 2px solid #5b6b3d;
        border-radius: 8px;
    }
</style>

<div id="top" class="container">
    <div class="row">
        <div class="col-lg-12">
            <h3>Webcam IPV</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <p style="text-align:center;">Let's do a quick in-person-verification over webcam.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h6 style="text-align:center;">Write the below code on a piece of paper and hold it in front of the camera</h6>
            @php
                $code = session('ipv_code', rand(1111, 9999));
                session(['ipv_code' => $code]);
            @endphp
            <h4 style="text-align:center;" class="ipv-otp text-center"><strong>{{ $code }}</strong></h4>
            <p style="text-align:center;">Ensure that your face and the code are clearly visible.</p>
        </div>
        <div class="col-md-6 mx-auto">
            <div class="code_img">
                <img src="{{ asset('images/code_img.jpg') }}" alt="IPV Code">
                <h5>{{ $code }}</h5>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="row">
                <div class="col-md-6">
                    <video width="100%" id="vid1" autoplay></video>
                </div>
                <div class="col-md-6 vid2">
                    <video width="100%" id="vid2"></video>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <button style="float:left" class="forward btn btn-primary" id="btnStart">Capture</button>
            <div class="loader" id="ipv_progress" style="margin-left: 50px;display:none"></div>

            <div class="row">
                <div class="col-md-12 text-center">
                    <button style="display:none" class="forward btn btn-success" id="save_ipv">Save IPV</button>
                    <button style="display:none" class="forward btn btn-warning" id="redeo_ipv">Redo IPV</button>
                </div>
            </div>
            <canvas style="display:none" id="canvas"></canvas>
            <div style="display:none" class="output">
                <img id="photo" name="img" alt="The screen capture will appear in this box.">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const SITE_URL = '{{ url('/') }}/';
    let mediaRecorder;
    let recordedBlobs;
    let stream;

    // Get user location
    function getLocation() {
        return new Promise((resolve, reject) => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    position => {
                        resolve({
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude
                        });
                    },
                    error => {
                        console.log('Location error:', error);
                        resolve({ latitude: 0, longitude: 0 });
                    }
                );
            } else {
                resolve({ latitude: 0, longitude: 0 });
            }
        });
    }

    // Initialize camera
    async function init() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: true
            });

            const videoElement = document.getElementById('vid1');
            videoElement.srcObject = stream;
            videoElement.play();
        } catch (error) {
            console.error('Error accessing camera:', error);
            alert('Unable to access camera. Please ensure camera permissions are granted.');
        }
    }

    // Handle data available
    function handleDataAvailable(event) {
        if (event.data && event.data.size > 0) {
            recordedBlobs.push(event.data);
        }
    }

    // Start recording
    function startRecording() {
        recordedBlobs = [];
        let options = { mimeType: 'video/webm;codecs=vp9,opus' };

        if (!MediaRecorder.isTypeSupported(options.mimeType)) {
            options = { mimeType: 'video/webm;codecs=vp8,opus' };
            if (!MediaRecorder.isTypeSupported(options.mimeType)) {
                options = { mimeType: 'video/webm' };
                if (!MediaRecorder.isTypeSupported(options.mimeType)) {
                    options = { mimeType: '' };
                }
            }
        }

        try {
            mediaRecorder = new MediaRecorder(stream, options);
        } catch (e) {
            console.error('Exception while creating MediaRecorder:', e);
            alert('MediaRecorder creation failed. Browser not supported.');
            return;
        }

        mediaRecorder.ondataavailable = handleDataAvailable;
        mediaRecorder.start(100); // Collect data every 100ms
        console.log('MediaRecorder started', mediaRecorder);
    }

    // Stop recording
    function stopRecording() {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
            console.log('Recorded Blobs:', recordedBlobs);
        }
    }

    // Play recorded video
    function playRecording() {
        const superBuffer = new Blob(recordedBlobs, { type: 'video/webm' });
        const videoElement = document.getElementById('vid2');
        videoElement.src = window.URL.createObjectURL(superBuffer);
        videoElement.play();
        $('.vid2').show();
    }

    // Save IPV video
    async function saveIPV() {
        $('#save_ipv').hide();
        $('#redeo_ipv').hide();
        $('#ipv_progress').show();

        const blob = new Blob(recordedBlobs, { type: 'video/webm' });
        const formData = new FormData();
        formData.append('video', blob, 'ipv_video.webm');
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('ipv_code', '{{ $code }}');

        // Get location
        const location = await getLocation();
        formData.append('latitude', location.latitude);
        formData.append('longitude', location.longitude);

        $.ajax({
            url: '{{ route("ipv.record") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#ipv_progress').hide();
                if (response.success) {
                    alert('IPV video saved successfully!');
                    window.location.href = '{{ route("dashboard") }}';
                } else {
                    alert('Error: ' + (response.message || 'Failed to save IPV video'));
                    $('#save_ipv').show();
                    $('#redeo_ipv').show();
                }
            },
            error: function(xhr) {
                $('#ipv_progress').hide();
                alert('Error uploading video. Please try again.');
                $('#save_ipv').show();
                $('#redeo_ipv').show();
            }
        });
    }

    // Redo IPV
    function redoIPV() {
        $('.vid2').hide();
        $('#save_ipv').hide();
        $('#redeo_ipv').hide();
        $('#btnStart').show();
        recordedBlobs = [];
    }

    // Document ready
    $(document).ready(function() {
        // Initialize camera on page load
        init();

        // Capture button
        $('#btnStart').on('click', function() {
            $('#btnStart').hide();
            $('#ipv_progress').show();

            startRecording();

            // Record for 10 seconds
            setTimeout(function() {
                stopRecording();
                $('#ipv_progress').hide();
                playRecording();
                $('#save_ipv').show();
                $('#redeo_ipv').show();
            }, 10000); // 10 seconds recording
        });

        // Save IPV button
        $('#save_ipv').on('click', function() {
            saveIPV();
        });

        // Redo IPV button
        $('#redeo_ipv').on('click', function() {
            redoIPV();
        });

        // Stop camera when leaving page
        $(window).on('beforeunload', function() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        });
    });
</script>
@endpush
@endsection
