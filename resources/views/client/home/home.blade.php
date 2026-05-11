@extends('client.layouts.app')

@section('title', 'Home Page')

@section('content')

<style>
    .container{
        width: 90%;
        margin: 40px auto;
    }

</style>



<div class="container">

 @include('client.pages.products.products')
</div>

@endsection