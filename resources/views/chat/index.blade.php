<x-app-layout>

{{-- Variabel JS diset via @push ke @stack('js-vars') di <head>, sebelum app.js jalan --}}
@push('js-vars')
<script>
    window.currentUserName = "{{ Auth::user()->name }}";
    window.currentUserId   = {{ Auth::id() }};
    window.selectedUserId  = {{ $selectedUser ? $selectedUser->id : 'null' }};
</script>
@endpush

<div class="max-w-7xl mx-auto py-6">
    <div class="bg-white shadow-2xl rounded-3xl overflow-hidden flex h-[700px]">

        <!-- ===================== SIDEBAR ===================== -->
        <div class="w-1/3 border-r bg-gray-50 flex flex-col">

            <div class="p-5 border-b">
                <h2 class="text-2xl font-bold text-blue-600">💬 NgobrolYuk</h2>
                <p class="text-sm text-gray-500">{{ Auth::user()->name }}</p>
            </div>

            <div class="overflow-y-auto flex-1">
                @foreach($users as $user)
                    <a href="{{ route('chat.index', $user->id) }}">
                        <div class="p-4 border-b hover:bg-blue-50 transition cursor-pointer {{ isset($selectedUser) && $selectedUser->id == $user->id ? 'bg-blue-50 border-l-4 border-blue-500' : '' }}">

                            <div class="flex items-center justify-between">
                                <div class="font-bold text-gray-800">{{ $user->name }}</div>

                                {{-- Badge online/offline --}}
                                <span
                                    class="text-xs px-2 py-0.5 rounded-full font-medium online-badge"
                                    data-user-id="{{ $user->id }}"
                                    data-offline-class="bg-gray-100 text-gray-400"
                                    data-online-class="bg-green-100 text-green-600"
                                >● Offline</span>
                            </div>

                            @if($user->last_message)
                                <div class="text-sm text-gray-500 truncate mt-1" data-last-message="{{ $user->id }}">
                                    @if($user->last_message->sender_id == Auth::id()) Kamu: @endif
                                    {{ $user->last_message->message ?? '📎 File' }}
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ $user->last_message->created_at->format('H:i') }}
                                </div>
                            @else
                                <div class="text-sm text-gray-400 mt-1" data-last-message="{{ $user->id }}">
                                    Belum ada pesan
                                </div>
                            @endif

                        </div>
                    </a>
                @endforeach
            </div>

        </div>

        <!-- ===================== CHAT AREA ===================== -->
        <div class="w-2/3 flex flex-col">

            @if($selectedUser)

                <!-- Header chat -->
                <div class="bg-blue-600 text-white p-5 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center font-bold text-lg">
                        {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">{{ $selectedUser->name }}</h2>
                        {{-- Status di header —  data-user-id dipakai JS --}}
                        <div
                            class="text-sm online-badge"
                            data-user-id="{{ $selectedUser->id }}"
                            data-offline-class="text-white/50"
                            data-online-class="text-green-300 font-semibold"
                        >● Offline</div>
                    </div>
                </div>

                <!-- Chat box -->
                <div id="chat-box" class="flex-1 overflow-y-auto p-6 bg-gray-100 space-y-4">

                    @forelse($messages as $msg)
                        @if($msg->sender_id == Auth::id())
                            <div class="flex justify-end">
                                <div class="max-w-md">
                                    <div class="bg-blue-600 text-white px-4 py-3 rounded-2xl rounded-tr-sm shadow">
                                        {{ $msg->message }}
                                        @if($msg->file)
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $msg->file) }}" target="_blank" class="text-blue-200 underline text-sm">📎 Lihat File</a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-right text-xs text-gray-400 mt-1">{{ $msg->created_at->format('H:i') }}</div>
                                </div>
                            </div>
                        @else
                            <div class="flex justify-start">
                                <div class="max-w-md">
                                    <div class="bg-white px-4 py-3 rounded-2xl rounded-tl-sm shadow">
                                        {{ $msg->message }}
                                        @if($msg->file)
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $msg->file) }}" target="_blank" class="text-blue-600 underline text-sm">📎 Lihat File</a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">{{ $msg->created_at->format('H:i') }}</div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center text-gray-400 mt-10">
                            <div class="text-4xl mb-2">👋</div>
                            Belum ada percakapan. Mulai kirim pesan!
                        </div>
                    @endforelse

                </div>

                <!-- Typing indicator -->
                <div id="typing-indicator" class="px-6 text-sm text-gray-400 italic min-h-[24px]"></div>

                <!-- Form kirim pesan -->
                <div class="border-t bg-white p-4">
                    <form id="chat-form" method="POST" action="{{ route('message.store') }}" enctype="multipart/form-data" class="flex gap-2">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $selectedUser->id }}">
                        <input
                            id="message-input"
                            type="text"
                            name="message"
                            placeholder="Tulis pesan..."
                            autocomplete="off"
                            class="flex-1 border rounded-2xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        <label class="flex items-center justify-center border rounded-xl px-3 cursor-pointer hover:bg-gray-50" title="Kirim File">
                            📎
                            <input type="file" name="file" class="hidden">
                        </label>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-2xl hover:bg-blue-700 font-semibold">
                            Kirim
                        </button>
                    </form>
                </div>

            @else

                <div class="flex items-center justify-center h-full bg-gray-50">
                    <div class="text-center">
                        <div class="text-6xl mb-4">💬</div>
                        <h2 class="text-2xl font-bold text-gray-700">Pilih siapa yang ingin kamu ajak ngobrol</h2>
                        <p class="text-gray-400 mt-2">Klik nama user di sebelah kiri untuk mulai chat.</p>
                    </div>
                </div>

            @endif

        </div>

    </div>
</div>

<script>
    // Auto scroll ke bawah
    const chatBox = document.getElementById('chat-box');
    if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
</script>

</x-app-layout>