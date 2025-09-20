<!--==================================================-->
<!-- Start Echofy Header Area -->
<!--==================================================-->
<div class="header-area home-six" id="sticky-header">
    <div class="container">
        <div class="row add-bg align-items-center">
            <div class="col-lg-3">
                <div class="header-logo">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('assets/images/home6/logo.png') }}" alt="logo">
                    </a>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="header-menu">
                    <ul>
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li><a href="{{ url('/about') }}">About</a></li>
                        <li class="menu-item-has-children">
                            <a href="#">Service<i class="fas fa-chevron-down"></i></a>
                            <ul class="sub-menu">
                                <li><a href="{{ url('/service') }}">Service</a></li>
                                <li><a href="{{ url('/service-details') }}">Service Details</a></li>
                            </ul>
                        </li>
                        <li class="menu-item-has-children">
                            <a href="#">Pages<i class="fas fa-chevron-down"></i></a>
                            <ul class="sub-menu">
                                <li><a href="{{ url('/about') }}">About</a></li>
                                <li><a href="{{ url('/service') }}">Service</a></li>
                                <li><a href="{{ url('/service-details') }}">Service Details</a></li>
                                <li><a href="{{ url('/project') }}">Project</a></li>
                                <li><a href="{{ url('/project-details') }}">Project Details</a></li>
                                <li><a href="{{ url('/donation') }}">Donation</a></li>
                                <li><a href="{{ url('/donation-details') }}">Donation Details</a></li>
                                <li><a href="{{ url('/team') }}">Team</a></li>
                                <li><a href="{{ url('/faqs') }}">Faqs</a></li>
                                <li><a href="{{ url('/testimonial') }}">Testimonial</a></li>
                            </ul>
                        </li>
                        <li class="menu-item-has-children">
                            <a href="#">Blog<i class="fas fa-chevron-down"></i></a>
                            <ul class="sub-menu">
                                <li><a href="{{ url('/blog-grid') }}">Blog Grid</a></li>
                                <li><a href="{{ url('/blog-list') }}">Blog List</a></li>
                                <li><a href="{{ url('/blog-2column') }}">Blog 2column</a></li>
                                <li><a href="{{ url('/blog-details') }}">Blog Details</a></li>
                            </ul>
                        </li>
                        <li><a href="{{ url('/contact') }}">Contact</a></li>
                    </ul>
                    <div class="header-secrch-icon search-box-outer">
                        <a href="#"><i class="bi bi-search"></i></a>
                    </div>
                    <div class="header-button">
                        <a href="#">Get A Solution<i class="bi bi-arrow-right-short"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--==================================================-->
<!-- End Echofy Header Area -->
<!--==================================================-->

<!--==================================================-->
<!-- Start Mobile Menu Area -->
<!--==================================================-->
<div class="mobile-menu-area sticky d-sm-block d-md-block d-lg-none">
    <div class="mobile-menu">
        <nav class="header-menu">
            <ul class="nav_scroll">
                <li class="menu-item-has-children">
                    <a href="#">Home</a>
                    <ul class="sub-menu">
                        <li><a href="{{ url('/') }}">Home 1</a></li>
                        <li><a href="{{ url('/home2') }}">Home 2</a></li>
                        <li><a href="{{ url('/home3') }}">Home 3</a></li>
                        <li><a href="{{ url('/home4') }}">Home 4</a></li>
                        <li><a href="{{ url('/home5') }}">Home 5</a></li>
                        <li><a href="{{ url('/home6') }}">Home 6</a></li>
                        <li><a href="{{ url('/home7') }}">Home 7</a></li>
                    </ul>
                </li>
                <li><a href="{{ url('/about') }}">About</a></li>
                <li class="menu-item-has-children">
                    <a href="#">Service</a>
                    <ul class="sub-menu">
                        <li><a href="{{ url('/service') }}">Service</a></li>
                        <li><a href="{{ url('/service-details') }}">Service Details</a></li>
                    </ul>
                </li>
                <li class="menu-item-has-children">
                    <a href="#">Pages</a>
                    <ul class="sub-menu">
                        <li><a href="{{ url('/about') }}">About</a></li>
                        <li><a href="{{ url('/service') }}">Service</a></li>
                        <li><a href="{{ url('/service-details') }}">Service Details</a></li>
                        <li><a href="{{ url('/project') }}">Project</a></li>
                        <li><a href="{{ url('/project-details') }}">Project Details</a></li>
                        <li><a href="{{ url('/donation') }}">Donation</a></li>
                        <li><a href="{{ url('/donation-details') }}">Donation Details</a></li>
                        <li><a href="{{ url('/team') }}">Team</a></li>
                        <li><a href="{{ url('/faqs') }}">Faqs</a></li>
                        <li><a href="{{ url('/testimonial') }}">Testimonial</a></li>
                    </ul>
                </li>
                <li class="menu-item-has-children">
                    <a href="#">Blog</a>
                    <ul class="sub-menu">
                        <li><a href="{{ url('/blog-grid') }}">Blog Grid</a></li>
                        <li><a href="{{ url('/blog-list') }}">Blog List</a></li>
                        <li><a href="{{ url('/blog-2column') }}">Blog 2column</a></li>
                        <li><a href="{{ url('/blog-details') }}">Blog Details</a></li>
                    </ul>
                </li>
                <li><a href="{{ url('/contact') }}">Contact</a></li>
            </ul>
        </nav>
    </div>
</div>
<!--==================================================-->
<!-- End Mobile Menu Area -->
<!--==================================================-->