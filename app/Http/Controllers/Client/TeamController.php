<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\DoctorTeam;
use Illuminate\Contracts\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        $members = DoctorTeam::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return view('client.pages.team.index', compact('members'));
    }

    public function founder(): View
    {
        $founder = DoctorTeam::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->first();

        return view('client.pages.team.founder', compact('founder'));
    }
}
