@extends('client.layouts.app')

@section('title', 'Tags')
@section('breadcrumb_title', 'Blog Tags')

@section('content')
    <section class="tags-page">
        <div class="container">
            <div class="tag-page-list">
                @forelse($tags as $tag)
                    <a href="{{ route('client.tags.show', $tag->slug) }}">
                        <span>{{ $tag->title }}</span>
                        <i class="ti ti-arrow-right"></i>
                    </a>
                @empty
                    <div class="blog-empty">
                        <i class="ti ti-tags"></i>
                        <strong>No tags available</strong>
                        <span>Add tags from blog meta tags in admin dashboard.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
