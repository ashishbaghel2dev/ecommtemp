<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class DashboardController 
{
    public function index()
    {
        return view('client.dashboard.home');
    }
}
