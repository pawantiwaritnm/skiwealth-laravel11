<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} | Admin Login</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Hind+Vadodara:400,500,600" rel="stylesheet">

    <style>
        .jumbo-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .m-h-100 {
            min-height: 100vh;
        }
        .bg-cover {
            background-size: cover;
            background-position: center;
        }
        .field_error {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }
        .loginmsg {
            color: red;
            font-weight: bold;
            margin-top: 10px;
        }
        .p-b-20 {
            padding-bottom: 20px;
        }
        .p-t-10 {
            padding-top: 10px;
        }
        .fw-400 {
            font-weight: 400;
        }
        .floating-label label {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .form-control {
            padding: 12px;
            font-size: 14px;
        }
        .btn-lg {
            padding: 12px 20px;
            font-size: 16px;
        }
    </style>
</head>
<body class="jumbo-page">
    <main class="admin-main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4 bg-white">
                    <div class="row align-items-center m-h-100">
                        <div class="mx-auto col-md-8">
                            <div class="p-b-20 text-center">
                                <p>
                                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" style="max-width: 200px;">
                                </p>
                            </div>
                            <h3 class="text-center p-b-20 fw-400">Admin Login</h3>
                            <form class="needs-validation" id="adminLoginForm">
                                @csrf
                                <div class="form-row">
                                    <div class="form-group floating-label col-md-12">
                                        <label>Username</label>
                                        <input type="text" id="username" name="username" class="form-control" placeholder="Please enter username" required>
                                        <div class="field_error" id="username_error"></div>
                                    </div>
                                    <div class="form-group floating-label col-md-12">
                                        <label>Password</label>
                                        <input type="password" id="password" name="password" class="form-control" placeholder="Please enter password" required>
                                        <div class="field_error" id="password_error"></div>
                                    </div>
                                </div>
                                <button type="button" onclick="checkAdmin()" class="btn btn-primary btn-block btn-lg">Login</button>
                            </form>
                            <br/>
                            <div id="errorMessage">
                                <div class="loginmsg"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 d-none d-md-block bg-cover" style="background-image: url('{{ asset('images/login.svg') }}');">
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const SITE_URL = '{{ url('/') }}/';

        // Check admin credentials
        function checkAdmin() {
            $('.field_error').html('');
            $('.loginmsg').html('');

            var username = $('#username').val();
            var password = $('#password').val();

            if (username == '') {
                $('#username_error').html('Please enter username');
                return false;
            }

            if (password == '') {
                $('#password_error').html('Please enter password');
                return false;
            }

            $.ajax({
                url: '{{ route("admin.login.post") }}',
                type: 'POST',
                data: {
                    email: username,
                    password: password,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        window.location.href = '{{ route("admin.dashboard") }}';
                    } else {
                        $('.loginmsg').html(response.message || 'Invalid username or password');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        if (errors.username) {
                            $('#username_error').html(errors.username[0]);
                        }
                        if (errors.password) {
                            $('#password_error').html(errors.password[0]);
                        }
                    } else {
                        $('.loginmsg').html('Error: Please try again');
                    }
                }
            });
        }

        // Enter key submit
        $(document).ready(function() {
            $(document).keypress(function(e) {
                if (e.which == 13) {
                    checkAdmin();
                }
            });
        });
    </script>
</body>
</html>
