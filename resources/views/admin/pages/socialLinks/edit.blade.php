<form action="{{ route('social-links.update', $social_link->id) }}" method="POST">
    @csrf
    @method('PUT')

    <input type="text" name="name" value="{{ $social_link->name }}" required><br><br>

    <input type="text" name="url" value="{{ $social_link->url }}" required><br><br>

    <input type="text" name="icon" value="{{ $social_link->icon }}"><br><br>

    <input type="number" name="priority" value="{{ $social_link->priority }}"><br><br>

    <label>
        <input type="checkbox" name="is_active" {{ $social_link->is_active ? 'checked' : '' }}> Active
    </label><br><br>

    <button type="submit">Update</button>
</form>
