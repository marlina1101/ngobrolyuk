<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GroupMessage;
use App\Events\GroupMessageSent;


class GroupController extends Controller
{
public function sendMessage(Request $request, $id)
{
    $request->validate([
        'message' => 'required'
    ]);

    $message = GroupMessage::create([
        'group_id' => $id,
        'sender_id' => auth()->id(),
        'message' => $request->message
    ]);

    broadcast(new GroupMessageSent($message))->toOthers();

    return back();
}

public function show($id)
{
    $group = Group::with('users')->findOrFail($id);

    $messages = GroupMessage::with('sender')
                ->where('group_id', $id)
                ->orderBy('created_at')
                ->get();

    return view('groups.show', compact(
        'group',
        'messages'
    ));
}
    public function index()
    {
        $groups = Group::with('users')->get();

        return view('groups.index', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $group = Group::create([
            'name' => $request->name
        ]);

        // otomatis creator masuk group
        $group->users()->attach(Auth::id());

        return back();
    }
}