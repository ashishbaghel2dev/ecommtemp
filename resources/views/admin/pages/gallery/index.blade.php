@extends('admin.layouts.app')

@section('title', 'Gallery')

@section('content')
    <section class="banner-page">
        <div class="banner-list-head">
            <h2>Gallery</h2>
            <p>Create and manage Gallery</p>
            <a href="{{ route('gallery.create') }}" class="banner-primary-btn">
                <i class="ti ti-plus"></i>
                <span>Add Gallery</span>
            </a>
        </div>

        @if(session('success'))
            <div class="banner-alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="banner-table-card">
            <table class="banner-table gallery-table">
                <thead>
                    <tr>
                        <th>S.No</th>
                     
                        <th>Image</th>
                        <th>Title</th>
                        <th>Alt Text</th>
                        <th>Sort Order</th>
                        <th>Status</th>
             
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($galleries as $gallery)
                        <tr>
                            <td class="serial-cell">{{ $loop->iteration }}</td>
                          
                            <td>
                                <img class="gallery-thumb" src="{{ asset($gallery->image) }}" alt="{{ $gallery->alt_text ?: $gallery->title }}">
                            </td>
                            <td>{{ $gallery->title }}</td>
                            <td>{{ $gallery->alt_text ?: 'No alt text' }}</td>
                            <td>{{ $gallery->sort_order }}</td>
                            <td>
                                <span class="banner-status {{ $gallery->status ? 'active' : 'inactive' }}">
                                    {{ $gallery->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
      
                            <td>
                                <div class="banner-actions">
                                    <a href="{{ route('gallery.edit', $gallery) }}" class="banner-icon-btn" aria-label="Edit gallery">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('gallery.destroy', $gallery) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="banner-icon-btn" title="Move to bin" aria-label="Move gallery image to bin" onclick="return confirm('Move this gallery image to bin?')">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">
                                <div class="banner-empty">
                                    <i class="ti ti-photo"></i>
                                    <strong>No gallery images yet</strong>
                                    <span>Create your first gallery image to show it here.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
