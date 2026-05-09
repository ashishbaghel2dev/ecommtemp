<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminDashboardController 
{
    public function index()
    {
        return view('admin.pages.home.home');
    }
}
