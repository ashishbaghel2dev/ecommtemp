<a href="{{ route('social-links.create') }}">Add Link</a>

<table border="1">
    <tr>
        <th>Name</th>
        <th>Icon</th>
        <th>URL</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    @foreach($links as $link)
    <tr>
        <td>{{ $link->name }}</td>
        <td><i class="{{ $link->icon }}"></i></td>
        <td>{{ $link->url }}</td>
        <td>{{ $link->is_active ? 'Active' : 'Inactive' }}</td>
        <td>
            <a href="{{ route('social-links.edit', $link->id) }}">Edit</a>

            <form action="{{ route('social-links.destroy', $link->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button>Delete</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>
