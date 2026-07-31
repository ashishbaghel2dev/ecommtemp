@extends('admin.layouts.app')

@section('title', 'Tags')

@section('content')
    <section class="banner-page">
        <div class="banner-list-head">
            <h2>Tags</h2>
            <p>Tags auto-generate from Blog meta tags</p>
            <span></span>
        </div>

        @if(session('success'))
            <div class="banner-alert">{{ session('success') }}</div>
        @endif

        <div class="banner-table-card">
            <table class="banner-table tag-table">
                <thead>
                    <tr>
                        <th>S.No</th>
           
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Meta Description</th>
                        <th>Status</th>
                        <th>Sort Order</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tags as $tag)
                        <tr>
                            <td class="serial-cell">{{ $loop->iteration }}</td>
                           
                            <td>{{ $tag->title }}</td>
                            <td>{{ $tag->slug }}</td>
                            <td><span class="faq-answer">{{ $tag->description ? \Illuminate\Support\Str::limit(strip_tags($tag->description), 120) : 'No description' }}</span></td>
                            <td><span class="faq-answer">{{ $tag->meta_description ?: 'No meta description' }}</span></td>
                            <td>
                                <span class="banner-status {{ $tag->status ? 'active' : 'inactive' }}">
                                    {{ $tag->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ $tag->sort_order }}</td>
                            <td>{{ optional($tag->created_at)->format('d M Y') }}</td>
                            <td>{{ optional($tag->updated_at)->format('d M Y') }}</td>
                            <td>
                                <div class="banner-actions">
                                    <a href="{{ route('tags.edit', $tag) }}" class="banner-icon-btn" aria-label="Edit tag">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('tags.destroy', $tag) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="banner-icon-btn" title="Move to bin" aria-label="Move tag to bin" onclick="return confirm('Move this tag to bin?')">
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
                                    <i class="ti ti-tags"></i>
                                    <strong>No tags yet</strong>
                                    <span>Add meta tags in a blog to generate tags automatically.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
