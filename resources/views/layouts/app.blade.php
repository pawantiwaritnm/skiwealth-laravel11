<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SKI Capital - Trading and Demat Account')</title>

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
    </style>

    @stack('styles')
</head>
<body>
    <!-- Top Section -->
    <section id="top-area">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center m20">
                    <img src="{{ asset('images/skilogo.png') }}" class="img-responsive skilogo" alt="SKI Capital Logo">
                    <ul class="list-inline pull-right sign">
                    @guest
                        <li><a href="{{ route('auth.login') }}" class="signup2">Login</a></li>
                    @else
                        <li><a href="{{ route('auth.logout') }}" class="signup2"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a></li>
                        <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @endguest
                    </ul>
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
    <footer>
        <section id="">
            <div class="container-fluid footer_bg" style="position:relative; z-index:1000;">
                <div class="container nopad">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 nopad">
                        <div class="footer_links col-lg-12 col-md-12 col-sm-12 col-xs-12 nopad">
                            <div class="policies_part">
                                <a href="/">Home</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                <a href="/AboutUs">About us</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                <a href="/Download">Downloads</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                <a href="https://companies.naukri.com/skicapital-jobs/" target="_blank">Careers</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                <a href="/KnowledgeCenter">Knowledge Center</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                <a href="/FAQ">FAQs</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                <a href="/ContactUs">Contact Us</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                <a>Policies</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                <a href="/Disclaimer">Disclaimer</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                <a href="/PrivacyPolicy">Privacy Statement</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                <a href="http://www.scores.gov.in/" target="_blank">SEBI Scores</a>
                            </div>
                            <div class="clearfix" style="height:40px;"></div>
                            <div class="footer_line col-lg-12 col-md-12 col-sm-12 col-xs-12 nopad">
                                <div class="foot_left col-sm-5 col-xs-12 nopad">
                                    Copyright {{ date('Y') }}. SKI Capital. All Rights Reserved
                                </div>
                                <div class="foot_rgt col-sm-7 col-xs-12 nopad">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </footer>

    <!-- Scripts -->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>

    <script>
        // CSRF Token setup for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
