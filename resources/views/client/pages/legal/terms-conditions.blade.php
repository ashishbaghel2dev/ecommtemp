@extends('client.layouts.app')

@section('content')

<section class="terms-page py-5">
    <div class="container">

        <div class="terms-header text-center mb-5">
            <h1 class="terms-title">Terms & Conditions</h1>

            <button class="terms-back-btn" onclick="history.back()">
                ← Back
            </button>
        </div>

        <div class="terms-card">

            <section class="terms-section">
                <p class="terms-text">
                    These terms and conditions apply to all the products that we sell through our website
                    <a href="https://www.gosowa.com" target="_blank" class="terms-link">www.gosowa.com</a>.
                    Please note that you agree to accept these terms and conditions by purchasing any of our products.
                </p>
            </section>

            <section class="terms-section">
                <h2 class="terms-heading">Who We Are</h2>

                <p class="terms-text">
                    Go Sowa is based in Delhi NCR, India, where you will embrace the natural goodness of our Herbal Teas.
                    Our registered Head Office is at Plot No. 379, Niti Khand III, Near Pipal Chowk,
                    Indirapuram, Ghaziabad – 201014.
                </p>
            </section>

            <section class="terms-section">
                <h2 class="terms-heading">Ordering & Shipping</h2>

                <p class="terms-text">
                    You can order products through our website. After receiving your order,
                    we will send an email confirming your order.
                </p>

                <p class="terms-text">
                    Once payment is received, we will confirm the receipt and dispatch of your order.
                    Dispatch generally takes 1–2 working days for Delhi NCR and varies for PAN India deliveries.
                </p>

                <p class="terms-text">
                    We reserve the right to cancel any order and refund your payment before dispatch
                    due to product unavailability or other unforeseen circumstances.
                </p>
            </section>

            <section class="terms-section">
                <h2 class="terms-heading">Prices</h2>

                <p class="terms-text">
                    All prices listed on our website are subject to change.
                    However, accepted orders will not be affected.
                </p>

                <p class="terms-text">
                    If a pricing error occurs, we will contact you before processing your order,
                    allowing you to either cancel the order or pay the revised amount.
                </p>

                <p class="terms-text">
                    Payments must be completed before dispatch through Credit Card,
                    Debit Card, UPI, or other supported payment methods.
                </p>
            </section>

            <section class="terms-section">
                <h2 class="terms-heading">Shipping</h2>

                <p class="terms-text">
                    Orders are shipped according to the delivery option selected during checkout.
                    Delivery typically takes 1–2 working days within Delhi NCR and 4–5 working days across India.
                </p>

                <p class="terms-text">
                    Shipping charges are displayed during checkout.
                    Orders are usually dispatched within 24 hours after payment confirmation.
                </p>
            </section>

            <section class="terms-section">
                <h2 class="terms-heading">Delivery</h2>

                <p class="terms-text">
                    Retail orders received before 12 PM IST are generally processed by the next working day.
                    During exceptional situations, processing may take up to 3 working days.
                </p>

                <p class="terms-text">
                    If a product becomes unavailable after purchase,
                    customers will be informed and the item will be dispatched once available.
                </p>
            </section>

            <section class="terms-section">
                <h2 class="terms-heading">Shelf Life & Storage</h2>

                <p class="terms-text">
                    Our herbal teas have a shelf life of 18 months due to premium packaging
                    and vacuum sealing.
                </p>

                <p class="terms-text">
                    Store tea in an airtight container away from moisture, heat,
                    and strong odors. Keep in a cool, dry place.
                    Freezing is not recommended.
                </p>
            </section>

            <section class="terms-section">
                <h2 class="terms-heading">Changes to Terms</h2>

                <p class="terms-text">
                    We reserve the right to update these Terms & Conditions whenever required
                    to reflect changes in our policies or legal obligations.
                </p>

                <p class="terms-text">
                    The applicable Terms & Conditions are those in effect
                    at the time your order is placed.
                </p>
            </section>

            <section class="terms-section">
                <h2 class="terms-heading">Copyright & Trademarks</h2>

                <p class="terms-text">
                    All content on this website is owned by Go Sowa or its licensors
                    and is protected under Indian copyright and trademark laws.
                </p>

                <p class="terms-text">
                    You may not copy, reproduce, publish, distribute,
                    or commercially use any website content without written permission.
                </p>

                <p class="terms-text">
                    You may download or print a single copy of the website material
                    solely for your personal, non-commercial use.
                </p>
            </section>

        </div>

    </div>
</section>

<style>/*=========================================
    Terms & Conditions Page
=========================================*/

.terms-page{
    background:#f8faf8;
}

.terms-header{
    margin-bottom:50px;
}

.terms-title{
    font-size:42px;
    font-weight:700;
    color:#1d4d2f;
    margin-bottom:18px;
}

.terms-back-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:10px 28px;
    border:2px solid #1d4d2f;
    border-radius:50px;
    background:#fff;
    color:#1d4d2f;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
    transition:all .3s ease;
}

.terms-back-btn:hover{
    background:#1d4d2f;
    color:#fff;
}

.terms-card{
    max-width:900px;
    margin:0 auto;
    background:#fff;
    padding:45px;
    border-radius:18px;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
}

.terms-section{
    margin-bottom:40px;
}

.terms-section:last-child{
    margin-bottom:0;
}

.terms-heading{
    font-size:28px;
    font-weight:700;
    color:#1d4d2f;
    margin-bottom:18px;
    position:relative;
    padding-bottom:10px;
}

.terms-heading::after{
    content:"";
    width:60px;
    height:3px;
    background:#2d8b57;
    position:absolute;
    left:0;
    bottom:0;
    border-radius:20px;
}

.terms-text{
    font-size:16px;
    line-height:1.9;
    color:#555;
    margin-bottom:18px;
}

.terms-link{
    color:#2d8b57;
    text-decoration:none;
    font-weight:600;
    transition:.3s;
}

.terms-link:hover{
    color:#1d4d2f;
    text-decoration:underline;
}

.terms-list{
    padding-left:22px;
    margin:20px 0;
}

.terms-list li{
    font-size:16px;
    color:#555;
    line-height:1.8;
    margin-bottom:12px;
}

.terms-card strong{
    color:#1d4d2f;
}

@media (max-width:992px){

    .terms-card{
        padding:35px;
    }

}

@media (max-width:768px){

    .terms-page{
        padding:50px 0;
    }

    .terms-title{
        font-size:32px;
    }

    .terms-card{
        padding:25px 20px;
        border-radius:12px;
    }

    .terms-heading{
        font-size:22px;
    }

    .terms-text,
    .terms-list li{
        font-size:15px;
        line-height:1.8;
    }

    .terms-back-btn{
        width:100%;
        max-width:180px;
    }

}
    </style>

@endsection