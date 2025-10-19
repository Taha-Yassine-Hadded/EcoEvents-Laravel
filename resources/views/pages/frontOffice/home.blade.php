@extends('layouts.app')

@section('title', 'EcoEvents - Accueil')

@section('content')
    <!-- Toast de bienvenue personnalisé -->
    <div id="welcome-toast" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-leaf me-2"></i>
                    <span id="toast-message"></span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <!-- Put the hero, sections, etc. from index-3.html here -->
    <section class="hero">
        <!--==================================================-->
        <!-- Start Echofy Hero Area -->
        <!--==================================================-->		
            <div class="hero-area home-six d-flex align-items-center">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-12 text-center">
                            <div class="hero-content">
                                <h4 data-splitting>Solutions For Enviromental Protection</h4>
                                <h1 data-splitting>Your Journey to Solar</h1>
                                <h1 data-splitting>Empowerment</h1>
                            </div>
                            <div class="echofy-button style-five">
                                <a href="#">Find Out More<i class="bi bi-arrow-right-short"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>		
        <!--==================================================-->
        <!-- End Echofy Hero Area -->
        <!--==================================================-->




        <!--==================================================-->
        <!-- Start Echofy Feature Area Home Six-->
        <!--==================================================-->
        <div class="feature-area home-six">
            <div class="container">
                <div class="row margin-top">
                    <div class="col-lg-4 col-md-6">
                        <div class="single-feature-box">
                            <div class="feature-icon">
                                <img src="{{asset('assets/images/home6/feature-icon-1.png')}}" alt="">
                            </div>
                            <div class="feature-content">
                                <h4>Certified Engineers</h4>
                                <p>Eagle ray ray snoek rough person brown trout ropefish lake</p>
                            </div>
                            <div class="single-feature-shape">
                                <img src="{{asset('assets/images/home6/feature-shape.png')}}" alt="">
                            </div>
                        </div>
                    </div>			
                    <div class="col-lg-4 col-md-6">
                        <div class="single-feature-box">
                            <div class="feature-icon">
                                <img src="{{asset('assets/images/home6/feature-icon-2.png')}}" alt="">
                            </div>
                            <div class="feature-content">
                                <h4>Solar System Design</h4>
                                <p>Eagle ray ray snoek rough person brown trout ropefish lake</p>
                            </div>
                            <div class="single-feature-shape">
                                <img src="{{asset('assets/images/home6/feature-shape.png')}}" alt="">
                            </div>
                        </div>
                    </div>			
                    <div class="col-lg-4 col-md-6">
                        <div class="single-feature-box">
                            <div class="feature-icon">
                                <img src="{{asset('assets/images/home6/feature-icon-3.png')}}" alt="">
                            </div>
                            <div class="feature-content">
                                <h4>Solar eco-power </h4>
                                <p>Eagle ray ray snoek rough person brown trout ropefish lake</p>
                            </div>
                            <div class="single-feature-shape">
                                <img src="{{asset('assets/images/home6/feature-shape.png')}}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--==================================================-->
        <!-- End Echofy Feature Area Home Six -->
        <!--==================================================-->



        <!--==================================================-->
        <!-- Start Echofy About Area Home Six-->
        <!--==================================================-->
        <div class="about-area home-six">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="about-thumb">
                            <img src="{{asset('assets/images/home6/about-thumb.png')}}" alt="">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="section-title left">
                            <h4><img src="{{asset('assets/images/home6/section-title-shape.png')}}" alt="">Get To know Us</h4>
                            <h1 data-splitting>Innovative Renewable</h1>
                            <h1 data-splitting>Energy Solutions.</h1>
                            <p class="section-desc">
                                Sometimes goods can arrive early when they being brought into the solution
                                other times items need a place to be stored for short goods can arrive desig
                                headquarters have enough space your needs.
                            </p>
                        </div>
                        <div class="about-item-box">
                            <div class="about-item-thumb">
                                <img src="{{asset('assets/images/home6/about-item.png')}}" alt="">
                            </div>
                            <div class="about-list-item">
                                <ul>
                                    <li><i class="bi bi-square-fill"></i>Solar energy also has some limitations</li>
                                    <li><i class="bi bi-square-fill"></i>Reliability and performance</li>
                                    <li><i class="bi bi-square-fill"></i>Every day fresh and quality products</li>
                                </ul>
                            </div>
                        </div>
                        <div class="echofy-button style-five">
                            <a href="about.html">About Echofy<i class="bi bi-arrow-right-short"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--==================================================-->
        <!-- End Echofy About Area Home Six-->
        <!--==================================================-->



        <!--==================================================-->
        <!-- Start Echofy Service Title Area Home Six-->
        <!--==================================================-->
        <div class="services-title-area home-six">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="section-title left">
                            <h4><img src="{{asset('assets/images/home6/section-title-shape.png')}}" alt="">Echofy Services</h4>
                            <h1 class="text-animation-2">Pioneering Sustainable</h1>
                            <h1 class="text-animation-2">Energy Services.</h1>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <p class="section-desc">
                            Sometimes goods can arrive early when they being brought into the solution
                            other times items need a place to be stored for short goods can arrive desig
                            headquarters have enough space your needs.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!--==================================================-->
        <!-- End Echofy Service Title Area Home Six-->
        <!--==================================================-->



        <!--==================================================-->
        <!-- Start Echofy Service Area Home Six-->
        <!--==================================================-->
        <div class="service-area home-six">
            <div class="container-fulid">
                <div class="row upper">
                    <div class="service-list-6 owl-carousel">
                        <div class="col-lg-12">
                            <div class="single-service-box">
                                <div class="service-thumb">
                                    <img src="{{asset('assets/images/home6/services-1.jpg')}}" alt="">
                                    <div class="service-icon">
                                        <img src="{{asset('assets/images/home6/services-icon-1.jpg')}}" alt="">
                                    </div>
                                </div>
                                <div class="services-content">
                                    <h4>Cumulative Energy</h4>
                                    <p>Sometims goods can arrive early when ses need a place  short goods can arrive solari
                                    have space your needs.</p>
                                </div>
                                <div class="service-button">
                                    <a href="service.html">Explore More<i class="bi bi-arrow-right-short"></i></a>
                                </div>
                            </div>
                        </div>				
                        <div class="col-lg-12">
                            <div class="single-service-box">
                                <div class="service-thumb">
                                    <img src="{{asset('assets/images/home6/services-2.jpg')}}" alt="">
                                    <div class="service-icon">
                                        <img src="{{asset('assets/images/home6/services-icon-2.jpg')}}" alt="">
                                    </div>
                                </div>
                                <div class="services-content">
                                    <h4>Solar Panels Services</h4>
                                    <p>Sometims goods can arrive early when ses need a place  short goods can arrive solari
                                    have space your needs.</p>
                                </div>
                                <div class="service-button">
                                    <a href="service.html">Explore More<i class="bi bi-arrow-right-short"></i></a>
                                </div>
                            </div>
                        </div>				
                        <div class="col-lg-12">
                            <div class="single-service-box">
                                <div class="service-thumb">
                                    <img src="{{asset('assets/images/home6/services-3.jpg')}}" alt="">
                                    <div class="service-icon">
                                        <img src="{{asset('assets/images/home6/services-icon-3.jpg')}}" alt="">
                                    </div>
                                </div>
                                <div class="services-content">
                                    <h4>Certified Engineers</h4>
                                    <p>Sometims goods can arrive early when ses need a place  short goods can arrive solari
                                    have space your needs.</p>
                                </div>
                                <div class="service-button">
                                    <a href="service.html">Explore More<i class="bi bi-arrow-right-short"></i></a>
                                </div>
                            </div>
                        </div>				
                        <div class="col-lg-12">
                            <div class="single-service-box">
                                <div class="service-thumb">
                                    <img src="{{asset('assets/images/home6/services-4.jpg')}}" alt="">
                                    <div class="service-icon">
                                        <img src="{{asset('assets/images/home6/services-icon-4.jpg')}}" alt="">
                                    </div>
                                </div>
                                <div class="services-content">
                                    <h4>Consult & Planning</h4>
                                    <p>Sometims goods can arrive early when ses need a place  short goods can arrive solari
                                    have space your needs.</p>
                                </div>
                                <div class="service-button">
                                    <a href="service.html">Explore More<i class="bi bi-arrow-right-short"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--==================================================-->
        <!-- End Echofy Service Area Home Six-->
        <!--==================================================-->



        <!--==================================================-->
        <!-- Start Echofy Why Choose Area Home-Six -->
        <!--==================================================-->
        <div class="why-choose-area home-six">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="why-choose-thumb">
                        <img src="{{asset('assets/images/home6/why-choose-thumb.jpg')}}" alt="">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="section-title left">
                            <h4><img src="{{asset('assets/images/home6/section-title-shape.png')}}" alt="">Why Choose Us</h4>
                            <h1>Building a Foundation</h1>
                            <h1>Energy Solutions.</h1>
                            <p class="section-desc">
                                Sometimes goods can arrive early when they being brought into the solution
                                other times items need a place to be stored for short goods can arrive desig
                                headquarters have enough space your needs.
                            </p>
                        </div>
                        <div class="why-choose-us-item">
                            <div class="choose-us-icon">
                                <img src="{{asset('assets/images/home6/choose-us-icon-1.png')}}" alt="">
                            </div>
                            <div class="choose-us-content">
                                <h4>Solar Panel Solutions</h4>
                                <p>Sometimes goods can arrive being brought into the solution so
                                headquarters have enough space your needs.</p>
                            </div>
                        </div>				
                        <div class="why-choose-us-item">
                            <div class="choose-us-icon">
                                <img src="{{asset('assets/images/home6/choose-us-icon-1.png')}}" alt="">
                            </div>
                            <div class="choose-us-content">
                                <h4>Renewable Energy Consulting</h4>
                                <p>Sometimes goods can arrive being brought into the solution so
                                headquarters have enough space your needs.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--==================================================-->
        <!-- End Echofy Why Choose Area Home-Six -->
        <!--==================================================-->



        <!--==================================================-->
        <!-- Start Echofy Team Area Home-Six -->
        <!--==================================================-->
        <div class="team-area home-six">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <div class="section-title center">
                            <h4><img src="{{asset('assets/images/home6/section-title-shape.png')}}" alt="">Professionals Team<img class="images-2" src="{{asset('assets/images/home6/section-title-shape.png')}}" alt=""></h4>
                            <h1>Our Creative Members</h1>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="single-team-box">
                            <div class="team-thumb">
                                <img src="{{asset('assets/images/home6/team-1.png')}}" alt="">
                                <div class="team-social-icon">
                                    <ul>
                                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                        <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="team-content">
                                <h4>Muntaha Jannat</h4>
                                <p>Solar Engineer</p>
                            </div>
                        </div>
                    </div>			
                    <div class="col-lg-3 col-md-6">
                        <div class="single-team-box">
                            <div class="team-thumb">
                                <img src="{{asset('assets/images/home6/team-2.png')}}" alt="">
                                <div class="team-social-icon">
                                    <ul>
                                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                        <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="team-content">
                                <h4>Al-Amin Hosain</h4>
                                <p>Solar Engineer</p>
                            </div>
                        </div>
                    </div>			
                    <div class="col-lg-3 col-md-6">
                        <div class="single-team-box">
                            <div class="team-thumb">
                                <img src="{{asset('assets/images/home6/team-3.png')}}" alt="">
                                <div class="team-social-icon">
                                    <ul>
                                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                        <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="team-content">
                                <h4> Kevin Hardson</h4>
                                <p>Solar Engineer</p>
                            </div>
                        </div>
                    </div>			
                    <div class="col-lg-3 col-md-6">
                        <div class="single-team-box">
                            <div class="team-thumb">
                                <img src="{{asset('assets/images/home6/team-4.png')}}" alt="">
                                <div class="team-social-icon">
                                    <ul>
                                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                        <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="team-content">
                                <h4>Mostafa Kamal</h4>
                                <p>Solar Engineer</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--==================================================-->
        <!-- End Echofy Team Area Home-Six -->
        <!--==================================================-->



        <!--==================================================-->
        <!-- Start Echofy Skill Area Home-Six -->
        <!--==================================================-->
        <div class="skill-area home-six">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="section-title left">
                            <h4><img src="{{asset('assets/images/home6/section-title-shape.png')}}" alt="">Company Benefits</h4>
                            <h1>Solar Energy is a Free</h1>
                            <h1>Raw Material.</h1>
                            <p class="section-desc">
                                Sometimes goods can arrive early when they being brought into the solution
                                other times items need a place to be stored for short goods can arrive desig
                                headquarters have enough space your needs.
                            </p>
                        </div>
                        <div class="skill">
                            <p>Wind turbines</p>
                            <div class="skill-bar wow slideInLeft delay-0-4s animated animated">
                                <span class="skill-count1">80%</span>
                            </div>
                        </div>
                        <div class="skill">
                            <p>Hybrid energy</p>
                            <div class="skill-bar two wow slideInLeft delay-0-4s animated animated">
                                <span class="skill-count2">95%</span>
                            </div>
                        </div>
                        <div class="skill">
                            <p>Solar energy</p>
                            <div class="skill-bar three wow slideInLeft delay-0-4s animated animated animated">
                                <span class="skill-count3">70%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--==================================================-->
        <!-- End Echofy Skill Area Home-Six -->
        <!--==================================================-->



        <!--==================================================-->
        <!-- Start Echofy Testimonial Area Home-Six -->
        <!--==================================================-->
        <div class="testimonial-area home-six">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="section-title center">
                            <h4><img src="{{asset('assets/images/home6/section-title-shape.png')}}" alt="">Our Testimonials<img class="images-2" src="{{asset('assets/images/home6/section-title-shape.png')}}" alt=""></h4>
                            <h1>What Our Clinets Says</h1>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="testimonial-list-6 owl-carousel">
                        <div class="col-lg-12">
                            <div class="single-testimonial-box">
                            <div class="testimonial-content">
                                <p>Sometimes goods can arrive early when clients
                                    being brought into the solution creative design
                                    need place to stored for short goods can arrive
                                    desig have space your needs.</p>
                            </div>
                            <div class="testi-author">
                                    <div class="testi-author-thumb">
                                        <img src="{{asset('assets/images/home6/testi-author-1.png')}}" alt="">
                                    </div>
                                    <div class="testi-author-content">
                                        <h4>Gleen Maxwell</h4>
                                        <p>Solar Engineer</p>
                                    </div>
                                </div>
                                <div class="author-rating">
                                    <ul>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                    </ul>
                                </div>
                                <div class="testi-quote">
                                <img src="{{asset('assets/images/home6/testi-quote.png')}}" alt="">
                                </div>   
                            </div>
                        </div>			   
                        <div class="col-lg-12">
                            <div class="single-testimonial-box">
                            <div class="testimonial-content">
                                <p>Sometimes goods can arrive early when clients
                                    being brought into the solution creative design
                                    need place to stored for short goods can arrive
                                    desig have space your needs.</p>
                            </div>
                            <div class="testi-author">
                                    <div class="testi-author-thumb">
                                        <img src="{{asset('assets/images/home6/testi-author-2.png')}}" alt="">
                                    </div>
                                    <div class="testi-author-content">
                                        <h4>Gleen Evaliyan</h4>
                                        <p>Solar Engineer</p>
                                    </div>
                                </div>
                                <div class="author-rating">
                                    <ul>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                    </ul>
                                </div>
                                <div class="testi-quote">
                                <img src="{{asset('assets/images/home6/testi-quote.png')}}" alt="">
                                </div>   
                            </div>
                        </div>			    
                        <div class="col-lg-12">
                            <div class="single-testimonial-box">
                            <div class="testimonial-content">
                                <p>Sometimes goods can arrive early when clients
                                    being brought into the solution creative design
                                    need place to stored for short goods can arrive
                                    desig have space your needs.</p>
                            </div>
                            <div class="testi-author">
                                    <div class="testi-author-thumb">
                                        <img src="{{asset('assets/images/home6/testi-author-3.png')}}" alt="">
                                    </div>
                                    <div class="testi-author-content">
                                        <h4>Gleen Muktar</h4>
                                        <p>Solar Engineer</p>
                                    </div>
                                </div>
                                <div class="author-rating">
                                    <ul>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                        <li><i class="bi bi-star-fill"></i></li>
                                    </ul>
                                </div>
                                <div class="testi-quote">
                                <img src="{{asset('assets/images/home6/testi-quote.png')}}" alt="">
                                </div>   
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--==================================================-->
        <!-- End Echofy Testimonia Area Home-Six -->
        <!--==================================================-->



        <!--==================================================-->
        <!-- Start Echofy Blog Area Home-Six -->
        <!--==================================================-->
        <div class="blog-area home-six">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="section-title center">
                            <h4><img src="{{asset('assets/images/home6/section-title-shape.png')}}" alt="">Our Latest Blog<img class="images-2" src="{{asset('assets/images/home6/section-title-shape.png')}}" alt=""></h4>
                            <h1>Read the Latest News</h1>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="single-blog-box">
                            <div class="blog-thumb">
                                <img src="{{asset('assets/images/home6/blog-1.png')}}" alt="">
                            </div>
                            <div class="blog-content">
                                <div class="blog-text">
                                    <span>Renewabole / Science</span>
                                </div>
                                <a href="#">Solar-Powered Transportation</a>
                                <p>Need place to stored for short goods can arive
                                desig have space your needs brand.</p>
                                <div class="blog-button">
                                    <a href="#">Explore More<i class="bi bi-arrow-right-short"></i></a>
                                </div>
                            </div>
                            <div class="meta-blog">
                                <span>Home2021March</span>
                            </div>
                        </div>
                    </div>			
                    <div class="col-lg-4 col-md-6">
                        <div class="single-blog-box">
                            <div class="blog-thumb">
                                <img src="{{asset('assets/images/home6/blog-2.png')}}" alt="">
                            </div>
                            <div class="blog-content">
                                <div class="blog-text">
                                    <span>Renewabole / Science</span>
                                </div>
                                <a href="#">Pioneering the Future of Power</a>
                                <p>Need place to stored for short goods can arive
                                desig have space your needs brand.</p>
                                <div class="blog-button">
                                    <a href="#">Explore More<i class="bi bi-arrow-right-short"></i></a>
                                </div>
                            </div>
                            <div class="meta-blog">
                                <span>Home2021March</span>
                            </div>
                        </div>
                    </div>			
                    <div class="col-lg-4 col-md-6">
                        <div class="single-blog-box">
                            <div class="blog-thumb">
                                <img src="{{asset('assets/images/home6/blog-3.png')}}" alt="">
                            </div>
                            <div class="blog-content">
                                <div class="blog-text">
                                    <span>Renewabole / Science</span>
                                </div>
                                <a href="#">Solar-Powered Innovations</a>
                                <p>Need place to stored for short goods can arive
                                desig have space your needs brand.</p>
                                <div class="blog-button">
                                    <a href="#">Explore More<i class="bi bi-arrow-right-short"></i></a>
                                </div>
                            </div>
                            <div class="meta-blog">
                                <span>Home2021March</span>
@endsection

@push('styles')
<style>
    .toast-container .toast {
        background: linear-gradient(135deg, #28a745, #20c997) !important;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
        min-width: 350px;
    }
    
    .toast-body {
        font-weight: 500;
        font-size: 16px;
    }
    
    .toast .fas {
        color: #fff;
    }
    
    /* Fallback pour affichage manuel */
    .toast.show {
        display: block !important;
        opacity: 1 !important;
    }
    
    .toast {
        display: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Attendre que Bootstrap soit chargé
    setTimeout(function() {
        // Vérifier s'il y a un message de bienvenue dans localStorage
        const welcomeMessage = localStorage.getItem('welcome_message');
        
        // Vérifier s'il y a un message depuis le serveur (pour les routes avec role_message)
        const serverMessage = {!! json_encode(session('role_message', '')) !!};
        
        const messageToShow = welcomeMessage || serverMessage;
        
        console.log('Message à afficher:', messageToShow); // Debug
        
        if (messageToShow) {
            // Afficher le toast
            const toastElement = document.querySelector('.toast');
            const toastMessage = document.getElementById('toast-message');
            
            if (toastElement && toastMessage) {
                toastMessage.textContent = messageToShow;
                
                // Vérifier si Bootstrap Toast est disponible
                if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                    const toast = new bootstrap.Toast(toastElement, {
                        delay: 5000 // 5 secondes
                    });
                    toast.show();
                } else {
                    // Fallback : afficher le toast manuellement
                    toastElement.classList.add('show');
                    setTimeout(function() {
                        toastElement.classList.remove('show');
                    }, 5000);
                }
                
                // Supprimer le message du localStorage après affichage
                if (welcomeMessage) {
                    localStorage.removeItem('welcome_message');
                }
            } else {
                console.error('Éléments toast non trouvés'); // Debug
            }
        }
    }, 500); // Attendre 500ms
});
</script>
@endpush
