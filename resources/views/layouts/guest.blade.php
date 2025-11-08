<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SKI Capital - Open your Trading and Demat Account')</title>

    <link rel="icon" href="{{ asset('images/favicon.ico') }}">

    <!-- CSS -->
    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/jquery.switch.css') }}">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800,300' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">

    <style>
        .error {
            color: red;
        }
        .field_error {
            color: red;
            font-size: 12px;
        }
        .loader {
            border: 5px solid #f3f3f3;
            border-radius: 50%;
            border-top: 5px solid #387ed1;
            width: 16px;
            height: 16px;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .hide_box {
            display: none;
        }
        .signup__body-wrapper-ad-panel-details {
            padding: 0 0 20px 0;
            margin-top: 6%;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Top Section with Logo -->
    <section id="top-area">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center m20">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('images/skilogo.png') }}" class="img-responsive skilogo" alt="SKI Capital Logo">
                    </a>
                </div>
            </div>
        </div>
        <hr class="top-border">
    </section>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <p>&copy; {{ date('Y') }} SKI Capital Services Limited. All rights reserved.</p>
                    <p>
                        <a href="{{ route('auth.login') }}">Login</a> |
                        <a href="{{ route('auth.register') }}">Register</a> |
                        <a href="https://www.skicapital.net" target="_blank">Visit Website</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>

    <script>
        // CSRF Token setup for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Common functions
        function showLoader(buttonId) {
            $('#' + buttonId).prop('disabled', true).html('<span class="loader"></span> Processing...');
        }

        function hideLoader(buttonId, originalText) {
            $('#' + buttonId).prop('disabled', false).html(originalText);
        }

        function showError(fieldId, message) {
            $('#' + fieldId + '_error').html(message);
        }

        function clearErrors() {
            $('.field_error').html('');
            $('.error').html('');
        }
    </script>

    @stack('scripts')
</body>
</html>
