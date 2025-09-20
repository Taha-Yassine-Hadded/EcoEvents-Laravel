@extends('layouts.app')

@section('title', 'Echofy - Blog Grid')

@section('content')
<!--==================================================-->
<!-- Start Echofy Breadcrumb Area -->
<!--==================================================-->
<div class="breadcumb-area">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12 text-center">
                <div class="breadcumb-content">
                    <div class="breadcumb-title">
                        <h4>Blog Grid</h4>
                    </div>
                    <ul>
                        <li>
                            <a href="{{ url('/') }}">
                                <img src="{{ asset('assets/images/inner-images/breadcumb-text-shape.png') }}" alt="">Echofy
                            </a>
                        </li>
                        <li>Blog Grid</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!--==================================================-->
<!-- End Echofy Breadcrumb Area -->
<!--==================================================-->

<!--==================================================-->
<!-- Start Echofy Blog Grid Area -->
<!--==================================================-->
<div class="blog-grid-area">
    <div class="container">
        <div class="row">
            @php
                $blogs = [
                    ['img'=>'blog-1.jpg', 'title'=>'Top 10 Recycling Tips for Environment', 'desc'=>'Competently cultivate worldwide to e-tailers professionally engineer high', 'author'=>'John D. Alexon', 'initial'=>'J'],
                    ['img'=>'blog-2.jpg', 'title'=>'How Every Individual Can Make a Difference', 'desc'=>'Competently cultivate worldwide to e-tailers professionally engineer high', 'author'=>'Anjelina Watson', 'initial'=>'A'],
                    ['img'=>'blog-3.jpg', 'title'=>'Innovations in Renewable Energy Technology', 'desc'=>'Competently cultivate worldwide to e-tailers professionally engineer high', 'author'=>'David Watson', 'initial'=>'D'],
                    ['img'=>'blog-4.jpg', 'title'=>'Completely Leverage Existing Customer Directed', 'desc'=>'Competently cultivate worldwide to e-tailers professionally engineer high', 'author'=>'Masrafi', 'initial'=>'M'],
                    ['img'=>'blog-5.jpg', 'title'=>'Progressively Syndicate Vista Seamless Natural', 'desc'=>'Competently cultivate worldwide to e-tailers professionally engineer high', 'author'=>'Hasan Kaku', 'initial'=>'H'],
                    ['img'=>'blog-6.jpg', 'title'=>'Dramatically Architect New Model Before Flexible', 'desc'=>'Competently cultivate worldwide to e-tailers professionally engineer high', 'author'=>'Alex Song', 'initial'=>'A'],
                ];
            @endphp

            @foreach($blogs as $blog)
                <div class="col-lg-4 col-md-6">
                    <div class="single-blog-box">
                        <div class="single-blog-thumb">
                            <img src="{{ asset('assets/images/home1/'.$blog['img']) }}" alt="{{ $blog['title'] }}">
                        </div>
                        <div class="blog-content">
                            <a href="#">{{ $blog['title'] }}</a>
                            <p>{{ $blog['desc'] }}</p>
                        </div>
                        <div class="blog-arthor">
                            <div class="blog-author-title">
                                <h6><span>{{ $blog['initial'] }}</span>{{ $blog['author'] }}</h6>
                            </div>
                            <div class="blog-button">
                                <a href="#"><i class="bi bi-arrow-right-short"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</div>
<!--==================================================-->
<!-- End Echofy Blog Grid Area -->
<!--==================================================-->
@endsection