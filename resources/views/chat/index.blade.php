<x-app-layout>

<div class="max-w-4xl mx-auto py-10">

    <div class="bg-white shadow-xl rounded-2xl p-6">

        <h1 class="text-3xl font-bold mb-6">
            💬 NgobrolYuk Chat
        </h1>

        <div class="border rounded-xl p-4 h-96 overflow-y-auto mb-6 bg-gray-50">

            @foreach($messages as $msg)

                <div class="mb-4">

                    <div class="font-bold text-blue-600">
                        {{ $msg->sender->name }}
                    </div>

                    <div class="bg-white p-3 rounded-xl shadow mt-1">
                        {{ $msg->message }}
                    </div>

                </div>

            @endforeach

        </div>

        <form method="POST" action="{{ route('message.store') }}">

            @csrf

            <div class="flex gap-3">

                <input
                    type="text"
                    name="message"
                    placeholder="Tulis pesan..."
                    class="w-full border rounded-xl px-4 py-3"
                    required
                >

                <button
                    type="submit"
                    class="bg-blue-600 text-white px-6 rounded-xl hover:bg-blue-700"
                >
                    Kirim
                </button>

            </div>

        </form>

    </div>

</div>

</x-app-layout>