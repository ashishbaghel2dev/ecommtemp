@extends('client.layouts.app')

@section('content')

<section class="policy-page py-5">

    <div class="container">

        <div class="heading-section text-center mb-5">
            <p>Privacy & Payment Policy</p>
            <h3>Go Sowa Herbal Tea</h3>
        </div>

        <div class="policy-card">
            <h2 class="policy-card-title">1. Payment & Security</h2>

            <p class="policy-text">
                We accept the following secure payment methods for your herbal tea orders:
            </p>

            <ul class="policy-list">
                <li>UPI</li>
                <li>Credit and Debit Cards</li>
                <li>Digital Wallets</li>
                <li>Net Banking</li>
            </ul>

            <p class="policy-text">
                Your payment security is our priority. All transactions are processed through trusted and encrypted gateways.
                For any queries, please call us at
                <strong>
                    <a href="tel:+919638666602" class="policy-link">
                        +91 9638666602
                    </a>
                </strong>
            </p>
        </div>

        <div class="policy-card">
            <h2 class="policy-card-title">2. Data Protection</h2>

            <p class="policy-text">
                At Go Sowa Herbal Tea, we respect your privacy. All personal information shared during purchase is handled with care and in accordance with applicable data protection laws.
            </p>

            <p class="policy-text">
                Your debit/credit card details are never stored in our systems and are always transmitted securely via encrypted channels. We will never ask you to share sensitive details through email or SMS.
            </p>

            <p class="policy-text">
                We do not sell or share your information with third-party companies. Occasionally, we may send you updates about new herbal tea blends, offers, and wellness tips. You may opt out anytime by contacting us.
            </p>
        </div>

        <div class="policy-card">
            <h2 class="policy-card-title">3. Privacy Policy</h2>

            <p class="policy-text">
                For detailed information on how Go Sowa Herbal Tea collects, uses, and protects your data, please refer to our main Privacy Policy.
            </p>
        </div>

    </div>

</section>
<style>

    /*=========================
    Privacy & Payment Policy
==========================*/

.policy-page{
    background:#f8faf8;
}

.policy-card{
    background:#fff;
    border-radius:16px;
    padding:40px;
    margin-bottom:30px;
    box-shadow:0 8px 25px rgba(0,0,0,.08);
}

.policy-card-title{
    font-size:28px;
    font-weight:700;
    color:#1d4d2f;
    margin-bottom:20px;
}

.policy-text{
    font-size:16px;
    color:#555;
    line-height:1.9;
    margin-bottom:18px;
}

.policy-list{
    margin:20px 0;
    padding-left:22px;
}

.policy-list li{
    font-size:16px;
    color:#555;
    margin-bottom:12px;
    line-height:1.8;
}

.policy-link{
    color:#2d8b57;
    text-decoration:none;
    font-weight:600;
    transition:.3s;
}

.policy-link:hover{
    color:#1d4d2f;
    text-decoration:underline;
}

@media(max-width:768px){

    .policy-card{
        padding:25px 20px;
    }

    .policy-card-title{
        font-size:22px;
    }

    .policy-text,
    .policy-list li{
        font-size:15px;
    }

}
    </style>
@endsection