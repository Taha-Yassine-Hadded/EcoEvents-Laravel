@extends('layouts.app')

@section('title', 'Echofy - Our Services')

@section('content')
    <!-- Breadcrumb Area -->
    <div class="breadcumb-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <div class="breadcumb-content">
                        <div class="breadcumb-title">
                            <h4>Our Services</h4>
                        </div>
                        <ul>
                            <li><a href="{{ url('/') }}"><img src="{{ Vite::asset('resources/assets/images/inner-images/breadcumb-text-shape.png') }}" alt="">Echofy</a></li>
                            <li>Service</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Area -->
    <div class="service-area home-two inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="section-title center">
                        <h4><img src="{{ Vite::asset('resources/assets/images/home1/section-shape.png') }}" alt="">Our Services</h4>
                        <h1>Echofy Provide Environment</h1>
                        <h1>Best Leading Services</h1>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="single-service-box">
                        <div class="service-thumb">
                            <img src="{{ Vite::asset('resources/assets/images/home2/services-1.png') }}" alt="">
                        </div>
                        <div class="service-content">
                            <div class="services-icon">
                                <img src="{{ Vite::asset('resources/assets/images/home2/service-icon-1.png') }}" alt="">
                            </div>
                            <a href="{{ url('/service-details') }}">Cleaning Ocean</a>
                            <p>Alternative innovation to ethical network environmental whiteboard</p>
                            <div class="service-button">
                                <a href="{{ url('/service') }}">Discover More<i class="bi bi-arrow-right-short"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-service-box">
                        <div class="service-thumb">
                            <img src="{{ Vite::asset('resources/assets/images/home2/services-2.png') }}" alt="">
                        </div>
                        <div class="service-content">
                            <div class="services-icon">
                                <img src="{{ Vite::asset('resources/assets/images/home2/service-icon-2.png') }}" alt="">
                            </div>
                            <a href="{{ url('/service-details') }}">Dust Recycling</a>
                            <p>Alternative innovation to ethical network environmental whiteboard</p>
                            <div class="service-button">
                                <a href="{{ url('/service') }}">Discover More<i class="bi bi-arrow-right-short"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-service-box">
                        <div class="service-thumb">
                            <img src="{{ Vite::asset('resources/assets/images/home2/services-3.png') }}" alt="">
                        </div>
                        <div class="service-content">
                            <div class="services-icon">
                                <img src="{{ Vite::asset('resources/assets/images/home2/service-icon-3.png') }}" alt="">
                            </div>
                            <a href="{{ url('/service-details') }}">Plant Seedlings</a>
                            <p>Alternative innovation to ethical network environmental whiteboard</p>
                            <div class="service-button">
                                <a href="{{ url('/service') }}">Discover More<i class="bi bi-arrow-right-short"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-service-box">
                        <div class="service-thumb">
                            <img src="{{ Vite::asset('resources/assets/images/home2/services-4.png') }}" alt="">
                        </div>
                        <div class="service-content">
                            <div class="services-icon">
                                <img src="{{ Vite::asset('resources/assets/images/home2/service-icon-4.png') }}" alt="">
                            </div>
                            <a href="{{ url('/service-details') }}">Carbon Offsetting</a>
                            <p>Alternative innovation to ethical network environmental whiteboard</p>
                            <div class="service-button">
                                <a href="{{ url('/service') }}">Discover More<i class="bi bi-arrow-right-short"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-service-box">
                        <div class="service-thumb">
                            <img src="{{ Vite::asset('resources/assets/images/home2/services-5.png') }}" alt="">
                        </div>
                        <div class="service-content">
                            <div class="services-icon">
                                <img src="{{ Vite::asset('resources/assets/images/home2/service-icon-5.png') }}" alt="">
                            </div>
                            <a href="{{ url('/service-details') }}">Renewable Energy</a>
                            <p>Alternative innovation to ethical network environmental whiteboard</p>
                            <div class="service-button">
                                <a href="{{ url('/service') }}">Discover More<i class="bi bi-arrow-right-short"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-service-box">
                        <div class="service-thumb">
                            <img src="{{ Vite::asset('resources/assets/images/home2/services-6.png') }}" alt="">
                        </div>
                        <div class="service-content">
                            <div class="services-icon">
                                <img src="{{ Vite::asset('resources/assets/images/home2/service-icon-6.png') }}" alt="">
                            </div>
                            <a href="{{ url('/service-details') }}">Global Warming</a>
                            <p>Alternative innovation to ethical network environmental whiteboard</p>
                            <div class="service-button">
                                <a href="{{ url('/service') }}">Discover More<i class="bi bi-arrow-right-short"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection