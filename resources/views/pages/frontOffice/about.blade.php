@extends('layouts.app')

@section('title', 'Echofy - About')

@section('content')
<!--==================================================-->
<!-- Start Echofy Breadcumb Area -->
<!--==================================================-->
<div class="breadcumb-area" id="breadcumb">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12 text-center">
                <div class="breadcumb-content">
                    <div class="breadcumb-title">
                        <h4>About Us</h4>
                    </div>
                    <ul>
                        <li><a href="{{ url('/') }}"><img src="{{ Vite::asset('resources/assets/images/inner-images/breadcumb-text-shape.png') }}" alt="">Echofy</a></li>
                        <li>About Us</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!--==================================================-->
<!-- End Echofy Breadcumb Area -->
<!--==================================================-->

<!--==================================================-->
<!-- Start Echofy About Area -->
<!--==================================================-->
<div class="about-area home-two" id="about">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-12">
                <div class="about-thumb">
                    <img src="{{ Vite::asset('resources/assets/images/home2/about-thumb.png') }}" alt="">
                    <div class="about-video">
                        <a class="video-vemo-icon venobox vbox-item" data-vbtype="youtube" data-autoplay="true" href="https://www.youtube.com/watch?v=e6R6VsgD8yQ&t=179s"><i class="bi bi-play"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="about-right">
                    <div class="section-title left">
                        <h4><img src="{{ Vite::asset('resources/assets/images/home1/section-shape.png') }}" alt="">Get to know echofy</h4>
                        <h1>Environmental Sustainable</h1>
                        <h1>Forever Green Future</h1>
                    </div>
                    <div class="about-text">
                        <span><img src="{{ Vite::asset('resources/assets/images/home2/shape.png') }}" alt=""></span><a href="#">We’ve 10+ years of Experience Ecology</a>
                    </div>
                    <p class="about-desc">Credibly incentivize leveraged catalysts for change whereas premium scenarios. Professionally foster synergistic ROI for multidisciplinary</p>
                    <div class="about-list-item">
                        <ul>
                            <li><i class="bi bi-check"></i>Know what your target market wants and needs</li>
                            <li><i class="bi bi-check"></i>A Whole Lot of Digital Love for Less</li>
                        </ul>
                    </div>
                    <div class="about-single-box">
                        <div class="about-author-box">
                            <div class="about-author">
                                <img src="{{ Vite::asset('resources/assets/images/home2/about-author.png') }}" alt="">
                            </div>
                            <div class="about-author-content">
                                <h4>Anjelina Watson</h4>
                                <p>Ecologist</p>
                            </div>
                        </div>
                        <div class="echofy-button">
                            <a href="{{ url('/about') }}">More About <img src="{{ Vite::asset('resources/assets/images/home1/button-shape.png') }}" alt=""></a>
                            <img class="two" src="{{ Vite::asset('resources/assets/images/home1/button-shape-2.png') }}" alt="">
                        </div>
                    </div>
                    <div class="about-shape-1">
                        <img src="{{ Vite::asset('resources/assets/images/home1/about-shape-1.png') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--==================================================-->
<!-- End Echofy About Area -->
<!--==================================================-->

<!--==================================================-->
<!-- Start Echofy Counter Area -->
<!--==================================================-->
<div class="counter-area" id="counter">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="single-counter-box">
                    <div class="conuter-icon">
                        <img src="{{ Vite::asset('resources/assets/images/home1/counter-icon-1.png') }}" alt="">
                    </div>
                    <div class="counter-content">
                        <h4 class="counter">100</h4>
                        <span>+</span>
                        <p>Team Member</p>
                    </div>
                </div>
            </div>          
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="single-counter-box">
                    <div class="conuter-icon">
                        <img src="{{ Vite::asset('resources/assets/images/home1/counter-icon-2.png') }}" alt="">
                    </div>
                    <div class="counter-content">
                        <h4 class="counter">960</h4>
                        <span>+</span>
                        <p>Complete Works</p>
                    </div>
                </div>
            </div>           
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="single-counter-box">
                    <div class="conuter-icon">
                        <img src="{{ Vite::asset('resources/assets/images/home1/counter-icon-3.png') }}" alt="">
                    </div>
                    <div class="counter-content">
                        <h4 class="counter">38</h4>
                        <p>Award Winning</p>
                    </div>
                </div>
            </div>           
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="single-counter-box">
                    <div class="conuter-icon">
                        <img src="{{ Vite::asset('resources/assets/images/home1/counter-icon-4.png') }}" alt="">
                    </div>
                    <div class="counter-content">
                        <h4 class="counter">4.7</h4>
                        <p>Avg Ratings</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--==================================================-->
<!-- End Echofy Counter Area -->
<!--==================================================-->

<!--==================================================-->
<!-- Start Echofy Process Area -->
<!--==================================================-->
<div class="process-area" id="process">
    <div class="container">
        <div class="row align-items-center" id="process-row">
            <div class="col-lg-6 col-md-12">
                <div class="porcess-thumb">
                    <img src="{{ Vite::asset('resources/assets/images/home1/process-thumb.jpg') }}" alt="">
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="process-left">
                    <div class="section-title left">
                        <h4><img src="{{ Vite::asset('resources/assets/images/home1/section-shape.png') }}" alt="">Contact Us </h4>
                        <h1>Fresh Environmental</h1>
                        <h1>Plant & Safe Trees</h1>
                        <p class="section-desc">
                            Competently cultivate worldwide e-tailers through principle-centered value 
                            professionally engineer high-payoff deliverables without exceptional processes. 
                            Rapidiously network cost effective vortals
                        </p>
                    </div>
                    <div class="echofy-button">
                        <a href="{{ url('/contact') }}">Contact Us <img src="{{ Vite::asset('resources/assets/images/home1/button-shape.png') }}" alt=""></a>
                        <img class="two" src="{{ Vite::asset('resources/assets/images/home1/button-shape-2.png') }}" alt="">
                    </div>
                    <div class="process-shape">
                        <img src="{{ Vite::asset('resources/assets/images/home1/about-shape-2.png') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--==================================================-->
<!-- End Echofy Process Area -->
<!--==================================================-->

<!--==================================================-->
<!-- Start Echofy Brand Area -->
<!--==================================================-->
<div class="brand-area home-two" id="brand">
    <div class="container">
        <div class="row">
            <div class="brand-list-2 owl-carousel">
                @for ($i = 1; $i <= 4; $i++)
                <div class="col-md-12">
                    <div class="single-brand-box">
                        <img src="{{ Vite::asset('resources/assets/images/home2/brand-'.$i.'.png') }}" alt="Brand {{$i}}">
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </div>
</div>
<!--==================================================-->
<!-- End Echofy Brand Area -->
<!--==================================================-->

<!--==================================================-->
<!-- Start Echofy Team Area -->
<!--==================================================-->
<div class="team-area inner" id="team">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="section-title center">
                    <h4><img src="{{ Vite::asset('resources/assets/images/home1/section-shape.png') }}" alt="">Our Team</h4>
                    <h1>Meet Our Dedicated Members</h1>
                </div>
            </div>
        </div>
        <div class="row">
            @php
                $teamMembers = [
                    ['name'=>'Connie Diaz','role'=>'CEO & Founder','img'=>'team-1.png'],
                    ['name'=>'James E. Huey','role'=>'Co Founder','img'=>'team-2.png'],
                    ['name'=>'June D. Vargas','role'=>'Environmental','img'=>'team-3.png']
                ];
            @endphp
            @foreach ($teamMembers as $member)
            <div class="col-lg-4 col-md-6">
                <div class="single-team-box">
                    <div class="single-team-thumb">
                        <img src="{{ Vite::asset('resources/assets/images/home1/'.$member['img']) }}" alt="{{ $member['name'] }}">
                    </div>
                    <div class="team-content">
                        <h4>{{ $member['name'] }}</h4>
                        <p>{{ $member['role'] }}</p>
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
            @endforeach
        </div>
    </div>
</div>
<!--==================================================-->
<!-- End Echofy Team Area -->
<!--==================================================-->

<!--==================================================-->
<!-- Start Echofy Marquee Text Area -->
<!--==================================================-->
<div class="marquee home-two" id="marquee">
    @for($i=0; $i<3; $i++)
    <div class="marquee-content scroll">
        <div class="text-block">Ultimate battle victorious.</div>
    </div>
    @endfor
</div>
<!--==================================================-->
<!-- End Echofy Marquee Text Area -->
<!--==================================================-->

<!--==================================================-->
<!-- Start Echofy Testimonial Area -->
<!--==================================================-->
<div class="testimonial-area" id="testimonial">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="section-title center">
                    <h4><img src="{{ Vite::asset('resources/assets/images/home1/section-shape.png') }}" alt="">Testimonials</h4>
                    <h1>Clients Best Feedback About</h1>
                    <h1>Echofy Provision</h1>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="testimonial-list-1 owl-carousel">
                @php
                    $testimonials = [
                        ['name'=>'Anjelina Watson','role'=>'UI/UX Designer','img'=>'testi-author-1.png','rating'=>5,'comment'=>'Service Quality'],
                        ['name'=>'John D. Alexon','role'=>'Web Developer','img'=>'testi-author-2.png','rating'=>4.5,'comment'=>'Supports']
                    ];
                @endphp
                @foreach ($testimonials as $testi)
                <div class="col-lg-12">
                    <div class="single-testimonial-box">
                        <div class="testi-qutoe">
                            <img src="{{ Vite::asset('resources/assets/images/home1/testi-quote.png') }}" alt="">
                        </div>
                        <div class="testi-author">
                            <div class="testi-author-thumb">
                                <img src="{{ Vite::asset('resources/assets/images/home1/'.$testi['img']) }}" alt="{{ $testi['name'] }}">
                            </div>
                            <div class="testi-author-content">
                                <div class="testi-author-rating">
                                    <ul>
                                        @for ($r=1;$r<=5;$r++)
                                            @if($r <= floor($testi['rating']))
                                                <li><i class="bi bi-star-fill"></i></li>
                                            @elseif($r - $testi['rating'] < 1)
                                                <li><i class="bi bi-star-half"></i></li>
                                            @else
                                                <li><i class="bi bi-star"></i></li>
                                            @endif
                                        @endfor
                                    </ul>
                                </div>
                                <h4>{{ $testi['name'] }}</h4>
                                <p>{{ $testi['role'] }}</p>
                            </div>
                        </div>
                        <p class="testi-desc">
                            “Competently cultivate worldwide e-tailers through to principles professionally engineer high-payoff deliverables without excet. Rapidiously network effective”
                        </p>
                        <div class="what-for-comment">
                            <p>{{ $testi['comment'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="testi-shape">
        <img src="{{ Vite::asset('resources/assets/images/home1/testi-shape.png') }}" alt="">
    </div>
</div>
<!--==================================================-->
<!-- End Echofy Testimonial Area -->
<!--==================================================-->
@endsection