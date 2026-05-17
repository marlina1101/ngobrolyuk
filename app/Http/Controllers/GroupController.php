<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Models\Message;
use App\Models\GroupMessage;
use App\Events\GroupMessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    // Daftar semua grup
    public function index()
    {
        $groups = Group::with(['users', 'creator'])->get();

        // Semua user selain diri sendiri (untuk form tambah anggota)
        $allUsers = User::where('id', '!=', Auth::id())->get();

        return view('groups.index', compact('groups', 'allUsers'));
    }

    // Buat grup baru
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:users,id',
        ]);

        $group = Group::create([
            'name'       => $request->name,
            'created_by' => Auth::id(),
        ]);

        // Otomatis creator jadi anggota
        $members = [$group->created_by];

        // Tambah anggota yang dipilih
        if ($request->member_ids) {
            $members = array_merge($members, $request->member_ids);
        }

        $group->users()->attach(array_unique($members));

        return back()->with('success', 'Grup berhasil dibuat!');
    }

    // Halaman chat grup — hanya anggota yang boleh masuk
    public function show($id)
    {
        $group = Group::with(['users', 'creator'])->findOrFail($id);

        // Cek apakah user saat ini adalah anggota grup
        if (!$group->hasMember(Auth::id())) {
            abort(403, 'Kamu bukan anggota grup ini.');
        }

        $messages = GroupMessage::with('sender')
            ->where('group_id', $id)
            ->orderBy('created_at')
            ->get();

        $allUsers = User::where('id', '!=', Auth::id())->get();

        return view('groups.show', compact('group', 'messages', 'allUsers'));
    }

    // Kirim pesan ke grup — hanya anggota yang boleh
    public function sendMessage(Request $request, $id)
    {
        $group = Group::findOrFail($id);

        // Cek keanggotaan
        if (!$group->hasMember(Auth::id())) {
            abort(403, 'Kamu bukan anggota grup ini.');
        }

        $request->validate([
            'message' => 'required|string',
        ]);

        $message = GroupMessage::create([
            'group_id'  => $id,
            'sender_id' => Auth::id(),
            'message'   => $request->message,
        ]);

        broadcast(new GroupMessageSent($message->load('sender')))->toOthers();

        return back();
    }

    // Tambah anggota ke grup — hanya creator yang boleh
    public function addMember(Request $request, $id)
    {
        $group = Group::findOrFail($id);

        if (!$group->isCreator(Auth::id())) {
            abort(403, 'Hanya pembuat grup yang bisa menambah anggota.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Cegah duplikat anggota
        if (!$group->hasMember($request->user_id)) {
            $group->users()->attach($request->user_id);
        }

        return back()->with('success', 'Anggota berhasil ditambahkan!');
    }

    // Hapus grup — hanya creator yang boleh
    public function destroy($id)
    {
        $group = Group::findOrFail($id);

        if (!$group->isCreator(Auth::id())) {
            abort(403, 'Hanya pembuat grup yang bisa menghapus grup.');
        }

        $group->delete();

        return redirect()->route('groups.index')->with('success', 'Grup berhasil dihapus.');
    }
}