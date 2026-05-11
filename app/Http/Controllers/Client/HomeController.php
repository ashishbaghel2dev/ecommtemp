<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\HomePageService;

class HomeController extends Controller
{
    public function index(HomePageService $homePageService)
    {
        $data = $homePageService->getHomePageData();

        return view('client.home.home', $data);
    }

    
}