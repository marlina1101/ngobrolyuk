<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $messages = Message::with('sender')
                    ->latest()
                    ->get();

        return view('chat.index', compact('messages'));
    }
}