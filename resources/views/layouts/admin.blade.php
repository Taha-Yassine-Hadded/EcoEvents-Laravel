<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title', 'Echofy - Dashboard')</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="56x56" href="{{ Vite::asset('resources/assets/images/fav-icon/icon.png') }}">

    <!-- bootstrap CSS -->
    @vite('resources/assets/css/bootstrap.min.css')
    <!-- carousel CSS -->
    @vite('resources/assets/css/owl.carousel.min.css')
    <!-- animate CSS -->
    @vite('resources/assets/css/animate.css')
    <!-- animated-text CSS -->
    @vite('resources/assets/css/animated-text.css')
    <!-- font-awesome CSS -->
    @vite('resources/assets/css/all.min.css')
    <!-- font-flaticon CSS -->
    @vite('resources/assets/css/flaticon.css')
    <!-- theme-default CSS -->
    @vite('resources/assets/css/theme-default.css')
    <!-- meanmenu CSS -->
    @vite('resources/assets/css/meanmenu.min.css')
    <!-- transitions CSS -->
    @vite('resources/assets/css/owl.transitions.css')
    <!-- venobox CSS -->
    @vite('resources/assets/venobox/venobox.css')
    <!-- bootstrap icons -->
    @vite('resources/assets/css/bootstrap-icons.css')
    <!-- Main Style CSS -->
    @vite('resources/assets/css/style.css')
    <!-- responsive CSS -->
    @vite('resources/assets/css/responsive.css')

    <!-- modernizr js -->
    @vite('resources/assets/js/vendor/modernizr-3.5.0.min.js')

    <!-- extra plugins -->
    <link rel="stylesheet" href="https://unpkg.com/splitting/dist/splitting.css" />
    <link rel="stylesheet" href="https://unpkg.com/splitting/dist/splitting-cells.css" />

    @stack('styles')
</head>
<body>
    <!-- Loader -->
    <div class="loader-wrapper">
        <span class="loader"></span>
        <div class="loder-section left-section"></div>
        <div class="loder-section right-section"></div>
    </div>

    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Page Content -->
    <main>
        @yield('content')
    </main>


    <!-- Scripts -->
    @vite('resources/assets/js/vendor/jquery-3.6.2.min.js')
    @vite('resources/assets/js/popper.min.js')
    @vite('resources/assets/js/bootstrap.min.js')
    @vite('resources/assets/js/owl.carousel.min.js')
    @vite('resources/assets/js/jquery.counterup.min.js')
    @vite('resources/assets/js/waypoints.min.js')
    @vite('resources/assets/js/wow.js')
    @vite('resources/assets/js/imagesloaded.pkgd.min.js')
    @vite('resources/assets/venobox/venobox.js')
    @vite('resources/assets/js/animated-text.js')
    @vite('resources/assets/venobox/venobox.min.js')
    @vite('resources/assets/js/isotope.pkgd.min.js')
    @vite('resources/assets/js/jquery.meanmenu.js')
    @vite('resources/assets/js/jquery.scrollUp.js')
    @vite('resources/assets/js/theme.js')
    @vite('resources/assets/js/coustom.js')
    @vite('resources/assets/js/jquery.barfiller.js')
    @vite('resources/assets/js/vanilla-tilt.min.js')
    @vite('resources/assets/js/silik-slider.js')

    <script src="https://unpkg.com/splitting/dist/splitting.min.js"></script>
    <script>Splitting();</script>

    @stack('scripts')
</body>
</html>