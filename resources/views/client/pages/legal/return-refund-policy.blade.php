@extends('client.layouts.app')

@section('content')

<section class="return-policy-section py-5">
    <div class="container">
        <div class="return-policy-card">

            <h1 class="return-policy-title">Return Policy</h1>

            <p class="return-policy-text">
                Thank you for choosing
                <span class="return-policy-highlight">Go Sowa Herbal Teas</span>.
            </p>

            <p class="return-policy-text">
                If you aren’t satisfied with your order, you can reach out to
                <a href="mailto:support@gosowa.com" class="return-policy-link">
                    support@gosowa.com
                </a>
                and our Customer Experience Team will revert back to you promptly.
            </p>

            <h2 class="return-policy-heading">Cancellations</h2>

            <p class="return-policy-text">
                Our company's purpose is to enrich your life with positivity, and this extends to the genuine love we have for our Infusions and other Ayurvedic Herbs. We’re sure that your order will reach your doorstep at the correct time, and if it does not, please let us know. We’ll make it right.
            </p>

            <p class="return-policy-text">
                <strong>No cancellation or refund policy applicable after the product has been shipped.</strong>
            </p>

            <h2 class="return-policy-heading">Replacements</h2>

            <p class="return-policy-text">
                A request for replacement must be initiated within a maximum of 5 days from the day of delivery.
            </p>

            <p class="return-policy-text">
                Please attach a picture of the delivered order along with the invoice and send a mail to
                <a href="mailto:support@gosowa.com" class="return-policy-link">
                    support@gosowa.com
                </a>.
                Our quality team will review and send you a replacement, if applicable, in the following conditions:
            </p>

            <ul class="return-policy-list">
                <li>Damaged / Incorrect Product Received</li>
                <li>Item Missing from Order</li>
                <li>Expired Product Received (Please attach a picture showing the expiry date along with the invoice)</li>
            </ul>

            <h2 class="return-policy-heading">Returns</h2>

            <p class="return-policy-text">
                Due to the perishable nature of tea, we do not accept returns on any products. However, we can replace the product if applicable.
            </p>

        </div>
    </div>

    
</section>

<style>
  /*=========================
    Return Policy
=========================*/

.return-policy-section{
    background:#f8faf8;
}

.return-policy-card{
    background:#fff;
    padding:50px;
    border-radius:16px;
    box-shadow:0 8px 30px rgba(0,0,0,.08);
}

.return-policy-title{
    font-size:40px;
    font-weight:700;
    color:#1d4d2f;
    margin-bottom:30px;
    padding-bottom:15px;
    border-bottom:2px solid #e8efe9;
}

.return-policy-heading{
    font-size:26px;
    font-weight:600;
    color:#1d4d2f;
    margin:35px 0 18px;
}

.return-policy-text{
    font-size:16px;
    line-height:1.9;
    color:#555;
    margin-bottom:18px;
}

.return-policy-highlight{
    color:#2d8b57;
    font-weight:700;
}

.return-policy-link{
    color:#2d8b57;
    text-decoration:none;
    font-weight:600;
    transition:.3s;
}

.return-policy-link:hover{
    color:#1d4d2f;
    text-decoration:underline;
}

.return-policy-list{
    padding-left:22px;
    margin-bottom:20px;
}

.return-policy-list li{
    color:#555;
    font-size:16px;
    line-height:1.8;
    margin-bottom:12px;
}

.return-policy-text strong{
    color:#c0392b;
    font-weight:700;
}

@media (max-width:768px){

    .return-policy-card{
        padding:30px 20px;
    }

    .return-policy-title{
        font-size:30px;
    }

    .return-policy-heading{
        font-size:22px;
    }

    .return-policy-text,
    .return-policy-list li{
        font-size:15px;
    }

}
</style>
@endsection