@extends('layouts.app')

@section('title', 'Echofy - Donation Details')

@section('content')
    <!-- Breadcrumb Area -->
    <div class="breadcumb-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <div class="breadcumb-content">
                        <div class="breadcumb-title">
                            <h4>Donation Details</h4>
                        </div>
                        <ul>
                            <li><a href="{{ url('/') }}"><img src="{{ asset('assets/images/inner-images/breadcumb-text-shape.png') }}" alt="">Echofy</a></li>
                            <li><a href="{{ url('/donation') }}">Donations</a></li>
                            <li class="khela-hbe">Fund Raising for Tree Plantation 2024</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Donation Details Area -->
    <div class="donation-details-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="single-donation-box">
                                <div class="donation-thumb">
                                    <img src="{{ asset('assets/images/inner-images/donation-details-thumb.png') }}" alt="">
                                </div>
                                <div class="donation-content">
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
                            <h4 class="donation-details-title">Fund Raising for Tree Plantation - 2024</h4>
                            <p class="donation-details-desc">Continually fashion orthogonal leadership skills whereas wireless metrics. Uniquely syndicate exceptional opportunities with interdependent users. Globally enhance fully tested meta-services rather than pandemic solutions. Proactively integrate client-integrated go forward architectures and turnkey meta-services. Interactively harness integrated ROI whereas frictionless products</p>

                            <!-- Tabbed Donation Amounts -->
                            <div id="Home" class="tabcontent">
                                <h3>$10</h3>
                            </div>
                            <div id="News" class="tabcontent">
                                <h3>$15</h3>
                            </div>
                            <div id="Contact" class="tabcontent">
                                <h3>$20</h3>
                            </div>
                            <div id="About" class="tabcontent">
                                <h3>$30</h3>
                            </div>
                            <div id="Ab" class="tabcontent">
                                <h3>$50</h3>
                            </div>
                            <div id="Abc" class="tabcontent">
                                <h3>$100</h3>
                            </div>

                            <button class="tablink" data-page="Home">$10</button>
                            <button class="tablink" data-page="News" id="defaultOpen">$15</button>
                            <button class="tablink" data-page="Contact">$20</button>
                            <button class="tablink" data-page="About">$30</button>
                            <button class="tablink" data-page="Ab">$50</button>
                            <button class="tablink" data-page="Abc">$100</button>

                            <!-- Donation Form -->
                            <form action="{{ url('/donate') }}" method="post">
                                @csrf
                                <div class="contact-form-box">
                                    <div class="forms-title up">
                                        <h1>Details of You</h1>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-title">
                                                <h6>First Name*</h6>
                                            </div>
                                            <div class="form-box up">
                                                <input type="text" name="first_name" placeholder="Enter First Name" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-title">
                                                <h6>Last Name*</h6>
                                            </div>
                                            <div class="form-box up">
                                                <input type="text" name="last_name" placeholder="Enter Last Name" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-title">
                                                <h6>Email*</h6>
                                            </div>
                                            <div class="form-box up">
                                                <input type="email" name="email" placeholder="Your E-Mail Address" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-title">
                                                <h6>Phone No</h6>
                                            </div>
                                            <div class="form-box up">
                                                <input type="text" name="phone" placeholder="Enter Phone No">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-title">
                                                <h6>Address Line-1*</h6>
                                            </div>
                                            <div class="form-box up">
                                                <input type="text" name="address_line_1" placeholder="Address Line-1" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-title">
                                                <h6>Address Line-2</h6>
                                            </div>
                                            <div class="form-box up">
                                                <input type="text" name="address_line_2" placeholder="Address Line-2">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-title">
                                                <h6>City*</h6>
                                            </div>
                                            <div class="form-box up">
                                                <input type="text" name="city" placeholder="Enter Your City" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-title">
                                                <h6>Zip Code</h6>
                                            </div>
                                            <div class="form-box up">
                                                <input type="text" name="zip_code" placeholder="Enter Zip Code">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="check-content">
                                    <h5>Select Payment Method</h5>
                                    <div class="dpx">
                                        <label>
                                            <input type="radio" class="option-input radio" name="payment_method" value="paypal" checked />
                                            Paypal
                                        </label>
                                        <label>
                                            <input type="radio" class="option-input radio" name="payment_method" value="offline" />
                                            Offline Donation
                                        </label>
                                    </div>
                                </div>
                                <input type="hidden" name="amount" id="donation-amount" value="15">
                                <button type="submit" class="submit-donation">Donate Now</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="widget-sidber">
                                    <div class="widget_search">
                                        <form action="{{ url('/search') }}" method="get">
                                            <input type="text" name="s" placeholder="Search Here" title="Search for:">
                                            <button type="submit" class="icons">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="widget-sidber">
                                    <div class="widget-sidber-content">
                                        <h4>Donations</h4>
                                    </div>
                                    <div class="sidber-widget-recent-post">
                                        <div class="recent-widget-thumb">
                                            <img src="{{ asset('assets/images/inner-images/donation-details-1.png') }}" alt="">
                                        </div>
                                        <div class="recent-widget-content">
                                            <a href="{{ url('/donation-details') }}">Fund Raising for Forest Recycling & Repair</a>
                                            <p>Goal: $1000.00</p>
                                        </div>
                                    </div>
                                    <div class="sidber-widget-recent-post">
                                        <div class="recent-widget-thumb">
                                            <img src="{{ asset('assets/images/inner-images/donation-details-2.png') }}" alt="">
                                        </div>
                                        <div class="recent-widget-content">
                                            <a href="{{ url('/donation-details') }}">Environmental Dust Clean And Recycling</a>
                                            <p>Goal: $1000.00</p>
                                        </div>
                                    </div>
                                    <div class="sidber-widget-recent-post">
                                        <div class="recent-widget-thumb">
                                            <img src="{{ asset('assets/images/inner-images/donation-details-3.png') }}" alt="">
                                        </div>
                                        <div class="recent-widget-content">
                                            <a href="{{ url('/donation-details') }}">Donations for Plant Seedlings Orphan Peoples</a>
                                            <p>Goal: $1000.00</p>
                                        </div>
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
    </div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.tablink').forEach(button => {
        button.addEventListener('click', function() {
            const pageName = this.getAttribute('data-page');
            const tabcontent = document.getElementsByClassName('tabcontent');
            const tablinks = document.getElementsByClassName('tablink');
            const donationAmount = this.textContent.replace('$', '');
            
            // Hide all tab content
            for (let i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = 'none';
            }
            
            // Remove background color from all tablinks
            for (let i = 0; i < tablinks.length; i++) {
                tablinks[i].style.backgroundColor = '';
            }
            
            // Show selected tab content and set background color
            document.getElementById(pageName).style.display = 'block';
            this.style.backgroundColor = '#4caf50'; // Adjust color as per your CSS
            
            // Update hidden input for donation amount
            document.getElementById('donation-amount').value = donationAmount;
        });
    });

    // Trigger default tab
    document.getElementById('defaultOpen').click();
</script>
@endpush