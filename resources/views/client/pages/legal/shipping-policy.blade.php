@extends('client.layouts.app')
@section('content')

<section class="my-5">
    
    
<div class="container my-5">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-5">

        <h1 class="text-center  fw-bold mb-4">Shipping Policy</h1>

        <p class="fs-5 mb-3">
          We offer <strong>free shipping</strong> on all prepaid orders above <strong>INR 600</strong>.
        </p>

        <p class="fs-5 mb-3">
          We charge a small fee of <strong>INR 50</strong> on COD (Cash on Delivery) orders.
        </p>

        <p class="fs-5 mb-3">
          The Service generally has a <strong>delivery ETA of 3–5 days</strong> from the date of dispatch.
        </p>

        <p class="fs-5 mb-3">
          The products are generally dispatched within <strong>24 hours</strong> of receiving the order.
        </p>

        <p class="fs-5 mb-3">
          For any return or refund related queries, please reach out to us at 
          <a href="mailto:support@gosowa.com" class="text-decoration-none text-primary fw-semibold">
            support@gosowa.com
          </a>.
        </p>

        <div class="text-center mt-4">
          <a href="javascript:history.back()" class="btn btn-outline-secondary me-2">← Back</a>

        </div>

      </div>
    </div>
  </div>
</section>
@endsection
