<x-app-layout>

<script>
    window.currentUserId   = {{ Auth::id() }};
    window.currentUserName = @json(Auth::user()->name);
    window.selectedUserId  = null; // grup tidak pakai selected user
</script>

<div class="max-w-6xl mx-auto py-6 px-4">

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex gap-4 h-[700px]">

        {{-- ============ SIDEBAR KANAN: INFO GRUP ============ --}}
        <div class="w-72 flex-shrink-0 bg-white rounded-2xl shadow border border-gray-100 flex flex-col">

            {{-- Header grup --}}
            <div class="bg-blue-600 text-white p-5 rounded-t-2xl">
                <h2 class="text-xl font-bold">👥 {{ $group->name }}</h2>
                <p class="text-sm opacity-80 mt-1">
                    Admin: {{ $group->creator->name ?? '-' }}
                </p>
            </div>

            {{-- Daftar anggota --}}
            <div class="p-4 flex-1 overflow-y-auto">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">
                    Anggota ({{ $group->users->count() }})
                </p>
                <div class="space-y-2">
                    @foreach($group->users as $member)
                        <div class="flex items-center gap-3 py-2">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center
                                        text-blue-600 font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-700">
                                    {{ $member->name }}
                                    @if($group->isCreator($member->id))
                                        <span class="text-xs text-blue-500 font-normal">(Admin)</span>
                                    @endif
                                    @if($member->id == Auth::id())
                                        <span class="text-xs text-gray-400 font-normal">(Kamu)</span>
                                    @endif
                                </div>
                                {{-- Badge online/offline — diupdate otomatis oleh app.js --}}
                                <div
                                    class="text-xs online-badge"
                                    data-user-id="{{ $member->id }}"
                                    data-offline-class="text-gray-400"
                                    data-online-class="text-green-600 font-semibold"
                                >● Offline</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Tambah anggota — hanya creator --}}
            @if($group->isCreator(Auth::id()))
                <div class="p-4 border-t">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">
                        Tambah Anggota
                    </p>
                    <form method="POST" action="{{ route('groups.addMember', $group->id) }}">
                        @csrf
                        <select name="user_id"
                                class="w-full border rounded-lg px-3 py-2 text-sm mb-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="">-- Pilih user --</option>
                            @foreach($allUsers as $user)
                                @if(!$group->hasMember($user->id))
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <button type="submit"
                                class="w-full bg-blue-600 text-white py-2 rounded-lg text-sm font-semibold hover:bg-blue-700">
                            + Tambah
                        </button>
                    </form>
                </div>
            @endif

            {{-- Kembali ke daftar grup --}}
            <div class="p-4 border-t">
                <a href="{{ route('groups.index') }}"
                   class="block text-center text-sm text-gray-500 hover:text-blue-600">
                    ← Kembali ke Daftar Grup
                </a>
            </div>

        </div>

        {{-- ============ MAIN: CHAT AREA ============ --}}
        <div class="flex-1 bg-white rounded-2xl shadow border border-gray-100 flex flex-col">

            {{-- Header --}}
            <div class="bg-blue-600 text-white p-5 rounded-t-2xl">
                <h2 class="text-xl font-bold">{{ $group->name }}</h2>
                <p class="text-sm opacity-80">{{ $group->users->count() }} anggota</p>
            </div>

            {{-- Pesan --}}
            <div id="chat-box" class="flex-1 overflow-y-auto p-6 bg-gray-50 space-y-4">

                @forelse($messages as $msg)

                    @if($msg->sender_id == Auth::id())
                        {{-- PESAN SENDIRI --}}
                        <div class="flex justify-end">
                            <div class="max-w-md">
                                <div class="bg-blue-600 text-white px-4 py-3 rounded-2xl rounded-tr-sm shadow">
                                    <div class="text-xs opacity-70 mb-1">Kamu</div>
                                    {{ $msg->message }}
                                </div>
                                <div class="text-right text-xs text-gray-400 mt-1">
                                    {{ $msg->created_at->format('H:i') }}
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- PESAN ANGGOTA LAIN --}}
                        <div class="flex justify-start gap-2">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center
                                        text-blue-600 font-bold text-sm flex-shrink-0 mt-1">
                                {{ strtoupper(substr($msg->sender->name, 0, 1)) }}
                            </div>
                            <div class="max-w-md">
                                <div class="bg-white px-4 py-3 rounded-2xl rounded-tl-sm shadow">
                                    <div class="text-xs font-bold text-blue-600 mb-1">
                                        {{ $msg->sender->name }}
                                    </div>
                                    {{ $msg->message }}
                                </div>
                                <div class="text-xs text-gray-400 mt-1">
                                    {{ $msg->created_at->format('H:i') }}
                                </div>
                            </div>
                        </div>
                    @endif

                @empty
                    <div class="text-center text-gray-400 mt-16">
                        <div class="text-4xl mb-3">💬</div>
                        <p>Belum ada pesan. Mulai percakapan!</p>
                    </div>
                @endforelse

            </div>

            {{-- Typing indicator --}}
            <div id="typing-indicator" class="px-6 py-1 text-sm text-gray-400 italic min-h-[24px]"></div>

            {{-- Form kirim pesan - HANYA ANGGOTA --}}
            <div class="border-t bg-white p-4 rounded-b-2xl">
                <form id="group-chat-form" method="POST"
                      action="{{ route('groups.message', $group->id) }}"
                      class="flex gap-2">
                    @csrf
                    <input
                        id="group-message-input"
                        type="text"
                        name="message"
                        placeholder="Tulis pesan ke grup..."
                        autocomplete="off"
                        class="flex-1 border rounded-2xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    <button type="submit"
                            class="bg-blue-600 text-white px-6 py-3 rounded-2xl hover:bg-blue-700 font-semibold">
                        Kirim
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>

<script>
    // Auto scroll ke bawah
    const chatBox = document.getElementById('chat-box');
    if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;

    // Kirim pesan grup via AJAX
    const groupForm = document.getElementById('group-chat-form');
    if (groupForm) {
        groupForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const input = document.getElementById('group-message-input');
            if (!input.value.trim()) return;

            const formData = new FormData(groupForm);
            try {
                await fetch(groupForm.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData,
                });
                // Tampilkan pesan sendiri langsung
                appendGroupMessageLocal(input.value.trim());
                groupForm.reset();
            } catch (err) {
                console.error('Gagal kirim pesan grup:', err);
            }
        });
    }

    function appendGroupMessageLocal(text) {
        const chatBox = document.getElementById('chat-box');
        if (!chatBox) return;
        const now = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        chatBox.insertAdjacentHTML('beforeend', `
            <div class="flex justify-end">
                <div class="max-w-md">
                    <div class="bg-blue-600 text-white px-4 py-3 rounded-2xl rounded-tr-sm shadow">
                        <div class="text-xs opacity-70 mb-1">Kamu</div>
                        ${text}
                    </div>
                    <div class="text-right text-xs text-gray-400 mt-1">${now}</div>
                </div>
            </div>
        `);
        chatBox.scrollTop = chatBox.scrollHeight;
    }
</script>

</x-app-layout>