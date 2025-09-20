@extends('layouts.app')

@section('title', 'Echofy - Contact Us')

@section('content')
    <!-- Breadcrumb Area -->
    <div class="breadcumb-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <div class="breadcumb-content">
                        <div class="breadcumb-title">
                            <h4>Contact Us</h4>
                        </div>
                        <ul>
                            <li><a href="/"><img src="{{ asset('assets/images/inner-images/breadcumb-text-shape.png') }}" alt="">Echofy</a></li>
                            <li>Contact Us</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Area -->
    <div class="contact-area">
        <div class="container">
            <div class="row contact-info-bg">
                <div class="col-lg-4 col-md-6">
                    <div class="contact-info-box">
                        <div class="contact-info-icon">
                            <img src="{{ asset('assets/images/inner-images/contact-info-icon-1.png') }}" alt="">
                        </div>
                        <div class="contact-info-content">
                            <h4>Office Address</h4>
                            <p>200 Navana ST, West end Right Sandigo, USA</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="contact-info-box">
                        <div class="contact-info-icon">
                            <img src="{{ asset('assets/images/inner-images/contact-info-icon-2.png') }}" alt="">
                        </div>
                        <div class="contact-info-content">
                            <h4>Make A Call</h4>
                            <p>+880 172 6546 020</p>
                            <p>+880 172 6546 020</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="contact-info-box two">
                        <div class="contact-info-icon">
                            <img src="{{ asset('assets/images/inner-images/contact-info-icon-3.png') }}" alt="">
                        </div>
                        <div class="contact-info-content">
                            <h4>24/7 Supports</h4>
                            <p>example@gmail.com</p>
                            <p>info@gmail.com</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row pagla-shahin align-items-center">
                <div class="col-lg-6">
                    <div class="google-map">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3021.066200910237!2d-73.9681583234562!3d40.78255873329217!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c2589a018531e3%3A0xb9df1f7387a94119!2sCentral%20Park!5e0!3m2!1sen!2sbd!4v1705590690304!5m2!1sen!2sbd"></iframe>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="section-title left">
                        <h4><img src="{{ asset('assets/images/home1/section-shape.png') }}" alt="">Get In Touch</h4>
                        <h1>Write us Something</h1>
                    </div>
                    <form>
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="single-input-box">
                                    <input type="text" name="name" placeholder="Enter Your Name*" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="single-input-box">
                                    <input type="email" name="email" placeholder="Enter Your Email*" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="single-input-box">
                                    <input type="text" name="phone" placeholder="Phone Number*" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="single-input-box">
                                    <input type="text" name="subject" placeholder="Subject*" required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="single-input-box">
                                    <textarea name="message" placeholder="Write Message"></textarea>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="input-check">
                                    <input type="checkbox" name="agree_terms">
                                    <span>I agree with the Terms & Conditions</span>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="contact-submit-button">
                                    <button type="submit">Submit Message</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection