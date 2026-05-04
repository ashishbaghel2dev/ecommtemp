<form action="{{ route('social-links.store') }}" method="POST">
    @csrf

    <input type="text" name="name" placeholder="Facebook" required><br><br>

    <input type="text" name="url" placeholder="https://facebook.com/yourpage" required><br><br>

    <input type="text" name="icon" placeholder="fa fa-facebook"><br><br>

    <input type="number" name="priority" placeholder="Priority"><br><br>

    <label>
        <input type="checkbox" name="is_active" checked> Active
    </label><br><br>

    <button type="submit">Save</button>
</form>
