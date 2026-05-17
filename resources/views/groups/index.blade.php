<x-app-layout>

<div class="max-w-5xl mx-auto py-8 px-4">

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">👥 Daftar Grup</h1>
        <button
            onclick="document.getElementById('modalBuatGrup').classList.remove('hidden')"
            class="bg-blue-600 text-white px-5 py-2.5 rounded-xl hover:bg-blue-700 font-semibold"
        >
            + Buat Grup
        </button>
    </div>

    {{-- DAFTAR GRUP --}}
    @forelse($groups as $group)

        <div class="bg-white rounded-2xl shadow border border-gray-100 p-5 mb-4 flex items-center justify-between">

            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xl">👥</span>
                    <h2 class="text-lg font-bold text-gray-800">{{ $group->name }}</h2>

                    {{-- Badge kreator --}}
                    @if($group->isCreator(Auth::id()))
                        <span class="text-xs bg-blue-100 text-blue-600 font-semibold px-2 py-0.5 rounded-full">
                            Admin
                        </span>
                    @endif
                </div>

                <p class="text-sm text-gray-500 mt-1">
                    Dibuat oleh: <strong>{{ $group->creator->name ?? 'Tidak diketahui' }}</strong>
                    · {{ $group->users->count() }} anggota
                </p>

                {{-- Daftar anggota --}}
                <div class="flex gap-1 mt-2 flex-wrap">
                    @foreach($group->users as $member)
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                            {{ $member->name }}
                        </span>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-2 flex-shrink-0 ml-4">

                {{-- Tombol masuk — hanya anggota --}}
                @if($group->hasMember(Auth::id()))
                    <a href="{{ route('groups.show', $group->id) }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-blue-700">
                        Masuk Chat
                    </a>
                @else
                    <span class="text-xs text-gray-400 italic">Bukan anggota</span>
                @endif

                {{-- Hapus grup — hanya creator --}}
                @if($group->isCreator(Auth::id()))
                    <form method="POST" action="{{ route('groups.destroy', $group->id) }}"
                          onsubmit="return confirm('Yakin hapus grup ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="text-red-500 hover:text-red-700 text-sm px-3 py-2 border border-red-200 rounded-xl hover:bg-red-50">
                            Hapus
                        </button>
                    </form>
                @endif

            </div>

        </div>

    @empty
        <div class="text-center text-gray-400 py-20">
            <div class="text-5xl mb-4">👥</div>
            <p class="text-lg">Belum ada grup. Buat grup pertama kamu!</p>
        </div>
    @endforelse

</div>

{{-- MODAL BUAT GRUP --}}
<div id="modalBuatGrup"
     class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50"
     onclick="if(event.target===this) this.classList.add('hidden')">

    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md mx-4">

        <h2 class="text-2xl font-bold text-gray-800 mb-6">Buat Grup Baru</h2>

        <form method="POST" action="{{ route('groups.store') }}">
            @csrf

            {{-- Nama grup --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Grup</label>
                <input
                    type="text"
                    name="name"
                    placeholder="Contoh: Tim Proyek Web"
                    required
                    class="w-full border rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            {{-- Pilih anggota --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Tambah Anggota (opsional)
                </label>
                <div class="border rounded-xl p-3 max-h-48 overflow-y-auto space-y-2">
                    @foreach($allUsers as $user)
                        <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 rounded-lg p-2">
                            <input type="checkbox" name="member_ids[]" value="{{ $user->id }}"
                                   class="rounded text-blue-600">
                            <span class="text-sm text-gray-700">{{ $user->name }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-gray-400 mt-1">Kamu otomatis jadi admin grup.</p>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700">
                    Buat Grup
                </button>
                <button type="button"
                        onclick="document.getElementById('modalBuatGrup').classList.add('hidden')"
                        class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-200">
                    Batal
                </button>
            </div>

        </form>

    </div>

</div>

</x-app-layout>