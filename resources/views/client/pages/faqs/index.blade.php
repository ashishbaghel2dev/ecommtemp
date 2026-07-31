@extends('client.layouts.app')

@section('title', 'FAQ')
@section('meta_description', 'Find answers about Adzone retail branding, BTL activations, event campaigns, display fixtures, timelines, and inquiry support.')
@section('meta_keywords', 'Adzone FAQ, retail branding questions, BTL activation questions, campaign support')
@section('breadcrumb_title', 'Frequently Asked Questions')

@section('content')
   @php
    $displayFaqs = $faqs->take(70);
    $leftFaqs = $displayFaqs->take(ceil($displayFaqs->count() / 2));
    $rightFaqs = $displayFaqs->slice(ceil($displayFaqs->count() / 2));
@endphp

<section class="faq-section-page py-5">
    <div class="container">

        <!-- Section Heading -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">

              

                <h2 class="section-title mt-3">
                    Have Questions? We Have Answers.
                </h2>

            

            </div>
        </div>

        <div class="row g-4">

            <!-- Left Column -->
            <div class="col-lg-6">
                <div class="accordion custom-faq" id="faqLeft">

                    @foreach($leftFaqs as $index => $faq)

                        <div class="accordion-item">

                            <h2 class="accordion-header">

                                <button
                                    class="accordion-button {{ $index == 0 ? '' : 'collapsed' }}"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#faq{{ $faq->id }}"
                                    aria-expanded="{{ $index == 0 ? 'true' : 'false' }}">

                                    <span class="faq-question">
                                        {{ $faq->question }}
                                    </span>

                                </button>

                            </h2>

                            <div
                                id="faq{{ $faq->id }}"
                                class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                                data-bs-parent="#faqLeft">

                                <div class="accordion-body">
                                    {!! $faq->answer !!}
                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-6">
                <div class="accordion custom-faq" id="faqRight">

                    @foreach($rightFaqs as $faq)

                        <div class="accordion-item">

                            <h2 class="accordion-header">

                                <button
                                    class="accordion-button collapsed"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#faq{{ $faq->id }}"
                                    aria-expanded="false">

                                    <span class="faq-question">
                                        {{ $faq->question }}
                                    </span>

                                </button>

                            </h2>

                            <div
                                id="faq{{ $faq->id }}"
                                class="accordion-collapse collapse"
                                data-bs-parent="#faqRight">

                                <div class="accordion-body">
                                    {!! $faq->answer !!}
                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>
            </div>

        </div>

        @if($displayFaqs->count() == 0)
            <div class="text-center py-5">
                <h5>No FAQs Available</h5>
            </div>
        @endif

    </div>
</section>
@endsection
