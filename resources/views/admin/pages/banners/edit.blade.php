@extends('admin.layouts.app')

@section('title', 'Home Page')

@section('content')
<form action="{{ route('banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <img src="{{ asset('storage/'.$banner->image) }}" width="150"><br><br>

    <input type="file" name="image"><br><br>

    <input type="text" name="link" value="{{ $banner->link }}"><br><br>

    <input type="number" name="priority" value="{{ $banner->priority }}"><br><br>

    <input type="text" name="position" value="{{ $banner->position }}"><br><br>

    <label>
        <input type="checkbox" name="is_active" {{ $banner->is_active ? 'checked' : '' }}> Active
    </label><br><br>

    <button type="submit">Update</button>
</form>


@endsection
