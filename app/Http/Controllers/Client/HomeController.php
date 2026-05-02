<?php

namespace App\Http\Controllers\Client;

class HomeController
{
    public function index()
    {
        return view('client.home.home');
    }
}
