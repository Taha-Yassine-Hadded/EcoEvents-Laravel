@extends('layouts.app')

@section('title', 'Echofy - Blog List')

@section('content')
    <!-- Breadcrumb Area -->
    <div class="breadcumb-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <div class="breadcumb-content">
                        <div class="breadcumb-title">
                            <h4>Blog List</h4>
                        </div>
                        <ul>
                            <li>
                                <a href="{{ url('/') }}"><img src="{{ asset('assets/images/inner-images/breadcumb-text-shape.png') }}" alt="">Echofy</a>
                            </li>
                            <li>Blog List</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Blog List Area -->
    <div class="blog-list-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="single-blog-list-box">
                                <div class="blog-thumb">
                                    <img src="{{ asset('assets/images/inner-images/blog-list-1.png') }}" alt="">
                                </div>
                                <div class="blog-list-content">
                                    <div class="meta-blog">
                                        <span class="mate-text">By Author</span>
                                        <span><i class="fas fa-calendar-alt"></i>05 January, 2024</span>
                                        <span><img src="{{ asset('assets/images/inner-images/category-icon.png') }}" alt="">Environment</span>
                                    </div>
                                    <a class="blog-list-title" href="{{ url('/blog-details') }}">Top 10 Recycling tips for Environment</a>
                                    <p class="blog-list-desc">Alternative innovation to ethical network environmental whiteboard pursue compelling results for methods empowerment. Dramatically architect go forward opportunities</p>
                                </div>
                                <div class="blog-list-button">
                                    <a href="{{ url('/blog-grid') }}">Continue Reading<i class="bi bi-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="single-blog-list-box">
                                <div class="blog-thumb">
                                    <img src="{{ asset('assets/images/inner-images/blog-list-2.png') }}" alt="">
                                </div>
                                <div class="blog-list-content">
                                    <div class="meta-blog">
                                        <span class="mate-text">By Author</span>
                                        <span><i class="fas fa-calendar-alt"></i>05 January, 2024</span>
                                        <span><img src="{{ asset('assets/images/inner-images/category-icon.png') }}" alt="">Environment</span>
                                    </div>
                                    <a class="blog-list-title" href="{{ url('/blog-details') }}">How Every Individual Can Make a Difference</a>
                                    <p class="blog-list-desc">Alternative innovation to ethical network environmental whiteboard pursue compelling results for methods empowerment. Dramatically architect go forward opportunities</p>
                                </div>
                                <div class="blog-list-button">
                                    <a href="{{ url('/blog-grid') }}">Continue Reading<i class="bi bi-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="single-blog-list-box">
                                <div class="blog-thumb">
                                    <img src="{{ asset('assets/images/inner-images/blog-list-3.png') }}" alt="">
                                </div>
                                <div class="blog-list-content">
                                    <div class="meta-blog">
                                        <span class="mate-text">By Author</span>
                                        <span><i class="fas fa-calendar-alt"></i>05 January, 2024</span>
                                        <span><img src="{{ asset('assets/images/inner-images/category-icon.png') }}" alt="">Environment</span>
                                    </div>
                                    <a class="blog-list-title" href="{{ url('/blog-details') }}">Completely Leverage Existing Customer Directed</a>
                                    <p class="blog-list-desc">Alternative innovation to ethical network environmental whiteboard pursue compelling results for methods empowerment. Dramatically architect go forward opportunities</p>
                                </div>
                                <div class="blog-list-button">
                                    <a href="{{ url('/blog-grid') }}">Continue Reading<i class="bi bi-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="single-blog-list-box">
                                <div class="blog-thumb">
                                    <img src="{{ asset('assets/images/inner-images/blog-list-4.png') }}" alt="">
                                </div>
                                <div class="blog-list-content">
                                    <div class="meta-blog">
                                        <span class="mate-text">By Author</span>
                                        <span><i class="fas fa-calendar-alt"></i>05 January, 2024</span>
                                        <span><img src="{{ asset('assets/images/inner-images/category-icon.png') }}" alt="">Environment</span>
                                    </div>
                                    <a class="blog-list-title" href="{{ url('/blog-details') }}">Progressively Syndicate Vista Seamless Natural</a>
                                    <p class="blog-list-desc">Alternative innovation to ethical network environmental whiteboard pursue compelling results for methods empowerment. Dramatically architect go forward opportunities</p>
                                </div>
                                <div class="blog-list-button">
                                    <a href="{{ url('/blog-grid') }}">Continue Reading<i class="bi bi-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="single-blog-list-box">
                                <div class="blog-thumb">
                                    <img src="{{ asset('assets/images/inner-images/blog-list-5.png') }}" alt="">
                                </div>
                                <div class="blog-list-content">
                                    <div class="meta-blog">
                                        <span class="mate-text">By Author</span>
                                        <span><i class="fas fa-calendar-alt"></i>05 January, 2024</span>
                                        <span><img src="{{ asset('assets/images/inner-images/category-icon.png') }}" alt="">Environment</span>
                                    </div>
                                    <a class="blog-list-title" href="{{ url('/blog-details') }}">Innovations in Renewable Energy Technology</a>
                                    <p class="blog-list-desc">Alternative innovation to ethical network environmental whiteboard pursue compelling results for methods empowerment. Dramatically architect go forward opportunities</p>
                                </div>
                                <div class="blog-list-button">
                                    <a href="{{ url('/blog-grid') }}">Continue Reading<i class="bi bi-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="widget-sidber">
                                <div class="widget_search">
                                    <form>
                                        @csrf
                                        <input type="text" name="search" value="" placeholder="Search Here" title="Search for:">
                                        <button type="submit" class="icons">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="widget-sidber">
                                <div class="widget-sidber-content">
                                    <h4>Categories</h4>
                                </div>
                                <div class="widget-category">
                                    <ul>
                                        <li><a href="#"><img src="{{ asset('assets/images/inner-images/category-icon.png') }}" alt="">Ocean Cleaning<i class="bi bi-arrow-right"></i></a></li>
                                        <li><a href="#"><img src="{{ asset('assets/images/inner-images/category-icon.png') }}" alt="">Dust Recycling<i class="bi bi-arrow-right"></i></a></li>
                                        <li><a href="#"><img src="{{ asset('assets/images/inner-images/category-icon.png') }}" alt="">Plant Seedlings<i class="bi bi-arrow-right"></i></a></li>
                                        <li><a href="#"><img src="{{ asset('assets/images/inner-images/category-icon.png') }}" alt="">Renewable Energy<i class="bi bi-arrow-right"></i></a></li>
                                        <li><a href="#"><img src="{{ asset('assets/images/inner-images/category-icon.png') }}" alt="">Environmental<i class="bi bi-arrow-right"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="widget-sidber">
                                <div class="widget-sidber-content">
                                    <h4>Popular Post</h4>
                                </div>
                                <div class="sidber-widget-recent-post">
                                    <div class="recent-widget-thumb">
                                        <img src="{{ asset('assets/images/inner-images/recent-post-1.png') }}" alt="">
                                    </div>
                                    <div class="recent-widget-content">
                                        <a href="{{ url('/blog-details') }}">Dramatically Architect new model before...</a>
                                        <p>Jan, 26 2024</p>
                                    </div>
                                </div>
                                <div class="sidber-widget-recent-post">
                                    <div class="recent-widget-thumb">
                                        <img src="{{ asset('assets/images/inner-images/recent-post-2.png') }}" alt="">
                                    </div>
                                    <div class="recent-widget-content">
                                        <a href="{{ url('/blog-details') }}">Progressively Syndicate Vista Seamless...</a>
                                        <p>Jan, 26 2024</p>
                                    </div>
                                </div>
                                <div class="sidber-widget-recent-post">
                                    <div class="recent-widget-thumb">
                                        <img src="{{ asset('assets/images/inner-images/recent-post-3.png') }}" alt="">
                                    </div>
                                    <div class="recent-widget-content">
                                        <a href="{{ url('/blog-details') }}">Completely Leverage Existing Customer...</a>
                                        <p>Jan, 26 2024</p>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-sidber">
                                <div class="widget-sidber-content">
                                    <h4>Tags</h4>
                                </div>
                                <div class="widget-catefories-tags">
                                    <a href="#">Environmental</a>
                                    <a href="#">Ecology</a>
                                    <a href="#">Seedlings</a>
                                    <a href="#">Tree Plantation</a>
                                    <a href="#">Recycling</a>
                                    <a href="#">Cleaning</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection