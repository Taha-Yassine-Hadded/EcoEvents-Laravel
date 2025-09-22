@extends('layouts.app')

@section('title', 'Echofy - Testimonials')

@section('content')
    <!-- Breadcrumb Area -->
    <div class="breadcumb-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <div class="breadcumb-content">
                        <div class="breadcumb-title">
                            <h4>Testimonials</h4>
                        </div>
                        <ul>
                            <li><a href="{{ url('/') }}"><img src="{{ Vite::asset('resources/assets/images/inner-images/breadcumb-text-shape.png') }}" alt="">Echofy</a></li>
                            <li>Testimonials</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonial Area -->
    <div class="testimonial-area inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="section-title center">
                        <h4><img src="{{ Vite::asset('resources/assets/images/home1/section-shape.png') }}" alt="">Testimonials</h4>
                        <h1>Clients' Best Feedback About</h1>
                        <h1>Echofy Provision</h1>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="single-testimonial-box">
                        <div class="testi-qutoe">
                            <img src="{{ Vite::asset('resources/assets/images/home1/testi-quote.png') }}" alt="">
                        </div>
                        <div class="testi-author">
                            <div class="testi-author-thumb">
                                <img src="{{ Vite::asset('resources/assets/images/home1/testi-author-1.png') }}" alt="">
                            </div>
                            <div class="testi-author-content">
                                <div class="testi-author-rating">
                                    <ul>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                    </ul>
                                </div>
                                <h4>Anjelina Watson</h4>
                                <p>UI/UX Designer</p>
                            </div>
                        </div>
                        <p class="testi-desc">
                            “Competently cultivate worldwide e-tailers through to principles
                            professionally engineer high-payoff deliverables without exceptional
                            Rapidiously network effective”
                        </p>
                        <div class="what-for-comment">
                            <p>Service Quality</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="single-testimonial-box">
                        <div class="testi-qutoe">
                            <img src="{{ Vite::asset('resources/assets/images/home1/testi-quote.png') }}" alt="">
                        </div>
                        <div class="testi-author">
                            <div class="testi-author-thumb">
                                <img src="{{ Vite::asset('resources/assets/images/home1/testi-author-2.png') }}" alt="">
                            </div>
                            <div class="testi-author-content">
                                <div class="testi-author-rating">
                                    <ul>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-half"></i></li>
                                    </ul>
                                </div>
                                <h4>John D. Alexon</h4>
                                <p>Web Developer</p>
                            </div>
                        </div>
                        <p class="testi-desc">
                            “Competently cultivate worldwide e-tailers through to principles
                            professionally engineer high-payoff deliverables without exceptional
                            Rapidiously network effective”
                        </p>
                        <div class="what-for-comment">
                            <p>Supports</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="single-testimonial-box">
                        <div class="testi-qutoe">
                            <img src="{{ Vite::asset('resources/assets/images/home1/testi-quote.png') }}" alt="">
                        </div>
                        <div class="testi-author">
                            <div class="testi-author-thumb">
                                <img src="{{ Vite::asset('resources/assets/images/home1/testi-author-3.png') }}" alt="">
                            </div>
                            <div class="testi-author-content">
                                <div class="testi-author-rating">
                                    <ul>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-half"></i></li>
                                    </ul>
                                </div>
                                <h4>June D. Vargas</h4>
                                <p>Volunteer</p>
                            </div>
                        </div>
                        <p class="testi-desc">
                            “Competently cultivate worldwide e-tailers through to principles
                            professionally engineer high-payoff deliverables without exceptional
                            Rapidiously network effective”
                        </p>
                        <div class="what-for-comment">
                            <p>Supports</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="single-testimonial-box">
                        <div class="testi-qutoe">
                            <img src="{{ Vite::asset('resources/assets/images/home1/testi-quote.png') }}" alt="">
                        </div>
                        <div class="testi-author">
                            <div class="testi-author-thumb">
                                <img src="{{ Vite::asset('resources/assets/images/home1/testi-author-4.png') }}" alt="">
                            </div>
                            <div class="testi-author-content">
                                <div class="testi-author-rating">
                                    <ul>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-half"></i></li>
                                    </ul>
                                </div>
                                <h4>Jane Smith</h4>
                                <p>Co-Founder</p>
                            </div>
                        </div>
                        <p class="testi-desc">
                            “Competently cultivate worldwide e-tailers through to principles
                            professionally engineer high-payoff deliverables without exceptional
                            Rapidiously network effective”
                        </p>
                        <div class="what-for-comment">
                            <p>Supports</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="single-testimonial-box">
                        <div class="testi-qutoe">
                            <img src="{{ Vite::asset('resources/assets/images/home1/testi-quote.png') }}" alt="">
                        </div>
                        <div class="testi-author">
                            <div class="testi-author-thumb">
                                <img src="{{ Vite::asset('resources/assets/images/home1/testi-author-5.png') }}" alt="">
                            </div>
                            <div class="testi-author-content">
                                <div class="testi-author-rating">
                                    <ul>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-half"></i></li>
                                    </ul>
                                </div>
                                <h4>Michael Floyed</h4>
                                <p>Web Developer</p>
                            </div>
                        </div>
                        <p class="testi-desc">
                            “Competently cultivate worldwide e-tailers through to principles
                            professionally engineer high-payoff deliverables without exceptional
                            Rapidiously network effective”
                        </p>
                        <div class="what-for-comment">
                            <p>Service Quality</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="single-testimonial-box">
                        <div class="testi-qutoe">
                            <img src="{{ Vite::asset('resources/assets/images/home1/testi-quote.png') }}" alt="">
                        </div>
                        <div class="testi-author">
                            <div class="testi-author-thumb">
                                <img src="{{ Vite::asset('resources/assets/images/home1/testi-author-6.png') }}" alt="">
                            </div>
                            <div class="testi-author-content">
                                <div class="testi-author-rating">
                                    <ul>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-half"></i></li>
                                    </ul>
                                </div>
                                <h4>Carole Bower</h4>
                                <p>Web Developer</p>
                            </div>
                        </div>
                        <p class="testi-desc">
                            “Competently cultivate worldwide e-tailers through to principles
                            professionally engineer high-payoff deliverables without exceptional
                            Rapidiously network effective”
                        </p>
                        <div class="what-for-comment">
                            <p>Supports</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="testi-shape">
            <img src="{{ Vite::asset('resources/assets/images/home1/testi-shape.png') }}" alt="">
        </div>
    </div>
@endsection