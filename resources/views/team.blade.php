@extends('layouts.app')

@section('title', 'Echofy - Meet Our Team')

@section('content')
    <!-- Breadcrumb Area -->
    <div class="breadcumb-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <div class="breadcumb-content">
                        <div class="breadcumb-title">
                            <h4>Meet Our Team</h4>
                        </div>
                        <ul>
                            <li><a href="{{ url('/') }}"><img src="{{ asset('assets/images/inner-images/breadcumb-text-shape.png') }}" alt="">Echofy</a></li>
                            <li>Our Team</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Area -->
    <div class="team-area inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="section-title center">
                        <h4><img src="{{ asset('assets/images/home1/section-shape.png') }}" alt="">Our Team</h4>
                        <h1>Meet Our Dedicated Members</h1>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="single-team-box">
                        <div class="single-team-thumb">
                            <img src="{{ asset('assets/images/home1/team-1.png') }}" alt="">
                        </div>
                        <div class="team-content">
                            <h4>Connie Diaz</h4>
                            <p>CEO & Founder</p>
                            <div class="team-social-icon">
                                <ul>
                                    <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    <li><a href="#"><i class="fab fa-pinterest-p"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-team-box">
                        <div class="single-team-thumb">
                            <img src="{{ asset('assets/images/home1/team-2.png') }}" alt="">
                        </div>
                        <div class="team-content">
                            <h4>James E. Huey</h4>
                            <p>Co-Founder</p>
                            <div class="team-social-icon">
                                <ul>
                                    <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    <li><a href="#"><i class="fab fa-pinterest-p"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-team-box">
                        <div class="single-team-thumb">
                            <img src="{{ asset('assets/images/home1/team-3.png') }}" alt="">
                        </div>
                        <div class="team-content">
                            <h4>June D. Vargas</h4>
                            <p>Environmental Specialist</p>
                            <div class="team-social-icon">
                                <ul>
                                    <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    <li><a href="#"><i class="fab fa-pinterest-p"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-team-box">
                        <div class="single-team-thumb">
                            <img src="{{ asset('assets/images/home1/team-4.png') }}" alt="">
                        </div>
                        <div class="team-content">
                            <h4>Sarah Johnson</h4>
                            <p>Environmental Specialist</p>
                            <div class="team-social-icon">
                                <ul>
                                    <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    <li><a href="#"><i class="fab fa-pinterest-p"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-team-box">
                        <div class="single-team-thumb">
                            <img src="{{ asset('assets/images/home1/team-5.png') }}" alt="">
                        </div>
                        <div class="team-content">
                            <h4>Michael Lee</h4>
                            <p>Environmental Specialist</p>
                            <div class="team-social-icon">
                                <ul>
                                    <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    <li><a href="#"><i class="fab fa-pinterest-p"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-team-box">
                        <div class="single-team-thumb">
                            <img src="{{ asset('assets/images/home1/team-6.png') }}" alt="">
                        </div>
                        <div class="team-content">
                            <h4>Emily Chen</h4>
                            <p>Environmental Specialist</p>
                            <div class="team-social-icon">
                                <ul>
                                    <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    <li><a href="#"><i class="fab fa-pinterest-p"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection