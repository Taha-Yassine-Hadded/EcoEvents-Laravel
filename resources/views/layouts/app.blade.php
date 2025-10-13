<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title', 'Echofy')</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Support user for Messenger-like widget -->
    <meta name="support-user-id" content="{{ env('SUPPORT_USER_ID', 1) }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="56x56" href="{{ asset('assets/images/fav-icon/icon.png') }}">

    <!-- bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- carousel CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/owl.carousel.min.css') }}">
    <!-- animate CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/animate.css') }}">
    <!-- animated-text CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/animated-text.css') }}">
    <!-- font-awesome CSS via CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- font-flaticon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/flaticon.css') }}">
    <!-- theme-default CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/theme-default.css') }}">
    <!-- meanmenu CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/meanmenu.min.css') }}">
    <!-- transitions CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/owl.transitions.css') }}">
    <!-- venobox CSS -->
    <link rel="stylesheet" href="{{ asset('assets/venobox/venobox.css') }}">
    <!-- bootstrap icons -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.css') }}">
    <!-- Main Style CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <!-- responsive CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">

    <!-- modernizr js -->
    <script src="{{ asset('assets/js/vendor/modernizr-3.5.0.min.js') }}"></script>

    <!-- extra plugins -->
    <link rel="stylesheet" href="https://unpkg.com/splitting/dist/splitting.css" />
    <link rel="stylesheet" href="https://unpkg.com/splitting/dist/splitting-cells.css" />

    @stack('styles')

    <!-- Custom EcoEvents Styles -->
    <style>
        /* Forcer l'arrière-plan blanc sur toute l'application */
        html, body {
            background-color: #ffffff !important;
        }
        
        /* Correction pour éviter que le contenu soit caché sous la navbar */
        .navbar-fixed-top, .fixed-top {
            position: relative !important;
        }
        
        /* S'assurer que les boutons sont toujours visibles */
        .btn {
            z-index: 1050 !important;
            position: relative !important;
        }
        
        /* Supprimer le padding-top du body si la navbar est fixe */
        body {
            padding-top: 0 !important;
        }
        
        .header-button .btn-register {
            background: transparent;
            color: #28a745;
            border: 2px solid #28a745;
            padding: 10px 20px;
            border-radius: 25px;
            margin-right: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .header-button .btn-register:hover {
            background: #28a745;
            color: white;
        }

        .header-button .btn-login {
            background: #28a745;
            color: white;
            border: 2px solid #28a745;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .header-button .btn-login:hover {
            background: #218838;
            border-color: #218838;
        }

        /* Success/Error Messages */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        /* Toast position */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1060;
        }
    </style>
</head>
<body>
    <!-- Loader -->
    <div class="loader-wrapper">
        <span class="loader"></span>
        <div class="loder-section left-section"></div>
        <div class="loder-section right-section"></div>
    </div>

    <!-- Header -->
    @include('partials.frontOffice.header')

    <!-- Page Content -->
    <main>
        <!-- Toast Notifications -->
        @if(session('success') || session('error'))
            <div class="toast-container">
                <div id="statusToast" class="toast align-items-center text-white {{ session('success') ? 'bg-success' : 'bg-danger' }} border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
                    <div class="d-flex">
                        <div class="toast-body">
                            {{ session('success') ?? session('error') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="container">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    @include('partials.frontOffice.footer')

    <!--==================================================-->
    <!-- Search Popup -->
    <!--==================================================-->
    <div class="search-popup">
        <button class="close-search style-two">
            <span class="flaticon-multiply"><i class="far fa-times-circle"></i></span>
        </button>
        <button class="close-search"><i class="bi bi-arrow-up"></i></button>
        <form method="post" action="#">
            <div class="form-group">
                <input type="search" name="search-field" placeholder="Search Here" required="">
                <button type="submit"><i class="fa fa-search"></i></button>
            </div>
        </form>
    </div>
    <!--==================================================-->
    <!-- End Search Popup -->
    <!--==================================================-->

    <!--==================================================-->
    <!-- Start Scroll Up -->
    <!--==================================================-->
    <div class="prgoress_indicator active-progress">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
                style="transition: stroke-dashoffset 10ms linear 0s;
                        stroke-dasharray: 307.919, 307.919;
                        stroke-dashoffset: 0;"></path>
        </svg>
    </div>
    <!--==================================================-->
    <!-- End Scroll Up -->
    <!--==================================================-->

    <!-- Scripts -->
    <script src="{{ asset('assets/js/vendor/jquery-3.6.2.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('assets/js/waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/js/wow.js') }}"></script>
    <script src="{{ asset('assets/js/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/venobox/venobox.js') }}"></script>
    <script src="{{ asset('assets/js/animated-text.js') }}"></script>
    <script src="{{ asset('assets/venobox/venobox.min.js') }}"></script>
    <script src="{{ asset('assets/js/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.meanmenu.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.scrollUp.js') }}"></script>
    <script src="{{ asset('assets/js/theme.js') }}"></script>
    <script src="{{ asset('assets/js/coustom.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.barfiller.js') }}"></script>
    <script src="{{ asset('assets/js/vanilla-tilt.min.js') }}"></script>
    <script src="{{ asset('assets/js/silik-slider.js') }}"></script>

    <script src="https://unpkg.com/splitting/dist/splitting.min.js"></script>
    <script>Splitting();</script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toastEl = document.getElementById('statusToast');
            if (toastEl && window.bootstrap && bootstrap.Toast) {
                var toast = new bootstrap.Toast(toastEl);
                toast.show();
            }
        });
    </script>

    @stack('scripts')
    
    <!-- Messenger-like floating chat widget -->
    <script src="/chat-widget.js" defer></script>
</body>
</html>
