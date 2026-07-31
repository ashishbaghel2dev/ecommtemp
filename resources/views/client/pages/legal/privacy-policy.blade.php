@extends('client.layouts.app')

@section('content')

<section class="privacy-page py-5">
    <div class="container">

        <div class="privacy-header text-center mb-5">
            <h1>Privacy Policy</h1>

            <button class="btn btn-outline-secondary back-btn mt-3" onclick="history.back()">
                ← Back
            </button>
        </div>

        <div class="privacy-card">

            <section class="privacy-section">
                <p>
                    At <strong>Go Sowa</strong>, we prioritise the privacy of our visitors with utmost importance and safety.
                    This policy details the steps we take to preserve and safely guard your privacy and information when you visit
                    or communicate with our website or personnel. First and foremost, we do not rent, sell or trade any of your
                    personal information (like name, address, phone, credit card info, etc.) to any third party without your
                    permission. A detailed explanation of how we store and use your personal information is provided in this Privacy
                    Policy below. Please read the following thoroughly to understand our practices and measures to safeguard your
                    personal data.
                </p>
            </section>

            <section class="privacy-section">
                <h2>Data Collected From You</h2>

                <p>We may collect and process the following data about you:</p>

                <ul class="privacy-list">
                    <li>
                        Data that you provide by filling out forms on our website,
                        <a href="https://www.gosowa.com" target="_blank">www.gosowa.com</a>,
                        like purchase or registration. We may keep a record of the information given by you when you contact us.
                    </li>

                    <li>Details of transactions you carry out through our website.</li>

                    <li>
                        Details of your visits to our website including, but not limited to, traffic data,
                        location data, weblogs, and other communication data.
                    </li>
                </ul>

                <p>
                    We partner with Microsoft Clarity and Microsoft Advertising to capture how you use and interact
                    with our website through behavioral metrics, heatmaps, and session replay to improve and market
                    our products/services.
                </p>

                <p>
                    For more information visit
                    <a href="https://privacy.microsoft.com/en-us/privacystatement" target="_blank">
                        Microsoft Privacy Statement
                    </a>.
                </p>
            </section>

            <section class="privacy-section">
                <h2>Cookies and IP Addresses</h2>

                <p>
                    We may gather data about your computer, including your IP address,
                    operating system, and browser type.
                </p>

                <p>
                    We may also collect information about your general Internet usage using cookies.
                    You may disable cookies in your browser settings, although some website features
                    may not function correctly.
                </p>
            </section>

            <section class="privacy-section">
                <h2>How Do We Protect Your Personal Data</h2>

                <p>
                    The data collected from you is stored on secured servers and protected using
                    SSL (Secure Socket Layer) encryption.
                </p>

                <p>
                    While we use industry-standard security measures, no Internet transmission
                    can be guaranteed to be 100% secure.
                </p>
            </section>

            <section class="privacy-section">
                <h2>How Your Data Is Used</h2>

                <p>We may use your personal information to:</p>

                <ul class="privacy-list">
                    <li>Provide the best experience on our website.</li>
                    <li>Inform you about products and services.</li>
                    <li>Process your purchases.</li>
                    <li>Notify you about updates and changes.</li>
                </ul>

                <p>
                    We never store customer credit card details.
                </p>

                <p>
                    To unsubscribe from our updates, email us at
                    <a href="mailto:support@gosowa.com">support@gosowa.com</a>.
                </p>

                <p>
                    Third-party websites linked from our website have their own privacy policies.
                </p>
            </section>

            <section class="privacy-section">
                <h2>Changes to Our Privacy Policy</h2>

                <p>
                    Updates to this Privacy Policy will be published on this page.
                </p>
            </section>

            <section class="privacy-section">
                <h2>Contact</h2>

                <p>
                    For any questions regarding this Privacy Policy, please contact us at
                    <a href="mailto:support@gosowa.com">support@gosowa.com</a>.
                </p>
            </section>

        </div>

    </div>
</section>

@endsection