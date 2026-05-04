<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use App\Services\NotificationService;

class NotificationController 
{
    public function index()
    {
        $data = NotificationService::getActive(auth()->id());
        return response()->json($data);
    }

    public function store(Request $request)
    {
        NotificationService::send(
            auth()->id(),
            $request->title,
            $request->message,
            $request->type
        );

        return back();
        
    }

    public function markRead($id)
    {
        NotificationService::markAsRead($id, auth()->id());
        return response()->json(['success' => true]);
    }
}

