@php
    $faqList = $faqs->take(10);

    $leftFaqs = $faqList->take(ceil($faqList->count() / 2));
    $rightFaqs = $faqList->slice(ceil($faqList->count() / 2));
@endphp
<section class="faq-section py-5">
    <div class="container">

        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">

                <span class="ab-badge">
                    <i class="ti ti-help-circle"></i>
                    Frequently Asked Questions
                </span>

                <h2 class="section-title mt-3">
                    Have Questions? We Have Answers.
                </h2>

                <p class="section-desc">
                    Find quick answers to the most common questions about our products,
                    orders, shipping, payments and customer support.
                </p>

            </div>
        </div>

        <div class="row g-4">

            <!-- Left FAQ -->
            <div class="col-lg-6">

                <div class="accordion custom-faq" id="faqLeft">

                    @foreach($leftFaqs as $index => $faq)

                        <div class="accordion-item">

                            <h2 class="accordion-header">

                                <button
                                    class="accordion-button {{ $index != 0 ? 'collapsed' : '' }}"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#faq{{ $faq->id }}"
                                    aria-expanded="{{ $index==0 ? 'true' : 'false' }}">

                                 

                                    <span class="faq-question">
                                        {{ $faq->question }}
                                    </span>

                                </button>

                            </h2>

                            <div
                                id="faq{{ $faq->id }}"
                                class="accordion-collapse collapse {{ $index==0 ? 'show' : '' }}"
                                data-bs-parent="#faqLeft">

                                <div class="accordion-body">
                                    {!! $faq->answer !!}
                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

            <!-- Right FAQ -->
            <div class="col-lg-6">

                <div class="accordion custom-faq" id="faqRight">

                    @foreach($rightFaqs as $index => $faq)

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

        @if($faqs->count()==0)
            <div class="text-center py-5">
                <h5>No FAQs Available</h5>
            </div>
        @endif

    </div>
</section>


