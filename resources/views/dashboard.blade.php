<x-app-layout>
    <div class="min-h-screen bg-gray-100 py-10">

        <div class="max-w-6xl mx-auto px-6">

            <div class="bg-white shadow-xl rounded-2xl p-8 mb-8">
                <h1 class="text-4xl font-bold text-gray-800 mb-2">
                    welcome 👋
                </h1>

                <p class="text-gray-600 text-lg">
                    Haiii,🤗 {{ Auth::user()->name }}. Selamat datang di aplikasi chat NgobrolYuk.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">

                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition">
                    <div class="text-5xl mb-4">💬</div>
                    <h2 class="text-2xl font-bold mb-2">Mulai Chat</h2>
                    <p class="text-gray-600 mb-4">
                        Masuk ke halaman percakapan dan mulai mengirim pesan.
                    </p>

                    <a href="/chat"
                       class="bg-blue-600 text-white px-5 py-2 rounded-xl hover:bg-blue-700">
                        Buka Chat
                    </a>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition">
                    <div class="text-5xl mb-4">👤</div>
                    <h2 class="text-2xl font-bold mb-2">Profile</h2>
                    <p class="text-gray-600 mb-4">
                        Kelola akun dan data profile pengguna.
                    </p>

                    <a href="/profile"
                       class="bg-green-600 text-white px-5 py-2 rounded-xl hover:bg-green-700">
                        Edit Profile
                    </a>
                </div>

                <!-- GROUP CHAT -->
<a href="{{ route('groups.index') }}">

    <div class="bg-green-600 text-white rounded-3xl p-8 shadow-xl hover:scale-105 transition">

        <div class="text-5xl mb-4">
            👥
        </div>

        <h2 class="text-2xl font-bold mb-2">
            Group Chat
        </h2>

        <p class="opacity-80">
            Buat dan gabung ke group chat realtime.
        </p>

    </div>

</a>
</div>

                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition">
                    <div class="text-5xl mb-4">🚪</div>
                    <h2 class="text-2xl font-bold mb-2">Logout</h2>
                    <p class="text-gray-600 mb-4">
                        see you later...
                    </p>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit"
                            class="bg-red-600 text-white px-5 py-2 rounded-xl hover:bg-red-700">
                            Logout
                        </button>
                    </form>
                </div>

            </div>

        </div>

    </div>
</x-app-layout>