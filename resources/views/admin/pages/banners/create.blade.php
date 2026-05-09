@extends('admin.layouts.app')

@section('title', 'Home Page')

@section('content')

<form action="{{ route('banners.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <input type="file" name="image" required><br><br>

    <input type="text" name="link" placeholder="https://example.com"><br><br>

    <input type="number" name="priority" placeholder="Priority"><br><br>

    <input type="text" name="position" value="home_slider"><br><br>

    <label>
        <input type="checkbox" name="is_active" checked> Active
    </label><br><br>

    <button type="submit">Save</button>
</form>

@endsection
