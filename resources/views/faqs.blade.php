@extends('layouts.app')

@section('title', 'Echofy - FAQ')

@section('content')
    <!-- Breadcrumb Area -->
    <div class="breadcumb-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <div class="breadcumb-content">
                        <div class="breadcumb-title">
                            <h4>FAQ</h4>
                        </div>
                        <ul>
                            <li><a href="{{ url('/') }}"><img src="{{ asset('assets/images/inner-images/breadcumb-text-shape.png') }}" alt="">Echofy</a></li>
                            <li>FAQ</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQs Area -->
    <div class="faqs-area inner">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="faqs-left">
                        <div class="section-title left">
                            <h4><img src="{{ asset('assets/images/home1/section-shape.png') }}" alt="">FAQ</h4>
                            <h1>Frequently Asked</h1>
                            <h1>Questions</h1>
                        </div>
                        <div class="faqs-thumb">
                            <img src="{{ asset('assets/images/inner-images/faqs-thumb.png') }}" alt="">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="faqs-container">
                        <div class="faq-singular">
                            <h2 class="faq-question"><img src="{{ asset('assets/images/home2/shape.png') }}" alt="">How to Clean a Sea Beach Properly?</h2>
                            <div class="faq-answer">
                                <div class="desc">
                                    Distinctively plagiarize ubiquitous mindshare goal-oriented collaboration 
                                    idea-sharing. Efficiently transition dynamic initiatives to business testing 
                                    procedures enthusiastically negotiate high
                                </div>
                            </div>
                        </div>
                        <div class="faq-singular">
                            <h2 class="faq-question"><img src="{{ asset('assets/images/home2/shape.png') }}" alt="">How to Save Earth Using Trees?</h2>
                            <div class="faq-answer">
                                <div class="desc">
                                    Distinctively plagiarize ubiquitous mindshare goal-oriented collaboration 
                                    idea-sharing. Efficiently transition dynamic initiatives to business testing 
                                    procedures enthusiastically negotiate high
                                </div>
                            </div>
                        </div>
                        <div class="faq-singular">
                            <h2 class="faq-question"><img src="{{ asset('assets/images/home2/shape.png') }}" alt="">When to Plant Seedlings?</h2>
                            <div class="faq-answer">
                                <div class="desc">
                                    Distinctively plagiarize ubiquitous mindshare goal-oriented collaboration 
                                    idea-sharing. Efficiently transition dynamic initiatives to business testing 
                                    procedures enthusiastically negotiate high
                                </div>
                            </div>
                        </div>
                        <div class="faq-singular">
                            <h2 class="faq-question"><img src="{{ asset('assets/images/home2/shape.png') }}" alt="">Can I Donate Money Using the Website?</h2>
                            <div class="faq-answer">
                                <div class="desc">
                                    Distinctively plagiarize ubiquitous mindshare goal-oriented collaboration 
                                    idea-sharing. Efficiently transition dynamic initiatives to business testing 
                                    procedures enthusiastically negotiate high
                                </div>
                            </div>
                        </div>
                        <div class="faq-singular">
                            <h2 class="faq-question"><img src="{{ asset('assets/images/home2/shape.png') }}" alt="">When to Plant Seedlings?</h2>
                            <div class="faq-answer">
                                <div class="desc">
                                    Distinctively plagiarize ubiquitous mindshare goal-oriented collaboration 
                                    idea-sharing. Efficiently transition dynamic initiatives to business testing 
                                    procedures enthusiastically negotiate high
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
    document.querySelectorAll('.faq-question').forEach(question => {
        question.addEventListener('click', () => {
            const answer = question.nextElementSibling;
            const isVisible = answer.style.display === 'block';
            
            // Hide all answers
            document.querySelectorAll('.faq-answer').forEach(ans => {
                ans.style.display = 'none';
            });
            
            // Toggle the clicked answer
            answer.style.display = isVisible ? 'none' : 'block';
        });
    });
</script>
@endpush