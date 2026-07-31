@extends('admin.layouts.app')

@section('title', 'FAQs')

@section('content')
    <section class="banner-page">
        <div class="banner-list-head">
            <h2>FAQ</h2>
            <p>Create and manage FAQ</p>
            <a href="{{ route('faqs.create') }}" class="banner-primary-btn">
                <i class="ti ti-plus"></i>
                <span>Add FAQ</span>
            </a>
        </div>

        @if(session('success'))
            <div class="banner-alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="banner-table-card">
            <table class="banner-table faq-table">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Question</th>
                        <th>Answer</th>
                        <th>Written By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faqs as $faq)
                        <tr>
                            <td class="serial-cell">{{ $loop->iteration }}</td>
                            <td>
                                <strong class="faq-question">{{ $faq->question }}</strong>
                            </td>
                            <td>
                                <span class="faq-answer">{{ \Illuminate\Support\Str::limit(strip_tags($faq->answer), 140) }}</span>
                            </td>
                            <td>{{ $faq->written_by ?: 'Admin' }}</td>
                            <td>
                                <div class="banner-actions">
                                    <a href="{{ route('faqs.edit', $faq) }}" class="banner-icon-btn" aria-label="Edit FAQ">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('faqs.destroy', $faq) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="banner-icon-btn" title="Move to bin" aria-label="Move FAQ to bin" onclick="return confirm('Move this FAQ to bin?')">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="banner-empty">
                                    <i class="ti ti-message-question"></i>
                                    <strong>No FAQs yet</strong>
                                    <span>Create your first FAQ to show it here.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
