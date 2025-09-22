@extends('layouts.app')

@section('title', 'Echofy - Donations')

@section('content')
    <!-- Breadcrumb Area -->
    <div class="breadcumb-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <div class="breadcumb-content">
                        <div class="breadcumb-title">
                            <h4>Donations</h4>
                        </div>
                        <ul>
                            <li><a href="{{ url('/') }}"><img src="{{ Vite::asset('resources/assets/images/inner-images/breadcumb-text-shape.png') }}" alt="">Echofy</a></li>
                            <li>Donations</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Donation Area -->
    <div class="donation-area inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="section-title center">
                        <h4><img src="{{ Vite::asset('resources/assets/images/home1/section-shape.png') }}" alt="">Donation</h4>
                        <h1>Building a Greener Future</h1>
                        <h1>Donation Money</h1>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="single-donation-box">
                        <div class="donation-thumb">
                            <img src="{{ Vite::asset('resources/assets/images/home2/donation-1.png') }}" alt="">
                            <div class="donate-button">
                                <a href="{{ url('/donation-details') }}">Donate Now</a>
                            </div>
                        </div>
                        <div class="donation-content">
                            <a href="{{ url('/donation-details') }}">Fund Raising for Tree Plantation - 2024</a>
                            <p>Cultivate worldwide tailers through nature professionally engineer high</p>
                            <div class="skills-content">
                                <div class="skill-bg"></div>
                                <div class="skills html active style-one"><span class="number">85%</span></div>
                            </div>
                            <div class="slider-content">
                                <span>Raised: <span class="price">$780.00</span></span>
                                <span>Goal: <span class="price">$1000.00</span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-donation-box">
                        <div class="donation-thumb">
                            <img src="{{ Vite::asset('resources/assets/images/home2/donation-2.png') }}" alt="">
                            <div class="donate-button">
                                <a href="{{ url('/donation-details') }}">Donate Now</a>
                            </div>
                        </div>
                        <div class="donation-content">
                            <a href="{{ url('/donation-details') }}">Fund Raising for Forest Recycling & Repair</a>
                            <p>Cultivate worldwide tailers through nature professionally engineer high</p>
                            <div class="skills-content">
                                <div class="skill-bg"></div>
                                <div class="skills html active style-two"><span class="number">65%</span></div>
                            </div>
                            <div class="slider-content">
                                <span>Raised: <span class="price">$780.00</span></span>
                                <span>Goal: <span class="price">$1000.00</span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-donation-box">
                        <div class="donation-thumb">
                            <img src="{{ Vite::asset('resources/assets/images/home2/donation-3.png') }}" alt="">
                            <div class="donate-button">
                                <a href="{{ url('/donation-details') }}">Donate Now</a>
                            </div>
                        </div>
                        <div class="donation-content">
                            <a href="{{ url('/donation-details') }}">Environmental Dust Clean And Recycling</a>
                            <p>Cultivate worldwide tailers through nature professionally engineer high</p>
                            <div class="skills-content">
                                <div class="skill-bg"></div>
                                <div class="skills html active"><span class="number">90%</span></div>
                            </div>
                            <div class="slider-content">
                                <span>Raised: <span class="price">$780.00</span></span>
                                <span>Goal: <span class="price">$1000.00</span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-donation-box">
                        <div class="donation-thumb">
                            <img src="{{ Vite::asset('resources/assets/images/home2/donation-4.png') }}" alt="">
                            <div class="donate-button">
                                <a href="{{ url('/donation-details') }}">Donate Now</a>
                            </div>
                        </div>
                        <div class="donation-content">
                            <a href="{{ url('/donation-details') }}">Fund Raising for Renewable Energy Poor Peoples</a>
                            <p>Cultivate worldwide tailers through nature professionally engineer high</p>
                            <div class="skills-content">
                                <div class="skill-bg"></div>
                                <div class="skills html active"><span class="number">40%</span></div>
                            </div>
                            <div class="slider-content">
                                <span>Raised: <span class="price">$780.00</span></span>
                                <span>Goal: <span class="price">$1000.00</span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-donation-box">
                        <div class="donation-thumb">
                            <img src="{{ Vite::asset('resources/assets/images/home2/donation-5.png') }}" alt="">
                            <div class="donate-button">
                                <a href="{{ url('/donation-details') }}">Donate Now</a>
                            </div>
                        </div>
                        <div class="donation-content">
                            <a href="{{ url('/donation-details') }}">Donations for Plant Seedlings Orphan Peoples</a>
                            <p>Cultivate worldwide tailers through nature professionally engineer high</p>
                            <div class="skills-content">
                                <div class="skill-bg"></div>
                                <div class="skills html active"><span class="number">90%</span></div>
                            </div>
                            <div class="slider-content">
                                <span>Raised: <span class="price">$780.00</span></span>
                                <span>Goal: <span class="price">$1000.00</span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-donation-box">
                        <div class="donation-thumb">
                            <img src="{{ Vite::asset('resources/assets/images/home2/donation-6.png') }}" alt="">
                            <div class="donate-button">
                                <a href="{{ url('/donation-details') }}">Donate Now</a>
                            </div>
                        </div>
                        <div class="donation-content">
                            <a href="{{ url('/donation-details') }}">Fund Raising for Tree Plantation - 2024</a>
                            <p>Cultivate worldwide tailers through nature professionally engineer high</p>
                            <div class="skills-content">
                                <div class="skill-bg"></div>
                                <div class="skills html active"><span class="number">60%</span></div>
                            </div>
                            <div class="slider-content">
                                <span>Raised: <span class="price">$780.00</span></span>
                                <span>Goal: <span class="price">$1000.00</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection