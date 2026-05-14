<x-app-layout>

<div class="max-w-5xl mx-auto py-8">

    <div class="bg-white shadow-2xl rounded-3xl p-8">

        <h1 class="text-3xl font-bold text-blue-600 mb-6">
            👥 Group Chat
        </h1>

        <!-- FORM -->
        <form method="POST"
              action="{{ route('groups.store') }}"
              class="flex gap-3 mb-8">

            @csrf

            <input
                type="text"
                name="name"
                placeholder="Nama group..."
                class="w-full border rounded-2xl px-5 py-3"
                required
            >

            <button
                type="submit"
                class="bg-blue-600 text-white px-6 rounded-2xl"
            >
                Buat
            </button>

        </form>

        <!-- LIST GROUP -->
        <div class="space-y-4">

            @forelse($groups as $group)

                <div class="border rounded-2xl p-5 hover:bg-gray-50">

                    <div class="flex justify-between items-center">

                        <div>

                            <h2 class="text-xl font-bold text-gray-800">
                                {{ $group->name }}
                            </h2>

                            <p class="text-sm text-gray-500">
                                {{ $group->users->count() }} anggota
                            </p>

                        </div>

                        <a href="#"
                           class="bg-blue-600 text-white px-5 py-2 rounded-xl">

                            Masuk

                        </a>

                    </div>

                </div>

            @empty

                <div class="text-center text-gray-500">
                    Belum ada group.
                </div>

            @endforelse

        </div>

    </div>

</div>

</x-app-layout>