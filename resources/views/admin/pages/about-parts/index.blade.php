@extends('admin.layouts.app')

@section('title', 'About Parts')

@section('content')
    <section class="banner-page">
        <div class="banner-list-head">
            <h2>About Parts</h2>
            <p>Create and manage About section content</p>
            <a href="{{ route('about-parts.create') }}" class="banner-primary-btn">
                <i class="ti ti-plus"></i>
                <span>Add About Part</span>
            </a>
        </div>

        @if(session('success'))
            <div class="banner-alert">{{ session('success') }}</div>
        @endif

        <div class="banner-table-card">
            <table class="banner-table about-part-table">
                <thead>
                    <tr>
                        <th>S.No</th>
 
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Short Description</th>
                        <th>Description</th>
                        <th>Images</th>
                        <th>Status</th>
           
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($aboutParts as $aboutPart)
                        <tr>
                            <td class="serial-cell">{{ $loop->iteration }}</td>

                            <td>{{ $aboutPart->title }}</td>
                            <td>{{ $aboutPart->slug }}</td>
                            <td>
                                <span class="faq-answer">{{ $aboutPart->short_description ? \Illuminate\Support\Str::limit(strip_tags($aboutPart->short_description), 100) : 'No short description' }}</span>
                            </td>
                            <td>
                                <span class="faq-answer">{{ \Illuminate\Support\Str::limit(strip_tags($aboutPart->description), 120) }}</span>
                            </td>
                            <td>
                                <div class="about-part-thumbs">
                                    @foreach(['image_1', 'image_2', 'image_3'] as $imageField)
                                        @if($aboutPart->{$imageField})
                                            <img class="about-part-thumb" src="{{ asset($aboutPart->{$imageField}) }}" alt="{{ $aboutPart->title }}">
                                        @else
                                            <span class="about-part-thumb empty"><i class="ti ti-photo"></i></span>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <span class="banner-status {{ $aboutPart->status ? 'active' : 'inactive' }}">
                                    {{ $aboutPart->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            
                            <td>
                                <div class="banner-actions">
                                    <a href="{{ route('about-parts.edit', $aboutPart) }}" class="banner-icon-btn" aria-label="Edit about part">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('about-parts.destroy', $aboutPart) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="banner-icon-btn" title="Move to bin" aria-label="Move about part to bin" onclick="return confirm('Move this about part to bin?')">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">
                                <div class="banner-empty">
                                    <i class="ti ti-info-circle"></i>
                                    <strong>No about parts yet</strong>
                                    <span>Create your first about part to show it here.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
