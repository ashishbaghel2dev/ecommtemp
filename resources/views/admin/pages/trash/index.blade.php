@extends('admin.layouts.app')

@section('title', $config['label'] . ' Trash')

@section('content')
    <section class="banner-page trash-page">
        <div class="banner-list-head">
            <div>
                <h2>{{ $config['label'] }} Trash</h2>
                <p>Restore deleted {{ strtolower($config['label']) }} or remove them permanently.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="banner-alert">{{ session('success') }}</div>
        @endif

        <div class="trash-layout">
            <aside class="trash-nav">
                @foreach($modules as $key => $item)
                    <a href="{{ route('trash.index', $key) }}" class="{{ $module === $key ? 'active' : '' }}">
                        <i class="{{ $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </aside>

            <div class="banner-table-card trash-table-card">
                <table class="banner-table trash-table">
                    <thead>
                        <tr>
                            <th>Deleted Item</th>
                            <th>Details</th>
                            <th>Deleted At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>
                                    <strong>{{ data_get($item, $config['primary']) ?: $config['single'] . ' #' . $item->id }}</strong>
                                </td>
                                <td>
                                    <span class="faq-answer">{{ data_get($item, $config['secondary']) ?: 'No extra detail' }}</span>
                                </td>
                                <td>{{ optional($item->deleted_at)->format('d M Y, h:i A') }}</td>
                                <td>
                                    <div class="banner-actions">
                                        <form method="POST" action="{{ route('trash.restore', [$module, $item->id]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="banner-icon-btn restore" title="Restore" aria-label="Restore {{ $config['single'] }}">
                                                <i class="ti ti-restore"></i>
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('trash.force-delete', [$module, $item->id]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="banner-icon-btn delete" title="Permanent delete" aria-label="Permanent delete {{ $config['single'] }}" onclick="return confirm('Permanently delete this item? This cannot be undone.')">
                                                <i class="ti ti-trash-x"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="banner-empty">
                                        <i class="{{ $config['icon'] }}"></i>
                                        <strong>No deleted {{ strtolower($config['label']) }}</strong>
                                        <span>Deleted records from this module will appear here.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($items->hasPages())
                    <div class="trash-pagination">
                        {{ $items->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
