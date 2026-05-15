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
            'message'     => 'nullable',
            'file'        => 'nullable|file|max:2048',
        ]);

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')
                ->store('chat_files', 'public');
        }

        $message = Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message'     => $request->message,
            'file'        => $filePath,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        // Jika request dari AJAX (fetch), return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => $message,
            ]);
        }

        return back();
    }
}