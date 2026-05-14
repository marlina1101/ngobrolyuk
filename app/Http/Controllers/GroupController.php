<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
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