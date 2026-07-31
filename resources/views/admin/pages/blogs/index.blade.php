@extends('admin.layouts.app')

@section('title', 'Blogs')

@section('content')
    <section class="banner-page">
        <div class="banner-list-head">
            <h2>Blogs</h2>
            <p>Create and manage Blogs</p>
            <a href="{{ route('blogs.create') }}" class="banner-primary-btn">
                <i class="ti ti-plus"></i>
                <span>Add Blog</span>
            </a>
        </div>

        @if(session('success'))
            <div class="banner-alert">{{ session('success') }}</div>
        @endif

        <div class="banner-table-card">
            <table class="banner-table blog-table">
                <thead>
                    <tr>
                        <th>S.No</th>

                        <th>Title</th>
                        <th>Slug</th>
                        <th>Category</th>
                        <th>Image</th>
                        <th>Description</th>
                        <th>Meta Keyword</th>
                        <th>Meta Tags</th>
                        <th>Publish</th>
                        <th>Status</th>
                        <th>Sort Order</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($blogs as $blog)
                        <tr>
                            <td class="serial-cell">{{ $blogs->firstItem() + $loop->index }}</td>
          
                            <td>{{ $blog->title }}</td>
                            <td>{{ $blog->slug }}</td>
                            <td>{{ $blog->category ?: 'No category' }}</td>
                            <td>
                                @if($blog->image)
                                    <img class="blog-thumb" src="{{ asset($blog->image) }}" alt="{{ $blog->image_alt ?: $blog->title }}">
                                @else
                                    <span class="muted-text">No image</span>
                                @endif
                            </td>
                            <td><span class="faq-answer">{{ $blog->description ? \Illuminate\Support\Str::limit(strip_tags($blog->description), 120) : 'No description' }}</span></td>
                            <td><span class="faq-answer">{{ $blog->meta_keyword ?: 'No keywords' }}</span></td>
                            <td><span class="tag-list-preview">{{ $blog->meta_tags ?: 'No tags' }}</span></td>
                            <td>
                                <span class="banner-status {{ $blog->publish_status === 'posted' ? 'active' : 'inactive' }}">
                                    {{ $blog->publish_status === 'posted' ? 'Posted' : 'Draft' }}
                                </span>
                            </td>
                            <td>
                                <span class="banner-status {{ $blog->status ? 'active' : 'inactive' }}">
                                    {{ $blog->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ $blog->sort_order }}</td>
                            <td>{{ optional($blog->created_at)->format('d M Y') }}</td>
                            <td>{{ optional($blog->updated_at)->format('d M Y') }}</td>
                            <td>
                                <div class="banner-actions">
                                    <a href="{{ route('blogs.edit', $blog) }}" class="banner-icon-btn" aria-label="Edit blog">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('blogs.destroy', $blog) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="banner-icon-btn" title="Move to bin" aria-label="Move blog to bin" onclick="return confirm('Move this blog to bin?')">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15">
                                <div class="banner-empty">
                                    <i class="ti ti-news"></i>
                                    <strong>No blogs yet</strong>
                                    <span>Create your first blog to show it here.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($blogs->hasPages())
                <div class="trash-pagination">
                    {{ $blogs->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
