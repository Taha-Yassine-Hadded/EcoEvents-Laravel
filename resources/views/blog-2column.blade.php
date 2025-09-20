@extends('layouts.app')

@section('title', 'Echofy - Blog 2 Column')

@section('content')
    <!--==================================================-->
    <!-- Start Echofy Breadcumb Area -->
    <!--==================================================-->
    <div class="breadcumb-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <div class="breadcumb-content">
                        <div class="breadcumb-title">
                            <h4>Blog Grid with Sidebar</h4>
                        </div>
                        <ul>
                            <li>
                                <a href="{{ url('/') }}">
                                    <img src="{{ asset('assets/images/inner-images/breadcumb-text-shape.png') }}" alt="">Echofy
                                </a>
                            </li>
                            <li>Blog Grid with Sidebar</li>
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
    <!-- Start Echofy Blog Grid Area-->
    <!--==================================================-->
    <div class="blog-grid-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="row">
                        @for ($i = 1; $i <= 6; $i++)
                        <div class="col-lg-6 col-md-6">
                            <div class="single-blog-box">
                                <div class="single-blog-thumb">
                                    <img src="{{ asset('assets/images/home1/blog-1.jpg') }}" alt="">
                                </div>
                                <div class="blog-content">
                                    <a href="{{ url('blog-details') }}">Blog Title {{ $i }}</a>
                                    <p>Competently cultivate worldwide to e-tailers professionally engineer high</p>
                                </div>
                                <div class="blog-arthor">
                                    <div class="blog-author-title">
                                        <h6><span>{{ chr(64+$i) }}</span>Author {{ $i }}</h6>
                                    </div>
                                    <div class="blog-button">
                                        <a href="{{ url('blog-grid') }}"><i class="bi bi-arrow-right-short"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row">
                        <div class="col-lg-12">
                            <!-- Search Widget -->
                            <div class="widget-sidber">
                                <div class="widget_search">
                                    <form action="#" method="get">
                                        <input type="text" name="s" value="" placeholder="Search Here" title="Search for:">
                                        <button type="submit" class="icons">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <!-- Categories Widget -->
                            <div class="widget-sidber">
                                <div class="widget-sidber-content">
                                    <h4>Categories</h4>
                                </div>
                                <div class="widget-category">
                                    <ul>
                                        @foreach(['Ocean Cleaning','Dust Recycling','Plant Seedlings','Renewable Energy','Environmental'] as $category)
                                            <li>
                                                <a href="#">
                                                    <img src="{{ asset('assets/images/inner-images/category-icon.png') }}" alt="">{{ $category }}
                                                    <i class="bi bi-arrow-right"></i>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <!-- Popular Post Widget -->
                            <div class="widget-sidber">
                                <div class="widget-sidber-content">
                                    <h4>Popular Post</h4>
                                </div>
                                @for ($i = 1; $i <= 3; $i++)
                                    <div class="sidber-widget-recent-post">
                                        <div class="recent-widget-thumb">
                                            <img src="{{ asset("assets/images/inner-images/recent-post-$i.png") }}" alt="">
                                        </div>
                                        <div class="recent-widget-content">
                                            <a href="{{ url('blog-details') }}">Popular Post {{ $i }}</a>
                                            <p>Jan, 26 2024</p>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            <!-- Tags Widget -->
                            <div class="widget-sidber">
                                <div class="widget-sidber-content">
                                    <h4>Tags</h4>
                                </div>
                                <div class="widget-catefories-tags">
                                    @foreach(['Environmental','Ecology','Seedlings','Tree Plantation','Recycling','Cleaning'] as $tag)
                                        <a href="#">{{ $tag }}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--==================================================-->
    <!-- End Echofy Blog Grid Area-->
    <!--==================================================-->
@endsection