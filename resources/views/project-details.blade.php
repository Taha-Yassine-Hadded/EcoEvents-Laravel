@extends('layouts.app')

@section('title', 'Echofy - Project Details')

@section('content')
    <!-- Breadcrumb Area -->
    <div class="breadcumb-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <div class="breadcumb-content">
                        <div class="breadcumb-title">
                            <h4>Project Details</h4>
                        </div>
                        <ul>
                            <li><a href="{{ url('/') }}"><img src="{{ asset('assets/images/inner-images/breadcumb-text-shape.png') }}" alt="">Echofy</a></li>
                            <li>Project Details</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Details Area -->
    <div class="project-details-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="project-details-thumb">
                        <img src="{{ asset('assets/images/inner-images/project-details-thumb.jpg') }}" alt="">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="project-details-content">
                                <h4>Rapidiously Formulate Efficient</h4>
                                <p class="project-details-desc">Alternative innovation to ethical network environmental whiteboard pursue compelling results for premier methods empowerment. Dramatically architect go forward opportunities before user-centric partnerships. Credibly implement exceptional</p>
                                <p class="project-details-desc">Continually fashion orthogonal leadership skills whereas wireless metrics. Uniquely syndicate exceptional opportunities with interdependent users. Globally enhance fully tested meta-services rather than pandemic solutions. Proactively integrate client-integrated go forward architectures and turnkey meta-services. Interactively harness integrated ROI whereas frictionless products.</p>
                                <h3>Quickly Parallel Task</h3>
                                <p class="project-details-desc">Innovate wireless e-markets for inexpensive e-markets. Monotonectally grow progressive processes before seamless ideas facilitate an expanded array of scenarios rather than backend users. Objectively impact intuitive users and low-risk high-yield networks. Conveniently supply visionary</p>
                                <div class="row align-items-center">
                                    <div class="col-lg-6">
                                        <div class="project-details-item-images">
                                            <img src="{{ asset('assets/images/inner-images/project-details-item-images.jpg') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="project-details-list-item">
                                            <h4>Project Benefits</h4>
                                            <ul>
                                                <li><i class="bi bi-check-circle-fill"></i>Innovate wireless market</li>
                                                <li><i class="bi bi-check-circle-fill"></i>Productivate resource sucking</li>
                                                <li><i class="bi bi-check-circle-fill"></i>Proactively unleash oriented communities</li>
                                                <li><i class="bi bi-check-circle-fill"></i>Credibly develop progressive architecture</li>
                                                <li><i class="bi bi-check-circle-fill"></i>Phosfluorescently to customer</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <a class="project-details-text" href="{{ url('/project-details') }}">Competently architect intermandated deliverables client niches continually underwhelm</a>
                                <p class="project-details-desc">Appropriately communicate economically sound e-commerce after enterprise services. Dramatically target cross-media solutions and error-free platforms. Monotonectally pontificate 24/365 human capital and dynamic potentialities compellingly pursue</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="project-details-right">
                                <div class="project-details-info">
                                    <p>Clients:</p>
                                    <h6>Dream It Solutions</h6>
                                </div>
                                <div class="project-details-info">
                                    <p>Category:</p>
                                    <h6>Environment</h6>
                                </div>
                                <div class="project-details-info">
                                    <p>Starting Date:</p>
                                    <h6>12 December, 2024</h6>
                                </div>
                                <div class="project-details-info">
                                    <p>End Date:</p>
                                    <h6>20 December, 2024</h6>
                                </div>
                                <div class="project-details-info">
                                    <p>Project Value:</p>
                                    <h6>$6500.00</h6>
                                </div>
                            </div>
                            <div class="widget-sidber-contact-box">
                                <div class="widget-sidber-contact">
                                    <img src="{{ asset('assets/images/inner-images/sidber-cont-icon.png') }}" alt="">
                                </div>
                                <p class="widget-sidber-contact-text">Call Us Anytime</p>
                                <h3 class="widget-sidber-contact-number">+123 (4567) 890</h3>
                                <span class="widget-sidber-contact-gmail"><i class="bi bi-envelope-fill"></i> mailto:example@gmail.com</span>
                                <div class="widget-sidber-contact-btn">
                                    <a href="{{ url('/contact') }}">Contact Us <i class="bi bi-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Similar Completed Projects Area -->
    <div class="project-area inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="section-title center">
                        <h4><img src="{{ asset('assets/images/home1/section-shape.png') }}" alt="">Latest Works</h4>
                        <h1>Similar Completed Projects</h1>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="single-project-box">
                        <div class="project-thumb">
                            <img src="{{ asset('assets/images/home1/project-1.jpg') }}" alt="">
                        </div>
                        <div class="project-content">
                            <h4>Climate</h4>
                            <a href="{{ url('/project-details') }}">Cleaning Forest</a>
                            <a class="project-button" href="{{ url('/project-details') }}">View Details<i class="bi bi-arrow-right-short"></i></a>
                            <div class="project-shape">
                                <img src="{{ asset('assets/images/home1/project-shape.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-project-box">
                        <div class="project-thumb">
                            <img src="{{ asset('assets/images/home1/project-2.jpg') }}" alt="">
                        </div>
                        <div class="project-content">
                            <h4>Environment</h4>
                            <a href="{{ url('/project-details') }}">Climate Solutions</a>
                            <a class="project-button" href="{{ url('/project-details') }}">View Details<i class="bi bi-arrow-right-short"></i></a>
                            <div class="project-shape">
                                <img src="{{ asset('assets/images/home1/project-shape.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-project-box">
                        <div class="project-thumb">
                            <img src="{{ asset('assets/images/home1/project-3.jpg') }}" alt="">
                        </div>
                        <div class="project-content">
                            <h4>Recycling</h4>
                            <a href="{{ url('/project-details') }}">Plastic Recycling</a>
                            <a class="project-button" href="{{ url('/project-details') }}">View Details<i class="bi bi-arrow-right-short"></i></a>
                            <div class="project-shape">
                                <img src="{{ asset('assets/images/home1/project-shape.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection