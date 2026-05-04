<a href="{{ route('banners.create') }}">Add Banner</a>

<table border="1">
    <tr>
        <th>Image</th>
        <th>Link</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    @foreach($banners as $banner)
    <tr>
        <td>
            <img src="{{ asset('storage/'.$banner->image) }}" width="120">
        </td>
        <td>{{ $banner->link }}</td>
        <td>{{ $banner->is_active ? 'Active' : 'Inactive' }}</td>
        <td>
  <a href="{{ route('banners.edit', $banner->id) }}">Edit</a> 

            <form action="{{ route('banners.destroy', $banner->id) }}" method="POST">
                @csrf
                @method('DELETE')
              <button onclick="return confirm('Delete this banner?')">Delete</button>
            </form> 
        </td>
    </tr>
    @endforeach
</table>
