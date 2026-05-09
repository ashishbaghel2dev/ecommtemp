
@extends('admin.layouts.app')

@section('title', 'Home Page')

@section('content')


<div class="container">

    <h2 class="mb-4">Create Product Label</h2>

    <form action="{{ route('productlabels.store') }}"
          method="POST">

        @csrf

        <div class="mb-3">
            <label>Name</label>

            <input type="text"
                   name="name"
                   class="form-control"
                   required>
        </div>

        <div class="mb-3">
            <label>Color</label>

            <input type="color"
                   name="color"
                   class="form-control form-control-color">
        </div>

        <div class="mb-3">

            <label>
                <input type="checkbox"
                       name="is_active"
                       checked>

                Active
            </label>

        </div>

        <button type="submit" class="btn btn-success">
            Save Label
        </button>

    </form>

</div>


@endsection