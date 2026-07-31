@extends('admin.layouts.app')

@section('title', 'Inquiries')

@section('content')
    <section class="banner-page">
        <div class="banner-list-head">
            <h2>Inquiries</h2>
            <p>Manage contact form inquiries</p>
            <a href="{{ route('contact') }}" class="banner-primary-btn" target="_blank">
                <i class="ti ti-external-link"></i>
                <span>Public Form</span>
            </a>
        </div>

        @if(session('success'))
            <div class="banner-alert">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('inquiries.index') }}" class="inquiry-filter-card">
            <label class="banner-field">
                <span>Search</span>
                <input type="search" class="adsearch-input" name="search" value="{{ $search }}" placeholder="Name, phone, email, or message">
            </label>

            <label class="banner-field">
                <span>Status</span>
                <select name="status">
                    <option value="">All Status</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" {{ $activeStatus === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <div class="inquiry-filter-actions">
                <button type="submit" class="banner-primary-btn">
                    <i class="ti ti-filter"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('inquiries.index') }}" class="banner-secondary-btn">Reset</a>
            </div>
        </form>

        <div class="banner-table-card">
            <table class="banner-table inquiry-table">
                <thead>
                    <tr>
                        <th>S.No</th>

                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inquiries as $inquiry)
                        <tr>
                            <td class="serial-cell">{{ $loop->iteration }}</td>
                       
                            <td>{{ $inquiry->name }}</td>
                            <td><a href="tel:{{ $inquiry->phone }}">{{ $inquiry->phone }}</a></td>
                            <td>
                                @if($inquiry->email)
                                    <a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a>
                                @else
                                    <span class="muted-text">No email</span>
                                @endif
                            </td>
                            <td><span class="faq-answer">{{ \Illuminate\Support\Str::limit(strip_tags($inquiry->message), 140) }}</span></td>
                            <td>
                                <form method="POST" action="{{ route('inquiries.status', $inquiry) }}" class="inquiry-status-form">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="inquiry-status-select {{ $inquiry->status }}" onchange="this.form.submit()">
                                        @foreach($statuses as $value => $label)
                                            <option value="{{ $value }}" {{ $inquiry->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td>{{ optional($inquiry->created_at)->format('d M Y') }}</td>
                            <td>{{ optional($inquiry->updated_at)->format('d M Y') }}</td>
                            <td>
                                <div class="banner-actions">
                                    <form method="POST" action="{{ route('inquiries.destroy', $inquiry) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="banner-icon-btn" title="Move to bin" aria-label="Move inquiry to bin" onclick="return confirm('Move this inquiry to bin?')">
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
                                    <i class="ti ti-message-circle"></i>
                                    <strong>No inquiries found</strong>
                                    <span>Contact form inquiries will appear here.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
