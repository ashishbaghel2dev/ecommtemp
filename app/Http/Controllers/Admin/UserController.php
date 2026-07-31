<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
public function index()
{
    $users = User::where('role', 'user')
                ->latest()
                ->paginate(10);

    return view('admin.pages.users.index', compact('users'));
}

public function show(User $user)
{
    return redirect()->route('sales.customers.show', $user);
}

}
