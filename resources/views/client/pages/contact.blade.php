@extends('client.layouts.app')

@section('title', 'Contact Us | Go Sowa')
@section('meta_description', 'Contact Go Sowa for premium herbal teas, wellness products, bulk orders, dealership opportunities, and customer support.')
@section('meta_keywords', 'Go Sowa contact, herbal tea, wellness products, natural health, bulk orders, dealership, customer support')
@section('breadcrumb_title', 'Contact Us')

@section('content')

<section class="contact-page">
    <div class="container">

  <div class="contact-page-head">
    <span class="section-label">Contact Go Sowa</span>

    <h1>Get in Touch</h1>

    <p>
        We are here to help. Contact us for product information, order support, or any questions about Go Sowa.
    </p>
</div>
        <!-- Contact Cards -->
        <div class="contact-info-grid">

            <a href="tel:+919876543210" class="contact-info-card">
                <i class="ti ti-phone-call"></i>
                <span>Call Us</span>
                <strong>+91 9818610666</strong>
            </a>

            <a href="https://maps.app.goo.gl/xC6xyg54Nk8eXJqbA" target="_blank" class="contact-info-card">
<i class="ti ti-map-2"></i>
                <span>Adress</span>
                <strong>Plot No. 379, Niti Khand-III
Near Peepal Chowk,
Indirapuram, Ghaziabad - 201014
Uttar Pradesh, India</strong>
            </a>

            <a href="mailto:support@gosowa.com" class="contact-info-card">
                <i class="ti ti-mail"></i>
                <span>Email</span>
                <strong>support@gosowa.com</strong>
            </a>

        </div>

        <div class="contact-main-layout">

            <!-- Left Panel -->
            <aside class="contact-quick-panel">
  <div class="contact-page-head">

            <span class="section-label">Visit Us</span>

            <h2>Find Go Sowa</h2>

        </div>
  <iframe
   src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d16249.310932208635!2d77.374497!3d28.643695!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d1f9be3b25ec5%3A0x1f0270dadc0f5bd8!2sGo%20Sowa!5e1!3m2!1sen!2sin!4v1783666062865!5m2!1sen!2sin" 

                width="100%"
                height="450"
                style="border:0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>

            </aside>

            <!-- Form -->
            <div class="contact-form-card">

                <div class="contact-form-head">
                    <span class="section-label">Send Us a Message</span>
                    <h2>We're Happy to Assist You.</h2>
                </div>

                @if(session('success'))
                    <div class="contact-alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if(isset($errors) && $errors->any())
                    <div class="contact-error">
                        Please correct the highlighted fields and try again.
                    </div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST">

                    @csrf

                    <div class="contact-form-grid">

                        <label>
                            <span>Name <em>*</em></span>

                            <input
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Enter your full name"
                                required>

                            @if($errors->has('name'))
                                <small>{{ $errors->first('name') }}</small>
                            @endif
                        </label>

                        <label>
                            <span>Phone <em>*</em></span>

                            <input
                                type="tel"
                                name="phone"
                                value="{{ old('phone') }}"
                                placeholder="+91 12345 67890"
                                required>

                            @if($errors->has('phone'))
                                <small>{{ $errors->first('phone') }}</small>
                            @endif
                        </label>

                        <label class="contact-email-field">
                            <span>Email</span>

                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="example@email.com">

                            @if($errors->has('email'))
                                <small>{{ $errors->first('email') }}</small>
                            @endif
                        </label>

                        <label class="contact-message-field">
                            <span>Message <em>*</em></span>

                            <textarea
                                name="message"
                                rows="6"
                                placeholder="Tell us about your inquiry, or any questions you have."
                                required>{{ old('message') }}</textarea>

                            @if($errors->has('message'))
                                <small>{{ $errors->first('message') }}</small>
                            @endif
                        </label>

                    </div>

                    <button type="submit" class="contact-submit-btn">
                        <i class="ti ti-send"></i>
                        <span>Send Message</span>
                    </button>

                </form>

            </div>

        </div>

    </div>
</section>

<!-- Google Map -->

<section class="contact-map-section">

    <div class="container">

      

        <div class="contact-map">


          
        </div>

    </div>

</section>

@endsection