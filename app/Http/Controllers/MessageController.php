<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;

class MessageController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'receiver_id' => 'required',
        'message' => 'required'
    ]);

    $message = Message::create([
        'sender_id' => Auth::id(),
        'receiver_id' => $request->receiver_id,
        'message' => $request->message
    ]);

    broadcast(new MessageSent($message))->toOthers();

    return back();
}
}