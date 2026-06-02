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
            'receiver_id' => 'required|exists:users,id',
            'message'     => 'nullable|string',
            'file'        => 'nullable|file|max:2048',
        ]);

        // minimal harus ada text atau file
        if (
            !$request->filled('message')
            && !$request->hasFile('file')
        ) {
            return response()->json([
                'error' => 'Pesan kosong'
            ], 422);
        }

        $filePath = null;

        // upload file
        if ($request->hasFile('file')) {

            $filePath = $request
                ->file('file')
                ->store(
                    'chat_files',
                    'public'
                );
        }

        // simpan message
        $message = Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message'     => $request->message,
            'file'        => $filePath,
        ]);

        // load sender relation
        $message->load('sender');

        // realtime broadcast
        broadcast(
            new MessageSent($message)
        )->toOthers();

        // response AJAX
        if (
            $request->ajax()
            || $request->wantsJson()
        ) {

            return response()->json([
                'message' => $message,
            ]);
        }

        return back();
    }
}