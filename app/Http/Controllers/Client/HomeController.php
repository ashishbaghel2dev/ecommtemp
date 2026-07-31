<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AboutPart;
use App\Services\HomePageService;

class HomeController extends Controller
{
    public function index(HomePageService $homePageService)
    {
        $data = $homePageService->getHomePageData();

        return view('client.home.home', $data);
    }

    public function about()
    {
        $aboutPart = AboutPart::query()
            ->where('status', true)
            ->latest()
            ->first();

        return view('client.pages.about', compact('aboutPart'));
    }
    
}
