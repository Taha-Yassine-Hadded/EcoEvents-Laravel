@extends('layouts.app')

@section('title', 'Echofy - Blog Details')

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
                        <h4>Blog Details</h4>
                    </div>
                    <ul>
                        <li>
                            <a href="{{ url('/') }}">
                                <img src="{{ asset('assets/images/inner-images/breadcumb-text-shape.png') }}" alt="">Echofy
                            </a>
                        </li>
                        <li>Blog Details</li>
                        <li class="khela-hbe">How Every Individual Can Make a Difference</li>
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
<!-- Start Echofy Blog Details Area -->
<!--==================================================-->
<div class="blog-details-area">
    <div class="container">
        <div class="row">
            <!-- Blog Content -->
            <div class="col-lg-8">
                <div class="blog-details-thumb">
                    <img src="{{ asset('assets/images/inner-images/blog-details-thumb.jpg') }}" alt="">
                </div>

                <div class="blog-details-content">
                    <div class="meta-blog">
                        <span class="mate-text">By Author</span>
                        <span><i class="fas fa-calendar-alt"></i>05 January, 2024</span>
                        <span>
                            <img src="{{ asset('assets/images/inner-images/category-icon.png') }}" alt="">Environment
                        </span>
                    </div>

                    <h4 class="blog-details-title">How Every Individual Can Make a Difference</h4>

                    <p class="blog-details-desc">
                        Alternative innovation to ethical network environmental whiteboard pursue compelling results for methods empowerment. Dramatically architect go forward opportunities before user-centric. Credibly implement exceptional.
                    </p>  

                    <p class="blog-details-desc">
                        Continually fashion orthogonal leadership skills whereas wireless metrics. Uniquely syndicate opportunities with interdependent users. Globally enhance fully tested meta-services rather than solutions. Proactively integrate client architectures and turnkey meta. Interactively harness integrated ROI.
                    </p>

                    <div class="blog-details-author-talk">
                        <div class="blog-details-quote">
                            <img src="{{ asset('assets/images/inner-images/blog-details-quote.png') }}" alt="">
                        </div>
                        <div class="blog-details-author-title">
                            <p>Competently architect intermandated deliverables client with niches continually underwhelm build cross-media growth strategies without robust.</p>
                            <span>CEO & Founder</span>
                        </div>
                    </div>

                    <h3 class="blog-details-title">Clean Environment Policy</h3>
                    <p class="blog-details-desc two">
                        Dynamically optimize leading-edge value via pandemic manufactured products. Conveniently seize sticky growth strategies and ethical potentialities. Professionally create high-quality rather than intuitive portals.
                    </p>

                    <div class="blog-details-list-item">
                        <ul>
                            <li><i class="bi bi-check-circle-fill"></i>Innovate wireless market</li>
                            <li><i class="bi bi-check-circle-fill"></i>Productivate resource management</li>
                            <li><i class="bi bi-check-circle-fill"></i>Proactively unleash oriented communities</li>
                            <li><i class="bi bi-check-circle-fill"></i>Credibly develop progressive architecture</li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="blog-details-thumb two">
                                <img src="{{ asset('assets/images/inner-images/services-details-benifis-thumb-1.png') }}" alt="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="blog-details-thumb">
                                <img src="{{ asset('assets/images/inner-images/services-details-benifis-thumb-2.png') }}" alt="">
                            </div>
                        </div>
                    </div>

                    <h3 class="blog-details-title two">Tree Plantation for Humans</h3>
                    <p class="blog-details-desc three">
                        Progressively target highly efficient business for distributed interfaces. Globally visualize networks rather than viral collaboration and idea-sharing. Continually utilize turnkey networks via productized intuitive information.
                    </p>
                </div>

                <!-- Social & Categories -->
                <div class="blog-details-socila-box">
                    <div class="row align-items-center">
                        <div class="col-lg-6 col-md-6">
                            <div class="blog-details-category">
                                <span><a href="#">Environmental</a></span>
                                <span><a class="active-class" href="#">Renewable</a></span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="blog-details-social-icon">
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

                <!-- Related Posts Carousel -->
                <div class="blog-details-post">
                    <div class="row">
                        <div class="blog-post-list owl-carousel">
                            @for ($i = 1; $i <= 4; $i++)
                                <div class="col-lg-12">
                                    <div class="blog-post-box {{ $i % 2 == 0 ? 'style-two' : '' }}">
                                        @if($i % 2 == 0)
                                            <div class="blog-post-content">
                                                <a href="#">Sample Post {{ $i }}</a>
                                                <p>August 10, 2024</p>
                                            </div>
                                            <div class="blog-post-thumb">
                                                <img src="{{ asset('assets/images/inner-images/blog-post-2.png') }}" alt="">
                                            </div>
                                        @else
                                            <div class="blog-post-thumb">
                                                <img src="{{ asset('assets/images/inner-images/blog-post-1.png') }}" alt="">
                                            </div>
                                            <div class="blog-post-content">
                                                <a href="#">Sample Post {{ $i }}</a>
                                                <p>August 10, 2024</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="single-comment-area">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="blog-details-comment-title">
                                <h4>2 Comments</h4>
                            </div>

                            @php
                                $comments = [
                                    ['name'=>'Michael Jordan', 'date'=>'22 August, 2024', 'text'=>'Interactively visualize top-line internal or "organic" sources rather than top-line niche market.'],
                                    ['name'=>'Johon Alex', 'date'=>'22 August, 2024', 'text'=>'Interactively visualize top-line internal or "organic" sources rather than top-line niche market.']
                                ];
                            @endphp

                            @foreach($comments as $comment)
                                <div class="blog-details-comment {{ $loop->iteration == 2 ? 'style-two' : '' }}">
                                    <div class="blog-details-comment-reply">
                                        <a href="#">Reply</a>
                                    </div>
                                    <div class="blog-details-comment-thumb">
                                        <img src="{{ asset("assets/images/inner-images/blog-details-author-{$loop->iteration}.png") }}" alt="">
                                    </div>
                                    <div class="blog-details-comment-content">
                                        <h2>{{ $comment['name'] }}</h2>
                                        <span>{{ $comment['date'] }}</span>
                                        <p>{{ $comment['text'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Comment Form -->
                    <div class="blog-details-contact">
                        <div class="blog-details-contact-title">
                            <h4>Leave A Comment</h4>
                        </div>
                        <form action="#" method="post">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="contact-input-box">
                                        <input type="text" name="name" placeholder="Full Name*" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="contact-input-box">
                                        <input type="email" name="email" placeholder="Email Address*" required>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="contact-input-box">
                                        <input type="text" name="website" placeholder="Your Website">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="contact-input-box">
                                        <textarea name="message" placeholder="Write Comments..." required></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-check-box">
                                        <input type="checkbox" id="saveInfo">
                                        <label for="saveInfo">Save your email info in the browser for next comments.</label>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="blog-details-submi-button">
                                        <button type="submit">Post Comment</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

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
<!-- End Echofy Blog Details Area -->
<!--==================================================-->
@endsection