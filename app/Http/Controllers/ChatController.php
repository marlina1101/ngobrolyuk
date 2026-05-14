<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index($id = null)
    {
        $users = User::where('id', '!=', Auth::id())->get();

        // Ambil last message tiap user
        foreach ($users as $user) {

            $lastMessage = Message::where(function ($query) use ($user) {

                $query->where('sender_id', Auth::id())
                      ->where('receiver_id', $user->id);

            })->orWhere(function ($query) use ($user) {

                $query->where('sender_id', $user->id)
                      ->where('receiver_id', Auth::id());

            })->latest()->first();

            $user->last_message = $lastMessage;
        }

        $selectedUser = null;
        $messages = collect();

        if ($id) {

            $selectedUser = User::findOrFail($id);

            $messages = Message::where(function ($query) use ($id) {

                $query->where('sender_id', Auth::id())
                      ->where('receiver_id', $id);

            })->orWhere(function ($query) use ($id) {

                $query->where('sender_id', $id)
                      ->where('receiver_id', Auth::id());

            })->orderBy('created_at')->get();
        }

        return view('chat.index', compact(
            'users',
            'messages',
            'selectedUser'
        ));
    }
}