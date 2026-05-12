  
  @extends('admin.layouts.app')

@section('title', 'Categories')

@section('content')


  <div class="main-content">

 
        <div class="top-bar">
            <h2 class="page-title">Whishlisted Products</h2>
            <p class="page-subtitle"> Create and manage categories</p>
        
        </div>

        @if(session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif



        <!-- Table Card -->
        <div class="table-card">
            <table class="custom-table">
        <thead>
            <tr>
                    <th>#</th>
                <th>Product Image</th>
                <th>Product Name</th>
                <th>SKU</th>
                <th>Total Wishlists</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $key =>$product)
            <tr>
                   <td>{{ $key+1}}</td>
                <td>
                    <img src="{{ asset($product->image ?: $product->images->first()->image) }}"
                                             alt="{{ $product->name }}"
                                             class="table-img">
                </td>
               
                <td>{{ $product->name }}</td>
                <td>{{ $product->sku }}</td>
                <td>
                    <span class="badge bg-primary">
                        {{ $product->wishlists_count }}
                    </span>
                </td>
                <td>
            

    <a
        href="{{ route('admin.wishlist.users', $product->id) }}"
        class="btn-icon edit"
    >
<i class="ti ti-eye"></i>
     
    </a>

</td>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    {{ $products->links() }}
</div>
  </div>

  

@endsection