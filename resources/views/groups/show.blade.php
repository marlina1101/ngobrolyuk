<x-app-layout>

<div class="max-w-6xl mx-auto py-8">

    <div class="bg-white shadow-2xl rounded-3xl overflow-hidden">

        <!-- HEADER -->
        <div class="bg-blue-600 text-white p-6">

            <h1 class="text-3xl font-bold">
                👥 {{ $group->name }}
            </h1>

            <p class="text-sm opacity-80 mt-1">
                {{ $group->users->count() }} anggota
            </p>

        </div>

        <!-- CHAT AREA -->
        <div id="chat-box"
             class="h-[500px] overflow-y-auto p-6 bg-gray-100 space-y-4">

            @forelse($messages as $msg)

                @if($msg->sender_id == Auth::id())

                    <!-- MY MESSAGE -->
                    <div class="flex justify-end">

                        <div class="max-w-md">

                            <div class="bg-blue-600 text-white px-4 py-3 rounded-2xl shadow">

                                <div class="text-xs opacity-70 mb-1">
                                    Kamu
                                </div>

                                {{ $msg->message }}

                            </div>

                            <div class="text-right text-xs text-gray-500 mt-1">
                                {{ $msg->created_at->format('H:i') }}
                            </div>

                        </div>

                    </div>

                @else

                    <!-- OTHER -->
                    <div class="flex justify-start">

                        <div class="max-w-md">

                            <div class="bg-white px-4 py-3 rounded-2xl shadow">

                                <div class="text-xs font-bold text-blue-600 mb-1">
                                    {{ $msg->sender->name }}
                                </div>

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
                    Belum ada pesan group.
                </div>

            @endforelse

        </div>

        <!-- FORM -->
        <div class="border-t bg-white p-4">

            <form method="POST"
                  action="{{ route('groups.message', $group->id) }}"
                  class="flex gap-3">

                @csrf

                <input
                    type="text"
                    name="message"
                    placeholder="Tulis pesan group..."
                    class="w-full border rounded-2xl px-5 py-3"
                >

                <button
                    type="submit"
                    class="bg-blue-600 text-white px-6 rounded-2xl">

                    Kirim

                </button>

            </form>

        </div>

    </div>

</div>

<script>

    const chatBox = document.getElementById('chat-box');

    if(chatBox){
        chatBox.scrollTop = chatBox.scrollHeight;
    }

</script>

</x-app-layout>