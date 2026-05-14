<x-app-layout>

<div class="max-w-7xl mx-auto py-6">

    <div class="bg-white shadow-2xl rounded-3xl overflow-hidden flex h-[700px]">

        <!-- SIDEBAR -->
        <div class="w-1/3 border-r bg-gray-50">

            <div class="p-5 border-b">

                <h2 class="text-2xl font-bold text-blue-600">
                    💬 NgobrolYuk
                </h2>

                <p class="text-sm text-gray-500">
                    {{ Auth::user()->name }}
                </p>

            </div>

            <div class="overflow-y-auto h-full">

                @foreach($users as $user)

                    <a href="{{ route('chat.index', $user->id) }}">

                        <div class="p-4 border-b hover:bg-gray-100 transition cursor-pointer">

                            <div class="font-bold text-gray-800">
                                {{ $user->name }}
                            </div>

                            @if($user->last_message)

<div
    class="text-xs mb-1"
    data-user-id="{{ $user->id }}"
>
    <span class="text-gray-400">
        ● Offline
    </span>
</div>

    <div class="text-sm text-gray-500 truncate">

        @if($user->last_message->sender_id == Auth::id())
            Kamu:
        @endif

        {{ $user->last_message->message }}

    </div>

    <div class="text-xs text-gray-400 mt-1">
        {{ $user->last_message->created_at->format('H:i') }}
    </div>

@else

    <div class="text-sm text-gray-400">
        Belum ada pesan
    </div>

@endif

                        </div>

                    </a>

                @endforeach

            </div>

        </div>

        <!-- CHAT AREA -->
        <div class="w-2/3 flex flex-col">

            @if($selectedUser)

                <!-- HEADER -->
                <div class="bg-blue-600 text-white p-5">

                    <h2 class="text-xl font-bold">
                        {{ $selectedUser->name }}
                    </h2>

                    <p class="text-sm opacity-80">
                        Private Conversation
                    </p>

                </div>

                <!-- MESSAGE AREA -->
                <div id="chat-box"
                     class="flex-1 overflow-y-auto p-6 bg-gray-100 space-y-4">

                    @forelse($messages as $msg)

                        @if($msg->sender_id == Auth::id())

                            <!-- MY MESSAGE -->
                            <div class="flex justify-end">

                                <div class="max-w-md">

                                    <div class="bg-blue-600 text-white px-4 py-3 rounded-2xl shadow">
                                        {{ $msg->message }}
                                    </div>

                                    <div class="text-right text-xs text-gray-500 mt-1">
                                        {{ $msg->created_at->format('H:i') }}
                                    </div>

                                </div>

                            </div>

                        @else

                            <!-- OTHER MESSAGE -->
                            <div class="flex justify-start">

                                <div class="max-w-md">

                                    <div class="bg-white px-4 py-3 rounded-2xl shadow">
                                        {{ $msg->message }}
                                    </div>

                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $msg->created_at->format('H:i') }}
                                    </div>

                                </div>

                            </div>

                        @endif

                    @empty

                        <div class="text-center text-gray-500">
                            Belum ada percakapan.
                        </div>

                    @endforelse

                </div>

                <!-- FORM -->
                <div class="border-t bg-white p-4">

                    <form method="POST"
                          action="{{ route('message.store') }}"
                          class="flex gap-3">

                        @csrf

                        <input
                            type="hidden"
                            name="receiver_id"
                            value="{{ $selectedUser->id }}"
                        >

                        <input
                            type="text"
                            name="message"
                            placeholder="Tulis pesan..."
                            class="w-full border rounded-2xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required
                        >

                        <button
                            type="submit"
                            class="bg-blue-600 text-white px-6 rounded-2xl hover:bg-blue-700"
                        >
                            Kirim
                        </button>

                    </form>

                </div>

            @else

                <!-- EMPTY CHAT -->
                <div class="flex items-center justify-center h-full bg-gray-100">

                    <div class="text-center">

                        <div class="text-6xl mb-4">
                            💬
                        </div>

                        <h2 class="text-2xl font-bold text-gray-700">
                            Pilih User
                        </h2>

                        <p class="text-gray-500 mt-2">
                            Mulai percakapan private sekarang.
                        </p>

                    </div>

                </div>

            @endif

        </div>

    </div>

</div>

<!-- AUTO SCROLL -->
<script>

    const chatBox = document.getElementById('chat-box');

    // Auto scroll bawah
    if(chatBox){
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    // Auto refresh tiap 2 detik
    setInterval(() => {

        window.location.reload();

    }, 2000);

</script>

</x-app-layout>