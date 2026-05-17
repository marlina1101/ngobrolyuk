# 💬 NgobrolYuk

Aplikasi chat realtime berbasis web yang dibangun dengan **Laravel 12**, **Laravel Reverb** (WebSocket), dan **Alpine.js**. Mendukung chat private antar pengguna, chat grup dengan sistem admin, kirim file, indikator mengetik, serta tracking status **Online / Offline** secara realtime.

---

## ✨ Fitur

- 🔐 **Autentikasi** — Register, Login, Logout, dan Profile via Laravel Breeze
- 💬 **Chat Private** — Pesan antar dua user secara realtime, mendukung lampiran file (maks 2MB)
- 👥 **Chat Grup** — Buat grup, pilih anggota, kirim pesan ke semua anggota secara realtime
- 👑 **Sistem Admin Grup** — Pembuat grup otomatis jadi Admin, bisa tambah anggota dan hapus grup
- 🔒 **Akses Terbatas Grup** — Hanya anggota yang bisa masuk dan kirim pesan di grup
- 🟢 **Tracking Online/Offline** — Status realtime muncul di sidebar chat dan daftar anggota grup
- ⌨️ **Typing Indicator** — "Sedang mengetik..." muncul realtime via WebSocket Whisper
- ⚡ **Realtime tanpa Reload** — Pesan langsung muncul via WebSocket, tidak perlu refresh halaman

---

## 🛠️ Teknologi

| Teknologi | Versi | Fungsi |
|---|---|---|
| PHP | ^8.2 | Backend |
| Laravel | ^12.0 | Framework utama (MVC) |
| Laravel Reverb | ^1.10 | WebSocket server (self-hosted) |
| Laravel Breeze | ^2.4 | Autentikasi |
| MySQL | - | Database |
| Alpine.js | ^3.4 | Reaktivitas UI |
| Tailwind CSS | ^3.1 | Styling |
| Vite | ^6.0 | Bundler aset frontend |
| Laravel Echo | ^2.3 | WebSocket client (JS) |
| Pusher JS | ^8.5 | Driver WebSocket client |

---

## 📁 Struktur Folder Penting

```
ngobrolyuk/
├── app/
│   ├── Events/
│   │   ├── MessageSent.php             # Broadcast pesan private → PresenceChannel('chat')
│   │   └── GroupMessageSent.php        # Broadcast pesan grup → Channel('group-chat')
│   ├── Http/Controllers/
│   │   ├── ChatController.php          # Halaman chat private, query pesan & last message
│   │   ├── MessageController.php       # Kirim pesan private, return JSON untuk AJAX
│   │   └── GroupController.php         # CRUD grup, cek anggota, cek creator
│   └── Models/
│       ├── User.php                    # Relasi: belongsToMany Group
│       ├── Message.php                 # sender_id, receiver_id, message, file
│       ├── Group.php                   # name, created_by | hasMember(), isCreator()
│       └── GroupMessage.php            # group_id, sender_id, message
├── database/migrations/
│   ├── create_messages_table.php
│   ├── create_groups_table.php
│   ├── add_created_by_to_groups_table.php  # Kolom admin/pembuat grup
│   ├── create_group_user_table.php     # Pivot many-to-many
│   └── create_group_messages_table.php
├── resources/
│   ├── js/
│   │   ├── app.js                      # Presence tracking, realtime chat, AJAX kirim pesan
│   │   └── bootstrap.js               # Konfigurasi Echo + Axios
│   └── views/
│       ├── chat/
│       │   └── index.blade.php         # Halaman chat private
│       ├── groups/
│       │   ├── index.blade.php         # Daftar grup, form buat grup, badge Admin
│       │   └── show.blade.php          # Chat grup, daftar anggota, tambah anggota
│       └── layouts/
│           └── app.blade.php           # Layout utama (@stack('js-vars') sebelum Vite)
├── routes/
│   ├── web.php                         # Semua route, semua dalam middleware auth
│   └── channels.php                    # Presence channel 'chat'
└── .env
```

---

## ⚙️ Skema Database

### `users`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | Primary key |
| name | varchar | Nama pengguna |
| email | varchar unique | Email |
| password | varchar | Password (di-hash bcrypt) |
| timestamps | - | created_at, updated_at |

### `messages`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | Primary key |
| sender_id | bigint FK | Pengirim → users.id |
| receiver_id | bigint FK | Penerima → users.id |
| message | text | Isi pesan |
| file | varchar nullable | Path file di storage/public/chat_files |
| timestamps | - | created_at, updated_at |

### `groups`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | Primary key |
| name | varchar | Nama grup |
| created_by | bigint FK | Pembuat/admin grup → users.id |
| timestamps | - | created_at, updated_at |

### `group_user` (pivot)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | Primary key |
| group_id | bigint FK | groups.id |
| user_id | bigint FK | users.id |
| timestamps | - | created_at, updated_at |

### `group_messages`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | Primary key |
| group_id | bigint FK | groups.id |
| sender_id | bigint FK | users.id |
| message | text | Isi pesan |
| timestamps | - | created_at, updated_at |

---

## 🔗 Daftar Route

Semua route dilindungi middleware `auth` — wajib login.

| Method | URL | Nama Route | Controller | Keterangan |
|---|---|---|---|---|
| GET | `/` | - | - | Redirect ke /login |
| GET | `/login` | login | Auth | Halaman login |
| POST | `/login` | - | Auth | Proses login |
| GET | `/register` | register | Auth | Halaman register |
| POST | `/register` | - | Auth | Proses register |
| POST | `/logout` | logout | Auth | Logout |
| GET | `/dashboard` | dashboard | - | Dashboard |
| GET | `/chat/{id?}` | chat.index | ChatController | Halaman chat private |
| POST | `/message` | message.store | MessageController | Kirim pesan private |
| GET | `/groups` | groups.index | GroupController | Daftar semua grup |
| POST | `/groups` | groups.store | GroupController | Buat grup baru |
| GET | `/groups/{id}` | groups.show | GroupController | Chat grup (anggota saja) |
| POST | `/groups/{id}/message` | groups.message | GroupController | Kirim pesan grup (anggota saja) |
| POST | `/groups/{id}/add-member` | groups.addMember | GroupController | Tambah anggota (admin saja) |
| DELETE | `/groups/{id}` | groups.destroy | GroupController | Hapus grup (admin saja) |
| GET | `/profile` | profile.edit | ProfileController | Edit profil |
| PATCH | `/profile` | profile.update | ProfileController | Update profil |
| DELETE | `/profile` | profile.destroy | ProfileController | Hapus akun |

---

## 🚀 Cara Instalasi & Menjalankan

### Prasyarat

- PHP >= 8.2
- Composer
- Node.js >= 18 & NPM
- MySQL

---

### Langkah 1 — Masuk ke Folder Proyek

```bash
cd ngobrolyuk
```

---

### Langkah 2 — Install Dependensi PHP

```bash
composer install
```

---

### Langkah 3 — Install Dependensi JavaScript

```bash
npm install
```

---

### Langkah 4 — Salin File Environment

```bash
cp .env.example .env
php artisan key:generate
```

---

### Langkah 5 — Konfigurasi `.env`

Buka file `.env` dan sesuaikan bagian berikut:

```env
APP_NAME=NgobrolYuk
APP_URL=http://127.0.0.1:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=app_cating
DB_USERNAME=root
DB_PASSWORD=           # isi password MySQL kamu

# ⚠️ WAJIB 'file' — jika 'database', WebSocket auth selalu gagal
SESSION_DRIVER=file

# Broadcasting & Queue
BROADCAST_CONNECTION=reverb
QUEUE_CONNECTION=database

# Reverb WebSocket
REVERB_APP_ID=498196
REVERB_APP_KEY=feuxewsbcsbrojl18jlm
REVERB_APP_SECRET=h0bbgjntid0hcrwhnpsx
REVERB_HOST="127.0.0.1"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

---

### Langkah 6 — Buat Database

Buka MySQL dan jalankan:

```sql
CREATE DATABASE app_cating;
```

---

### Langkah 7 — Jalankan Migrasi

```bash
php artisan migrate
```

Jika kolom `created_by` belum ada di tabel `groups` (migrasi sudah tercatat tapi gagal dibuat), jalankan SQL ini langsung di MySQL:

```sql
ALTER TABLE `groups`
ADD COLUMN `created_by` BIGINT UNSIGNED NOT NULL AFTER `name`,
ADD CONSTRAINT `groups_created_by_foreign`
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE;
```

---

### Langkah 8 — Symlink Storage

```bash
php artisan storage:link
```

---

### Langkah 9 — Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
```

---

### Langkah 10 — Build Aset Frontend

```bash
npm run build
```

---

### Langkah 11 — Jalankan Semua Service

Buka **4 terminal** secara bersamaan:

```bash
# Terminal 1 — Laravel server
php artisan serve

# Terminal 2 — Reverb WebSocket server
php artisan reverb:start

# Terminal 3 — Queue worker (wajib agar broadcast berfungsi)
php artisan queue:work

# Terminal 4 — Vite dev server (opsional, untuk development)
npm run dev
```

> 💡 **Shortcut:** Jalankan semuanya sekaligus:
> ```bash
> composer run dev
> ```

---

### Langkah 12 — Buka Aplikasi

```
http://127.0.0.1:8000
```

Daftarkan minimal **2 akun** untuk mencoba fitur chat dan tracking online/offline.

---

## 🧪 Cara Tes Fitur

### Chat Private & Tracking Online/Offline
1. Buka **2 browser berbeda** (Chrome + Firefox, atau Chrome + Incognito)
2. Login dengan akun berbeda di masing-masing browser
3. Buka halaman **Chat** di keduanya
4. Status **● Online** langsung muncul di sidebar
5. Tutup salah satu tab → status berubah jadi **● Offline** secara otomatis

### Chat Grup
1. Login sebagai User A → buka `/groups` → klik **Buat Grup**
2. Isi nama grup, centang anggota yang ingin diundang → klik **Buat Grup**
3. Klik **Masuk Chat** di grup yang baru dibuat
4. Login sebagai User B (yang sudah ditambahkan) → buka `/groups` → masuk ke grup yang sama
5. Keduanya bisa saling kirim pesan realtime
6. User yang **bukan anggota** tidak akan melihat tombol "Masuk Chat" dan akan mendapat **403** jika akses langsung via URL

---

## 👑 Aturan Sistem Grup

| Aksi | Siapa yang Bisa |
|---|---|
| Buat grup | Semua user yang login |
| Masuk halaman chat grup | Anggota grup saja |
| Kirim pesan di grup | Anggota grup saja |
| Tambah anggota baru | Admin (pembuat) grup saja |
| Hapus grup | Admin (pembuat) grup saja |

---

## ❗ Troubleshooting

**Status selalu Offline:**
- Pastikan `SESSION_DRIVER=file` di `.env` (bukan `database`)
- Pastikan `php artisan reverb:start` berjalan
- Pastikan `php artisan queue:work` berjalan
- Jalankan `php artisan config:clear` lalu `npm run build`
- Cek Console browser (F12) untuk error WebSocket

**Pesan tidak muncul realtime:**
- Pastikan `BROADCAST_CONNECTION=reverb` di `.env`
- Pastikan queue worker berjalan

**Error 403 saat masuk grup:**
- User belum ditambahkan sebagai anggota grup
- Minta admin grup untuk menambahkan lewat halaman chat grup

**Error `Column not found: created_by`:**
- Jalankan SQL ALTER TABLE secara manual (lihat Langkah 7)

**Error 500 saat upload file:**
- Pastikan sudah jalankan `php artisan storage:link`
- Pastikan folder `storage/` writable: `chmod -R 775 storage/`

**WebSocket gagal connect:**
- Pastikan port 8080 tidak dipakai proses lain
- Pastikan semua `VITE_REVERB_*` sudah sesuai di `.env`

---

## 👨‍💻 Dibuat Dengan

- [Laravel](https://laravel.com)
- [Laravel Reverb](https://reverb.laravel.com)
- [Laravel Breeze](https://github.com/laravel/breeze)
- [Alpine.js](https://alpinejs.dev)
- [Tailwind CSS](https://tailwindcss.com)
- [Laravel Echo](https://github.com/laravel/echo)