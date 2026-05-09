<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;

class UserController 
{
public function index()
{
    $users = User::where('role', 'user')
                ->latest()
                ->paginate(10);

    return view('admin.pages.users.index', compact('users'));
}


}

