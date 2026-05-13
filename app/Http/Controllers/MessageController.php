<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required'
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'message' => $request->message
        ]);

        return back();
    }
}