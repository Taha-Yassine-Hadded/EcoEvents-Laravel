<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title', 'Echofy - Dashboard')</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

    <!-- Admin Layout Styles -->
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fc;
        }

        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 260px;
            margin-top: 70px;
            padding: 30px;
            flex: 1;
            min-height: calc(100vh - 70px);
            transition: margin-left 0.3s ease;
        }

        .admin-sidebar.collapsed + .admin-layout .main-content {
            margin-left: 70px;
        }

        .content-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e3e6f0;
        }

        .page-title {
            font-size: 28px;
            font-weight: 600;
            color: #5a5c69;
            margin: 0;
        }

        .breadcrumb-nav {
            margin-top: 8px;
        }

        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
            font-size: 14px;
        }

        .breadcrumb-item {
            color: #858796;
        }

        .breadcrumb-item.active {
            color: #5a5c69;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: '/';
            color: #858796;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 24px;
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #e3e6f0;
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.25rem;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #5a5c69;
            margin: 0;
        }

        .btn {
            border-radius: 6px;
            font-weight: 500;
            padding: 8px 20px;
        }

        .btn-primary {
            background: #4e73df;
            border-color: #4e73df;
        }

        .btn-primary:hover {
            background: #2e59d9;
            border-color: #2e59d9;
        }

        .btn-success {
            background: #1cc88a;
            border-color: #1cc88a;
        }

        .btn-success:hover {
            background: #17a673;
            border-color: #17a673;
        }

        .btn-warning {
            background: #f6c23e;
            border-color: #f6c23e;
            color: #fff;
        }

        .btn-warning:hover {
            background: #f4b619;
            border-color: #f4b619;
            color: #fff;
        }

        .btn-danger {
            background: #e74a3b;
            border-color: #e74a3b;
        }

        .btn-danger:hover {
            background: #d52a1a;
            border-color: #d52a1a;
        }

        .table {
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead th {
            background: #f8f9fc;
            border-color: #e3e6f0;
            font-weight: 600;
            color: #5a5c69;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        .form-control {
            border-radius: 6px;
            border: 1px solid #d1d3e2;
            padding: 0.75rem 1rem;
        }

        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px 15px;
            }

            .admin-sidebar.collapsed + .admin-layout .main-content {
                margin-left: 0;
            }
        }

        /* Loader styles */
        .loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loader {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4e73df;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Hide loader after page load */
        .page-loaded .loader-wrapper {
            display: none;
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
    @include('partials.backOffice.header')

    <!-- Sidebar -->
    @include('partials.backOffice.sidebar')

    <!-- Admin Layout Container -->
    <div class="admin-layout">
        <!-- Page Content -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>

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

    <!-- Admin Layout Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Hide loader when page is loaded
            setTimeout(function() {
                document.body.classList.add('page-loaded');
            }, 1000);

            // Adjust main content margin when sidebar is toggled
            const sidebar = document.querySelector('.admin-sidebar');
            const mainContent = document.querySelector('.main-content');

            if (sidebar && mainContent) {
                // Create a MutationObserver to watch for class changes on sidebar
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                            if (sidebar.classList.contains('collapsed')) {
                                mainContent.style.marginLeft = '70px';
                            } else {
                                mainContent.style.marginLeft = '260px';
                            }
                        }
                    });
                });

                observer.observe(sidebar, {
                    attributes: true,
                    attributeFilter: ['class']
                });
            }

            // Handle responsive behavior
            function handleResize() {
                if (window.innerWidth <= 768) {
                    if (mainContent) {
                        mainContent.style.marginLeft = '0';
                    }
                } else {
                    if (mainContent && sidebar) {
                        if (sidebar.classList.contains('collapsed')) {
                            mainContent.style.marginLeft = '70px';
                        } else {
                            mainContent.style.marginLeft = '260px';
                        }
                    }
                }
            }

            window.addEventListener('resize', handleResize);
            handleResize(); // Call once on load
        });
    </script>

    @stack('scripts')
</body>
</html>
