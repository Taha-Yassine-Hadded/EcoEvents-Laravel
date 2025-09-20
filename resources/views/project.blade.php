@extends('layouts.app')

@section('title', 'Echofy - Our Project')

@section('content')
    <!-- Breadcrumb Area -->
    <div class="breadcumb-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <div class="breadcumb-content">
                        <div class="breadcumb-title">
                            <h4>Our Project</h4>
                        </div>
                        <ul>
                            <li><a href="{{ url('/') }}"><img src="{{ asset('assets/images/inner-images/breadcumb-text-shape.png') }}" alt="">Echofy</a></li>
                            <li>Project</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Area -->
    <div class="project-area inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="section-title center">
                        <h4><img src="{{ asset('assets/images/home1/section-shape.png') }}" alt="">Latest Works</h4>
                        <h1>Building A Greener Future</h1>
                        <h1>Get Echofy Benefits</h1>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="single-project-box">
                        <div class="project-thumb">
                            <img src="{{ asset('assets/images/home1/project-1.jpg') }}" alt="">
                        </div>
                        <div class="project-content">
                            <h4>Climate</h4>
                            <a href="{{ url('/project-details') }}">Cleaning Forest</a>
                            <a class="project-button" href="{{ url('/project-details') }}">View Details<i class="bi bi-arrow-right-short"></i></a>
                            <div class="project-shape">
                                <img src="{{ asset('assets/images/home1/project-shape.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-project-box">
                        <div class="project-thumb">
                            <img src="{{ asset('assets/images/home1/project-2.jpg') }}" alt="">
                        </div>
                        <div class="project-content">
                            <h4>Environment</h4>
                            <a href="{{ url('/project-details') }}">Climate Solutions</a>
                            <a class="project-button" href="{{ url('/project-details') }}">View Details<i class="bi bi-arrow-right-short"></i></a>
                            <div class="project-shape">
                                <img src="{{ asset('assets/images/home1/project-shape.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-project-box">
                        <div class="project-thumb">
                            <img src="{{ asset('assets/images/home1/project-3.jpg') }}" alt="">
                        </div>
                        <div class="project-content">
                            <h4>Recycling</h4>
                            <a href="{{ url('/project-details') }}">Plastic Recycling</a>
                            <a class="project-button" href="{{ url('/project-details') }}">View Details<i class="bi bi-arrow-right-short"></i></a>
                            <div class="project-shape">
                                <img src="{{ asset('assets/images/home1/project-shape.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-project-box">
                        <div class="project-thumb">
                            <img src="{{ asset('assets/images/home1/project-4.jpg') }}" alt="">
                        </div>
                        <div class="project-content">
                            <h4>Climate</h4>
                            <a href="{{ url('/project-details') }}">Ocean Cleaning</a>
                            <a class="project-button" href="{{ url('/project-details') }}">View Details<i class="bi bi-arrow-right-short"></i></a>
                            <div class="project-shape">
                                <img src="{{ asset('assets/images/home1/project-shape.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-project-box">
                        <div class="project-thumb">
                            <img src="{{ asset('assets/images/home1/project-5.jpg') }}" alt="">
                        </div>
                        <div class="project-content">
                            <h4>Plants</h4>
                            <a href="{{ url('/project-details') }}">Seedling Plants</a>
                            <a class="project-button" href="{{ url('/project-details') }}">View Details<i class="bi bi-arrow-right-short"></i></a>
                            <div class="project-shape">
                                <img src="{{ asset('assets/images/home1/project-shape.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-project-box">
                        <div class="project-thumb">
                            <img src="{{ asset('assets/images/home1/project-6.jpg') }}" alt="">
                        </div>
                        <div class="project-content">
                            <h4>Environment</h4>
                            <a href="{{ url('/project-details') }}">Renewable Energy</a>
                            <a class="project-button" href="{{ url('/project-details') }}">View Details<i class="bi bi-arrow-right-short"></i></a>
                            <div class="project-shape">
                                <img src="{{ asset('assets/images/home1/project-shape.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection