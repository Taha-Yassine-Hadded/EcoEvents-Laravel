@extends('layouts.app')

@section('title', 'Echofy - Service Details')

@section('content')
    <!-- Breadcrumb Area -->
    <div class="breadcumb-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <div class="breadcumb-content">
                        <div class="breadcumb-title">
                            <h4>Service Details</h4>
                        </div>
                        <ul>
                            <li><a href="{{ url('/') }}"><img src="{{ Vite::asset('resources/assets/images/inner-images/breadcumb-text-shape.png') }}" alt="">Echofy</a></li>
                            <li>Service Details</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Details Area -->
    <div class="services-details-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="services-details-thumb">
                                <img src="{{ Vite::asset('resources/assets/images/inner-images/services-details.png') }}" alt="">
                            </div>
                            <div class="services-details-content">
                                <h4 class="services-details-title">Appropriately engage leading-edge</h4>
                                <p class="services-details-desc">Alternative innovation to ethical network environmental whiteboard pursue compelling results for premier methods empowerment. Dramatically architect go forward opportunities before user-centric partnerships. Credibly implement exceptional</p>
                                <p class="services-details-desc">Continually fashion orthogonal leadership skills whereas wireless metrics. Uniquely syndicate exceptional opportunities with interdependent users. Globally enhance fully tested meta-services rather than pandemic solutions. Proactively integrate client-integrate go forward architectures and turnkey meta-services. Interactively harness integrated ROI whereas frictionless products.</p>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <div class="service-details-icon-box">
                                        <div class="service-details-icon-thumb">
                                            <img src="{{ Vite::asset('resources/assets/images/inner-images/services-details-icon-1.png') }}" alt="">
                                        </div>
                                        <div class="service-details-box-content">
                                            <h4>Cleaning Ocean</h4>
                                            <p>Ethical network environmental architect go forward opportu credibly implement</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="service-details-icon-box">
                                        <div class="service-details-icon-thumb">
                                            <img src="{{ Vite::asset('resources/assets/images/inner-images/services-details-icon-2.png') }}" alt="">
                                        </div>
                                        <div class="service-details-box-content">
                                            <h4>Plant Seedlings</h4>
                                            <p>Ethical network environmental architect go forward opportu credibly implement</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="services-details-text">
                                <div class="service-details-text-icon">
                                    <img src="{{ Vite::asset('resources/assets/images/inner-images/services-details-text-icon.png') }}" alt="">
                                </div>
                                <div class="service-details-text-content">
                                    <p>Competently architect intermandated deliverables client niches continually underwhelm</p>
                                </div>
                            </div>
                            <h4 class="services-details-title">What are the Benefits?</h4>
                            <p class="services-details-desc">Alternative innovation to ethical network environmental whiteboard pursue compelling results for premier methods empowerment. Dramatically architect go forward opportunities</p>
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <div class="single-benefits-box">
                                        <div class="benefits-thumb">
                                            <img src="{{ Vite::asset('resources/assets/images/inner-images/services-details-benefits-thumb-1.png') }}" alt="">
                                        </div>
                                        <div class="benefits-content">
                                            <h4>Renewable Energy</h4>
                                            <ul>
                                                <li><i class="bi bi-check2"></i>New Modern Equipment</li>
                                                <li><i class="bi bi-check2"></i>Expert Volunteers</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="single-benefits-box">
                                        <div class="benefits-thumb">
                                            <img src="{{ Vite::asset('resources/assets/images/inner-images/services-details-benefits-thumb-2.png') }}" alt="">
                                        </div>
                                        <div class="benefits-content">
                                            <h4>Green Cleaning Service</h4>
                                            <ul>
                                                <li><i class="bi bi-check2"></i>New Modern Equipment</li>
                                                <li><i class="bi bi-check2"></i>Expert Volunteers</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="widget-sidber">
                                <div class="widget-sidber-content">
                                    <h4>Categories</h4>
                                </div>
                                <div class="widget-category">
                                    <ul>
                                        <li><a href="{{ url('/service') }}"><img src="{{ Vite::asset('resources/assets/images/inner-images/category-icon.png') }}" alt="">Ocean Cleaning<i class="bi bi-arrow-right"></i></a></li>
                                        <li><a href="{{ url('/service') }}"><img src="{{ Vite::asset('resources/assets/images/inner-images/category-icon.png') }}" alt="">Dust Recycling<i class="bi bi-arrow-right"></i></a></li>
                                        <li><a href="{{ url('/service') }}"><img src="{{ Vite::asset('resources/assets/images/inner-images/category-icon.png') }}" alt="">Plant Seedlings<i class="bi bi-arrow-right"></i></a></li>
                                        <li><a href="{{ url('/service') }}"><img src="{{ Vite::asset('resources/assets/images/inner-images/category-icon.png') }}" alt="">Renewable Energy<i class="bi bi-arrow-right"></i></a></li>
                                        <li><a href="{{ url('/service') }}"><img src="{{ Vite::asset('resources/assets/images/inner-images/category-icon.png') }}" alt="">Environmental<i class="bi bi-arrow-right"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="widget-sidber">
                                <div class="widget-sidber-content">
                                    <h4>Downloads</h4>
                                </div>
                                <div class="widget-sidber-download-button">
                                    <a href="{{ url('/download/service-report') }}"><i class="bi bi-file-earmark-pdf"></i>Service Report<span><i class="bi bi-download"></i></span></a>
                                    <a class="active" href="{{ url('/download/lists') }}"><i class="bi bi-file-earmark-pdf"></i>Download Lists<span><i class="bi bi-download"></i></span></a>
                                </div>
                            </div>
                            <div class="widget-sidber-contact-box">
                                <div class="widget-sidber-contact">
                                    <img src="{{ Vite::asset('resources/assets/images/inner-images/sidber-cont-icon.png') }}" alt="">
                                </div>
                                <p class="widget-sidber-contact-text">Call Us Anytime</p>
                                <h3 class="widget-sidber-contact-number">+123 (4567) 890</h3>
                                <span class="widget-sidber-contact-gmail"><i class="bi bi-envelope-fill"></i> mailto:example@gmail.com</span>
                                <div class="widget-sidber-contact-btn">
                                    <a href="{{ url('/contact') }}">Contact Us <i class="bi bi-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection